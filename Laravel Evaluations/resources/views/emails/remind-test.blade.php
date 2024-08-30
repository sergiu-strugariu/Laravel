@extends('emails.layout')

@section('content')

    <div class="mail-text">
        {!! $body_en !!}
    </div>

    @include('emails.partials.button', ['text' => 'Take Test', 'href' => url('test/instructions/'.$link)])

    <hr style="border-color: rgb(233, 233, 233); opacity: 0.3;">

    <div class="mail-text">
        {!! $body_ro !!}
    </div>
    @include('emails.partials.button', ['text' => 'Take Test', 'href' => url('test/instructions/'.$link)])



@endsection
