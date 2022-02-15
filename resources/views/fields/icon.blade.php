@php
    /** @var \Infinity\Resources\Fields\Field $field */
    /** @var mixed $modelValue */
    /** @var mixed $rawValue */
    /** @var mixed $displayValue */
    /** @var \Illuminate\Database\Eloquent\Model $model */
    /** @var \Illuminate\Support\Collection $attributes */
@endphp

<i @foreach($attributes->except('class') as $attributeTag => $attributeValue) {{ $attributeTag }}="{{ $attributeValue }}" @endforeach
class="{{ $attributes->get('class') }}"></i>
