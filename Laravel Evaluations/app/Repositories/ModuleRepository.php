<?php

namespace App\Repositories;


use App\Models\Module;

class ModuleRepository implements ModuleInterface
{

    private $model;

    /**
     * ModuleRepository constructor.
     *
     * @param \App\Models\Module $model
     */

    public function __construct(Module $model)
    {
        $this->model = $model;
    }

    /**
     * Get all modules.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get module by id.
     *
     * @param integer $id
     *
     * @return \App\Models\Module
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new module.
     *
     * @param array $attributes
     *
     * @return \App\Models\Module
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update an module.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\Module
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an module.
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