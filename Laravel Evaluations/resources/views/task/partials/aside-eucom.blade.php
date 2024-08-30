<aside id="email_manager_modal" class="control-sidebar control-sidebar-edit">
    <div class="loading">
        <div class="loading-wheel"></div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
            @hasRole('client')
            Email Eucom Manager
            @endHasRole
            @hasRole('assessor')
            Email Eucom Manager
            @endHasRole
        </div>
        <div class="panel-body">
            {!! Form::open(['id' => 'email_manager_form', 'files' => true]) !!}
            {!! Form::hidden('task_id', $task->id, ['id' => 'task_id']) !!}
            <div class="form-group">
                {!! Form::label('subject', 'Subject') !!}
                {!! Form::input('text', 'subject', 'Task#'.$task->id, ['class' => 'form-control sel-status', 'id' => 'subject', 'placeholder' => 'Subject', 'required' => true]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('body', 'Body') !!}
                {!! Form::textarea('body', null, ['class' => 'form-control sel-status', 'id' => 'body', null, 'required' => true]) !!}
            </div>
            <input type="hidden" name="role"
                   value="@hasRole('client') client @endHasRole @hasRole('assessor') assessor @endHasRole"/>
            <div class="form-group">
                {!! Form::submit('Send', ['class' => 'btn btn-danger', 'id' => 'send_email_manager']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</aside>