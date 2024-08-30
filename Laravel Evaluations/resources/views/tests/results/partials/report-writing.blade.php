@if(!empty($task->writingTest(true)) && !empty($task->writingTest(true)->report))
    @php
        $paper = $task->writingTest(true);
        $paperAnswerFirst = $paper->paper_answers->first();
        $totalTime = gmdate('i:s', $paperAnswerFirst->time);
        $testReport = $task->writingTest(true)->report;
        $assessments = json_decode($testReport->assessments, true);
    @endphp
    <div class="col-sm-12 pb-check">
        {{--<div class="task-test-{{ $paper->type->name }}"></div>--}}
        <div class="panel" id="task-tests-result" style="margin-bottom: 0;">
            <div class="panel-heading">
                Writing Skills Assessment Report <small> - {{date('d M Y H:i', strtotime($paper->report->created_at))}} - {{$totalTime}}</small>
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

                <div class="row reference_description" style="margin-bottom: 0;">
                    <div class="col-xs-12 ">
                        {!! trans('CEFR_' . str_replace(' ', '_', strtolower($paper->type->name) .'/general_descriptors.' . $paper->report->grade)) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-sm-12 pb-check">

        <div class="panel-body">
            <p><strong>Strengths: </strong>{{$assessments['writing']['strengths']}}</p>
            <p><strong>Weaknesses: </strong>{{$assessments['writing']['weaknesses']}}</p>
            <p><strong>Examples of repetitive mistakes: </strong>{{$assessments['writing']['mistakes']}}</p>
        </div>

        <div class="pb_before"></div>

    </div>

    @foreach($skilsAssessments['writing'] as $key => $assessment)

        <div class="col-sm-12 pb-check">
            {{--<div class="task-test-{{ $paper->type->name }}"></div>--}}
            <div class="panel" id="task-tests-result" style="margin-bottom: 0;">
                <div class="panel-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel col-xs-12">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-2 text-center no-padding">
                                            <button class="btn btn-danger">{{$assessments[$assessment['name']]}}</button>
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
                            {!! str_replace(['”', '“'], '"', trans('CEFR_writing/' .$assessment['name'].'.'. $assessments[$assessment['name']])) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    @endforeach


@endif
