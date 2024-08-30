@extends('layouts.test')
@section('sectionClass') section-test-listening @endsection
@section('content')
    <div class="container" oncopy="return false" oncut="return false">

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="panel panel-default test-panel">
                    <div class="panel-body test-body">
                        <div class="panel-title">
                            {!! str_replace("\n", "<br>", $question->body) !!}
                            <br>
                            <br>
                            <audio id="plyr-audio">
                                @if($question->audio_file_path == 'sample.mp3')
                                    <source src=" {{url('audio/sample.mp3')}}" type="audio/mp3">
                                @else
                                    <source src=" {{url('audio/'.$question->id.'-'.$question->audio_file_path)}}"
                                            type="audio/{{$fileExtension}}">
                                @endif
                            </audio>
                            <form id="form-listening-test" method="POST" action="/test/submitListeningAnswer">
                                {{ csrf_field() }}

                                <div class="form-group text-area-answer">
                                    @foreach($choices as $choice)
                                        <div class="styled-input-single">
                                            <input type="radio" name="user_answer" id="a{{$choice->id}}"
                                                   value="{{$choice->id}}">
                                            <label for="a{{$choice->id}}"> {{$choice->answer}}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row test-writing-footer">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <input type="button" value="Next"
                                                   class="form-control listening_submit next-question-button"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-4"></div>
                                    <div class="col-xs-4"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script>
        testTimeLimit = '{{ base64_encode($timeLimit) }}';
        testPaperTypeId = '{{ base64_encode($test->paper_type_id) }}';
        currentAudioTime = '{{ $test->current_audio_time == null ? base64_encode(0) : base64_encode($test->current_audio_time) }}';
        testID = '{{ base64_encode($test->id) }}';
        testHash = '{{ base64_encode($test->task->link) }}';
    </script>
@endsection