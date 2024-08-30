<?php
/**
 * Repository to handle data flow for PaperType model
 */

namespace App\Repositories;

use App\Models\PaperType;


class PaperTypeRepository implements PaperTypeInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * PaperTypeRepository constructor.
     *
     * @param \App\Models\PaperType $model
     */

    public function __construct(PaperType $model)
    {
        $this->model = $model;
    }

    /**
     * Get all paper types.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get paper type by id.
     *
     * @param integer $id
     *
     * @return \App\Models\PaperType
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new paper type.
     *
     * @param array $attributes
     *
     * @return \App\Models\PaperType
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a paper type.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\PaperType
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an paper type.
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
     * Get paper type by name.
     *
     * @param string $name
     *
     * @return \App\Models\PaperType
     */
    public function getByName($name)
    {
        return $this->model->where(['name' => $name])->first();
    }

}