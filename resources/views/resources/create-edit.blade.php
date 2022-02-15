@php
    /** @var \Infinity\Resources\Resource $resource */
    /** @var \Illuminate\Database\Eloquent\Model|\Infinity\Resources\Fields\FieldSet $object */
    $add = empty($object->getKey());
@endphp

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}"> @endpush

@extends('infinity::layouts.app')

@section('main')
    <div class="px-4 md:px-10 mx-auto w-full md:pt-32 pb-32 pt-12">
        <div class="flex flex-wrap">
            <div class="w-full mb-12 xl:mb-0 px-4">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-blueGray-50 border-0">
                    <div class="rounded-t bg-white mb-0 px-6 py-6 border-b">
                        <div class="text-center flex justify-between">
                            <h3 class="text-blueGray-700 text-xl font-bold">
                                @if($add)
                                    {{ __('infinity::generic.creating', ['name' => $resource->getDisplayName(true)]) }}
                                @else
                                    {{ __('infinity::generic.editing', ['name' => $resource->getDisplayName(true)]) }}
                                @endif
                            </h3>
                            <div>
                                <a href="{{ $backRoute }}"
                                   class="bg-gray-500 text-white active:bg-gray-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-3 ease-linear transition-all duration-150">
                                    <i class="fas fa-chevron-left mr-2"></i>{{ $backRouteDisplay }}
                                </a>
                                <button type="submit" form="user-form"
                                        @if($add)
                                        formaction="{{ infinity_route(sprintf("%s.%s", $resource->getIdentifier(), 'store')) }}"
                                        @else
                                        formaction="{{ infinity_route(sprintf("%s.%s", $resource->getIdentifier(), 'update'), ['id' => $object->getKey()]) }}"
                                        @endif
                                        formmethod="POST"
                                        class="bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                                    {{ __('infinity::generic.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex-auto px-4 lg:px-10 py-10 pt-10">
                        <form id="user-form" name="user-form" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            @if(!$add)
                                {{ method_field("PUT") }}
                            @endif

                            @foreach($object as $column)
                                @php /** @var \Infinity\Resources\Fields\Field $column */ @endphp
                                @if($column->isHidden() || !$column->currentUserCan()) @continue @endif

                                <label
                                    class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                    for="grid-{{ $resource->getIdentifier() }}-{{ $column->getFieldName() }}">{{ $column->getDisplayName() }}</label>
                                {!! app('infinity')->formField($column, $resource) !!}
                                <div class="mb-5"></div>
                            @endforeach

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
