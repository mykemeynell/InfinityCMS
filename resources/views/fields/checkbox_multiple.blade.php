@php
    /** @var \Illuminate\Support\Collection $options */
    /** @var \Infinity\Resources\Resource $resource */
    /** @var \Infinity\Resources\Fields\Field $field */
    /** @var \Illuminate\Database\Eloquent\Model $model */
    /** @var mixed $modelValue */
    /** @var mixed $rawValue */
@endphp

@forelse($options as $option)
    @php $id = "grid-{$resource->getIdentifier()}-{$field->getFieldName()}-{$option->get('value')}"; @endphp

    <div class="mb-2">
        <input type="checkbox" name="{{ $field->getFieldName() }}[]" value="{{ $option->get('value') }}" id="{{ $id }}"
               @if(in_array($option->get('value'), $selected)) checked @endif>
        <label class="inline uppercase text-blueGray-600 text-xs font-bold mb-2" for="{{ $id }}">{{ $option->get('label') }}</label>
    </div>
@empty
    <span class="bg-gray-400 text-white px-2 py-1 rounded-full outline-none focus:outline-none ease-linear transition-all duration-150">{{ __('infinity::generic.no_results') }}</span>
@endforelse
