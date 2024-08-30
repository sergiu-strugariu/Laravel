{!! Form::open(['id' => 'datatable-form']) !!}
<div class="form-group">
    {!! Form::label('name', 'Name') !!}
    {!! Form::input('text', 'name', isset($paperType) ? $paperType->name : null, ['class' => 'form-control sel-status', 'id' => 'name', 'placeholder' => 'Name']) !!}
</div>
{!! Form::close() !!}