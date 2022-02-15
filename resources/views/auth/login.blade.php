@extends('infinity::layouts.auth', [
    'contentAreaClasses' => 'flex items-center justify-center h-screen'
])

@section('main')
    <div class="container mx-auto px-4 h-full">
        <div class="flex content-center items-center justify-center h-full">
            <div class="w-full lg:w-4/12 px-4">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-blueGray-200 border-0">
                    <div class="flex-auto px-4 lg:px-10 py-10">
                        <form name="login-form" id="login-form">
                            {!! csrf_field() !!}
                            <div class="relative w-full mb-3">
                                <label
                                    class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                    for="grid-password">Email</label><input
                                    name="email" type="email"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    placeholder="Email">
                            </div>
                            <div class="relative w-full mb-3">
                                <label
                                    class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                    for="grid-password">Password</label><input
                                    name="password" type="password"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    placeholder="Password">
                            </div>
                            <div>
                                <label
                                    class="inline-flex items-center cursor-pointer"><input
                                        id="customCheckLogin" type="checkbox"
                                        class="form-checkbox border-0 rounded text-blueGray-700 ml-1 w-5 h-5 ease-linear transition-all duration-150"><span
                                        class="ml-2 text-sm font-semibold text-blueGray-600">Remember me</span></label>
                            </div>
                            <div class="text-center mt-6">
                                <button
                                    class="bg-blueGray-800 text-white active:bg-blueGray-600 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full ease-linear transition-all duration-150"
                                    type="submit"
                                    formaction="{{ infinity_route('handleLogin') }}"
                                    formmethod="POST"
                                    form="login-form">
                                    Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="flex flex-wrap mt-6">
                    <div class="w-1/2">
                        {{-- TODO: Forgot password --}}
                        <a href="#" class="text-blueGray-200"><small>Forgot
                                password?</small></a>
                    </div>
                    <div class="w-1/2 text-right">
                        <a href="./register.html"
                           class="text-blueGray-200"><small>Create new
                                account</small></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
