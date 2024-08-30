<?php
/**
 * Repository to handle data flow for UserGroup model
 */

namespace App\Repositories;

use App\Models\Group;
use App\Models\UserGroup;


class GroupUserRepository implements GroupUserInterface
{
    private $model;

    /**
     * GroupUserRepository constructor.
     *
     * @param \App\Models\UserGroup $model
     */

    public function __construct(UserGroup $model)
    {
        $this->model = $model;
    }

    /**
     * Get all group users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get group user by id.
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
     * Create a new group user.
     *
     * @param array $attributes
     *
     * @return \App\Models\UserGroup
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update an group user.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\UserGroup
     */
    public function update($id, array $attributes)
    {
        if (!isset($attributes['native'])) {
            $attributes['native'] = false;
        }
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an group user.
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
     * Search in table.
     *
     * @param Group $group
     * @param array $filters
     *
     * @return mixed
     */
    function search($group, $filters)
    {
        $query = $this->model->query()->where('group_id', '=', $group->id)->with(['user' => function($q){
            $q->withTrashed();
        }, 'user.inactivities', 'group', 'group.language']);

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        return $query;
    }

    function searchALL()
    {
        $query = $this->model->query()->with(['user' => function($q){
            $q->withTrashed();
        }, 'group', 'group.language', 'user.inactivities']);

        return $query;
    }
    /**
     * Create a new group user or update if exists.
     *
     * @param array $attributes
     *
     * @return \App\Models\UserGroup
     */
    function createOrUpdate(array $attributes)
    {
        $userGroup = $this->model->where(['user_id' => $attributes['user_id'], 'group_id' => $attributes['group_id']])->first();

        if (empty($userGroup)) {
            return $this->create($attributes);
        } else {
            $this->update($userGroup->id, $attributes);
            return $userGroup;
        }
    }

    /**
     * Delete an user from group.
     *
     * @param array $attributes
     *
     * @return boolean
     */
    function deleteUserFromGroup(array $attributes)
    {
        return $this->model->where(['user_id' => $attributes['user_id'], 'group_id' => $attributes['group_id']])->first()->delete();
    }

}