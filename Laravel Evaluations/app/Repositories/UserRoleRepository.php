<?php

namespace App\Repositories;


use App\Models\Role;
use App\Models\UserRole;

class UserRoleRepository implements UserRoleInterface
{

    /**
     * @var $model
     */
    private $model;

    /**
     * UserRepository constructor.
     *
     * @param \App\Models\UserRole $model
     */

    public function __construct(UserRole $model)
    {
        $this->model = $model;
    }


    function getAll()
    {
        return $this->model->all();
    }

    function getById($id)
    {
        return $this->model->find($id);
    }

    function create(array $attributes)
    {
        $userRole = new UserRole();
        $userRole->role_id = $attributes['role_id'];
        $userRole->user_id = $attributes['user_id'];
        return $userRole->save();
    }

    function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    function getAssessorUsers()
    {
        return $this->model->query()->where('role_id', Role::ROLE_ASSESSOR)->with('user')->get()->pluck('user');
    }

}