@extends('infinity::layouts.app')

@php /** @var \Infinity\Models\Users\User $user */ @endphp

@section('main')
    <div class="px-4 md:px-10 mx-auto w-full md:pt-32 pb-32 pt-12">
        <div class="flex flex-wrap">
            <div class="w-full mb-12 xl:mb-0 px-4">

                <div class="container mx-auto px-4">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-xl rounded-lg mt-20">
                        <div class="px-6">
                            <div class="flex flex-wrap justify-center">
                                <div
                                    class="w-full lg:w-3/12 px-4 lg:order-2 flex justify-center">
                                    <div class="relative">
                                        <img
                                            alt="{{ $user->getDisplayName() }}'s user avatar"
                                            src="https://www.gravatar.com/avatar/{{ md5($user->getEmail()) }}?d=mm&s=256"
                                            class="shadow-xl rounded-full h-auto align-middle border-none absolute -m-16 -ml-20 lg:-ml-16 max-w-150-px">
                                    </div>
                                </div>
                                <div
                                    class="w-full lg:w-4/12 px-4 lg:order-3 lg:text-right lg:self-center">
                                    <div class="py-6 px-3 mt-32 sm:mt-0">
                                        <button
                                            class="bg-pink-500 active:bg-pink-600 uppercase text-white font-bold hover:shadow-md shadow text-xs px-4 py-2 rounded outline-none focus:outline-none sm:mr-2 mb-1 ease-linear transition-all duration-150"
                                            type="button">
                                            Message
                                        </button>
                                    </div>
                                </div>
                                <div class="w-full lg:w-4/12 px-4 lg:order-1">
                                    <div
                                        class="flex justify-center py-4 lg:pt-4 pt-8">
{{--                                        <div class="mr-4 p-3 text-center">--}}
{{--                                            <span--}}
{{--                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">1</span><span--}}
{{--                                                class="text-sm text-blueGray-400">STAT</span>--}}
{{--                                        </div>--}}
{{--                                        <div class="mr-4 p-3 text-center">--}}
{{--                                            <span--}}
{{--                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">1</span><span--}}
{{--                                                class="text-sm text-blueGray-400">STAT</span>--}}
{{--                                        </div>--}}
{{--                                        <div class="lg:mr-4 p-3 text-center">--}}
{{--                                            <span--}}
{{--                                                class="text-xl font-bold block uppercase tracking-wide text-blueGray-600">1</span><span--}}
{{--                                                class="text-sm text-blueGray-400">STAT</span>--}}
{{--                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                            <div class="text-center my-12">
                                <h3 class="text-4xl font-semibold leading-normal mb-2 text-blueGray-700 mb-2">
                                    {{ $user->getDisplayName() }}
                                </h3>
                                <div
                                    class="text-sm leading-normal mt-0 mb-2 text-blueGray-400 font-bold">
                                    <span
                                        class="bg-gray-200 text-blueGray-600 text-xs px-2 py-1 rounded-full outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 mr-2">
                                        {{ sprintf("@%s", $user->getUsername()) }}
                                    </span>
                                    @if($user->getKey() === auth()->user()->getKey())
                                        <span
                                            class="bg-pink-500 text-white text-xs px-2 py-1 rounded-full outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 mr-2">
                                            <i class="{{ fa('fas fa-user', 'fad fa-user') }} mr-2"></i>You
                                        </span>
                                    @endif
                                    <span
                                        class="bg-gray-200 text-blueGray-600 text-xs px-2 py-1 rounded-full outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 mr-2">
                                        <i class="{{ fa('fas fa-users', 'fad fa-users') }} mr-2"></i>{{ $user->group->getDisplayName() }}
                                    </span>
                                    <a href="mailto:{{ $user->getEmail() }}"
                                       class="bg-gray-200 text-blueGray-600 hover:text-blueGray-800 text-xs px-2 py-1 rounded-full outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150">
                                        <i class="{{ fa('fas fa-envelope', 'fad fa-envelope') }} mr-2"></i>{{ $user->getEmail() }}
                                    </a>
                                </div>
                            </div>
                            @if($user->getKey() === auth()->user()->getKey())
                                <div
                                    class="mt-10 py-10 border-t border-blueGray-200 text-center">
                                    <div class="flex flex-wrap justify-center">
                                        <div class="w-full lg:w-9/12 px-4">
                                            <a href="{{ infinity_route('users.showEditMe') }}"
                                               class="bg-pink-500 active:bg-pink-600 uppercase text-white font-bold hover:shadow-md shadow text-xs px-4 py-2 rounded outline-none focus:outline-none sm:mr-2 mb-1 ease-linear transition-all duration-150"><i
                                                    class="{{ fa('fas fa-marker', 'fad fa-marker') }} mr-2"></i>Edit</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
