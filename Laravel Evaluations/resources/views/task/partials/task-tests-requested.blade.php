@hasRole(['administrator', 'master', 'client', 'css', 'recruiter'])
<div class="col-md-12 no-padding flex" id="tests-details">
    <div class="col-md-6">
        <div class="panel" id="task-tests-requested">
            <div class="panel-heading row">
                <div class="col-xs-10">
                    Tests requested
                </div>
                <div class="col-xs-2 text-right">
                    @canAtLeast('task.update')
                    <span class="add-test task-edit-details-modal" title="Add test to this task"><i class="fa fa-plus"></i></span>
                    <!-- Modal -->
                    <div class="modal fade text-left modal-center" id="add-test-modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Task #{{$task->id}} add test:</h4>
                                </div>
                                <div class="modal-body">
                                    {!! Form::open(['class' => 'inline', 'method' => 'POST', 'action' => ['TaskController@updateField', $task->id]]) !!}
                                    <div class="form-group">
                                        {!! Form::label('type') !!}
                                        {!! Form::select('test[paper_type_id][]', $restPaperTypes, null, ['class' => 'form-control sel-status select2 add-test-type-task', 'id' => 'paper_type_id', 'multiple' => true]) !!}
                                    </div>

                                    <div class="form-group deadline-container hidden">
                                        {!! Form::label('deadline', 'Deadline for online tests') !!}
                                        <div class="input-group date with-icon">
                                            {!! Form::input('text', 'test[deadline]', null, ['class' => 'form-control sel-status', 'id' => 'deadline', 'placeholder' => 'Deadline']) !!}
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>

                                    <div class="availability-container hidden">
                                        {!! Form::label('availability', 'Availability for Speaking Test (day)') !!}
                                        <div class="row">
                                            <div class='col-md-12'>
                                                <div class="form-group">
                                                    <div class='input-group date with-icon'>
                                                        {!! Form::input('text', 'test[availability_from]', null, ['class' => 'form-control sel-status speaking-availability', 'id' => 'availability_from', 'placeholder' => 'Choose day']) !!}
                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <label for="">Availability for Speaking Test (time)</label>
                                            </div>
                                            <div class='col-md-6'>
                                                <label for="timepick_from">From</label>
                                                <div class="form-group">
                                                    <div class='input-group'>
                                                        {!! Form::input('text', 'test[from_date]', null, ['class' => 'form-control sel-status timepick_from', 'id' => 'timepick_from', 'placeholder' => 'Hour (Romanian time)']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='col-md-6'>
                                                <label for="timepick_to">To</label>
                                                <div class="form-group">
                                                    <div class='input-group'>
                                                        {!! Form::input('text', 'test[to_date]', null, ['class' => 'form-control sel-status timepick_to', 'id' => 'timepick_to', 'placeholder' => 'Hour (Romanian time)']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="assessor-container"></div>

                                    {!! Form::close() !!}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger submit-modal-form add-test-submit" data-action-type="add-test">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endCanAtLeast
                </div>

            </div>
            <div class="panel-body">
                @foreach($label as $paper)
                    <div class="row">
                        <div class="col-xs-4 col-sm-4" style="white-space: nowrap;">
                            <div>
                                <span>{{ $paper->type->name }}</span>
                                @if($paper->report)
                                    <span class="fa fa-info-circle info-circle-created">
                                        <div class="col-xs-12 no-padding info-report-created">
                                            <div class="col-xs-12 no-padding">
                                                    <div class="col-xs-4 no-padding">Date Taken</div>
                                                <div class="col-xs-8">{{
                                                date('M d Y H:i', strtotime(  $paper->report->created_at))
                                              }}</div>
                                            </div>
                                        </div>
                                    </span>
                                @endif
                            </div>

                            @if (!($hidePrices && !Auth()->user()->hasRole('master','administrator')))
                                <div>
                                    {{-- If the test has been billed, show the billing icon --}}
                                    @if ($paper->invoice_id)
                                        <span class="pricing-icon dark"></span>
                                    @endif

                                    @if ($paper->paper_type_id == TEST_SPEAKING)
                                        <span>{{$paper->cost + $task->custom_period_cost}} EUR</span>
                                    @else
                                        <span>{{$paper->cost}} EUR</span>
                                    @endif
                                </div>
                            @endif




                        </div>
                        <div class="col-xs-4 col-sm-4  col-md-7 col-lg-3 no-padding">
                            <div class="form-group">
                                @hasRole(['client'])
                                @if( $paper->status_id == ALLOCATED || $paper->status_id == CANCELED)
                                    {!! Form::select('Paper[' . $paper->id . '][status_id]', [ ALLOCATED => 'Allocated', CANCELED => 'Canceled'], $paper->status_id, ['class' => 'form-control paper_status', 'id' => 'paper-status-' . $paper->id, 'style' => 'background-color: ' . $paper->status->color, 'data-id' => $paper->id]) !!}
                                {{--@elseif ($paper->status_id == CANCELED)--}}
                                    {{--{!! Form::select('Paper[' . $paper->id . '][status_id]', [ CANCELED => 'Canceled'], $paper->status_id, ['class' => 'form-control paper_status', 'id' => 'paper-status-' . $paper->id, 'style' => 'background-color: ' . $paper->status->color, 'data-id' => $paper->id]) !!}--}}
                                @elseif ( $paper->status_id == DONE)
                                    {!! Form::select('Paper[' . $paper->id . '][status_id]', [ DONE => 'Done'], $paper->status_id, ['class' => 'form-control paper_status', 'id' => 'paper-status-' . $paper->id, 'style' => 'background-color: ' . $paper->status->color, 'data-id' => $paper->id]) !!}
                                @elseif ( $paper->status_id == ISSUE)
                                    {!! Form::select('Paper[' . $paper->id . '][status_id]', [ ISSUE => 'Issue'], $paper->status_id, ['class' => 'form-control paper_status', 'id' => 'paper-status-' . $paper->id, 'style' => 'background-color: ' . $paper->status->color, 'data-id' => $paper->id]) !!}
                                @elseif ( $paper->status_id == IN_PROGRESS)
                                    {!! Form::select('Paper[' . $paper->id . '][status_id]', [ IN_PROGRESS => 'In Progress'], $paper->status_id, ['class' => 'form-control paper_status', 'id' => 'paper-status-' . $paper->id, 'style' => 'background-color: ' . $paper->status->color, 'data-id' => $paper->id]) !!}
                                @endif
                                @endHasRole
                                @hasRole(['administrator', 'master', 'assessor', 'css', 'recruiter'])
                                {!! Form::select('Paper[' . $paper->id . '][status_id]', $taskStatuses, $paper->status_id, ['class' => 'form-control paper_status', 'id' => 'paper-status-' . $paper->id, 'style' => 'background-color: ' . $paper->status->color, 'data-id' => $paper->id]) !!}
                                @endHasRole
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-7 col-lg-4">
                            @if($paper->done && !auth()->user()->hasRole('client'))
                                <div class="form-group">
                                    @if(!empty($paper->report) && in_array($paper->type->id, [TEST_WRITING, TEST_SPEAKING]))
                                        <button class="btn btn-reset-report" data-id="{{ $paper->id }}">
                                            Reset Report
                                        </button>
                                    @elseif(empty($paper->report) || $paper->done == 1)
                                        <button class="btn btn-reset-test" data-id="{{ $paper->id }}">
                                            Reset Test
                                        </button>
                                    @endif
                                </div>
                            @endif
                            @hasRole(['client', 'administrator', 'master'])
                            @if (!$paper->done && empty($paper->report))
                                    <div class="form-group">
                                        <button class="btn btn-test-take-here-multiple" data-type="{{ $paper->type->id }}">
                                            Take this test
                                        </button>
                                    </div>
                            @endif
                            @endHasRole
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 no-padding">
                            @if(!$paper->done)
                                @canAtLeast('task.update')
                                <span class="add-test task-delete-test" title="Remove" data-paper-id="{{$paper->id}}"><i class="fa fa-remove"></i></span>
                                @endCanAtLeast
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if($task->completedTests()->count())
        <div class="col-md-6">
            <div class="panel" id="task-tests-details">
                <div class="panel-heading">
                    Tests details
                </div>
                <div class="panel-body">
                    <canvas id="task-tests-chart"></canvas>
                </div>
            </div>
        </div>
    @endif
</div>
@endHasRole