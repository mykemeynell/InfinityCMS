@php
    /** @var \Infinity\Resources\Resource $resource */
    /** @var \Infinity\Resources\Fields\Field $field */
    /** @var \Illuminate\Database\Eloquent\Model $model */
    /** @var mixed $modelValue */
    /** @var mixed $rawValue */
    /** @var bool $isReadOnly */
    /** @var bool $isDisabled */
@endphp

@if(!empty($modelValue))
    <img src="{{ $modelValue }}" class="max-w-sm max-h-40 mb-5">
@endif

<input type="file"
       id="grid-{{ $resource->getIdentifier() }}-{{ $field->getFieldName() }}"
       @if(!$isDisabled && !$isReadOnly) name="{{ $field->getFieldName() }}" @endif
       @if($isDisabled) disabled @endif
       @if($isReadOnly) readonly @endif
       class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer @if($isReadOnly || $isDisabled) light:bg-gray-200 dark:bg-gray-800 @else light:bg-white dark:text-gray-400 @endif  focus:outline-none focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
       value=""/>
