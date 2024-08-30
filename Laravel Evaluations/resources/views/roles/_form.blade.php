{!! Form::open(['id' => 'datatable-form']) !!}
{!! Form::label('name', 'Role name', array('class' => 'awesome')) !!}
{!! Form::text('name', isset($role) ? $role->name : null, array('class' => 'form-control')) !!}
{{--{!! Form::label('name', 'Role slug', array('class' => 'awesome')) !!}--}}
{{--{!! Form::text('slug',  isset($role) ? $role->slug : null, array('class' => 'form-control')) !!}--}}
{!! Form::close() !!}