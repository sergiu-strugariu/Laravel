<div class="panel" id="task-comments">
    <div class="panel-heading">
        Updates
    </div>
        <?php
            $canSeeCustom = Auth::user()->hasRole(['administrator', 'master', 'assessor']);
            $taskUpdates = $task->taskUpdates();
        ?>

        @if($taskUpdates->count())
            {{-- Clear the custom messages--}}
            @foreach($taskUpdates as $key => $log)
                @if (!$canSeeCustom && strpos($log->description, "__custom__") === 0)
                    <?php $taskUpdates = $taskUpdates->forget($key) ?>
                @endif
            @endforeach
        @endif
    <div class="panel-body" id="task-updates">
        <div class="row">
            <div class="col-xs-12">
                @hasRole(['administrator', 'master'])
                {!! Form::select('action', $updateActions['master'], null, ['class' => 'form-control', 'id' => 'task-update-action', 'required' => true, 'placeholder' => 'Insert update...']) !!}
                @endHasRole
                @hasRole(['assessor'])
                {!! Form::select('action', ["custom" => "Custom message"] + $updateActions['assessor'], null, ['class' => 'form-control', 'id' => 'task-update-action', 'required' => true, 'placeholder' => 'Insert update...']) !!}
                @endHasRole
                @hasRole(['client'])
                @if(($task->addedBy->id == auth()->user()->id
                        || in_array(auth()->user()->id, $task->followers->pluck('user_id')->toArray()))
                        || in_array(auth()->user()->id, $task->project->participants->pluck('user_id')->toArray())
                        &&  $task->task_status_id !== DONE )
                    {!! Form::select('action', $updateActions['client'], null, ['class' => 'form-control', 'id' => 'task-update-action', 'required' => true, 'placeholder' => 'Insert update...']) !!}
                @endif
                @endHasRole
                @hasRole(['css'])
                {!! Form::select('action', $updateActions['css'], null, ['class' => 'form-control', 'id' => 'task-update-action', 'required' => true, 'placeholder' => 'Insert update...']) !!}
                @endHasRole
            </div>

            <div class="col-xs-12" id="task-update-reschedule">
                <div class="row">
                    {!! Form::open(['id' => 'task-update-reschedule-form', 'files' => true]) !!}
                    <div class='col-md-12'>
                        <div class="form-group">
                            <div class='input-group date with-icon'>
                                {!! Form::input('text', 'availability_from', null, ['class' => 'form-control sel-status', 'id' => 'availability_from', 'placeholder' => 'Choose day', 'required' => true]) !!}
                                <span class="input-group-addon"><span
                                            class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class='col-md-6'>
                        {!! Form::label('edit_timepick_from', 'From') !!}
                        <div class="form-group">
                            <div class='input-group'>
                                {!! Form::input('text', 'from_date', null, ['class' => 'form-control sel-status timepick_from', 'id' => 'edit_timepick_from', 'placeholder' => 'Hour (Romanian time)', 'readonly' => 'readonly', 'required' => true]) !!}
                            </div>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        {!! Form::label('edit_timepick_to', 'To') !!}
                        <div class="form-group">
                            <div class='input-group'>
                                {!! Form::input('text', 'to_date', null, ['class' => 'form-control sel-statusx timepick_to', 'id' => 'edit_timepick_to', 'placeholder' => 'Hour (Romanian time)', 'readonly' => 'readonly', 'required' => true]) !!}
                            </div>
                        </div>
                    </div>
                    <div class='col-md-12'>
                        {!! Form::label('rescheduler', 'Who is requesting to reschedule?') !!}
                        <div class="form-group">
                            <div class='input-group'>
                                {!! Form::select('rescheduler', ['' => 'Choose an option from the list', 'client' => 'Client', 'candidate' => 'Candidate', 'assessor' => 'Assessor'], null, ['class' => 'form-control rescheduler', 'id' => 'rescheduler', 'required' => true]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 text-right">
                        {!! Form::submit('Update', ['class' => 'btn btn-danger', 'id' => 'task-update-reschedule-submit']) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        @if(auth()->user()->hasRole('assessor'))
        <div class="hidden">
            <div class="row custom-update-row">
                <div class="col-md-12">
                    <h3>Enter your custom update message below</h3>
                    <div class="form-group">
                        <textarea id="custom-update" maxlength="200" rows="3" class="form-control col-md-12" placeholder="Please enter your custom update message"></textarea>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="contents">
            @if($taskUpdates->count())
                @foreach($taskUpdates as $key => $log)

                    <div class="row {{ $key >= 5 ? 'hidden' : '' }}">
                        <div class="col-xs-12 relative pr-25">
                            {{ str_replace("__custom__", "", $log->description) }}
                            <span>{{ date('d M Y', strtotime($log->created_at)) . ' at ' .  date('H:i', strtotime($log->created_at)) }}
                                @if($log->user && auth()->user()->hasRole('client') && $log->user->hasRole('assessor'))
                                    by assessor.
                                @else
                                    by {{ $log->user == null ? 'System' : $log->user->full_name }}
                                @endif
                            </span>
                            @hasRole(['master','administrator'])
                            <i class="fa fa-remove task-delete-log" title="Remove" data-log-id="{{$log->id}}"></i>
                            @endHasRole
                        </div>
                    </div>

                @endforeach
                @if($taskUpdates->count() > 5)
                    <div class="row text-center task-show-more">Show more...</div>
                @endif
            @else
                <div class='row'>
                    <div class='col-xs-12 text-center'>No updates found.</div>
                </div>
            @endif
        </div>
    </div>
</div>