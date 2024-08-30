@extends('emails.layout')

@section('content')


    <div class="mail-text">

        <p style="margin-top: 50px; font-size: 18px; font-weight: bold; color: #848484;">{{config('email_translate.administrator.title')}}</p>

        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.administrator.content')}} {{$task->name}} ({{$task->email}})</p>

        <br>
        @include('emails.partials.button', ['text' => 'New Task', 'href' => url('/task/'.$task->id)])
        @include('emails.partials.button', ['text' => 'Old Task', 'href' => url('/task/'.$verifyTask->id)])

        <p style=" margin-top: 50px; font-size: 16px; color: #666666;">{{config('email_translate.administrator.formula')}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.administrator.team')}} </p>

    </div>


@endsection