{!! Form::open(['id' => 'datatable-form']) !!}
{!! Form::hidden('group_id', $group->id) !!}
<div class="form-group">
    {!! Form::label('user_id', 'User') !!}
    {!! Form::select('user_id', $assessors, isset($groupUser) ? $groupUser->user_id : null, ['class' => 'form-control sel-status select2-single', 'id' => 'user_id']) !!}
</div>
<div class="form-group">
    {!! Form::checkbox('native', true, isset($groupUser) ? $groupUser->native : 0) !!}
    {!! Form::label('native', 'Native') !!}
</div>
{!! Form::close() !!}