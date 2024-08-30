@extends('layouts.test')
@section('sectionClass') section-test-language-use @endsection

@section('content')
    <div class="container" oncopy="return false" oncut="return false">
        <?php
        $variantsArray = range('A', 'Z');
        ?>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="panel panel-default test-panel">
                    <div class="panel-body test-body">
                        <div class="panel-title">
                            <div class="panel-title">
                                @if($question->language_use_type != 1)
                                    {!! str_replace("\n", "<br>", $question->description) !!}
                                @else
                                    {!! str_replace("\n", "<br>", $question->body) !!}
                                @endif
                            </div>
                        </div>
                        <div class="panel-content">
                                @if($question->language_use_type == 1)
                                    <div class="form-group text-area-answer">
                                        @foreach($choices as $choice)
                                            <div class="multiple-choice-single-container">
                                                <div class="ordering">
                                                    {{ $variantsArray[$loop->index] }}
                                                </div>
                                                <div class="styled-input-single">
                                                    <input type="radio" name="user_answer" value="{{$choice->id}}" id="a{{$choice->id}}">
                                                    <label for="a{{$choice->id}}">{{$choice->answer}}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                @elseif($question->language_use_type == 2)
                                <div class="form-group arrange-answer">
                                    @if ($question->body_incorrect)
                                        <ul id="items">
                                            <?php $arrangeItems = json_decode($question->body_incorrect);?>
                                            @foreach($arrangeItems as $word)
                                                <li draggable="true">{{$word}}</li>
                                            @endforeach
                                        </ul>
                                        <div class="clearfix"></div>

                                        <ul id="arrangedList">
                                            @foreach($question->body as $word)
                                                <li class="ghost-li"></li>
                                            @endforeach
                                        </ul>

                                        <div class="clearfix"></div>

                                        <input type="hidden" id="user_answer" name="user_answer" value=""/>
                                    @else
                                        <ul id="items">
                                            <?php
                                            $firstAnswer = $question->body[0];
                                            $questionsBody = $question->body;
                                            $prunedArray = array_splice($questionsBody, 1);
                                            shuffle($prunedArray);
                                            ?>
                                            <li draggable="true" class="first-correct">{{$firstAnswer[0]}}</li>
                                            @foreach($prunedArray as $word)
                                                <li draggable="true">{{$word}}</li>
                                            @endforeach
                                        </ul>
                                        <div class="clearfix"></div>

                                        <ul id="arrangedList">
                                            @foreach($question->body as $word)
                                                @if ($loop->first)
                                                    <li class="ghost-li first-correct"></li>
                                                @else
                                                    <li class="ghost-li"></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        <div class="clearfix"></div>

                                        <input type="hidden" id="user_answer" name="user_answer" value=""/>
                                    @endif
                                </div>

                                @else
                                    <div class="form-group text-area-answer">
                                            <span>{!! preg_replace('/_+/', '<input type="text" name="user_answer" class="border_bottom"
                                                   value=""/>', $question->body) !!}</span>
                                        <br>
                                    </div>
                                @endif
                                <div class="row test-writing-footer">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            @if ($next)
                                                <a href="{{url('test/demo/' . $hash . '/' . $testType . '/' . $next)}}"
                                                   class="btn btn-danger begin_test go-to-test-button">Next</a>
                                            @else
                                                <a href="{{url('test/' . $hash . '/' . $testType)}}" class="btn btn-danger begin_test go-to-test-button">{!! $settingsArray['start_test_button'] !!}</a>
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

        $(document).ready(function() {
            $('body').bind('copy paste', function(e) {
                e.preventDefault();
            });
        });
    </script>
@endsection