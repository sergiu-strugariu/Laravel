@extends('emails.layout')

@section('content')

    <div class="mail-text">
        {!! $body_en !!}
    </div>

    @if( $link )
        @include('emails.partials.button', ['text' => 'Take Test', 'href' => url('test/instructions/'.$link)])
    @endif

    <hr style="border-color: rgb(233, 233, 233); opacity: 0.3;">

    <div class="mail-text">
        {!! $body_ro !!}
    </div>

    @if( $link )
        @include('emails.partials.button', ['text' => 'Incepe testul', 'href' => url('test/instructions/'.$link)])
    @endif

@endsection
