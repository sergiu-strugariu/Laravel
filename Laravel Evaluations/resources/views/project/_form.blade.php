{!! Form::open(['id' => 'datatable-form']) !!}
<div class="form-group">
    {!! Form::label('name', 'Project name', array('class' => 'awesome')); !!}
    {!! Form::text('name', null, array('class' => 'form-control', 'required', 'id' => 'project_name')); !!}
</div>
<div class="form-group">
    {!! Form::label('project_type_id', 'Project type', array('class' => 'awesome')); !!}
    {!! Form::select('project_type_id', $projectTypes, null, ['class' => 'form-control', 'id' => 'user_id']) !!}
</div>
<div class="form-group">
    {!! Form::label('first_name', 'Client', array('class' => 'awesome')); !!}
    <select name="user_id" class="form-control client_user">
        <option value="-1">Any Client</option>
        @foreach($clients as $key => $client)
            <option value="{{$key}}">{{$client}}</option>
        @endforeach
    </select>
</div>
<div class="form-group client_participants">
    {!! Form::label('participants_id', 'Project participants', array('class' => 'awesome')); !!}
    <select name="participants_id[]" id="participants_id" class="form-control" multiple="multiple" required>
    </select>
</div>
<div class="form-group">
    {!! Form::label('default_bill_client', 'Bill client', array('class' => 'awesome')); !!}
    {!! Form::select('default_bill_client', $projectSettings, null, ['class' => 'form-control', 'id' => 'user_id']) !!}
</div>
<div class="form-group">
    {!! Form::label('default_pay_assessor', 'Pay assessors', array('class' => 'awesome')); !!}
    {!! Form::select('default_pay_assessor', $projectSettings, null, ['class' => 'form-control', 'id' => 'user_id']) !!}
</div>
{!! Form::close() !!}
