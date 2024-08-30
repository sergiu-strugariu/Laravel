<?php
/**
 * Repository to handle data flow for TaskFollower model
 */

namespace App\Repositories;

use App\Models\Project;
use App\Models\TaskFollower;


class TaskFollowersRepository implements TaskFollowersInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * TaskFollowersRepository constructor.
     *
     * @param \App\Models\TaskFollower $model
     */

    public function __construct(TaskFollower $model)
    {
        $this->model = $model;
    }

    /**
     * Get all Task Followers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get Task Followers by id.
     *
     * @param integer $id
     *
     * @return \App\Models\TaskFollower
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get Task Follower by user and task.
     *
     * @param array $attributes
     *
     * @return \App\Models\TaskFollower
     */
    function getByUserAndTask(array $attributes)
    {

        return $this->model->newQuery()->where(['task_id' => $attributes['task_id'], 'user_id' => $attributes['user_id']])->first();
    }

    /**
     * Create a new Task Follower.
     *
     * @param array $attributes
     *
     * @return \App\Models\TaskFollower
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a Task Follower.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\TaskFollower
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an Task Follower.
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
     * Create a new follower or skip if exists.
     *
     * @param array $attributes
     *
     * @return \App\Models\TaskFollower
     */
    function createOrSkip(array $attributes)
    {
        $follower = $this->model->newQuery()->where(['task_id' => $attributes['task_id'], 'user_id' => $attributes['user_id']])->first();

        if (empty($follower)) {
            if (isset($attributes['emailService'])) {
                $attributes['emailService']->sendEmail($attributes['emailAttributes'], 'default');
            }
            return $this->create([
                'user_id' => $attributes['user_id'],
                'task_id' => $attributes['task_id']
            ]);
        } else {
            return $follower;
        }
    }


    /**
     * Delete a follower from task.
     *
     * @param array $attributes
     *
     * @return boolean
     */
    function deleteFollowerFromTask(array $attributes)
    {
        $follower = $this->model->newQuery()->where(['task_id' => $attributes['task_id'], 'user_id' => $attributes['user_id']])->first();

        return $follower->delete();
    }
}