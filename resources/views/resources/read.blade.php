@extends('infinity::layouts.app')

@section('main')
    <div class="px-4 md:px-10 mx-auto w-full md:pt-32 pb-32 pt-12">
        <div class="flex flex-wrap">
            <div class="w-full mb-12 xl:mb-0 px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-blueGray-50 border-0">
                    <div class="rounded-t bg-white mb-0 px-6 py-6 border-b">
                        <div class="text-center flex justify-between">
                            <h3 class="text-blueGray-700 text-xl font-bold">
                                {{ __('infinity::generic.viewing') }} {{ ucfirst($dataType->getTranslatedAttribute('display_name_singular')) }}
                            </h3>
                            <div>
                                @can(sprintf("%s.index", $dataType->getSlug()))
                                    <a href="{{ infinity_route($dataType->getSlug() . '.index') }}"
                                       class="bg-gray-500 text-white active:bg-gray-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-3 ease-linear transition-all duration-150">
                                        <i class="fas fa-chevron-left mr-2"></i>Back to {{ $dataType->getPluralDisplayName() }}</a>
                                @endcan
                                @can(sprintf("%s.delete", $dataType->getSlug()))
                                    <a href="#"
                                            class="bg-red-500 text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 ml-2">
                                        Delete
                                    </a>
                                @endcan
                                @can(sprintf("%s.edit", $dataType->getSlug()))
                                    <a href="#"
                                            class="bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 ml-2">
                                        Edit
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="flex-auto px-4 lg:px-10 py-10 pt-10">

                        @foreach($dataType->readRows as $row)
                            @php
                                if ($dataTypeContent->{$row->field.'_read'}) {
                                    $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_read'};
                                }
                            @endphp
                            <div class="mb-2">
                                <h3 class="block uppercase text-blueGray-600 text-xs font-bold">{{ $row->getTranslatedAttribute('display_name') }}</h3>
                            </div>

                            <div class="border-b mb-5 pb-5">
                                @if (isset($row->details->view))
                                    @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => 'read', 'view' => 'read', 'options' => $row->details])
                                @elseif($row->type == "image")
                                    <img class="img-responsive"
                                         src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Infinity::image($dataTypeContent->{$row->field}) }}"/>
                                @elseif($row->type == 'multiple_images')
                                    @if(json_decode($dataTypeContent->{$row->field}))
                                        @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                            <img class="img-responsive"
                                                 src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Infinity::image($file) }}"/>
                                        @endforeach
                                    @else
                                        <img class="img-responsive"
                                             src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Infinity::image($dataTypeContent->{$row->field}) }}"/>
                                    @endif
                                @elseif($row->type == 'relationship')
                                    @include('infinity::fields.relationship', ['view' => 'read', 'options' => $row->details])
                                @elseif($row->type == 'select_dropdown' && property_exists($row->details, 'options') &&
                                        !empty($row->details->options->{$dataTypeContent->{$row->field}})
                                )
                                    <?php echo $row->details->options->{$dataTypeContent->{$row->field}};?>
                                @elseif($row->type == 'select_multiple')
                                    @if(property_exists($row->details, 'relationship'))

                                        @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                            {{ $item->{$row->field}  }}
                                        @endforeach

                                    @elseif(property_exists($row->details, 'options'))
                                        @if (!empty(json_decode($dataTypeContent->{$row->field})))
                                            @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                                @if (@$row->details->options->{$item})
                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                @endif
                                            @endforeach
                                        @else
                                            {{ __('infinity::generic.none') }}
                                        @endif
                                    @endif
                                @elseif($row->type == 'date' || $row->type == 'timestamp')
                                    @if ( property_exists($row->details, 'format') && !is_null($dataTypeContent->{$row->field}) )
                                        {{ \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) }}
                                    @else
                                        {{ $dataTypeContent->{$row->field} }}
                                    @endif
                                @elseif($row->type == 'checkbox')
                                    @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                        @if($dataTypeContent->{$row->field})
                                            <span class="bg-green-500 text-white px-2 py-1 rounded-full outline-none focus:outline-none ease-linear transition-all duration-150">{{ $row->details->on }}</span>
                                        @else
                                            <span class="bg-yellow-400 text-white px-2 py-1 rounded-full outline-none focus:outline-none ease-linear transition-all duration-150">{{ $row->details->off }}</span>
                                        @endif
                                    @else
                                        {{ $dataTypeContent->{$row->field} }}
                                    @endif
                                @elseif($row->type == 'color')
                                    <span class="text-white px-2 py-1 rounded-full outline-none focus:outline-none ease-linear transition-all duration-150" style="background-color: {{ $dataTypeContent->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>
                                @elseif($row->type == 'coordinates')
                                    @include('infinity::partials.coordinates')
                                @elseif($row->type == 'rich_text_box')
                                    @include('infinity::multilingual.input-hidden-bread-read')
                                    {!! $dataTypeContent->{$row->field} !!}
                                @elseif($row->type == 'file')
                                    @if(json_decode($dataTypeContent->{$row->field}))
                                        @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                            <a href="{{ Storage::disk(config('infinity.storage.disk'))->url($file->download_link) ?: '' }}">
                                                {{ $file->original_name ?: '' }}
                                            </a>
                                            <br/>
                                        @endforeach
                                    @elseif($dataTypeContent->{$row->field})
                                        <a href="{{ Storage::disk(config('infinity.storage.disk'))->url($row->field) ?: '' }}">
                                            {{ __('infinity::generic.download') }}
                                        </a>
                                    @endif
                                @else
                                    @include('infinity::multilingual.input-hidden-bread-read')
                                    @if(empty($dataTypeContent->{$row->field}))
                                        <span class="bg-gray-300 text-xs font-bold text-white px-2 py-1 rounded-full outline-none focus:outline-none ease-linear transition-all duration-150">EMPTY</span>
                                    @else
                                        <span class="text-xs">{{ $dataTypeContent->{$row->field} }}</span>
                                    @endif
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
