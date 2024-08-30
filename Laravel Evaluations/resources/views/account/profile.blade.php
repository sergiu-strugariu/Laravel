@if(Auth()->user() && Auth()->user()->verified != 0)
    <?php $layout = 'layouts.app' ?>
@else
    <?php $layout = 'layouts.minimal' ?>
@endif


@extends($layout)

@section('content')
    <div class="container">
        <div class="row">
            <div class="container-login">
                <div class="col-sm-10 col-sm-offset-1 no-padding">
                    <div class="col-md-6 col-md-push-3 col-sm-6 col-sm-push-2 profile-complete">
                        <div class="logo-img-login">
                            <img src="../assets/img/logo-200x65.png" alt="logo-img" class="logo-img">
                        </div>
                        <div class="shadow-top-red"></div>
                        <div class="panel panel-default">
                            @if(Auth()->user()->verified == 0)
                                <div class="panel-heading">Please complete your profile to continue</div>
                            @endif

                            <div class="panel-body">
                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form class="form-horizontal" id="form">
                                    {{ csrf_field() }}


                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="password">New password</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                                    <input style="display:none">
                                                    <input type="password" class="form-control fly-placeholder" name='password'
                                                           id="password"
                                                           value=""
                                                           autocomplete="off"
                                                           minlength="6" {{Auth()->user()->verified == 0 ? "required" : "" }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="password_confirmation">Repeat password</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                                    <input type="password" class="form-control fly-placeholder"
                                                           name='password_confirmation'
                                                           id="password_confirmation" value=""
                                                           minlength="6"{{Auth()->user()->verified == 0 ? "required" : "" }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group{{ empty($user->first_name) ? '' : ' focused' }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="first_name">First Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user-o"></i>
                                                    </span>
                                                    <input type="text" class="form-control fly-placeholder" name='first_name'
                                                           id="first_name"
                                                           value="{{ $user->first_name}}"
                                                           required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group{{ empty($user->last_name) ? '' : ' focused' }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="last_name">Last Name</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </span>
                                                    <input type="text" class="form-control fly-placeholder" name='last_name'
                                                           id="last_name"
                                                           value="{{ $user->last_name}}"
                                                           required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}{{ empty($user->phone) ? '' : ' focused' }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="email">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                                    <input type="email" class="form-control fly-placeholder" name="email" id="email"
                                                           value="{{$user->email}}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group{{ empty($user->phone) ? '' : ' focused' }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="phone">Phone</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-phone"></i>
                                                    </span>
                                                    <input type="phone" class="form-control fly-placeholder" name="phone" id="phone"
                                                           value="{{$user->phone}}" pattern="[0-9]{10}"
                                                           required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <p>Email notifications</p>
                                                @foreach($notifications as $_slug => $_name)
                                                    <div class="checkbox" style="padding: 5px 0; margin: 0">
                                                        <label>
                                                            <input type="checkbox" value="1"
                                                                   name="notifications[{{$_slug}}]"
                                                                   style="margin-left: -20px;"
                                                                   @if(!in_array($_slug, $user->notifications)) checked @endif
                                                            /> {{$_name}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <button type="submit" class="btn btn-danger update_profile">
                                            Save
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
