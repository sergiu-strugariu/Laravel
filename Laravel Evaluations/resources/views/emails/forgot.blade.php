@extends('emails.layout')

@section('content')

    <div class="mail-text">
        {!! $body_en !!}
    </div>

    @include('emails.partials.button', ['text' => 'Recover Password', 'href' => $route_reset])

    <hr style="border-color: rgb(233, 233, 233); opacity: 0.3;">

    <div class="mail-text">
        {!! $body_ro !!}
    </div>
    @include('emails.partials.button', ['text' => 'Recover Password', 'href' => $route_reset])



@endsection
