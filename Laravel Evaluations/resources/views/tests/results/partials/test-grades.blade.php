@foreach($task->completedTests() as $key => $paper)

    @if(in_array($paper->paper_type_id, [TEST_WRITING, TEST_SPEAKING]))
        @continue
    @endif

    <div class="col-sm-12 " style="margin-top: 20px;;">
        {{--<div class="task-test-{{ $paper->type->name }}"></div>--}}
        <div class="panel pb-check" id="task-tests-result">
            <div class="panel-heading">
                {{ $paper->type->name }} Skills Assessment <small> - {{date('d M Y H:i', strtotime($paper->report->created_at))}}</small>
            </div>
            <div class="panel-body">
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

                <div class="row reference_description">
                    <div class="col-xs-12 ">
                        {!! trans('CEFR_' . str_replace(' ', '_', strtolower($paper->type->name) .'/general_descriptors.' . $paper->report->grade)) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>

@endforeach