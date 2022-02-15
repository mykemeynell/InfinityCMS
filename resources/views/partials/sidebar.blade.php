<nav class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4 px-6">
    <div class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto">
        <button class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent" type="button" onclick="toggleNavbar('example-collapse-sidebar')">
            <i class="fas fa-bars"></i>
        </button>
        <a class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0" href="{{ infinity_route('dashboard.show_dashboard') }}">
            <img src="{{ infinity_asset('images/logo/logo.svg') }}" class="w-10 mr-2 inline">{{ infinity_config('name') }}
        </a>
        <div class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded hidden" id="example-collapse-sidebar">
            <div class="md:min-w-full md:hidden block pb-4 mb-4">
                <div class="flex flex-wrap">
                    <div class="w-6/12">
                        <a class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0" href="{{ infinity_route('dashboard.show_dashboard') }}">
                            {{ infinity_config('name') }}
                        </a>
                    </div>
                    <div class="w-6/12 flex justify-end">
                        <button type="button" class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent" onclick="toggleNavbar('example-collapse-sidebar')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Divider -->
            <hr class="my-4 md:min-w-full">
            <!-- Heading -->
            <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4 no-underline">
                Core
            </h6>
            <!-- Navigation -->

            <ul class="md:flex-col md:min-w-full flex flex-col list-none">

                @if(auth()->user()->can('dashboard.browse'))
                <li class="items-center">
                    <a href="{{ infinity_route('dashboard.show_dashboard') }}"
                       class="{{ infinity_route_is('dashboard.show_dashboard') ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }}  transition-colors duration-150 text-xs uppercase py-3 font-bold block">
                        <i class="fas fa-tv mr-2 text-sm"></i>
                        Dashboard
                    </a>
                </li>
                @endif

                @foreach($coreSidebarResources as $coreSidebarResource)
                    @if(!$coreSidebarResource->displayInNavigation()) @continue @endif
                    @can("{$coreSidebarResource->getIdentifier()}.browse")
                    <li class="items-center">
                        <a href="{{ infinity_route(sprintf("%s.index", $coreSidebarResource->getIdentifier())) }}"
                           class="{{ infinity_route_is(sprintf("%s.*", $coreSidebarResource->getIdentifier())) ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }} transition-colors duration-150 text-xs uppercase py-3 font-bold block">
                            <i class="{{ $coreSidebarResource->getIcon() }} mr-2 text-sm"></i>
                            {{ $coreSidebarResource->getDisplayName() }}
                        </a>
                    </li>
                    @endcan
                @endforeach

            </ul>

            @if(!$sidebarResources->isEmpty())
            <!-- Divider -->
            <hr class="my-4 md:min-w-full">
            <!-- Heading -->
            <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4 no-underline">
                Resources
            </h6>
            <!-- Navigation -->

            <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
                @foreach($sidebarResources as $sidebarResource)
                    @if(!$sidebarResource->displayInNavigation()) @continue @endif
                    <li class="items-center">
                        <a href="{{ infinity_route(sprintf("%s.index", $sidebarResource->getIdentifier())) }}"
                            class="{{ infinity_route_is(sprintf("%s.*", $sidebarResource->getIdentifier())) ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }} transition-colors duration-150 text-xs uppercase py-3 font-bold block">
                            <i class="{{ $sidebarResource->getIcon() }} mr-2 text-sm"></i>
                            {{ $sidebarResource->getDisplayName() }}
                        </a>
                    </li>
                @endforeach
            </ul>
            @else
                @if(\Infinity\Facades\Infinity::isInfinityDevModeEnabled())
                <!-- Divider -->
                <hr class="my-4 md:min-w-full">
                <span class="md:min-w-full text-blueGray-500 text-xs block pt-1 pb-4 no-underline">
                        {{ __('infinity::resources.no_resources_dev') }}
                </span>
                @endif
            @endif
        </div>
    </div>
</nav>
