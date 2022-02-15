@extends('infinity::layouts.app')

@section('main')
    <div class="px-4 md:px-10 mx-auto w-full pt-24">
        <div class="flex flex-wrap mb-4">
            @foreach($cards as $card)
                {!! $card !!}
            @endforeach
        </div>
    </div>
@endsection
