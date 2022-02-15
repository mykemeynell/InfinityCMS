@php
    /** @var \Infinity\Resources\Fields\Field $field */
    /** @var mixed $modelValue */
    /** @var mixed $rawValue */
    /** @var mixed $displayValue */
    /** @var \Illuminate\Database\Eloquent\Model $model */
    /** @var \Illuminate\Support\Collection $attributes */
@endphp

<span @foreach($attributes->except('class') as $attributeTag => $attributeValue) {{ $attributeTag }}="{{ $attributeValue }}" @endforeach class="{{ $attributes->has('class') && str_contains($attributes->get('class'), 'bg-') ?: 'bg-gray-200 text-gray-600'  }} px-2 py-1 rounded-full outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 {{ $attributes->get('class') }}">
    {!! $displayValue !!}
</span>
