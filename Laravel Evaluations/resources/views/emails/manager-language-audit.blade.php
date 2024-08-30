@extends('emails.layout')

@section('content')


    <div class="mail-text">

        {!! $body !!}
        <br>
        @include('emails.partials.button', ['text' => 'Link', 'href' => $link])

    </div>


@endsection

