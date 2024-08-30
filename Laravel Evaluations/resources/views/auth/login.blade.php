@extends('layouts.minimal')

@section('content')
    <div class="container">
        <div class="row">
            <div class="container-login">
                <div class="col-md-4 col-md-push-4 col-sm-6 col-sm-push-3">
                    <div class="profile-complete">
                        <div class="logo-img-login">
                            <img src="assets/img/logo-200x65.png" alt="logo-img" class="logo-img">
                        </div>
                        <div class="shadow-top-red"></div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Login</div>

                            <div class="panel-body">
                                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                    {{ csrf_field() }}

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="email">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                                    <input id="email" type="email" class="form-control fly-placeholder"
                                                           name="email" value="{{ old('email') }}" >
                                                </div>
                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="password">Password</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-lock"></i>
                                                    </span>
                                                    <input id="password" type="password" class="form-control fly-placeholder"
                                                           name="password" value="">
                                                </div>
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="checkbox">
                                                    <input type="checkbox"
                                                           name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label>
                                                        Remember me
                                                    </label>
                                                    <div class="container-forgot-password">
                                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                                            Forgot password
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <button type="submit" class="btn btn-danger">
                                            Sign In
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
