@php /** @var \Infinity\Models\DataRow $row */ @endphp
@php /** @var \Infinity\Models\DataType $dataType */ @endphp

<input type="number" id="grid-{{ $dataType->getSlug() }}-{{ $row->getField() }}"
       @if(!$isDisabled && !$isReadOnly) name="{{ $row->getField() }}" @endif
       class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"/>
