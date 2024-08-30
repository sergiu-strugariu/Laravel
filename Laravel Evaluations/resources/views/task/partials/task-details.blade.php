<div class="panel" id="task-details">
    <div class="panel-heading">
        Task details
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="show-edit">
                @hasRole(['master', 'administrator', 'assessor', 'client', 'tds'])
                <div class="edit-html">
                    <strong>Language:</strong> <span
                            id="replace">{{ $task->language->name }}</span>
                    @canAtLeast(['task.update'])
                    <i class="fa fa-pencil task-edit-details" data-field="language_id"
                       title="Edit"></i>
                    @endCanAtLeast
                </div>
                @endHasRole
                @canAtLeast(['task.update'])
                <div class="edit-input">
                    <div class="edit-inline-form">
                        <strong>Language:</strong>
                        {!! Form::open(['class' => 'inline']) !!}
                        <div class="form-inline">
                            {!! Form::select('language_id', $languages, $task->language->id, ['class' => 'form-control', 'id' => 'edit-language', 'required' => true]) !!}
                            <div class="container-controls">
                                <i class="fa fa-check-circle-o submit-form-inline-task fa-2x"
                                   data-field="phone"
                                   title="Save"></i>
                                <i class="fa fa-times-circle-o fa-2x" data-field="phone"
                                   title="Exit"></i>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @endCanAtLeast
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="form-group">
            <div class="show-edit">
                <div class="edit-html">
                    <strong>Phone:</strong> <span id="replace" class="phone-input-task-details">{{ $task->phone }}</span>
                    @canAtLeast(['task.update'])
                    <i class="fa fa-pencil task-edit-details" data-field="phone"
                       title="Edit"></i>
                    @endCanAtLeast
                </div>
                @canAtLeast(['task.update'])
                <div class="edit-input">
                    <div class="edit-inline-form">
                        <strong>Phone:</strong>
                        {!! Form::open(['class' => 'inline']) !!}
                        <div class="form-inline">
                            {!! Form::input('number', 'phone', $task->phone, ['class' => 'form-control', 'id' => 'edit-phone', 'placeholder' => 'Task Phone', 'required' => true]) !!}
                            <div class="container-controls">
                                <i class="fa fa-check-circle-o submit-form-inline-task fa-2x"
                                   data-field="phone"
                                   title="Save"></i>
                                <i class="fa fa-times-circle-o fa-2x" data-field="phone"
                                   title="Exit"></i>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @endCanAtLeast
            </div>
        </div>

        <div class="clearfix"></div>


        <div class="form-group">
            <div class="show-edit">
                <div class="edit-html">
                    <strong>Skype:</strong> <span id="replace">{{ $task->skype }}</span>
                    @canAtLeast(['task.update'])
                    <i class="fa fa-pencil task-edit-details" data-field="skype"
                       title="Edit"></i>
                    @endCanAtLeast
                </div>
                @canAtLeast(['task.update'])
                <div class="edit-input">
                    <div class="edit-inline-form">
                        <strong>Skype:</strong>
                        {!! Form::open(['class' => 'inline']) !!}
                        <div class="form-inline">
                            {!! Form::input('text', 'skype', $task->skype, ['class' => 'form-control', 'id' => 'edit-skype', 'placeholder' => 'Task skype', 'required' => true]) !!}
                            <div class="container-controls">
                                <i class="fa fa-check-circle-o submit-form-inline-task fa-2x"
                                   data-field="phone"
                                   title="Save"></i>
                                <i class="fa fa-times-circle-o fa-2x" data-field="phone"
                                   title="Exit"></i>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @endCanAtLeast
            </div>
        </div>

        <div class="clearfix"></div>


        <div class="form-group show-edit">
            <div class="edit-html">
                <strong>Email:</strong> <span id="replace">{{ $task->email }}</span>
                @hasRole(['assessor', 'client', 'administrator', 'master', 'css'])
                <i class="fa fa-pencil task-edit-details" data-field="email" title="Edit"></i>
                @endHasRole
            </div>
            @hasRole(['assessor', 'client', 'administrator', 'master', 'css'])
            <div class="edit-input">
                <div class="edit-inline-form edit-email-input">
                    <strong>Email:</strong>
                    {!! Form::open(['class' => 'inline']) !!}
                    <div class="form-inline" style="padding-left: 5px">
                        {!! Form::input('email', 'email', $task->email, ['class' => 'form-control', 'id' => 'edit-email', 'placeholder' => 'Task Email', 'required' => true]) !!}
                        <div class="container-controls">
                            <i class="fa fa-check-circle-o submit-form-inline-task fa-2x"
                               data-field="phone"
                               title="Save"></i>
                            <i class="fa fa-times-circle-o fa-2x" data-field="phone"
                               title="Exit"></i>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            @endHasRole
        </div>

        <div class="clearfix"></div>

        @if(!empty($task->availability_from))
            <div class="form-group">
                <strong>Available on:</strong>
                {{ date('d M Y', strtotime($task->availability_from)) . ' from ' .  date('H:i', strtotime($task->availability_from)) . ' to ' .  date('H:i', strtotime($task->availability_to)) }}
            </div>
            @if(isset($calendarLinks) && count($calendarLinks))
            <div class="form-group calendar-links">
                <strong>Add to calendar:</strong>
                @foreach($calendarLinks as $event)
                    @if ($event['serviceName'] === "webOutlook")
                        <a clicktracking=off href="{{$event['serviceLink']}}">Outlook</a>
                    @else
                        <a clicktracking=off href="{{$event['serviceLink']}}">{{ucfirst($event['serviceName'])}}</a> -
                    @endif
                @endforeach
            </div>
            @endif
            <div class="clearfix"></div>

        @endif

        @if(!empty($task->extra_info))
            <div class="form-group">
                <div class="show-edit">
                    <div class="edit-html">
                        <strong>Extra Info:</strong> <span id="replace">{{ $task->extra_info }}</span>
                        @canAtLeast(['task.update'])
                        <i class="fa fa-pencil task-edit-details" data-field="extra_info"
                           title="Edit"></i>
                        @endCanAtLeast
                    </div>

                    @canAtLeast(['task.update'])
                    <div class="edit-input">
                        <div class="edit-inline-form">
                            <strong>Extra Info:</strong>
                            {!! Form::open(['class' => 'inline']) !!}
                            <div class="form-inline">
                                {!! Form::input('text', 'extra_info', $task->extra_info, ['class' => 'form-control', 'id' => 'extra-info', 'placeholder' => 'Extra Info', 'required' => true]) !!}
                                <div class="container-controls">
                                    <i class="fa fa-check-circle-o submit-form-inline-task fa-2x"
                                       data-field="extra_info"
                                       title="Save"></i>
                                    <i class="fa fa-times-circle-o fa-2x" data-field="extra_info"
                                       title="Exit"></i>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    @endCanAtLeast
                </div>
            </div>
        @endif

        <div class="form-group">
            <div class="show-edit">
                <div class="edit-html">
                    <strong>Deadline for online tests:</strong> <span id="replace">@if($task->deadline){{ date('d M Y', strtotime($task->deadline)) . ' at ' .  date('H:i', strtotime($task->deadline)) }}@endif</span>
                    @canAtLeast(['task.update'])
                    <i class="fa fa-pencil task-edit-details" data-field="deadline"
                       title="Edit"></i>
                    @endCanAtLeast
                </div>
                @canAtLeast(['task.update'])
                <div class="edit-input">
                    <div class="edit-inline-form">
                        <strong>Deadline for online tests:</strong>
                        {!! Form::open(['class' => 'inline']) !!}
                        <div class="form-inline">
                            {!! Form::input('text', 'deadline', $task->deadline, ['class' => 'form-control deadline-datepick', 'id' => 'edit-deadline', 'placeholder' => 'Task deadline', 'required' => true]) !!}
                            <div class="container-controls">
                                <i class="fa fa-check-circle-o submit-form-inline-task fa-2x"
                                   data-field="phone"
                                   title="Save"></i>
                                <i class="fa fa-times-circle-o fa-2x" data-field="deadline"
                                   title="Exit"></i>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @endCanAtLeast
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="form-group">
            Task created by {{ $task->addedBy->full_name }} on <span style="white-space: nowrap;">{{ date('d M Y \a\t H:i', strtotime($task->created_at))}}</span>
        </div>
    </div>
</div>