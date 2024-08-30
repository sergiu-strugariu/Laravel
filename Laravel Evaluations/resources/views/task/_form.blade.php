{!! Form::open(['id' => 'datatable-form']) !!}
{!! Form::hidden('project_id', $project->id) !!}
<div class="form-group">
    {!! Form::label('name', 'Name') !!}
    {!! Form::input('text', 'name', isset($task) ? $task->name : null, ['class' => 'form-control sel-status', 'id' => 'name', 'placeholder' => 'Name']) !!}
</div>
<div class="form-group">
    {!! Form::label('email', 'E-mail') !!}
    {!! Form::input('text', 'email', isset($task) ? $task->email : null, ['class' => 'form-control sel-status', 'id' => 'email', 'placeholder' => 'E-mail']) !!}
</div>
<div class="form-group">
    {!! Form::label('phone', 'Phone') !!}
    {!! Form::input('text', 'phone', isset($task) ? $task->phone : null, ['class' => 'form-control sel-status', 'id' => 'phone', 'placeholder' => 'Phone']) !!}
</div>
<div class="form-group">
    {!! Form::label('languages', 'Language') !!}
    {!! Form::select('languages[]', $languages, isset($task) ? $task->language_id : null, ['class' => 'form-control sel-status select2-multiple', 'id' => 'language_id', 'multiple' => isset($task) ? false : true]) !!}
</div>
@if(isset($task))
    <div class="form-group">
        {!! Form::label('assessor_id', 'Assessor') !!}
        {!! Form::select('assessor_id', $assessors, isset($task) ? $task->assessor_id : null, ['class' => 'form-control sel-status select2-single', 'id' => 'assessor_id']) !!}
    </div>
@endif
<div class="form-group">
    {!! Form::label('mark', 'Mark') !!}
    {!! Form::input('text', 'mark', isset($task) ? $task->mark : null, ['class' => 'form-control sel-status', 'id' => 'mark', 'placeholder' => 'Mark']) !!}
</div>
<div class="form-group">
    {!! Form::label('department', 'Department') !!}
    {!! Form::input('text', 'department', isset($task) ? $task->department : null, ['class' => 'form-control sel-status', 'id' => 'department', 'placeholder' => 'Department']) !!}
</div>

<div class="form-group">
    {!! Form::label('deadline', 'Deadline') !!}
    <div class="input-group date">
        {!! Form::input('text', 'deadline', (isset($task) && !empty($task->deadline)) ? date("m/d/Y h:i a" ,strtotime($task->deadline)) : null, ['class' => 'form-control sel-status', 'id' => 'deadline', 'placeholder' => 'Deadline']) !!}
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>
</div>

{!! Form::label('availability', 'Availability') !!}
<div class="row">
    <div class='col-md-6'>
        <div class="form-group">
            <div class='input-group date'>
                {!! Form::input('text', 'availability_from', (isset($task) && !empty($task->availability_from)) ? date("m/d/Y h:i a" ,strtotime($task->availability_from)) : null, ['class' => 'form-control sel-status', 'id' => 'availability_from', 'placeholder' => 'Availability From']) !!}
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-md-6'>
        <div class="form-group">
            <div class='input-group date'>
                {!! Form::input('text', 'availability_to', (isset($task) && !empty($task->availability_to)) ? date("m/d/Y h:i a" ,strtotime($task->availability_to)) : null, ['class' => 'form-control sel-status', 'id' => 'availability_to', 'placeholder' => 'Availability To']) !!}
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        @foreach($paperTypes as $key => $paperType)
            <div class="col-sm-6 text-left">
                {!! Form::checkbox('PaperTypes[' . $key . ']', true, (isset($task) && isset($papers[$key])) ? true : false) !!}
                {!! Form::label($paperType, $paperType) !!}
            </div>
        @endforeach
    </div>
</div>
{!! Form::close() !!}