@extends('layouts.test')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="panel panel-default panel-completed">
                    <div class="panel-body test-body">
                        <div class="panel-title">
                            <svg width="90" height="90">
                                <image xlink:href="{{url('/assets/img/check.png')}}" width="90" height="90"/>
                            </svg>
                        </div>
                        <div class="panel-content">
                            <div class="row">
                                <div class="col-xs-4"></div>
                                <div class="col-xs-4 thank-you">Thank you!</div>
                                <div class="col-xs-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-xs-1"></div>
                                <div class="col-xs-10 regards">
                                    {!! $settingsArray['thank_you_text'] !!}
                                </div>
                                <div class="col-xs-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection