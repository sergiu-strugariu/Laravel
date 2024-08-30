@extends('emails.layout')

@section('content')

    <div class="mail-text">
        <p style="margin-top: 50px; font-size: 18px; font-weight: bold; color: #848484;">{{config('email_translate.task_empty_group.title')}} {{ isset($user->full_name) ? $user->full_name : '' }},</p>
        <p>{{$task->language->name}}</p>

        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.task_empty_group.content')}} #{{$task->id}} {{config('email_translate.task_empty_group.details')}}</p>

        <br>
        @include('emails.partials.button', ['text' => 'New Task', 'href' => url('/task/'.$task->id)])
        <br>

        <p style=" margin-top: 50px; font-size: 16px; color: #666666;">{{config('email_translate.task_empty_group.formula')}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.task_empty_group.team')}} </p>
    </div>



@endsection