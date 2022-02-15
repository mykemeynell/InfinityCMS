<div class="w-full xl:w-{{ $width }} px-4">
    <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
        <div class="rounded-t mb-0 px-4 py-3 border-0">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                    <h3 class="font-semibold text-base text-blueGray-700">
                        {{ $title }}
                    </h3>
                </div>
                @if($hasTitleButton)
                <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                    <a href="{{ $titleButtonLink }}" class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150">
                        {{ $titleButtonLabel }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        <div class="block w-full overflow-x-auto">
            {!! $contentView !!}
        </div>
    </div>
</div>
