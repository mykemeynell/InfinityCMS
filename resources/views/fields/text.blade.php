@php
/** @var \Infinity\Resources\Resource $resource */
/** @var \Infinity\Resources\Fields\Field $field */
/** @var \Illuminate\Database\Eloquent\Model $model */
/** @var mixed $modelValue */
/** @var mixed $rawValue */
/** @var bool $isReadOnly */
/** @var bool $isDisabled */
@endphp

<input type="text"
       id="grid-{{ $resource->getIdentifier() }}-{{ $field->getFieldName() }}"
       @if(!$isDisabled && !$isReadOnly) name="{{ $field->getFieldName() }}" @endif
       @if($isDisabled) disabled @endif
       @if($isReadOnly) readonly @endif
       class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 @if($isReadOnly || $isDisabled) bg-gray-200 @else bg-white @endif rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
    value="{{ $modelValue }}"/>
