<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/3/2018
 * Time: 5:04 PM
 */

namespace App\Repositories;


use App\Models\QuestionChoice;

class QuestionChoiceRepository implements QuestionChoiceInterface
{

    private $model;

    /**
     * ProjectTypes constructor.
     *
     * @param \App\Models\QuestionChoice $model
     */

    public function __construct(QuestionChoice $model)
    {
        $this->model = $model;
    }

    /**
     * Get all question choices.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get question choice by id.
     *
     * @param integer $id
     *
     * @return \App\Models\QuestionChoice
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new question choice.
     *
     * @param array $attributes
     *
     * @return \App\Models\QuestionChoice
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a question choice.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\QuestionChoice
     */
    public function update($id, array $attributes)
    {
        return $this->model->withTrashed()->find($id)->update($attributes);
    }

    /**
     * Delete a question choice.
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
     *  Get user collection by filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function search($filters = [])
    {
        $query = $this->model->query();

        foreach ($filters as $key => $value) {

            if (empty($value) && $key != 'status') {
                continue;
            }

            switch ( $key ){

                case 'search':
                    $query->where('answer', 'LIKE', '%' . $value . '%');
                    break;

                default :
                    $query->where($key, 'LIKE', '%' . $value . '%');
                    break;
            }
        }

        return $query->withTrashed()->orderBy('correct', 'desc');
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