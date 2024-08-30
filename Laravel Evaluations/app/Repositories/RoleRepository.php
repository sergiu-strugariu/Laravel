<?php

namespace App\Repositories;


use App\Models\Role;

class RoleRepository implements RoleInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * RoleRepository constructor.
     *
     * @param \App\Models\Role $model
     */

    public function __construct(Role $model)
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
        return $this->model->create($attributes);
    }

    function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    function delete($id)
    {
        return $this->model->find($id)->delete();
    }
}