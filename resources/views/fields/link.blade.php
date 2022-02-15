@php
    /** @var \Infinity\Resources\Fields\Field $field */
    /** @var mixed $modelValue */
    /** @var mixed $rawValue */
    /** @var mixed $displayValue */
    /** @var \Illuminate\Database\Eloquent\Model $model */
    /** @var string $routeName */
    /** @var array $routeParamFieldBindings */

    $routeParams = [];
    foreach($routeParamFieldBindings as $routeParam => $fieldBinding) {
        $routeParams[$routeParam] = $model->getAttribute($fieldBinding);
    }
@endphp

<a href="{{ route($routeName, $routeParams) }}" @foreach($attributes->except('class') as $attributeTag => $attributeValue) {{ $attributeTag }}="{{ $attributeValue }}" @endforeach
    class="text-gray-500 hover:text-pink-500 ease-linear transition-all duration-150 {{ $attributes->get('class') }}">
    {!! $displayValue !!}
</span>
