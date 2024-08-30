<?php
/**
 * Repository to handle data flow for Reference model
 */

namespace App\Repositories;

use App\Models\Reference;


class ReferenceRepository implements ReferenceInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * ReferenceRepository constructor.
     *
     * @param \App\Models\Reference $model
     */

    public function __construct(Reference $model)
    {
        $this->model = $model;
    }

    /**
     * Get all references.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get reference by id.
     *
     * @param integer $id
     *
     * @return \App\Models\Reference
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new reference.
     *
     * @param array $attributes
     *
     * @return \App\Models\Reference
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a reference.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\Reference
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an reference.
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
     * @param array $filters
     *
     * @return mixed
     */
    function search($filters)
    {
        $query = $this->model->query();

        if(is_array($filters)){
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, '=', $value);
                }
            }
        }

        return $query;
    }

}