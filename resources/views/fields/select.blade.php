@php
/** @var \Infinity\Resources\Resource $resource */
/** @var \Infinity\Resources\Fields\Field $field */
/** @var \Illuminate\Database\Eloquent\Model $model */
/** @var mixed $modelValue */
/** @var mixed $rawValue */
/** @var bool $isReadOnly */
/** @var bool $isDisabled */
@endphp

<select id="grid-{{ $resource->getIdentifier() }}-{{ $field->getFieldName() }}"
        {{ isset($isMultiple) && $isMultiple === true ? 'multiple' : '' }}
        @if($isDisabled) disabled @endif
        @if($isReadOnly) readonly @endif
        class="w-full pl-3 pr-6 appearance-none border-0 py-3 placeholder-blueGray-300 text-blueGray-600 @if($isReadOnly || $isDisabled) bg-gray-200 @else bg-white @endif rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
        @if(!$isDisabled && !$isReadOnly) name="{{ $field->getFieldName() }}" @endif>
    @forelse($options as $option)
        <option value="{{ $option->get('value') }}" @if(in_array($option->get('value'), $selected)) selected @endif>{{ $option->get('label') }}</option>
    @empty
        <option disabled>{{ __('infinity::generic.no_results') }}</option>
    @endforelse
</select>
