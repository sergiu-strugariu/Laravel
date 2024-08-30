<?php
/**
 * Repository to handle data flow for TaskStatus model
 */

namespace App\Repositories;

use App\Models\TaskStatus;


class TaskStatusRepository implements TaskStatusInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * TaskStatusRepository constructor.
     *
     * @param \App\Models\TaskStatus $model
     */

    public function __construct(TaskStatus $model)
    {
        $this->model = $model;
    }

    /**
     * Get all task statuses.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get task status by id.
     *
     * @param integer $id
     *
     * @return \App\Models\TaskStatus
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new task status.
     *
     * @param array $attributes
     *
     * @return \App\Models\TaskStatus
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a task status.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\TaskStatus
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an task status.
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