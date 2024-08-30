<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\EmailService;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;


class UserRepository implements UserInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * UserRepository constructor.
     *
     * @param \App\Models\User $model
     */

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get user by id.
     *
     * @param integer $id
     *
     * @return \App\Models\User
     */
    public function getById($id)
    {
        return $this->model->with('roles', 'projects', 'projectsParticipating.project')->where('id', $id)->first();
    }

    /**
     * Create a new user.
     *
     * @param array $attributes
     *
     * @return \App\Models\User
     */
    public function create(array $attributes)
    {
        $user = $this->model->withTrashed()->where('email', $attributes['email'])->first();
        if ($user) {
            return null;
        }

        $attributes['password'] = Hash::make($attributes['password']);
        return $this->model->create($attributes);
    }

    /**
     * Update an user.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\User
     */
    public function update($id, array $attributes)
    {
        if (isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }
        $attributes['verified'] = 1;
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an user.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Generate a random password
     *
     * @return string
     */
    public function generateRandomPassword()
    {
        $password = str_random(8);
        return $password;
    }

    /**
     * Get users by role
     *
     * @param $roleSlug
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function getByRole($roleSlug, $id = null)
    {
        return $this->model->whereHas('roles', function ($query) use ($roleSlug) {
            $query->where('slug', $roleSlug);
        })->orderBy('first_name', 'asc')->where('id', '<>', $id)->get()->pluck('full_name', 'id');
    }

    /**
     * Get user by role and parent id null
     *
     * @param $roleSlug
     * @return mixed
     */
    function getByRoleAndParentIdNull($roleSlug)
    {
        return $this->model->whereHas('roles', function ($query) use ($roleSlug) {
            $query->where('slug', $roleSlug);
        })->orderBy('first_name', 'asc')->whereNull('client_id')->get()->pluck('first_name', 'id');
    }


    /**
     *  Get user collection by filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function searchUsers($filters = [])
    {
        $query = $this->model->query();

        foreach ($filters as $key => $value) {

            if (empty($value) && $key != 'status') {
                continue;
            }

            switch ( $key ){
                case 'status':

                    if ($value == '') { //all
                        $query->withTrashed();
                    } elseif ($value == 1) { //active only
                        $query->whereNull('deleted_at');
                    } else { //inactive
                        $query->whereNotNull('deleted_at')->withTrashed();
                    }

                    break;
                case 'role':
                    $query->whereHas('roles', function($q) use ( $value ) {
                        $q->where('roles.id', $value);
                    });
                    break;
                default :
                    $query->where($key, 'LIKE', '%' . $value . '%');
                    break;
            }
        }

        return $query;
    }

    /**
     * Get all active assessors
     *
     * @return mixed
     */
    function getAllAssessors()
    {
        return $this->model->whereHas('roles', function ($query) {
            $query->where('slug', 'assessor');
        })->orderBy('first_name', 'asc')->get()->pluck('full_name_email', 'id');
    }

    /**
     * Get only clients with parent id null
     *
     * @return mixed
     */
    function getClients()
    {
        return $this->model->whereHas('roles', function ($query) {
            $query->where('slug', 'client');
        })->orderBy('first_name', 'asc')->whereNull('client_id')->get()->pluck('first_name', 'id');
    }

    /**
     * Get users without specified role
     *
     * @param $roleSlug
     * @return mixed
     */
    function getWithoutRole($roleSlug)
    {
        return $this->model->whereHas('roles', function ($query) use ($roleSlug) {
            $query->where('slug', '<>', $roleSlug);
        })->orderBy('email', 'asc')->get()->pluck('email', 'id');
    }

    /**
     * Get users by parentId
     *
     * @param $parentId
     * @return mixed
     */
    function getByParentId($parentId)
    {
        return $this->model->where('client_id', $parentId)->with('projectsParticipating')->get();
    }

    /**
     * Untrash the user with specified id
     *
     * @param $id
     * @return mixed
     */
    function untrash($id)
    {
        return $this->model->withTrashed()->find($id)->restore();
    }

    /**
     * Get users by role id
     *
     * @param $roleSlug
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function getUserByRoleId($id)
    {
        return $this->model->whereHas('roles', function ($query) use ($id) {
            $query->where('roles.id', $id);
        })->get();
    }


    /**
     *  Sets a user's disabled_from and disabled_to attributes
     *
     * @param $userId
     * @param $params
     * @return bool
     */
    public function addUserTemporaryDisabled($userId, $params)
    {

        $user = $this->model->find($userId);

        if (!$user) {
            return false;
        }

        $disabled_from = isset($params['disabled_from']) ? $params['disabled_from'] : date('Y-m-d H:i:s');
        $disabled_to = isset($params['disabled_to']) ? $params['disabled_to'] : date('Y-m-d H:i:s');

        $user->disabled_from = Carbon::parse($disabled_from)->format('Y-m-d H:i:s');
        $user->disabled_to = Carbon::parse($disabled_to)->format('Y-m-d H:i:s');

        if (!$user->save() && $user->isDirty()) {
            return false;
        }
        return true;
    }


    /**
     * Removes user's temporary disabled attributes
     *
     * @param $userId
     * @return bool
     */
    public function removeUserTemporaryDisabled($userId)
    {

        $user = $this->model->find($userId);

        if (!$user) {
            return false;
        }

        $user->disabled_from = null;
        $user->disabled_to = null;

        if (!$user->save() && $user->isDirty()) {
            return false;
        }
        return true;
    }

    /**
     * Send mail notifications to all admin accounts
     *
     * @param EmailService $emailService
     * @param Task $task
     * @param Task $verifyTask
     * @param array $params
     * @return bool
     */
    public function sendTaskMailToAdmins(EmailService $emailService, Task $task, Task $verifyTask, $params)
    {
        try {
            $administrators = $this->getUserByRoleId(Role::ROLE_ADMINISTRATOR);
            foreach ($administrators as $administrator) {
                $emailService->sendEmail([
                    'template' => config('mail.administrator'),
                    'name' => $task->name,
                    'testTaker' => $params['email'],
                    'email' => $administrator->email,
                    'verifyTask' => $verifyTask,
                    'task' => $task
                ], 'administrator');
            }
            $masters = $this->getUserByRoleId(Role::ROLE_MASTER);
            foreach ($masters as $master) {
                $emailService->sendEmail([
                    'template' => config('mail.administrator'),
                    'name' => $task->name,
                    'testTaker' => $params['email'],
                    'email' => $master->email,
                    'verifyTask' => $verifyTask,
                    'task' => $task
                ], 'administrator');
            }
        } catch (RequestException $e) {
            addLog([
                'type' => MAIL_ERROR,
                'task_id' => $task->id,
                'description' => "Something went wrong with the server!"
            ]);
        }

        return true;
    }

    /**
     * Send mail notifications to all admin accounts
     *
     * @param EmailService $emailService
     * @param Task $task
     * @param array $params
     * @return bool
     */
    public function sendTaskGroupEmptyMailToAdmins(EmailService $emailService, Task $task, $params = [])
    {
        try {
            $administrators = $this->getUserByRoleId(Role::ROLE_ADMINISTRATOR);
            foreach ($administrators as $administrator) {
                $emailService->sendEmail_old([
                    'template' => config('mail.task_empty_group'),
                    'name' => $task->name,
                    'testTaker' => $task->email,
                    'email' => $administrator->email,
                    'user' => $administrator,
                    'task' => $task
                ]);
            }
            $masters = $this->getUserByRoleId(Role::ROLE_MASTER);

            foreach ($masters as $master) {
                $emailService->sendEmail_old([
                    'template' => config('mail.task_empty_group'),
                    'name' => $task->name,
                    'testTaker' => $task->email,
                    'email' => $master->email,
                    'user' => $master,
                    'task' => $task
                ]);
            }

        } catch (RequestException $e) {
            addLog([
                'type' => MAIL_ERROR,
                'task_id' => $task->id,
                'description' => "Something went wrong with the server!"
            ]);
        }
        return true;
    }

    /**
     * Get user by email.
     *
     * @param string $email
     *
     * @return \App\Models\User
     */
    public function getByEmail($email)
    {
        return $this->model->where(['email' => $email])->first();
    }

}