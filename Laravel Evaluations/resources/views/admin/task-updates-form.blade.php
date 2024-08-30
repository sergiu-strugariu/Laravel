{!! Form::open(['id' => 'datatable-form', 'url' => '/admin/task-updates/update/' . $taskUpdate->id]) !!}
<div class="form-group">
    {!! Form::label('name', 'Text') !!}
    {!! Form::text('name', $taskUpdate->name, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('display_name', 'List Text') !!}
    {!! Form::text('display_name', $taskUpdate->display_name, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('roles', 'Roles') !!}
    {!! Form::select('roles[]', $roles, $taskUpdate->roles->pluck('id'), ['class' => 'form-control select2', 'multiple', 'required']) !!}
</div>
<div class="form-group">
    <input type="submit" class="btn btn-primary btn-save-task-update" value="Save"/>
</div>

{!! Form::close() !!}