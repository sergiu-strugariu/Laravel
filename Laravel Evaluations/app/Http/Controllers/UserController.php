<?php

namespace App\Http\Controllers;

use App\Repositories\RoleInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Repositories\UserInterface;
use Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\UserRoleInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    const NOTIFICATIONS = [
        MAIL_TASK_DONE => 'Task Done',
        MAIL_CANDIDATE_UNINTERESTED => 'Candidate no longer interested',
        MAIL_CANDIDATE_REFUSED => 'Candidate refused to be assessed',
        MAIL_CANDIDATE_DIFFERENT_LANG => 'Candidate indicated that he/she should be assessed for a different language',
        MAIL_ONE_TEST_FINISH => 'One test in a task finished',
        MAIL_CANDIDATE_CANCELLED_CALL_BACK => 'Candidate was called and said he/she would call back later',
        MAIL_CANDIDATE_BUSY_SMS_SENT => 'Candidate was called several times but line was engaged each time. SMS text sent',
    ];

    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param \App\Repositories\UserInterface $userRepository
     * @param \App\Repositories\RoleInterface $roleRepository
     */
    public function __construct(UserInterface $userRepository, RoleInterface $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }


    /**
     * Get all users.
     *
     * @param null $id
     * @return mixed
     */
    public function getAllUsers($id = null)
    {
        $users = $this->userRepository->getAll();

        return $users;
    }


    /**
     * @param string $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function language($language = 'en')
    {
        if (array_key_exists($language, Config::get('languages'))) {
            Session::put('applocale', $language);
        }

        return Redirect::back();
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        $user = $this->userRepository->getById(Auth::user()->id);
        $roles = $this->roleRepository->getAll();
        $notifications = self::NOTIFICATIONS;

        return view('account.profile', compact('user', 'roles', 'notifications'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function updateProfileLogin(Request $request, Response $response)
    {
        if (Auth()->user()->verified == 0) {

            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'password' => 'required|min:6',
                'email' => 'required',
                'phone' => 'required',
            ];
        } else {

            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $phone = preg_replace("/[^0-9]/", "", str_replace('+', '00', $request->input('phone')));
        $request->merge(['phone' => $phone]);

        $user = $this->userRepository->getById(Auth()->user()->id);

        $requestNotifications = $request->get('notifications');

        if (is_null($requestNotifications)) {
            $requestNotifications = [];
        }

        $request->merge(['notifications' => array_diff(array_keys(self::NOTIFICATIONS), array_keys($requestNotifications))]);
        
        if ($request->input('password') == null) {
            $this->userRepository->update($user->id, $request->except('password'));
        } else {
            $this->userRepository->update($user->id, $request->all());
        }

        return ajaxResponse(SUCCESS);
    }

}