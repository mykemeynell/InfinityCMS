<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>
        {{ infinity_config('name') }} @if ($__env->yieldContent('title'))| @yield('title')@endif
    </title>
    <link rel="stylesheet" href="{{ infinity_asset('css/infinity.css') }}">
    <link rel="shortcut icon" href="{{ infinity_asset('images/logo/logo.png') }}">
    @stack('head')
</head>
<body class="text-blueGray-700 antialiased">
    @yield('main.before')

    @yield('main')

    @yield('main.after')
    <script src="{{ infinity_asset('js/infinity.js') }}" type="application/javascript"></script>
    @include('infinity::partials.fontawesome')
    <script>
        @if(Session::has('alerts'))
            let modals = [];
            @foreach(Session::get('alerts') as $alert)
                modals.push({
                    text: "{!! addslashes($alert['message']) !!}",
                    icon: "{{ $alert['type'] }}"
                })
            @endforeach
            window.Swal.queue(modals);
        @endif

        @if(Session::has('message'))

        var alertType = {!! json_encode(Session::get('alert-type', 'info')) !!};
        var alertMessage = {!! json_encode(Session::get('message')) !!};

        window.Swal.fire({
            text: alertMessage,
            icon: alertType
        });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
