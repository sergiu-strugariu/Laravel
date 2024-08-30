@extends('layouts.test')

@section('content')

    <div class="container">

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="panel panel-default test-panel">
                    <div class="panel-body test-body">
                        <div class="panel-title">
                            @if($containsListening == true)
                                {!! $settingsArray['welcome_audio'] !!}
                            @else
                                {!! $settingsArray['welcome'] !!}
                            @endif
                        </div>
                        <div class="panel-content">
                            <div class="form-group text-area-answer">
                                @if($containsListening== true)
                                    <audio id="plyr-audio">
                                        <source src=" {{url('audio/'.$settingsArray['audio_file_path'])}}"
                                                type="audio/{{$fileExtension}}">
                                    </audio>
                                    <div class="instructions">
                                        {!! $settingsArray['audio_instruction'] !!}
                                    </div>
                                @else <div class="instructions">
                                    {!! $settingsArray['instructions'] !!}
                                </div>
                                @endif
                            </div>

                            <div class="row test-writing-footer">
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <a href="{{url('test/' .$hash )}}"
                                           class="btn btn-danger begin_test go-to-test-button">{!! $settingsArray['start_test_button'] !!}</a>
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