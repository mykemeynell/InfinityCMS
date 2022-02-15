@php
    /** @var \Infinity\Resources\Resource $resource */
    /** @var \Infinity\Resources\Fields\Field $field */
    /** @var \Illuminate\Database\Eloquent\Model $model */
    /** @var mixed $modelValue */
    /** @var mixed $rawValue */
    /** @var bool $isReadOnly */
    /** @var bool $isDisabled */

    $checked = isset($modelValue) && $modelValue == true;
    $class = "";
@endphp

<input type="checkbox"
       @if($isDisabled) disabled @endif
       @if($isReadOnly) readonly @endif
       value="{{ $checked }}"
       @if(!$isDisabled && !$isReadOnly) name="{{ $field->getFieldName() }}" @endif
       class="{{ $class }}"
       id="grid-{{ $resource->getIdentifier() }}-{{ $field->getFieldName() }}"
       @if($checked) checked @endif>
