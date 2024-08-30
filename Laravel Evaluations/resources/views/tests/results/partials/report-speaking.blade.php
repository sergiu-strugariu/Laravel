@if(!empty($task->speakingTest(true)) && !empty($task->speakingTest(true)->report))
    @php
        $paper = $task->speakingTest(true);
        $testReport = $task->speakingTest(true)->report;
        $assessments = json_decode($testReport->assessments, true);
    @endphp
    <div class="col-sm-12 pb-check">
        {{--<div class="task-test-{{ $paper->type->name }}"></div>--}}
        <div class="panel" id="task-tests-result" style="margin-bottom: 0;">
            <div class="panel-heading">
                Speaking skills assessment report <small> - {{date('d M Y H:i', strtotime($testReport->created_at))}}</small>
            </div>
            <div class="panel-body" style="padding-bottom: 0;">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel col-xs-12">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-2 text-center no-padding">
                                        <button class="btn btn-danger">{{ $paper->report->grade }}</button>
                                    </div>
                                    <div class="task-test-grade col-xs-8 no-padding text-center">
                                        {{$user_abilities[$paper->report->grade]}}
                                    </div>
                                    <div class="task-test-score col-xs-2 text-right">
                                        Score
                                        <span>{{ $paper->report->ability }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if( $paper->report->grade != 'N' )
                    <div class="row reference_description" style="margin-bottom: 0;">
                        <div class="col-xs-12 ">
                            {!! trans('CEFR_' . str_replace(' ', '_', strtolower($paper->type->name) .'/general_descriptors.' . $paper->report->grade)) !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-sm-12  pb-check">

        <div class="panel-body">
            <p><strong>Strengths: </strong>{{$assessments['speaking']['strengths']}}</p>
            <p><strong>Weaknesses: </strong>{{$assessments['speaking']['weaknesses']}}</p>
            <p><strong>Examples of repetitive mistakes: </strong>{{$assessments['speaking']['mistakes']}}</p>
            <p><strong>Details on accent and pronunciation: </strong>{{$defaultSkillAssessment['accents'][$assessments['speaking']['accent']]}}</p>
        </div>

        @if( $paper->report->grade != 'N' )
            <div class="pb_before"></div>
        @endif

    </div>



    @foreach($skilsAssessments['speaking'] as $key => $assessment)

        @if( $paper->report->grade == 'N' )
            @continue;
        @endif

        <div class="col-sm-12  pb-check">

            {{--<div class="task-test-{{ $paper->type->name }}"></div>--}}
            <div class="panel" id="task-tests-result" style="margin-bottom: 0;">
                <div class="panel-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel col-xs-12">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-2 text-center no-padding">
                                            <button class="btn btn-danger">@if( $paper->report->grade == 'N' ) N @else {{ $assessments[$assessment['name']] }} @endif</button>
                                        </div>
                                        <div class="task-test-grade col-xs-8 no-padding text-center">
                                            {{ $key+1 }}. {{ $assessment['title'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row reference_description" style="margin-bottom: 0;">
                        <div class="col-xs-12 ">
                            {!! str_replace(['”', '“'], '"', trans('CEFR_speaking/' .$assessment['name'].'.'. $assessments[$assessment['name']])) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    @endforeach

@endif
