<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/21/2017
 * Time: 12:26 PM
 */

namespace App\Repositories;


use App\Models\PaperAnswers;

class PaperAnswerRepository implements PaperAnswerInterface
{

    /**
     * @var $model
     */
    private $model;

    /**
     * PaperAnswerRepository constructor.
     * @param PaperAnswers $model
     */
    public function __construct(PaperAnswers $model)
    {
        $this->model = $model;
    }

    /**
     * Get all papers answers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get paper answer by id.
     *
     * @param integer $id
     *
     * @return \App\Models\PaperAnswers
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new paper answer.
     *
     * @param array $attributes
     *
     * @return \App\Models\PaperAnswers
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a paper answer.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\PaperAnswers
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an paper answer.
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