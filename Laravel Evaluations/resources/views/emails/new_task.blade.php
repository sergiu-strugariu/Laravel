@extends('emails.layout')

@section('content')

    <table class="container" style="padding: 0 20px;">
        <tr>
            <td>
                <p style="margin-top: 50px; font-size: 18px; font-weight: bold; color: #848484;">{{config('email_translate.new_task.title')}}</p>
            </td>
        </tr>

        <tr>
            <td style="text-align: left;">
                <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.new_task.content')}}
                    <a href="{{$link}}">Link</a></p>
                <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.new_task.details')}}</p>
                <p style=" margin-top: 50px; font-size: 16px; color: #666666;">{{config('email_translate.new_task.formula')}}</p>
                <p style=" margin: 0; font-size: 16px; color: #666666;">{{config('email_translate.new_task.team')}} </p>

            </td>
        </tr>
    </table>


@endsection

