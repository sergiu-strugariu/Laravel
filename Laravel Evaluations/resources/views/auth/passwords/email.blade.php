@extends('layouts.minimal')

@section('content')
    <div class="container">
        <div class="row">
            <div class="container-login">
                <div class="col-md-4 col-md-push-4 col-sm-6 col-sm-push-3">
                    <div class="logo-img-login">
                        <img src="../assets/img/logo-200x65.png" alt="logo-img" class="logo-img">
                    </div>
                    <div class="shadow-top-red"></div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="{{ route('login') }}">
                                <span class="fa fa-arrow-left"></span>
                            </a>
                            Forgot password
                        </div>

                        <div class="panel-body">
                            @if (session('status'))
                                <div class="alert alert-success mail-sent">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="form-label" for="email">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-envelope"></i>
                                                </span>
                                                <input id="email" type="email" class="form-control fly-placeholder" name="email"
                                                       value="{{ old('email') }}" required>

                                            </div>
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <button type="submit" class="btn btn-danger">
                                        Reset password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
