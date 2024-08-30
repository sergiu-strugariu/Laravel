<?php
/**
 * Repository to handle data flow for UserGroup model
 */

namespace App\Repositories;

use App\Models\UserGroup;


class UserGroupRepository implements UserGroupInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * UserGroupRepository constructor.
     *
     * @param \App\Models\UserGroup $model
     */

    public function __construct(UserGroup $model)
    {
        $this->model = $model;
    }

    /**
     * Get all user groups.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get user group by id.
     *
     * @param integer $id
     *
     * @return \App\Models\UserGroup
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new user group.
     *
     * @param array $attributes
     *
     * @return \App\Models\UserGroup
     */
    public function create(array $attributes)
    {
        $userGroup = $this->model->where('user_id', $attributes['user_id'])->andWhere('group_id', $attributes['group_id'])->first();
        if ($userGroup) {
            return null;
        }

        return $this->model->create($attributes);
    }

    /**
     * Update an user group.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\UserGroup
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an user group.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

}