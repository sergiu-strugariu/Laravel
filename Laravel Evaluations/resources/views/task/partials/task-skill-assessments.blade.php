@hasRole(['administrator', 'master', 'client', 'css', 'tds', 'recruiter'])
<div class="col-md-12 no-padding">
    @foreach($task->completedTests() as $key => $paper)
        @if($key%2==0)
            <div class="flex">
                @endif
                <div class="col-md-6">
                    <div class="task-test-{{ $paper->type->name }}"></div>
                    <div class="panel" id="task-tests-result">
                        <div class="panel-heading">
                            {{ $paper->type->name }} Skills Assessment
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel col-xs-12">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-2 text-center no-padding">
                                                    <button class="btn btn-danger btn-grade">{{ $paper->report->grade }}</button>
                                                </div>
                                                <div class="task-test-grade col-sm-8 no-padding text-center">
                                                    {{$user_abilities[$paper->report->grade]}}
                                                    {{--<span>Effective Operational Proficiency</span>--}}
                                                </div>
                                                <div class="task-test-score col-sm-2 text-right">
                                                    Score
                                                    <span>{{ $paper->report->ability }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($paper->report->grade != 'N')
                                <div class="row reference_description">
                                    <div class="col-xs-2 no-padding text-center">
                                        <span class="fa fa-info-circle"></span>
                                    </div>
                                    <div class="col-xs-9 no-padding">
                                        {!! trans('CEFR_' . str_replace(' ', '_', strtolower($paper->type->name) .'/general_descriptors.' . $paper->report->grade)) !!}
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="{{ '/results/test/' . $paper->report->id . '/result' }}">
                                        <button class="btn btn-danger" id="view-test-{{ $paper->id }}">
                                            View Test
                                        </button>
                                    </a>
                                    @if($paper->paper_type_id == TEST_LANGUAGE_USE || $paper->paper_type_id == TEST_LANGUAGE_USE_NEW)
                                        <a href="{{ '/results/downloadPDF/' . $paper->report->id }}" class="btn btn-danger">Download as PDF</a>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                @if($key%2==1 || $key == $task->completedTests()->count() - 1)
            </div>
        @endif
    @endforeach
</div>
@endHasRole