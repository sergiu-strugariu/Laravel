<div class="form-group">
    <div class="col-sm-12">
        {{ Form::label('first_name', 'First Name:') }}
        {{ Form::input('text', 'first_name', $user->first_name, ['class' => 'form-control', 'required' => true]) }}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        {{ Form::label('last_name', 'Last Name:') }}
        {{ Form::input('text', 'last_name', $user->last_name, ['class' => 'form-control', 'required' => true]) }}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        {{ Form::label('email', 'Email:') }}
        {{ Form::input('email', 'email', $user->email, ['class' => 'form-control', 'required' => true]) }}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        {{ Form::label('roles', 'Roles:') }}
        {{ Form::select('roles[]', $roles, $user->roles->pluck('id')->toArray(), ['class' => 'form-control select2', 'multiple' => true, 'required' => true]) }}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6">
        <input type="submit" class="btn btn-primary edit-user-button"
               value="Save User"
               data-id="{{$user->id}}">
    </div>
</div>