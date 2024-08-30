<?php

namespace App\Repositories;

use App\Models\Question;
use Illuminate\Support\Facades\Hash;


class QuestionRepository implements QuestionInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * UserRepository constructor.
     *
     * @param \App\Models\Question $model
     */

    public function __construct(Question $model)
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
        return $this->model->withTrashed()->where('id', $id)->first();
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
        return $this->model->withTrashed()->find($id)->update($attributes);
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
                case 'status':

                    if ($value == '') { //all
                        $query->withTrashed();
                    } elseif ($value == 1) { //active only
                        $query->whereNull('deleted_at');
                    } else { //inactive
                        $query->whereNotNull('deleted_at')->withTrashed();
                    }

                    break;

                case 'question_level_id':

                    $query->where($key, $value);
                  
                    break;

                case 'q_type':

                    $query->where($key, $value);

                    break;


                case 'search':

                    $query->where(function($q) use ($value) {
                        $q->where('body', 'LIKE', '%' . $value . '%');
                        $q->orWhere('code', 'LIKE', '%' . $value . '%');
                        $q->orWhere('description', 'LIKE', '%' . $value . '%');
                    });


                    break;

                default :
                    $query->where($key, 'LIKE', '%' . $value . '%');
                    break;
            }
        }

        return $query;
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