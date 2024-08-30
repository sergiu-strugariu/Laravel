@if(!empty($task->writingTest(true)) && $task->task_status_id != CANCELED)
    @php
        $writingTest = $task->writingTest(true);
    @endphp
    <div class="col-xs-12 col-sm-12">
        <div class="task-test-Writing"></div>
        <div class="panel" id="task-writing-skills">
            <div class="panel-heading">
                Writing skills assessments report
            </div>
            <div class="panel-body">
                @if(!empty($writingTest->paper_answers))
                    <div class="form-group">
                        @php
                            $paperAnswerFirst = $writingTest->paper_answers->first();
                            $totalTime = gmdate('i:s', $paperAnswerFirst->time);
                            $questionBody = \App\Models\Question::withTrashed()->find($paperAnswerFirst->question_id)->body;
                        @endphp
                        <p style="font-weight: bold;">{!! str_replace("\n", "<br>", $questionBody) !!}</p>
                        {!! Form::textarea('writing-answer', $writingTest->paper_answers->first()->user_answer, ['class' => 'form-control', 'id' => 'writing-answer', 'readonly' => true, 'placeholder' => 'Writing answer...', 'required' => true]) !!}
                    </div>
                    @if ($writingTest->paper_answers->first()->user_answer)
                        <p>
                            Candidate wrote {{ count(explode(' ', $paperAnswerFirst->user_answer)) }} words in {{ $totalTime }}
                        </p>
                    @else
                        <p>The candidate submitted the test without an answer.</p>
                    @endif
                    <br>
                @endif
                {!! Form::open(['id' => 'task-assessment-form', 'class' => 'task-assessment-form']) !!}
                @if($writingReport)
                    {!! Form::hidden('report_id', $writingReport['id']) !!}
                    {!! Form::hidden('paper_id', $writingReport['paper_id']) !!}
                @endif
                @foreach($skilsAssessments['writing'] as $key => $assessment)
                    <div class="row">
                        <div class="col-xs-12">
                            {{ $key+1 }}. {{ $assessment['title'] }}
                        </div>
                        <div class="col-xs-12">
                            <ul class="nav nav-tabs">
                                @foreach($skilsAssessments['grades'] as $grade)
                                    <li data-grade="{{ $grade }}"
                                        @if($writingReport && $writingReport['assessments'][$assessment['name']] == $grade) class="active" @endif
                                        data-skill="{{ $assessment['name'] }}">
                                        <a data-toggle="tab"
                                           href="#{{ 'writing-' . $assessment['name'] . '-' . $grade }}">{{ $grade }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach($skilsAssessments['grades'] as $grade)
                                    <div id="{{ 'writing-' . $assessment['name'] . '-' . $grade }}"
                                         class="tab-pane fade @if($writingReport && $writingReport['assessments'][$assessment['name']] == $grade) active in @endif ">
                                        <div class="row reference_description">
                                            <div class="col-xs-1 no-padding text-center">
                                                <span class="fa fa-info-circle"></span>
                                            </div>
                                            <div class="col-xs-10 no-padding">
                                                {!! trans('CEFR_writing/' . $assessment['name'] . '.' . $grade) !!}
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
                    <div class="col-xs-12 form-group" id="global-result-writing">
                        <ul class="nav nav-tabs">
                            @foreach($skilsAssessments['writing-grades'] as $grade)
                                <li data-grade="{{ $grade }}" data-skill="general_descriptors" @if($writingReport && $writingReport['assessments']['general_descriptors'] == $grade) class="active" @endif>
                                    <a data-toggle="tab"
                                       href="#{{ 'writing-general_descriptors-' . $grade }}">{{ $grade }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($skilsAssessments['grades'] as $grade)
                                <div id="{{ 'writing-general_descriptors-' . $grade }}"
                                     class="tab-pane fade @if($writingReport && $writingReport['assessments']['general_descriptors'] == $grade) active in @endif">
                                    <div class="row reference_description">
                                        <div class="col-xs-1 no-padding text-center">
                                            <span class="fa fa-info-circle"></span>
                                        </div>
                                        <div class="col-xs-10 no-padding">
                                            {!! trans('CEFR_writing/general_descriptors.' . $grade) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-xs-12 skills-comments">
                        <div class="form-group writing-slider slider-1">
                            {!! Form::label('writing[ability-aquired]', 'Move the slider to indicate how much of the Pre-A1 level the test-taker has aquired:', ['class' => 'label-top']) !!}
                            <input name="writing[ability-aquired]" id="writing-ability-aquired"
                                   type="text"/>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group writing-slider slider-2 hidden">
                            {!! Form::label('writing[ability-next]', 'If the test-taker has aquired A1 skills, move the slider below to indicate how much:', ['class' => 'label-bottom']) !!}
                            <input name="writing[ability-next]" id="writing-ability-next" type="text"/>
                        </div>
                        <div class="form-group">
                            {!! Form::label('writing[strengths]', 'Strengths:') !!}
                            {!! Form::textarea('writing[strengths]', $writingReport['assessments']['writing']['strengths'] ?? null, ['class' => 'form-control', 'id' => 'writing-strengths', 'placeholder' => 'Strengths...', 'required' => true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('writing[weaknesses]', 'Weaknesses:') !!}
                            {!! Form::textarea('writing[weaknesses]', $writingReport['assessments']['writing']['weaknesses'] ?? null, ['class' => 'form-control', 'id' => 'writing-weaknesses', 'placeholder' => 'Weaknesses...', 'required' => true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('writing[mistakes]', 'Examples of repetitive mistakes:') !!}
                            {!! Form::textarea('writing[mistakes]', $writingReport['assessments']['writing']['mistakes'] ?? null, ['class' => 'form-control', 'id' => 'writing-mistakes', 'placeholder' => 'Mistakes...', 'required' => true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Save', ['class' => 'btn btn-danger form-submit-assessment', 'id' => 'task-writing-save']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endif