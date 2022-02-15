@php
    /** @var \Infinity\Resources\Resource $resource */
@endphp

@extends('infinity::layouts.app')

@section('main')
    <div class="px-4 md:px-10 mx-auto w-full md:pt-32 pb-32 pt-12">
        <div class="flex flex-wrap">
            <div class="w-full mb-12 xl:mb-0 px-4">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t bg-white mb-0 px-6 py-6 border-b">
                        <div class="flex flex-wrap items-center">
                            <div
                                class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="text-blueGray-700 text-xl font-bold">
                                    {{ "Delete {$resource->getDisplayName(true)}?" }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto p-10">
                        <p class="text-sm">{{ __('infinity::generic.delete_question') }}</p>

                        <table
                            class="items-center bg-transparent border-collapse my-10">
                            <tbody>
                            @foreach($object as $field)
                                <tr>
                                    @if($field->isHidden()) @continue @endif
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        {{ $field->getDisplayName() }}
                                    </th>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">{!! $field->handle() !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <form name="delete-form" id="delete-form">
                            {!! csrf_field() !!}
                            {!! method_field('DELETE') !!}
                        </form>

                        <button type="submit" form="delete-form"
                                formaction="{{ infinity_route(sprintf("%s.%s", $resource->getIdentifier(), 'destroy'), ['id' => $object->getKey()]) }}"
                                formmethod="POST"
                                class="bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                            {{ __('infinity::generic.delete_this_confirm') }}
                        </button>
                        <a href="{{ infinity_route("{$resource->getIdentifier()}.index") }}"
                           class="ml-2 bg-gray-500 text-white active:bg-gray-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                            {{ __('infinity::generic.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
