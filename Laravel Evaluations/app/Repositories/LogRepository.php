<?php

namespace App\Repositories;


use App\Models\Log;

class LogRepository implements LogInterface
{

    private $model;


    /**
     * LogRepository constructor.
     * @param Log $model
     */
    public function __construct(Log $model)
    {
        $this->model = $model;
    }

    /**
     * Get all logs
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get log by id
     *
     * @param $id
     * @return mixed
     */
    function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create new log
     *
     * @param array $attributes
     * @return mixed
     */
    function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update specified log
     *
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete specified log
     *
     * @param $id
     * @return mixed
     */
    function delete($id)
    {
        return $this->model->find($id)->delete();
    }
}