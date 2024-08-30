{!! Form::open(['id' => 'datatable-form']) !!}
{!! Form::label('name', 'Project type', array('class' => 'awesome')); !!}
{!! Form::text('name', isset($projectType) ? $projectType->name : null, array('class' => 'form-control')); !!}
{!! Form::close() !!}