<?php

namespace Infinity\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Facades\Log, Str};
use Infinity\Actions;
use Infinity\Actions\ActionInterface;
use Infinity\Http\Controllers\InfinityBaseController;
use Infinity\Resources\Fields\ID;
use JetBrains\PhpStorm\Pure;

abstract class Resource
{
    public static string $model;
    public static bool $displayInNavigation = true;
    public static string $icon = 'fas fa-infinity / fad fa-infinity';
    public static ?string $controller;

    private array $fields = [];
    private array $formFields = [];

    public function __construct()
    {
        $this->initialiseFieldsProperties();
    }

    /**
     * Initialise the field properties.
     *
     * @return void
     */
    public function initialiseFieldsProperties()
    {
        $fields = $this->fields();
        $formFields = $this->formFields();

        if (!in_array('id', collect($fields)->map(function ($field) {
            return $field->getFieldName();
        })->toArray())) {
            if (config('app.debug', false)) {
                Log::info(sprintf("No ID field was present on resource %s, so one has been added with default field.",
                    class_basename($this)));
            }

            array_unshift($fields, ID::make());
        }

        if (!in_array('id', collect($formFields)->map(function ($field) {
            return $field->getFieldName();
        })->toArray())) {
            if (config('app.debug', false)) {
                Log::info(sprintf("No ID field was present on resource %s, so one has been added with default field.",
                    class_basename($this)));
            }

            array_unshift($formFields, ID::make());
        }

        foreach ($fields as $field) {
            $this->fields[$field->getFieldName()] = $field;
        }

        foreach ($formFields as $formField) {
            $this->formFields[$formField->getFieldName()] = $formField;
        }
    }

    /**
     * Fields that the resource can output.
     *
     * @return \Infinity\Resources\Fields\Field[]
     */
    abstract public function fields(): array;

    /**
     * Get the fields that are to be displayed when output to a form.
     *
     * @return \Infinity\Resources\Fields\Field[]
     */
    public function formFields(): array
    {
        return $this->fields();
    }

    /**
     * Cards that can be output as a part of the resource.
     *
     * @return \Infinity\Cards\Card[]
     */
    public function cards(): array
    {
        return [];
    }

    /**
     * Additional routes that are to be registered with this resource.
     *
     * @return \Illuminate\Http\Resources\Routes\AdditionalRoute[]
     */
    public function additionalRoutes(): array
    {
        return [];
    }

    public function excludedRoutes(): array
    {
        $excludedRoutes = [];

        foreach ($this->excludedActions() as $action) {
            if ($action instanceof ActionInterface) {
                $action = $action::class;
            }

            $excludedRoutes[] = match ($action) {
                Actions\ViewAction::class => ['show'],
                Actions\EditAction::class => ['edit', 'update'],
                Actions\DeleteAction::class => ['showDelete', 'destroy'],
                Actions\CreateAction::class => ['create', 'store']
            };
        }

        return collect($excludedRoutes)->flatten()->toArray();
    }

    /**
     * Actions that should not be allowed.
     *
     * @return array
     */
    public function excludedActions(): array
    {
        return [];
    }

    /**
     * Addtional authentication gates that are to be registered within this resource.
     *
     * @return array
     */
    public function additionalGates(): array
    {
        return [];
    }

    /**
     * Get the excluded gate suffixes.
     *
     * @return array
     */
    public function excludedGates(): array
    {
        $excludedGates = [];

        foreach ($this->excludedActions() as $action) {
            if ($action instanceof ActionInterface) {
                $action = $action::class;
            }

            $excludedGates[] = match ($action) {
                Actions\ViewAction::class => ['read'],
                Actions\EditAction::class => ['edit', 'update'],
                Actions\DeleteAction::class => ['showDelete', 'delete'],
                Actions\CreateAction::class => ['add', 'create']
            };
        }

        return collect($excludedGates)->flatten()->map(function ($gate) {
            return sprintf("%s.%s", $this->getIdentifier(), $gate);
        })->toArray();
    }

    /**
     * Get the identifier of this resource.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->getDisplayNameLower();
    }

    /**
     * Get the display name as lower.
     *
     * @param bool $singular
     *
     * @return string
     */
    public function getDisplayNameLower(bool $singular = false): string
    {
        return Str::lower($this->getDisplayName($singular));
    }

    /**
     * Get the display name as it should appear to the user.
     *
     * @param bool $singular
     *
     * @return string
     */
    public function getDisplayName(bool $singular = false): string
    {
        $numeracy = $singular ? 'singular' : 'plural';

        return Str::title(
            Str::$numeracy(class_basename($this))
        );
    }

    /**
     * Test if an action is possible.
     *
     * @param \Infinity\Actions\ActionInterface $action
     *
     * @return bool
     */
    #[Pure] public function isActionPossible(ActionInterface $action): bool
    {
        return !in_array($action::class, $this->excludedActions());
    }

    /**
     * Get any fields that have not been declared to be hidden.
     *
     * @return array
     */
    public function getVisibleFields(): array
    {
        return collect($this->fields())->reject(function ($field) {
            return $field->isHidden();
        })->toArray();
    }

    /**
     * Get the controller for the resource type.
     *
     * @return string
     * @throws \Exception
     */
    public function getController(): string
    {
        if (!empty(static::$controller)) {
            return static::$controller;
        }

        $controller = sprintf("App\\Http\\Controllers\\Infinity\\%sController",
            $this->getDisplayName(true));

        $class = class_exists($controller)
            ? $controller
            : sprintf("Infinity\\Http\\Controllers\\%sController",
                'InfinityResource');

        $concreteClass = app($class);

        if (!$concreteClass instanceof InfinityBaseController) {
            throw new \Exception(sprintf("Controller for resource [%s] must be an instance of [%s]",
                __CLASS__, InfinityBaseController::class));
        }

        return $concreteClass::class;
    }

    /**
     * Get the resource class name.
     *
     * @return string
     */
    public function getResourceClassName(): string
    {
        return class_basename($this);
    }

    /**
     * Get the item icon.
     *
     * @return string
     */
    public function getIcon(): string
    {
        if (!Str::contains(static::$icon, '/')) {
            return static::$icon;
        }

        return is_fa_pro()
            ? trim(Str::afterLast(static::$icon, '/'))
            : trim(Str::beforeLast(static::$icon, '/'));
    }

    /**
     * Get the model class.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function modelClass(): Model
    {
        return app($this->model());
    }

    /**
     * Get the model class string.
     *
     * @return string
     */
    public function model(): string
    {
        return static::$model;
    }

    /**
     * Map a given model to fields passed to the resource type.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $fieldsSetName
     *
     * @return array
     * @throws \Exception
     */
    public function mapModelFieldValue(
        Model $model,
        string $fieldsSetName = 'fields'
    ): array {
        $fields = [];
        foreach ($this->{$fieldsSetName} as $column => $field) {
            $fields[$column] = clone ($this->{$fieldsSetName}[$column])
                ->setModelValue($model->getAttribute($column))
                ->setRawValue($model->{$column})
                ->setModel($model);
        }

        return $fields;
    }

    /**
     * Test if the resource should be displayed in the navigation.
     *
     * @return bool
     */
    public function displayInNavigation(): bool
    {
        return static::$displayInNavigation;
    }
}
