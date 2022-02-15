@php /** @var \Infinity\Resources\Resource $resource */ @endphp

@extends('infinity::layouts.app')

@section('main')
    <div class="px-4 md:px-10 mx-auto w-full md:pt-32 pb-32 pt-12">
        <div class="flex flex-wrap">
            <div class="w-full mb-12 xl:mb-0 px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t bg-white mb-0 px-6 py-6 border-b">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="text-blueGray-700 text-xl font-bold">
                                    {{ __('infinity::generic.browsing', ['name' => $resource->getDisplayName()]) }}
                                </h3>
                            </div>
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                                @if(!empty($createAction))
                                    @include('infinity::resources.partials.actions', ['action' => $createAction])
                                    {{--                                <a href="{{ infinity_route($resource->getIdentifier() . '.create') }}" class="bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">--}}
{{--                                    <i class="fas fa-plus mr-2"></i>{{ $createAction->get }}--}}
{{--                                </a>--}}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <!-- Table -->
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                            <tr>
                                @foreach($resource->getVisibleFields() as $field)
                                    @php /** @var \Infinity\Resources\Fields\Field $field */ @endphp
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        {{ $field->getDisplayName() }}
                                    </th>
                                @endforeach
                                <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    &nbsp;
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($objects as $row)
                                @php /** @var \Infinity\Resources\Fields\FieldSet $row */ @endphp
                                <tr>
                                    @foreach($row as $column)
                                        @php /** @var \Infinity\Resources\Fields\Field $column */ @endphp
                                        @if($column->isHidden()) @continue @endif
                                        <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">{!! $column->handle() !!}</td>
                                    @endforeach
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-right">
                                        @foreach($actions as $action)
                                            @if(!$resource->isActionPossible($action)) @continue @endif
                                            @include('infinity::resources.partials.actions', ['action' => $action, 'routeParams' => ['id' => $row->model()->getKey()]])
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-center" colspan="{{ count($resource->fields()) + 1 }}">{{ __('infinity::resources.no_resource_entries', ['plural_name' => $resource->getDisplayNameLower(false)]) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

{{--                    @if ($isServerSide)--}}
{{--                        @include('infinity::partials.filter-search')--}}
{{--                    @endif--}}
                </div>
            </div>
        </div>
    </div>
@endsection
