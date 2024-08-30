<?php

namespace App\Repositories;

use App\Models\MailTemplate;
use Illuminate\Support\Facades\Hash;


class MailsRepository implements MailsInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * UserRepository constructor.
     *
     * @param \App\Models\MailTemplate $model
     */

    public function __construct(MailTemplate $model)
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
     * @return \App\Models\MailTemplate
     */
    public function getById($id)
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * Get user by id.
     *
     * @param string $slug
     *
     * @return \App\Models\MailTemplate
     */
    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Create new item.
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
     * Update item by id.
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
     * Delete item by id.
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
     *  Get collection by filters
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

                case 'search':

                    $query->where(function($q) use ($value) {
                        $q->where('body', 'LIKE', '%' . $value . '%');
                        $q->orWhere('subject', 'LIKE', '%' . $value . '%');
                        $q->orWhere('slug', 'LIKE', '%' . $value . '%');
                        $q->orWhere('name', 'LIKE', '%' . $value . '%');
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
     * Untrash the item with specified id
     *
     * @param $id
     * @return mixed
     */
    function untrash($id)
    {
        return $this->model->withTrashed()->find($id)->restore();
    }

}