<?php

namespace App\Repositories;

use \App\Models\Permission;


class PermissionRepository implements PermissionInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * UserRepository constructor.
     *
     * @param \App\Models\Permission $model
     */

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    function getAll()
    {
        $this->model->all();
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