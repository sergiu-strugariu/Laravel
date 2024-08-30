@extends('layouts.test')

@section('content')
    <div class="container" oncopy="return false" oncut="return false">

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="panel panel-default test-panel">
                    <div class="panel-body test-body">
                        <div class="panel-title">
                            {!! str_replace("\n", "<br>", $question->body) !!}
                        </div>
                        <div class="panel-content">
                            <input type="hidden" id="words_counter"
                                   value="{{$question->max_words}}"/>
                            <form id="form-writing-test" method="POST" action="/test/submitWritingAnswer">
                                {{ csrf_field() }}
                                <div class="form-group text-area-answer">
                                    <textarea class="form-control" rows="5" id="answer" name="user_answer"
                                              placeholder="Your answer here" spellcheck="false"></textarea>
                                </div>
                                <div class="row test-writing-footer">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <input type="button" value="Submit" class="form-control writing_submit next-question-button"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-4"></div>
                                    <div class="col-xs-4">
                                        <label id="limit" class=""><span id="words_used">0</span><span id="words_total">/{{$question->max_words}}</span><span
                                                    id="words_left">Words</span></label>
                                    </div>
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