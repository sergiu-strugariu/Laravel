@extends('emails.layout')

@section('content')


    <div class="mail-text">

        {!! $body_en !!}

        @if( $skype )
            <p>Skype id changed to <b>{{$skype}}</b> </p>
        @endif
        @if( $phone )
            <p>Phone number changed to <b>{{$phone}}</b> </p>
        @endif

    </div>
    @include('emails.partials.button', ['text' => 'Task page', 'href' => $link])

    <hr style="border-color: rgb(233, 233, 233); opacity: 0.3;">

    <div class="mail-text">
        {!! $body_ro !!}

        @if( $skype )
            <p>Skype id s-a schimbat in <b>{{$skype}}</b> </p>
        @endif
        @if( $phone )
            <p>Numarul de telefon s-a schimbat in <b>{{$phone}}</b> </p>
        @endif

    </div>
    @include('emails.partials.button', ['text' => 'Pagina task', 'href' => $link])

@endsection