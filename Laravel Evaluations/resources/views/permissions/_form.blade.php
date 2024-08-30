{!! Form::open(['id' => 'datatable-form']) !!}
{!! Form::label('name', 'Permission name', array('class' => 'awesome')); !!}
{!! Form::text('name', isset($permission) ? $permission->name : null, array('class' => 'form-control')); !!}
{!! Form::label('slug', 'Permission slug', array('class' => 'awesome')); !!}
{!! Form::text('slug',  isset($permission) ? $permission->slug : null, array('class' => 'form-control')); !!}
{!! Form::label('module_id', 'Module', array('class' => 'awesome')); !!}
{!! Form::select('module_id', $modules, isset($module) ? $module->id : null, ['class' => 'form-control', 'module_id' => 'module_id']) !!}
{!! Form::close() !!}