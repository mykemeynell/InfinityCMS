<?php

namespace Infinity\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Infinity\Facades\Infinity;
use Infinity\Translator;
use JetBrains\PhpStorm\Pure;

trait Translatable
{
    /**
     * Check if this model can translate.
     *
     * @return bool
     */
    public function translatable(): bool
    {
        if (empty($this->translatableFields)) {
            return false;
        }

        return !empty($this->getTranslatableAttributes());
    }

    /**
     * Load translations relation.
     *
     * @return mixed
     */
    public function translations(): mixed
    {
        return $this->hasMany(Infinity::model('Translation'), 'foreign_key', $this->getKeyName())
            ->where('table_name', $this->getTable())
            ->whereIn('locale', config('Infinity.multilingual.locales', []));
    }

    /**
     * This scope eager loads the translations for the default and the fallback locale only.
     * We can use this as a shortcut to improve performance in our application.
     *
     * @param Builder     $query
     * @param string|null $locale
     * @param bool|string $fallback
     */
    public function scopeWithTranslation(Builder $query, ?string $locale = null, bool|string $fallback = true)
    {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $query->with(['translations' => function (Relation $query) use ($locale, $fallback) {
            $query->where(function ($q) use ($locale, $fallback) {
                $q->where('locale', $locale);

                if ($fallback !== false) {
                    $q->orWhere('locale', $fallback);
                }
            });
        }]);
    }

    /**
     * This scope eager loads the translations for the default and the fallback locale only.
     * We can use this as a shortcut to improve performance in our application.
     *
     * @param Builder           $query
     * @param array|string|null $locales
     * @param bool|string       $fallback
     */
    public function scopeWithTranslations(Builder $query, array|string $locales = null, bool|string $fallback = true)
    {
        if (is_null($locales)) {
            $locales = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $query->with(['translations' => function (Relation $query) use ($locales, $fallback) {
            if (is_null($locales)) {
                return;
            }

            $query->where(function ($q) use ($locales, $fallback) {
                if (is_array($locales)) {
                    $q->whereIn('locale', $locales);
                } else {
                    $q->where('locale', $locales);
                }

                if ($fallback !== false) {
                    $q->orWhere('locale', $fallback);
                }
            });
        }]);
    }

    /**
     * Translate the whole model.
     *
     * @param string|null $language
     * @param bool|string $fallback
     *
     * @return Translator
     */
    public function translate(?string $language = null, bool|string $fallback = true): Translator
    {
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return (new Translator($this))->translate($language, $fallback);
    }

    /**
     * Get a single translated attribute.
     *
     * @param $attribute
     * @param null $language
     * @param bool $fallback
     *
     * @return null
     */
    public function getTranslatedAttribute($attribute, $language = null, bool $fallback = true)
    {
        // If multilingual is not enabled don't check for translations
        if (!config('Infinity.multilingual.enabled')) {
            return $this->getAttributeValue($attribute);
        }

        [$value] = $this->getTranslatedAttributeMeta($attribute, $language, $fallback);

        return $value;
    }

    /**
     * @param            $attribute
     * @param array|null $languages
     * @param bool       $fallback
     *
     * @return array
     */
    public function getTranslationsOf($attribute, array $languages = null, bool $fallback = true): array
    {
        if (is_null($languages)) {
            $languages = config('Infinity.multilingual.locales', [config('Infinity.multilingual.default')]);
        }

        $response = [];
        foreach ($languages as $language) {
            $response[$language] = $this->getTranslatedAttribute($attribute, $language, $fallback);
        }

        return $response;
    }

    /**
     * @param $attribute
     * @param $locale
     * @param $fallback
     *
     * @return array
     */
    public function getTranslatedAttributeMeta($attribute, $locale = null, $fallback = true): array
    {
        // Attribute is translatable
        //
        if (!in_array($attribute, $this->getTranslatableAttributes())) {
            return [$this->getAttribute($attribute), config('Infinity.multilingual.default'), false];
        }

        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $default = config('Infinity.multilingual.default');

        if ($default == $locale) {
            return [$this->getAttribute($attribute), $default, true];
        }

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translations = $this->getRelation('translations')
            ->where('column_name', $attribute);

        $localeTranslation = $translations->where('locale', $locale)->first();

        if ($localeTranslation) {
            return [$localeTranslation->value, $locale, true];
        }

        if ($fallback == $locale) {
            return [$this->getAttribute($attribute), $locale, false];
        }

        if ($fallback == $default) {
            return [$this->getAttribute($attribute), $locale, false];
        }

        $fallbackTranslation = $translations->where('locale', $fallback)->first();

        if ($fallbackTranslation && $fallback !== false) {
            return [$fallbackTranslation->value, $locale, true];
        }

        return [null, $locale, false];
    }

    /**
     * Get attributes that can be translated.
     *
     * @return array
     */
    public function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatableFields') ? $this->translatableFields : [];
    }

    /**
     * @param       $attribute
     * @param array $translations
     * @param bool  $save
     *
     * @return array
     */
    public function setAttributeTranslations($attribute, array $translations, bool $save = false): array
    {
        $response = [];

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $default = config('Infinity.multilingual.default', 'en');
        $locales = config('Infinity.multilingual.locales', [$default]);

        foreach ($locales as $locale) {
            if (empty($translations[$locale])) {
                continue;
            }

            if ($locale == $default) {
                $this->$attribute = $translations[$locale];
                continue;
            }

            $tranlator = $this->translate($locale, false);
            $tranlator->$attribute = $translations[$locale];

            if ($save) {
                $tranlator->save();
            }

            $response[] = $tranlator;
        }

        return $response;
    }

    /**
     * Get entries filtered by translated value.
     *
     * @param                   $query
     * @param string            $field    the field your looking to find a value in.
     * @param string            $operator value you are looking for or a relation modifier such as LIKE, =, etc.
     * @param string|null       $value    value you are looking for. Only use if you supplied an operator.
     * @param array|string|null $locales  locale(s) you are looking for the field.
     * @param bool              $default  if true checks for $value is in default database before checking translations.
     *
     * @return Builder
     * @example  Class::whereTranslation('title', '=', 'zuhause', ['de', 'iu'])
     * @example  $query->whereTranslation('title', '=', 'zuhause', ['de', 'iu'])
     */
    public static function scopeWhereTranslation($query, string $field, string $operator, string $value = null, array|string $locales = null, bool $default = true): Builder
    {
        if ($locales && !is_array($locales)) {
            $locales = [$locales];
        }
        if (!isset($value)) {
            $value = $operator;
            $operator = '=';
        }

        $self = new static();
        $table = $self->getTable();

        return $query->whereIn(
            $self->getKeyName(),
            Translation::where('table_name', $table)
                ->where('column_name', $field)
                ->where('value', $operator, $value)
                ->when(!is_null($locales), function ($query) use ($locales) {
                    return $query->whereIn('locale', $locales);
                })
                ->pluck('foreign_key')
        )->when($default, function ($query) use ($field, $operator, $value) {
            return $query->orWhere($field, $operator, $value);
        });
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasTranslatorMethod($name): bool
    {
        if (!isset($this->translatorMethods)) {
            return false;
        }

        return isset($this->translatorMethods[$name]);
    }

    /**
     * @param $name
     *
     * @return mixed|void
     */
    public function getTranslatorMethod($name)
    {
        if (!$this->hasTranslatorMethod($name)) {
            return;
        }

        return $this->translatorMethods[$name];
    }

    /**
     * @param array $attributes
     * @param       $locales
     *
     * @return void
     */
    public function deleteAttributeTranslations(array $attributes, $locales = null)
    {
        $this->translations()
            ->whereIn('column_name', $attributes)
            ->when(!is_null($locales), function ($query) use ($locales) {
                $method = is_array($locales) ? 'whereIn' : 'where';

                return $query->$method('locale', $locales);
            })
            ->delete();
    }

    /**
     * @param $attribute
     * @param $locales
     *
     * @return void
     */
    public function deleteAttributeTranslation($attribute, $locales = null)
    {
        $this->translations()
            ->where('column_name', $attribute)
            ->when(!is_null($locales), function ($query) use ($locales) {
                $method = is_array($locales) ? 'whereIn' : 'where';

                return $query->$method('locale', $locales);
            })
            ->delete();
    }

    /**
     * Prepare translations and set default locale field value.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function prepareTranslations(\Illuminate\Http\Request $request): array
    {
        $translations = [];

        // Translatable Fields
        $transFields = $this->getTranslatableAttributes();

        $fields = !empty($request->attributes->get('breadRows')) ? array_intersect($request->attributes->get('breadRows'), $transFields) : $transFields;

        foreach ($fields as $field) {
            if (!$request->input($field.'_i18n')) {
                throw new \Exception('Invalid Translatable field'.$field);
            }

            $trans = json_decode($request->input($field.'_i18n'), true);

            // Set the default local value
            $request->merge([$field => $trans[config('Infinity.multilingual.default', 'en')]]);

            $translations[$field] = $this->setAttributeTranslations(
                $field,
                $trans
            );

            // Remove field hidden input
            unset($request[$field.'_i18n']);
        }

        // Remove language selector input
        unset($request['i18n_selector']);

        return $translations;
    }

    /**
     * Prepare translations and set default locale field value.
     *
     * @param        $field
     * @param object $requestData
     *
     * @return array
     */
    public function prepareTranslationsFromArray($field, object &$requestData): array
    {
        $translations = [];

        $field = 'field_display_name_'.$field;

        if (empty($requestData[$field.'_i18n'])) {
            throw new Exception('Invalid Translatable field '.$field);
        }

        $trans = json_decode($requestData[$field.'_i18n'], true);

        // Set the default local value
        $requestData['display_name'] = $trans[config('Infinity.multilingual.default', 'en')];

        $translations['display_name'] = $this->setAttributeTranslations(
            'display_name',
            $trans
        );

        // Remove field hidden input
        unset($requestData[$field.'_i18n']);

        return $translations;
    }

    /**
     * Save translations.
     *
     * @param object $translations
     *
     * @return void
     */
    public function saveTranslations(object $translations)
    {
        foreach ($translations as $field => $locales) {
            foreach ($locales as $locale => $translation) {
                $translation->save();
            }
        }
    }
}
