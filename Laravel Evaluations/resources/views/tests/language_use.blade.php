@extends('layouts.test')

@section('content')
    <div class="container" oncopy="return false" oncut="return false">

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="panel panel-default test-panel">
                    <div class="panel-body test-body">
                        <div class="panel-title">
                            {{$currentQuestionIndex}}/{{$totalQuestions}}
                            <br>
                            {!! str_replace("\n", "<br>", $question->body) !!}
                        </div>
                        <div class="panel-content">
                            <form id="form-language-use-test" method="POST" action="/test/submitLanguageUseAnswer">
                                {{ csrf_field() }}

                                <div class="form-group text-area-answer">
                                    @foreach($choices as $choice)
                                        <div class="styled-input-single">
                                            <input type="radio" name="user_answer" value="{{$choice->id}}" id="a{{$choice->id}}">
                                            <label for="a{{$choice->id}}">{{$choice->answer}}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row test-writing-footer">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <input type="button" value="Next"
                                                   class="form-control language_use_submit next-question-button"/>
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
    </script>
@endsection