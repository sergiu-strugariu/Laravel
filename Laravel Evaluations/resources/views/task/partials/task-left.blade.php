<div class="panel" id="task-user-details">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-4 col-sm-2 text-center">
                <span class="fa fa-user fa-4x"></span>
            </div>
            <div class="col-xs-8 col-sm-10">
                <div class="col-sm-12 no-padding" id="test-taker">
                    <div class="row">
                        <div class="col-xs-12">

                            {{--TASK NAME--}}

                            <div class="show-edit">
                                <div class="edit-html">
                                    <strong id="replace">{{ $task->name }}</strong>
                                    @canAtLeast(['task.update'])
                                    <i class="fa fa-pencil task-edit-details"
                                       data-field="name" title="Edit"></i>
                                    @endCanAtLeast
                                </div>
                                @canAtLeast(['task.update'])
                                <div class="edit-input">
                                    <div class="row">
                                        {!! Form::open(['class' => 'inline']) !!}
                                        <div class="col-xs-8">
                                            {!! Form::input('text', 'name', $task->name, ['class' => 'form-control', 'id' => 'edit-name', 'placeholder' => 'Task Name', 'required' => true]) !!}
                                        </div>
                                        <div class="col-xs-4 no-padding">
                                            <i class="fa fa-check-circle-o submit-form-inline-task fa-2x"
                                               data-field="name" title="Save"></i>
                                            <i class="fa fa-times-circle-o fa-2x"
                                               data-field="name" title="Exit"></i>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                                @endCanAtLeast
                            </div>

                            {{--TASK ID--}}

                            <span>#{{ $task->id }}</span>
                        </div>

                        {{--TASK STATUS--}}

                        <div class="col-xs-12 form-group">
                            @hasRole(['administrator', 'master', 'css', 'recruiter'])
                            {!! Form::select('task_status_id', $taskStatuses, $task->task_status_id, ['class' => 'form-control task-status-'.$task->task_status_id, 'id' => 'task_status', 'style' => 'background-color: ' . $task->color]) !!}
                            @endHasRole

                            @canAtLeast(['status.change_to_allocated','status.change_to_issue'])
                            @if(auth()->user()->hasRole('assessor') && auth()->user()->roles()->count() == 1)
                                {!! Form::select('task_status_id', [0=>$task->status->name] , $task->task_status_id, ['class' => 'form-control task-status-'.$task->task_status_id, 'id' => 'task_status', 'style' => 'background-color: ' . $task->color]) !!}
                            @else
                                @if( in_array($task->task_status_id, [ ALLOCATED, ISSUE ]))
                                    {!! Form::select('task_status_id', $taskStatusesAssessor, $task->task_status_id, ['class' => 'form-control task-status-'.$task->task_status_id, 'id' => 'task_status', 'style' => 'background-color: ' . $task->color]) !!}
                                @endif
                            @endif

                            @endCanAtLeast

                            @hasRole(['client'])
                                @if( in_array($task->task_status_id, [ DONE ]))
                                    {!! Form::select('task_status_id', [ DONE => 'Done'], $task->task_status_id, ['class' => 'form-control task-status-'.$task->task_status_id, 'id' => 'task_status', 'style' => 'background-color: ' . $task->color]) !!}
                                @else
                                    {!! Form::select('task_status_id', $taskStatusesClient, $task->task_status_id, ['class' => 'form-control task-status-'.$task->task_status_id, 'id' => 'task_status', 'style' => 'background-color: ' . $task->color]) !!}
                                @endif
                            @endHasRole


                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="panel-body">

            <div class="@if(in_array(TEST_WRITING, $task->papers->pluck('paper_type_id')->toArray())
                                     || in_array(TEST_SPEAKING, $task->papers->pluck('paper_type_id')->toArray())) not-hidden @else hidden @endif">

                {{--NATIVE--}}

                <div class="show-edit">
                    @hasRole(['master', 'administrator', 'client', 'css'])
                    <div class="edit-html">
                        <strong>Native assessor:</strong> <span id="replace">{{ $task->native == 1 ? 'Yes' : 'No' }}</span>
                        @canAtLeast(['task.update'])
                        <i class="fa fa-pencil task-edit-details-modal" data-field="native" title="Edit"></i>
                        <!-- Modal -->
                        <div class="modal fade modal-center" id="assessor-modal" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Task #{{$task->id}} Assessor:</h4>
                                    </div>
                                    <div class="modal-body row">
                                        {!! Form::open(['class' => 'inline', 'id' => 'assessor-form', 'method' => 'POST', 'action' => ['TaskController@updateField', $task->id]]) !!}
                                        {!! Form::hidden('project_type_id', $task->project->project_type_id) !!}
                                        {!! Form::hidden('language_id', $task->language_id) !!}
                                        @hasRole(['administrator', 'master', 'css', 'recruiter'])
                                        <div class="@if(count($nativeOptions) == 1) col-sm-12 @else col-sm-8 @endif">
                                            {!! Form::label('Assessor:') !!}
                                            {!! Form::select('assessor_id', $assessors, $task->assessor_id, ['class' => 'form-control select2', 'id' => 'assessor_id', 'required' => true]) !!}
                                        </div>
                                        <div class="@if(count($nativeOptions) == 1) hidden @else col-sm-4 @endif">
                                            {!! Form::label('Native assessor:') !!}
                                            {!! Form::select('native', $nativeOptions, $task->native, ['class' => 'form-control select2', 'id' => 'edit-native', 'required' => true]) !!}
                                        </div>
                                        @endHasRole
                                        @hasRole(['client'])
                                        <div class="col-sm-4">
                                            {!! Form::label('Native assessor:') !!}
                                            {!! Form::select('native', $nativeOptions, $task->native, ['class' => 'form-control select2', 'id' => 'edit-native', 'required' => true]) !!}
                                        </div>
                                        @endHasRole

                                        {!! Form::close() !!}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-danger submit-modal-form" data-action-type="assessor">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endCanAtLeast
                    </div>
                    @endHasRole
                </div>

                {{--ASSESSOR--}}


                <div class="show-edit">
                    @hasRole(['administrator', 'master', 'assessor', 'css','recruiter', 'tds'])
                    <div class="edit-html">
                        <strong>Assessor:</strong> <span id="replace">{{ empty($task->assessor) ? '' : $task->assessor->full_name }}</span>
                        @if(!Auth::user()->hasRole(['assessor']))
                            <i class="fa fa-pencil task-edit-details-assessor" data-field="native" title="Edit"></i>
                        @endif
                    </div>
                    @endHasRole
                </div>

                {{--PAY ASSESSOR--}}

                <div class="show-edit">
                    @hasRole(['master', 'administrator', 'assessor', 'css', 'recruiter'])
                    <div class="edit-html">
                        <strong>Pay Assessor:</strong> <span id="replace">{{ $task->pay_assessor == 1 ? 'Yes' : 'No' }}</span>
                        @canAtLeast(['task.update'])
                        <i class="fa fa-pencil task-edit-details" data-field="native" title="Edit"></i>
                        @endCanAtLeast
                    </div>
                    @endHasRole
                    @hasRole(['master', 'administrator', 'css', 'recruiter'])
                    <div class="edit-input">
                        <div class="edit-inline-form">
                            <strong>Pay Assessor:</strong>
                            {!! Form::open(['class' => 'inline']) !!}
                            <div class="form-inline">
                                {!! Form::select('pay_assessor', [ 0 => 'No', 1 => 'Yes' ], $task->pay_assessor, ['class' => 'form-control', 'id' => 'edit-language', 'required' => true]) !!}
                                <div class="container-controls">
                                    <i class="fa fa-check-circle-o submit-form-inline-task fa-2x" data-field="phone" title="Save"></i>
                                    <i class="fa fa-times-circle-o fa-2x" data-field="native" title="Exit"></i>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    @endHasRole
                </div>
            </div>
            {{--BILL CLIENT--}}

            <div class="show-edit">
                @hasRole(['master', 'administrator', 'css', 'recruiter'])
                <div class="edit-html">
                    <strong>Bill Client:</strong> <span id="replace">
                        @if($task->bill_client == 0 )
                            No
                        @elseif($task->bill_client == 1)
                            Yes
                        @elseif($task->bill_client == 2)
                            Half Price
                        @endif
                    </span>
                    <i class="fa fa-pencil task-edit-details" data-field="native" title="Edit"></i>
                </div>
                <div class="edit-input">
                    <div class="edit-inline-form">
                        <strong>Bill Client:</strong>
                        {!! Form::open(['class' => 'inline']) !!}
                        <div class="form-inline">
                            {!! Form::select('bill_client', [ 0 => 'No', 1 => 'Yes', 2 => 'Half Price' ], $task->bill_client, ['class' => 'form-control bill-client-select', 'id' => 'edit-language', 'required' => true]) !!}
                            <div class="container-controls">
                                <i class="fa fa-check-circle-o submit-form-inline-task fa-2x" data-field="phone" title="Save"></i>
                                <i class="fa fa-times-circle-o fa-2x" data-field="native" title="Exit"></i>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @endHasRole
            </div>
            <div class="show-edit {!! ($task->bill_client == 0) ? "hidden" : "" !!} bill-amount-container">
                @hasRole(['master', 'administrator', 'css', 'recruiter'])
                <div class="edit-html">
                    <strong>Additional Cost:</strong> <span id="replace">{!! ($task->additional_cost == null ) ? 0 : $task->additional_cost !!}</span>
                    <i class="fa fa-pencil task-edit-details" data-field="native" title="Edit"></i>
                </div>
                <div class="edit-input">
                    <div class="edit-inline-form">
                        <strong>Additional Cost:</strong>
                        {!! Form::open(['class' => 'inline']) !!}
                        <div class="form-inline">
                            {{ Form::text('additional_cost', $task->additional_cost, ['class' => 'form-control bill-amount', 'id' => 'bill-amount', 'required' => false]) }}
                            <div class="container-controls">
                                <i class="fa fa-check-circle-o submit-form-inline-task fa-2x" data-field="phone" title="Save"></i>
                                <i class="fa fa-times-circle-o fa-2x" data-field="native" title="Exit"></i>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @endHasRole
            </div>

            {{--HALF PRICE--}}
            {{--@hasRole(['master', 'administrator'])--}}
                {{--@if($task->half_price == 1)--}}
                {{--<div class="show-edit">--}}
                    {{--<div class="edit-html">--}}
                        {{--<strong>Half Price:</strong> <span id="replace">Yes</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--@endif--}}
            {{--@endHasRole--}}

            {{--FOLLOWERS--}}
            @canAtLeast(['task.update'])
            <div class="show-edit">
                <div class="edit-html">
                    <strong>Followers:</strong> <span id="followers-list">{{ implode(', ', $task->followers->pluck('user.first_name')->toArray()) }}</span>
                    <i class="fa fa-pencil task-edit-details-modal" data-field="native" title="Edit"></i>
                    <!-- Modal -->
                    <div class="modal fade modal-center" id="followers-modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Task #{{$task->id}} Followers:</h4>
                                </div>
                                <div class="modal-body">
                                    {!! Form::open(['class' => 'inline', 'method' => 'POST', 'action' => ['TaskController@updateField', $task->id]]) !!}
                                    {!! Form::select('followers[]', $projectParticipants, $task->followers->pluck('user.id')->toArray(), ['class' => 'form-control sel-status select2-multiple', 'id' => 'follower_id', 'multiple' => true]) !!}
                                    {!! Form::close() !!}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger submit-modal-form" data-action-type="followers">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @endCanAtLeast

            {{--ATTACHMENTS--}}
            @hasRole(['master', 'administrator', 'css', 'recruiter', 'client', 'tds', 'assessor'])
            <div class="show-edit">
                <div class="edit-html attachments">
                    <strong>Attachments:</strong> {!! Form::file('attachment', [ 'data-task-id' => $task->id, 'id' => 'attachment', 'placeholder' => 'Attachment']) !!}
                    <br>
                    @foreach( $task->attachments() as $attachment)
                    <div class="attachment-wrapper">
                        <a target="_blank" download href="{{$attachment->url}}"><span class="fa fa-file-o"></span> {{$attachment->filename}}</a>
                        @hasRole(['master', 'administrator', 'css','client'])
                        <i title="Remove attachment" data-attachment-id="{{$attachment->id}}" class="fa fa-remove task-delete-attachment"></i>
                        @endHasRole
                    </div>
                    @endforeach
                </div>
            </div>
            @endHasRole

        </div>
        @canAtLeast(['task.refuse'])
        @if($task->canRefuse() && $task->project->type->id == 1)
            <button class="btn btn-danger col-xs-12" id="task-refuse">
                <i class="fa fa-times"></i>
                Refuse Task
            </button>
        @endif
        @endCanAtLeast
    </div>
</div>