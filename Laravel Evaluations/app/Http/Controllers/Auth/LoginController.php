<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Yajra\Acl\Traits;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserInterface;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->middleware('guest')->except('logout');
        $this->userRepository = $userRepository;
    }

    protected function authenticated()
    {
        if (Auth::user()->verified == 0) {
            $this->redirectTo = '/user/profile';
        } else if (Auth::user()->hasRole(['assessor', 'client'])) {
            $this->redirectTo = '/tasks?all=true';
        }

        return redirect($this->redirectTo);
    }
}
