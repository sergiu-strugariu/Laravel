@extends('layouts.app')

@section('content')
    <div class="container create-manual">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Create user</div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form id="form-create-manual">

                            <div class="form-group">
                                <label for="email">E-mail:</label>
                                <input type="email" class="form-control" name='email' id="email" value=""
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group client_parent">
                                <label for="parent_id">Client</label>
                                <select class="form-control" id="client_id" name="" required>
                                    @foreach($clients as $client)
                                        <option value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                </select>

                            </div>

                            <input type="submit" class="btn btn-default create_user_manually" value="Create"/>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
