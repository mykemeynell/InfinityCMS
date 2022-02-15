@php
    /** @var \Infinity\Resources\Resource $resource */
    /** @var \Infinity\Resources\Fields\Field $field */
    /** @var \Illuminate\Database\Eloquent\Model $model */
    /** @var mixed $modelValue */
    /** @var mixed $rawValue */
    /** @var bool $isReadOnly */
    /** @var bool $isDisabled */

    $isMultiple =
        is_a($rawValue, \Illuminate\Database\Eloquent\Relations\BelongsToMany::class) ||
        is_a($rawValue, \Illuminate\Database\Eloquent\Relations\HasMany::class) ||
        is_a($rawValue, \Illuminate\Database\Eloquent\Relations\HasManyThrough::class);

    $selected = $isMultiple
        ? $modelValue->map(function ($model) {
            return $model->getKey();
        })->toArray()
        : [$modelValue];
@endphp

@if(!$isMultiple)
    @include('infinity::fields.select', compact('resource', 'field', 'model', 'modelValue', 'rawValue', 'isMultiple', 'options', 'selected'))
@else
    @include('infinity::fields.checkbox_multiple', compact('resource', 'field', 'model', 'modelValue', 'rawValue', 'isMultiple', 'options', 'selected'))
@endif
