@extends('emails.layout')

@section('content')

    <div class="mail-text">
        {!! $body_en !!}
    </div>
    @include('emails.partials.button', ['text' => 'Task page', 'href' => $link, 'style' => 'background: green;'])

    <hr style="border-color: rgb(233, 233, 233); opacity: 0.3;">

    <div class="mail-text">
        {!! $body_ro !!}
    </div>
    @include('emails.partials.button', ['text' => 'Pagina task', 'href' => $link, 'style' => 'background: green;'])

@endsection