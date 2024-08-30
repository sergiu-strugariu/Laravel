@extends('emails.layout')

@section('content')


    <div class="mail-text">

        <p style="margin-top: 50px; font-size: 18px; font-weight: bold; color: #848484;">{{config('email_translate.'.$taskInfo.'.title')}} {{ isset($user->full_name) ? $user->full_name : '' }},</p>

        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.'.$taskInfo.'.content')}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.'.$taskInfo.'.details')}}

        @include('emails.partials.button', ['text' => 'Link', 'href' => $link])

        <p style=" margin-top: 50px; font-size: 16px; color: #666666;">{{config('email_translate.'.$taskInfo.'.formula')}}</p>
        <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.'.$taskInfo.'.team')}} </p>

    </div>


@endsection

