{!! Form::open(['id' => 'datatable-form']) !!}
{!! Form::label('first_name', 'First name', array('class' => 'awesome')); !!}
{!! Form::text('first_name', isset($user) ? $user->first_name : null, array('class' => 'form-control')); !!}
{!! Form::label('last_name', 'Last name', array('class' => 'awesome')); !!}
{!! Form::text('last_name', isset($user) ? $user->last_name : null, array('class' => 'form-control')); !!}
{!! Form::label('email', 'Email', array('class' => 'awesome')); !!}
{!! Form::text('email', isset($user) ? $user->email : null, array('class' => 'form-control')); !!}
@if($user->hasRole('tds') || ($user->hasRole('client') && $user->parent_id != null))
    {!! Form::label('project_participating_id', 'Project Participating') !!}
    <select name="project_participating_id[]" id="project_participating_id" class="form-control client_user"
            data-id="{{$user->id}}" multiple>
    </select>
@endif
@if(count($user->projectsParticipating) > 0)
    <input type="hidden" name="isParticipant" id="isParticipant" value="1"/>
@endif

<input type="hidden" name="parent_id" id="parent_id" value="{{$user->parent_id}}"/>
{!! Form::close() !!}
