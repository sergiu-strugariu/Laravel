@extends('layouts.pdf')

@section('content')

    <div class="container">
        <div class="row" id="test-result-panel">
            <div class=" ">
                <div class="panel panel-default test-report-panel">
                    <div class="total_time">
                        @if($report->paper->paper_type_id != TEST_SPEAKING)
                            Total time: {{$total_time}}
                        @endif
                    </div>
                    <div class="panel-body test-report-body">
                        <div class="row">
                            <div class="col-xs-4 text-left">
                                <button class="btn btn-danger btn-disabled">{{$report->grade}}</button>
                            </div>
                            <div class="col-xs-4 text-center">
                                <button class="btn btn-danger btn-disabled">{{$report->paper->type->name}}</button>
                            </div>
                            <div class="test-report-score col-xs-4 text-right">
                                Score <span>{{ $report->ability }}</span>
                            </div>
                        </div>
                    </div>

                    @if(count($questions) > 0)
                        @foreach($questions as $questionKey=>$questionValue)
                            <div class="panel-body test-report-body">
                                @if($questionValue->language_use_type == TEST_LU_FILLGAPS)
                                    <div class="panel-title fill_gaps">
                                        {!! $questionValue->body !!}
                                        <div class="row info-question">
                                            <div class="col-xs-3">
                                                <div class="difficulty">D: {{$questionValue->question_level_id}}</div>
                                            </div>
                                            <div class="col-xs-3">
                                                <div class="time-spent"> {{gmdate("i:s", $questionValue['time_spent'])}}
                                                </div>
                                            </div>
                                            <div class="col-xs-3"></div>
                                        </div>
                                    </div>
                                @elseif($questionValue->language_use_type == TEST_LU_ARRANGE)
                                    <div class="panel-title">
                                        {{implode(" ", json_decode($questionValue->body, true))}}
                                        <div class="row info-question">
                                            <div class="col-xs-3">
                                                <div class="difficulty">D: {{$questionValue->question_level_id}}</div>
                                            </div>
                                            <div class="col-xs-3">
                                                <div class="time-spent"> {{gmdate("i:s",$questionValue['time_spent'])}}
                                                </div>
                                            </div>
                                            <div class="col-xs-3"></div>
                                        </div>
                                    </div>
                                    <div class="panel-content arrange_words">
                                        <div class="styled-input-single">
                                            <input type="radio" name="{{$questionValue->id}}"
                                                   id="a{{$questionValue['id']}}"
                                                   value=""
                                                   @if(json_decode($questionValue['user_answer'], true) == json_decode($questionValue->body, true))  class="correct"
                                                   @else class="incorrect"
                                                   @endif
                                                   @if(json_decode($questionValue['user_answer'], true) == json_decode($questionValue->body, true))  checked
                                                   @endif disabled/>
                                            <label for="a{{$questionValue['id']}}">
                                                @if( isset($questionValue['user_answer']) )
                                                    {{implode(" ", json_decode($questionValue['user_answer'], true))}}
                                                @else
                                                    Test taker didn't answer!
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @else
                                    <div class="panel-title">
                                        @if($questionValue->audio_file_path != null)
                                            {{$questionValue->body}}
                                            <br>
                                            <br>
                                            <audio id="plyr-audio">
                                                <source src=" {{url('audio/'.$questionValue->id.'/'.$questionValue->audio_file_path)}}"
                                                        type="audio/{{$questionValue['file_extension']}}">
                                            </audio>
                                        @else
                                            {{$questionValue->body}}
                                        @endif

                                        <div class="row info-question">
                                            @if($report->paper->paper_type_id != TEST_WRITING)
                                                <div class="col-xs-3">
                                                    <div class="difficulty @if($report->paper->paper_type_id == TEST_LANGUAGE_USE) hidden @endif">
                                                        D: {{$questionValue->question_level_id}}</div>
                                                </div>
                                                <div class="col-xs-3">
                                                    <div class="time-spent"> {{gmdate("i:s", $questionValue['time_spent'])}}
                                                    </div>
                                                </div>
                                                <div class="col-xs-3"></div>
                                            @else
                                                <div class="col-xs-12">
                                                    <div class="time-spent"> {{gmdate("i:s", $questionValue['time_spent'])}}
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    @if($questionValue["opts"] != null)
                                        @foreach($questionValue["opts"] as $choice)
                                            <div class="panel-content">
                                                <div class="styled-input-single">
                                                    <input type="radio" name="{{$questionValue->id}}"
                                                           id="a{{$choice['id']}}"
                                                           value=""
                                                           @if($choice['correct'] == 1 && $questionValue['user_answer'] == $choice['id'])  class="correct"
                                                           @else class="incorrect"
                                                           @endif
                                                           @if($choice['id'] == $questionValue['user_answer'])  checked
                                                           @endif disabled/>
                                                    <label for="a{{$choice['id']}}">{{$choice['answer']}}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="panel-content">
                                            <div class="form-group text-area-answer">
                                            <textarea class="form-control" id="answer" name="user_answer"
                                                      disabled="">{{$report->paper->paper_answers[0]->user_answer}}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @endif

                    {{--ASSESSMENTS--}}

                    @if( count($questions) == 0 || $report->paper->paper_type_id == TEST_WRITING || $report->paper->paper_type_id == TEST_SPEAKING)


                        @if($report->grade != 'N')
                            <div id="view-task">
                                <div class="panel" id="task-{{ strtolower($report->paper->type->name) }}-skills">
                                    <div class="panel-body">
                                        @foreach($skillsAssessments[strtolower($report->paper->type->name)] as $key => $assessment)
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    {{ $key+1 }}. {{ $assessment['title'] }}
                                                </div>
                                                <div class="col-xs-12">
                                                    <ul class="nav nav-tabs">
                                                        @foreach($skillsAssessments['grades'] as $grade)
                                                            <li data-grade="{{ $grade }}"
                                                                data-skill="{{ $assessment['name'] }}"
                                                                @if($grade == $report->assessments[$assessment['name']])
                                                                class="active"
                                                                    @endif>
                                                                <a data-toggle="tab"
                                                                   href="#{{ strtolower($report->paper->type->name) . '-' . $assessment['name'] . '-' . $grade }}">{{ $grade }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    <div class="tab-content">
                                                        @foreach($skillsAssessments['grades'] as $grade)
                                                            <div id="{{ strtolower($report->paper->type->name) . '-' . $assessment['name'] . '-' . $grade }}"
                                                                 @if($grade == $report->assessments[$assessment['name']])
                                                                 class="tab-pane fade active in"
                                                                 @else
                                                                 class="tab-pane fade"
                                                                    @endif
                                                            >
                                                                <div class="row reference_description">
                                                                    <div class="col-xs-1 no-padding text-center">
                                                                        {{--<span class="fa fa-info-circle"></span>--}}
                                                                    </div>
                                                                    <div class="col-xs-10 no-padding">
                                                                        {!! trans('CEFR_' . strtolower($report->paper->type->name) .'/' . $assessment['name'] . '.' . $grade) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="row">
                                            <div class="col-xs-12">
                                                GLOBAL RESULT
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <ul class="nav nav-tabs">
                                                    @if($report->paper->type->id == TEST_SPEAKING)
                                                        @foreach($skillsAssessments['speaking-grades'] as $grade)
                                                            <li data-grade="{{ $grade }}"
                                                                data-skill="general_descriptors"
                                                                @if($grade == $report->assessments['general_descriptors'])
                                                                class="tab-pane fade active in"
                                                            @else
                                                                    @endif>
                                                                <a data-toggle="tab"
                                                                   href="#{{ 'speaking-general_descriptors-' . $grade }}">{{ $grade }}</a>
                                                            </li>
                                                        @endforeach
                                                    @else
                                                        @foreach($skillsAssessments['grades'] as $grade)
                                                            <li data-grade="{{ $grade }}"
                                                                data-skill="general_descriptors"
                                                                @if($grade == $report->assessments['general_descriptors'])
                                                                class="tab-pane fade active in"
                                                            @else
                                                                    @endif>
                                                                <a data-toggle="tab"
                                                                   href="#{{ strtolower($report->paper->type->name) . '-general_descriptors-' . $grade }}">{{ $grade }}</a>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                                <div class="tab-content">
                                                    @if($report->paper->type->id == TEST_SPEAKING)
                                                        @foreach($skillsAssessments['speaking-grades'] as $grade)
                                                            <div id="{{ 'speaking-general_descriptors-' . $grade }}"
                                                                 @if($grade == $report->assessments['general_descriptors'])
                                                                 class="tab-pane fade active in"
                                                                 @else
                                                                 class="tab-pane fade"
                                                                    @endif
                                                            >
                                                                <div class="row reference_description">
                                                                    <div class="col-xs-1 no-padding text-center">
                                                                        {{--<span class="fa fa-info-circle"></span>--}}
                                                                    </div>
                                                                    <div class="col-xs-10 no-padding">
                                                                        {!! trans('CEFR_' . strtolower($report->paper->type->name) . '/general_descriptors.' . $grade) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        @foreach($skillsAssessments['grades'] as $grade)
                                                            <div id="{{ 'writing-general_descriptors-' . $grade }}"
                                                                 @if($grade == $report->assessments['general_descriptors'])
                                                                 class="tab-pane fade active in"
                                                                 @else
                                                                 class="tab-pane fade"
                                                                    @endif
                                                            >
                                                                <div class="row reference_description">
                                                                    <div class="col-xs-1 no-padding text-center">
                                                                        {{--<span class="fa fa-info-circle"></span>--}}
                                                                    </div>
                                                                    <div class="col-xs-10 no-padding">
                                                                        {!! trans('CEFR_' . strtolower($report->paper->type->name) . '/general_descriptors.' . $grade) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xs-12 skills-comments">
                                                <div class="form-group">
                                                    {!! Form::label(strtolower($report->paper->type->name) . '[ability-aquired]', 'Move the slider to indicate how much of the ' . $report->assessments['general_descriptors'] . ' level the test-taker has aquired:') !!}
                                                    <input name="{{ strtolower($report->paper->type->name) }}[ability-aquired]"
                                                           id="{{ strtolower($report->paper->type->name) }}-ability-aquired"
                                                           type="text"
                                                           value="{{ $report->assessments[strtolower($report->paper->type->name)]['ability-aquired'] }}"/>
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label(strtolower($report->paper->type->name) . '[ability-next]', 'If the test-taker has aquired ' . $report->assessments['general_descriptors'] . ' skills, move the slider below to indicate how much:') !!}
                                                    <input name="{{strtolower($report->paper->type->name)}}[ability-next]"
                                                           id="{{ strtolower($report->paper->type->name) }}-ability-next"
                                                           type="text"
                                                           value="{{ $report->assessments[strtolower($report->paper->type->name)]['ability-next'] }}"/>
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label(strtolower($report->paper->type->name) . '[strengths]', 'Strengths:') !!}
                                                    {!! Form::textarea(strtolower($report->paper->type->name) . '[strengths]', $report->assessments[strtolower($report->paper->type->name)]['strengths'], ['class' => 'form-control', 'id' => strtolower($report->paper->type->name) . '-strengths', 'placeholder' => 'Strengths...', 'readonly' => true]) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label(strtolower($report->paper->type->name) . '[weaknesses]', 'Weaknesses:') !!}
                                                    {!! Form::textarea(strtolower($report->paper->type->name) . '[weaknesses]', $report->assessments[strtolower($report->paper->type->name)]['weaknesses'], ['class' => 'form-control', 'id' => strtolower($report->paper->type->name) . '-weaknesses', 'placeholder' => 'Weaknesses...', 'readonly' => true]) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::label(strtolower($report->paper->type->name) . '[mistakes]', 'Examples of repetitive mistakes:') !!}
                                                    {!! Form::textarea(strtolower($report->paper->type->name) . '[mistakes]', $report->assessments[strtolower($report->paper->type->name)]['mistakes'], ['class' => 'form-control', 'id' => strtolower($report->paper->type->name) . '-mistakes', 'placeholder' => 'Mistakes...', 'readonly' => true]) !!}
                                                </div>
                                                @if(isset($report->assessments[strtolower($report->paper->type->name)]['accent']))
                                                    <div class="form-group">
                                                        {!! Form::label(strtolower($report->paper->type->name) . '[accent]', 'Details on accent and pronuntiation:') !!}
                                                        {!! Form::select(strtolower($report->paper->type->name) . '[accent]', $skillsAssessments['accents'], $report->assessments[strtolower($report->paper->type->name)]['accent'], ['class' => 'form-control', 'id' => strtolower($report->paper->type->name) . '-accent', 'readonly' => true, 'placeholder' => 'Details on accent and pronuntiation']) !!}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footer')
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/scripts.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            if ($('.panel#task-speaking-skills').length) {

                var speaking_slider_ability_aquired = new Slider('#speaking-ability-aquired', {
                    min: 0,
                    max: 100,
                    value: $('#speaking-ability-aquired')[0].value,
                    formatter: function (value) {
                        return 'Ability: ' + value + '%';
                    }
                });

                var aquired_percentage = $('#speaking-ability-aquired')[0].value;
                speaking_slider_ability_aquired.on("change", function (e) {
                    speaking_slider_ability_aquired.setValue(aquired_percentage);
                });

                var speaking_slider_ability_next = new Slider('#speaking-ability-next', {
                    min: 0,
                    max: 100,
                    value: $('#speaking-ability-next')[0].value,
                    formatter: function (value) {
                        return 'Ability: ' + value + '%';
                    }
                });

                var next_percentage = $('#speaking-ability-next')[0].value;
                speaking_slider_ability_next.on("change", function (e) {
                    speaking_slider_ability_next.setValue(next_percentage);
                });
            }

            if ($('.panel#task-writing-skills').length) {
                var writing_slider_ability_aquired = new Slider('#writing-ability-aquired', {
                    min: 0,
                    max: 100,
                    disable: true,
                    value: $('#writing-ability-aquired')[0].value,
                    formatter: function (value) {
                        return 'Ability: ' + value + '%';
                    }
                });

                var aquired_percentage = $('#writing-ability-aquired')[0].value;
                writing_slider_ability_aquired.on("change", function (e) {
                    writing_slider_ability_aquired.setValue(aquired_percentage);
                });

                var writing_slider_ability_next = new Slider('#writing-ability-next', {
                    min: 0,
                    max: 100,
                    value: $('#writing-ability-next')[0].value,
                    formatter: function (value) {
                        return 'Ability: ' + value + '%';
                    }
                });

                var next_percentage = $('#writing-ability-next')[0].value;
                writing_slider_ability_next.on("change", function (e) {
                    writing_slider_ability_next.setValue(next_percentage);
                });
            }
        });
    </script>
@endsection
