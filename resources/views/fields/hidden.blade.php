@php /** @var \Infinity\Models\DataRow $row */ @endphp
@php /** @var \Infinity\Models\DataType $dataType */ @endphp

<input type="hidden" id="grid-{{ $dataType->getSlug() }}-{{ $row->getField() }}"
       @if(!$isDisabled && !$isReadOnly) name="{{ $row->getField() }}" @endif/>
