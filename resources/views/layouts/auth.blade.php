@extends('infinity::layouts.base')

@section('main')
    <main>
        <section class="relative w-full h-full py-40 min-h-screen">
            <div class="absolute top-0 w-full h-full bg-blueGray-800 bg-center bg-no-repeat bg-cover" style="background-image: url({{ settings('auth.wallpaper', infinity_asset('images/decoration/background.png')) }})"></div>
            @yield('main')
            @include('infinity::partials.footer')
        </section>
    </main>
@overwrite
