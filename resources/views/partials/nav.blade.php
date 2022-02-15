<nav
    class="absolute top-0 left-0 w-full z-10 bg-transparent md:flex-row md:flex-nowrap md:justify-start flex items-center p-4">
    <div
        class="w-full mx-autp items-center flex justify-end md:flex-nowrap flex-wrap md:px-10 px-4">
        @if(isset($page_name))
            @if(is_string($page_name))
                <span
                    class="text-blueGray-500 text-2xl hidden lg:inline-block font-bold">{{ $page_name }}</span>
            @elseif(is_array($page_name))
                @php $pageIndex = 0 @endphp
                @foreach($page_name as $label => $url)
                    @php /** @var int $pageIndex */ @endphp
                    @if(count($page_name) - 1 !== $pageIndex)
                        <a class="text-gray-300 text-2xl hidden lg:inline-block font-bold hover:text-blueGray-400 transition-colors ease-linear duration-100"
                           href="{{ $url }}">{{ $label }}</a>
                        <span
                            class="text-gray-300 text-2xl hidden:lg-inline-block font-semibold mx-2">/</span>
                    @else
                        <span
                            class="text-blueGray-500 text-2xl hidden lg:inline-block font-bold">{{ $label }}</span>
                    @endif

                    @php
                        /** @var int $pageIndex */
                        $pageIndex++
                    @endphp
                @endforeach
            @endif
        @endif
        <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
            <a class="text-blueGray-500 block" href="#"
               onclick="openDropdown(event,'user-dropdown')">
                <div class="items-center flex">
                    <span
                        class="w-12 h-12 text-sm text-white bg-blueGray-200 inline-flex items-center justify-center rounded-full"><img
                            alt="{{ auth()->user()->getDisplayName() }}'s user avatar"
                            class="w-full rounded-full align-middle border-none shadow-lg"
                            src="https://www.gravatar.com/avatar/{{ md5(auth()->user()->email) }}?d=mm&s=128"></span>
                </div>
            </a>
            <div
                class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48"
                id="user-dropdown">
                <a href="{{ infinity_route('users.showMyProfile') }}"
                   class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700 hover:text-pink-600"
                >Profile</a>
                <div
                    class="h-0 my-2 border border-solid border-blueGray-100"></div>
                <form name="logout-form" id="logout-form">
                    {!! csrf_field() !!}
                </form>
                <button
                    form="logout-form"
                    formaction="{{ infinity_route('handleLogout') }}"
                    formmethod="POST"
                   class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700 text-left hover:text-pink-600"
                ><i class="{{ fa('fas fa-sign-out-alt', 'fad fa-sign-out') }} mr-2"></i>Logout</button>
            </div>
        </ul>
    </div>
</nav>
