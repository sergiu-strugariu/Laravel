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

                        <form id="form" enctype="multipart/form-data">

                            <div class="form-group">
                                <label for="file">Upload excel file</label>
                                <input type="file" class="form-control" name="file" id="file" required />
                            </div>


                            <input type="submit" class="btn btn-default create_user_automatically" value="Create"/>

                            <a download href="{{ url('UsersListing.xlsx') }}" class="btn btn-success">Export XLS Template</a>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
