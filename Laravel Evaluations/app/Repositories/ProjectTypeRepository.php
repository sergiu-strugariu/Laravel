<?php

namespace App\Repositories;


use App\Models\ProjectTypes;

class ProjectTypeRepository implements ProjectTypeInterface
{
    private $model;

    /**
     * ProjectTypes constructor.
     *
     * @param \App\Models\ProjectTypes $model
     */

    public function __construct(ProjectTypes $model)
    {
        $this->model = $model;
    }

    /**
     * Get all project types.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get project type by id.
     *
     * @param integer $id
     *
     * @return \App\Models\ProjectTypes
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new project type.
     *
     * @param array $attributes
     *
     * @return \App\Models\ProjectTypes
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update an project type.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\ProjectTypes
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an project type.
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