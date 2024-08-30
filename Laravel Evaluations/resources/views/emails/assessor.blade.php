@extends('emails.layout')

@section('content')

    <div class="mail-text">

        <p style="margin-top: 50px; font-size: 18px; font-weight: bold; color: #848484;">{{config('email_translate.assessor.title')}} {{ isset($user->full_name) ? $user->full_name : '' }},</p>
        <p>{{$name}}</p>

        @include('emails.partials.button', ['text' => 'Task page', 'href' => $link])
        @include('emails.partials.button', ['text' => 'Refuse', 'href' => $linkRefuse])

        <h2 style=" margin-top: 20px; font-size: 16px; color: #666666;">Here are the details</h2>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.assessor.testTakerName')}}: {{$testTaker}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.assessor.testTakerEmail')}}: {{$testTakerEmail}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.assessor.testTakerPhone')}}: {{$testTakerPhone}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.assessor.language')}}: {{$language}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.assessor.mark')}}: {{$mark}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.assessor.department')}}: {{$department}}</p>

        <p style=" margin-top: 50px; font-size: 16px; color: #666666;">{{config('email_translate.administrator.formula')}}</p>

    </div>

@endsection