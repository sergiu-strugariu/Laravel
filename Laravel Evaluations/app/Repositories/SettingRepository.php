<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/12/2018
 * Time: 9:55 AM
 */

namespace App\Repositories;


use App\Models\Setting;

class SettingRepository implements SettingInterface
{

    /**
     * @var $model
     */
    private $model;

    /**
     * SettingRepository constructor.
     *
     * @param \App\Models\Setting $model
     */

    public function __construct(Setting $model)
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

    function getByKey($key)
    {
        return $this->model->where('key', $key)->first();
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