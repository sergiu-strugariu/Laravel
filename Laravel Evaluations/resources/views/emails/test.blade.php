@extends('emails.layout')

@section('content')

    <h1>Hi, Susan Calvin</h1>

    {{--@include('emails.partials.callout', ['text' => 'lorem ipsum test'])--}}

    <p class="mail-text">
        continut mail text
    </p>

    @include('emails.partials.button', ['text' => 'Test text', 'href' => '123123'])

@endsection
