<?php

namespace App\Repositories;

use App\Models\LanguagePaperTypes;
use Illuminate\Support\Facades\Hash;


class LanguagePaperTypeRepository implements LanguagePaperTypeInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * UserRepository constructor.
     *
     * @param \App\Models\LanguagePaperTypes $model
     */

    public function __construct(LanguagePaperTypes $model)
    {
        $this->model = $model;
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get user by id.
     *
     * @param integer $id
     *
     * @return \App\Models\User
     */
    public function getById($id)
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * Create a new question.
     *
     * @param array $attributes
     *
     * @return \App\Models\User
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a question.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\Question
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete a question.
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
     * Untrash the question with specified id
     *
     * @param $id
     * @return mixed
     */
    function untrash($id)
    {
        return $this->model->withTrashed()->find($id)->restore();
    }

}