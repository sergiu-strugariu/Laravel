{!! Form::open(['id' => 'datatable-form']) !!}
<div class="form-group">
    {!! Form::label('name', 'Name') !!}
    {!! Form::input('text', 'name', isset($taskStatus) ? $taskStatus->name : null, ['class' => 'form-control sel-status', 'id' => 'name', 'placeholder' => 'Name']) !!}
</div>
<div class="form-group">
    {!! Form::label('color', 'Color') !!}
    {!! Form::input('text', 'color', isset($taskStatus) ? $taskStatus->color : null, ['class' => 'form-control sel-status', 'id' => 'color', 'placeholder' => 'Color']) !!}
</div>
{!! Form::close() !!}