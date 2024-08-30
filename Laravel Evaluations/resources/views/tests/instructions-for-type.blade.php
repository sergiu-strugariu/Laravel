@extends('layouts.test')

@section('content')
    <div class="container instructions-per-type">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="panel panel-default test-panel">
                    <div class="panel-body test-body">
                        <div class="panel-content">
                            <div class="form-group text-area-answer">
                                <div class="instructions">
                                    {!! $settingsArray['instructions'] !!}
                                </div>
                            </div>
                            <div class="row test-writing-footer">
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        @if (!empty($testType))
                                            @if ($testType == 1)
                                                <a href="{{url('test/demo/' . $hash . '/' . $testType . '/' . '1')}}"
                                                   class="btn btn-danger begin_test go-to-test-button">Take the test</a>
                                            @else
                                                <a href="{{url('test/' . $hash . '/' . $testType)}}"
                                                   class="btn btn-danger begin_test go-to-test-button">{!! $settingsArray['start_test_button'] !!}</a>
                                            @endif
                                        @else
                                            <a href="{{url('test/' . $hash )}}"
                                               class="btn btn-danger begin_test go-to-test-button">{!! $settingsArray['start_test_button'] !!}</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xs-4"></div>
                                <div class="col-xs-4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script>
        $(document).ready(function () {
            var plyr_options = {
                clickToPlay: true,
                showPosterOnEnd: true,
                controls: ['play', 'pause', 'volume', 'progress'],
                keyboardShortcuts: {focused: false, global: false}
            };
            plyr.setup(plyr_options);
        });
    </script>
@endsection