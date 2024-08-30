{!! Form::open(['id' => 'datatable-form']) !!}
{!! Form::select('language_id', $languages, isset($group) ? $group->language_id : null, ['class' => 'form-control sel-status', 'id' => 'language_id']) !!}
{!! Form::close() !!}