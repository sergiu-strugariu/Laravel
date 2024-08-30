@if($task->hasSpeaking() && $task->task_status_id != CANCELED)
    <div class="col-xs-12 col-sm-12">
        <div class="task-test-Speaking"></div>
        <div class="panel" id="task-speaking-skills">
            <div class="panel-heading">
                Speaking skills assessment report
            </div>
            <div class="panel-body">
                {!! Form::open(['id' => 'task-assessment-form', 'class' => 'task-assessment-form']) !!}
                @if($speakingReport)
                    {!! Form::hidden('report_id', $speakingReport['id']) !!}
                    {!! Form::hidden('paper_id', $speakingReport['paper_id']) !!}
                @endif
                <button type="button" class="btn btn-danger pull-right" id="native-toggle"
                        @if($speakingReport && $speakingReport['grade'] == 'N') data-checked="true" @else data-checked="false" @endif >
                    Native
                </button>
                @foreach($skilsAssessments['speaking'] as $key => $assessment)
                    <div class="row native-off">
                        <div class="col-xs-12">
                            {{ $key+1 }}. {{ $assessment['title'] }}
                        </div>
                        <div class="col-xs-12">
                            <ul class="nav nav-tabs">
                                @foreach($skilsAssessments['grades'] as $grade)
                                    <li data-grade="{{ $grade }}"
                                        @if($speakingReport && isset($speakingReport['assessments'][$assessment['name']]) && $speakingReport['assessments'][$assessment['name']] == $grade) class="active" @endif
                                        data-skill="{{ $assessment['name'] }}">
                                        <a data-toggle="tab"
                                           href="#{{ 'speaking-' . $assessment['name'] . '-' . $grade }}">{{ $grade }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach($skilsAssessments['grades'] as $grade)
                                    <div id="{{ 'speaking-' . $assessment['name'] . '-' . $grade }}"
                                         class="tab-pane fade @if($speakingReport && isset($speakingReport['assessments'][$assessment['name']]) && $speakingReport['assessments'][$assessment['name']] == $grade) active in @endif ">
                                        <div class="row reference_description">
                                            <div class="col-xs-1 no-padding text-center">
                                                <span class="fa fa-info-circle"></span>
                                            </div>
                                            <div class="col-xs-10 no-padding">
                                                {!! trans('CEFR_speaking/' . $assessment['name'] . '.' . $grade) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="row">
                    <div class="col-xs-12  native-off">
                        GLOBAL RESULT
                    </div>
                    <div class="col-xs-12 form-group native-off" id="global-result-speaking">
                        <ul class="nav nav-tabs">
                            @foreach($skilsAssessments['speaking-grades'] as $grade)
                                <li data-grade="{{ $grade }}" data-skill="general_descriptors" @if($speakingReport && isset($speakingReport['assessments']['general_descriptors']) && $speakingReport['assessments']['general_descriptors'] == $grade) class="active" @endif >
                                    <a data-toggle="tab"
                                       href="#{{ 'speaking-general_descriptors-' . $grade }}">{{ $grade }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($skilsAssessments['speaking-grades'] as $grade)
                                <div id="{{ 'speaking-general_descriptors-' . $grade }}"
                                     class="tab-pane fade @if($speakingReport && isset($speakingReport['assessments']['general_descriptors']) && $speakingReport['assessments']['general_descriptors'] == $grade) active in @endif ">
                                    <div class="row reference_description">
                                        <div class="col-xs-1 no-padding text-center">
                                            <span class="fa fa-info-circle"></span>
                                        </div>
                                        <div class="col-xs-10 no-padding">
                                            {!! trans('CEFR_speaking/general_descriptors.' . $grade) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-xs-12 skills-comments">
                        <div class="form-group speaking-slider slider-1  native-off">
                            {!! Form::label('speaking[ability-aquired]', 'Move the slider to indicate how much of the Pre-A1 level the test-taker has aquired:', ['class' => 'label-top']) !!}
                            <input name="speaking[ability-aquired]" id="speaking-ability-aquired"
                                   type="text"/>
                        </div>
                        <div class="native-off">
                            <div class="form-group speaking-slider slider-2  hidden ">
                                {!! Form::label('speaking[ability-next]', 'If the test-taker has aquired A1 skills, move the slider below to indicate how much:', ['class' => 'label-bottom']) !!}
                                <input name="speaking[ability-next]" id="speaking-ability-next"
                                       type="text"/>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('speaking[strengths]', 'Strengths:') !!}
                            {!! Form::textarea('speaking[strengths]', $speakingReport['assessments']['speaking']['strengths'] ?? null, ['class' => 'form-control', 'id' => 'speaking-strengths', 'placeholder' => 'Strengths...', 'required' => true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('speaking[weaknesses]', 'Weaknesses:') !!}
                            {!! Form::textarea('speaking[weaknesses]', $speakingReport['assessments']['speaking']['weaknesses'] ?? null, ['class' => 'form-control', 'id' => 'speaking-weaknesses', 'placeholder' => 'Weaknesses...', 'required' => true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('speaking[mistakes]', 'Examples of repetitive mistakes:') !!}
                            {!! Form::textarea('speaking[mistakes]', $speakingReport['assessments']['speaking']['mistakes'] ?? null, ['class' => 'form-control', 'id' => 'speaking-mistakes', 'placeholder' => 'Mistakes...', 'required' => true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('speaking[accent]', 'Details on accent and pronunciation:') !!}
                            {!! Form::select('speaking[accent]', $skilsAssessments['accents'], $speakingReport['assessments']['speaking']['accent'] ??  null, ['class' => 'form-control', 'id' => 'speaking-accent', 'required' => true, 'placeholder' => 'Details on accent and pronuntiation']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Save', ['class' => 'btn btn-danger form-submit-assessment  native-off', 'id' => 'task-speaking-save']) !!}
                            <button class="btn btn-danger hidden" id="native-user">
                                Save
                            </button>
                        </div>

                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endif