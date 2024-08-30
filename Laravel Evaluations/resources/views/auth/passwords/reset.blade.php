@extends('layouts.minimal')

@section('content')
    <div class="container">
        <div class="row">
            <div class="container-login">
                <div class="col-md-4 col-md-push-4 col-sm-6 col-sm-push-3">
                    <div class="profile-complete">
                        <div class="logo-img-login">
                            <img src="/assets/img/logo-200x65.png" alt="logo-img" class="logo-img">
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Reset Password</div>

                            <div class="panel-body">
                                <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                                    {{ csrf_field() }}

                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <div class="col-sm-6">
                                            <label for="email">E-Mail Address</label>
                                            <input type="email" class="form-control" name='email' id="email"
                                                   value="{{ request()->old('email') }}" placeholder="E-Mail Address" required autofocus>
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <div class="col-sm-6">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" name='password' id="password"
                                                   value="" placeholder="Password" required>

                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <div class="col-sm-6">
                                            <label for="password-confirm">Confirm Password</label>
                                            <input type="password" class="form-control" name='password_confirmation'
                                                   id="password-confirm"
                                                   value="" placeholder="Password confirm" required>

                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <div class="forgot-password-submit">
                                                <button type="submit" class="btn btn-primary xxupdate_profile">
                                                    Reset Password
                                                </button>
                                            </div>
                                        </div>
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
