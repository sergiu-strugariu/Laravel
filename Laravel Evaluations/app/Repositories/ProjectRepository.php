<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/4/2017
 * Time: 9:14 AM
 */

namespace App\Repositories;


use App\Models\Project;

class ProjectRepository implements ProjectInterface
{

    /**
     * @var $model
     */
    private $model;

    /**
     * ProjectRepository constructor.
     *
     * @param \App\Models\Project $model
     */

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * Get all projects
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    function getAll()
    {
        return $this->model->all();
    }


    /**
     * Get project by id
     *
     * @param $id
     * @return mixed
     */
    function getById($id)
    {
        return $this->model->with(
            'user', 'tasks', 'participants'
        )->find($id);
    }

    /**
     * Get just the project by id
     *
     * @param $id
     * @return mixed
     */
    function getProjectById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create project
     *
     * @param array $attributes
     * @return mixed
     */
    function create(array $attributes)
    {
        return $this->model->create($attributes);
    }


    /**
     * Update project
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
     * Delete project
     *
     * @param $id
     * @return mixed
     */
    function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    function getByClient($client_id)
    {
        return $this->model->where('client_id', $client_id)->get();
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

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        if (Auth()->user()->hasRole(['master', 'administrator'])) {
            return $query->with([
                'participants',
                'participants.user',
                'user',
            ]);
        }

        if (Auth()->user()->hasRole(['client'])) {
            return $query->with([
                'participants',
                'participants.user',
                'user',
            ])->where('user_id', Auth()->user()->id)
                ->orWhereHas('participants', function ($query) {
                    $query->where('user_id', '=', Auth()->user()->id);
                });
        }

        if (Auth()->user()->hasOnlyRole(['assessor'])) {
            return $query->with([
                'participants',
                'participants.user',
                'user',
            ])->whereHas('tasks', function ($query) {
                $query->where('assessor_id', '=', Auth()->user()->id);
            });
        }


        if (Auth()->user()->hasRole(['css', 'tds'])) {
            $query->with('participants.user', 'user')->whereHas('participants', function ($query) {
                $query->where('user_id', '=', Auth()->user()->id);
            });
            $query->with(['tasks' => function ($q){
                $q->whereHas('followers', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                });
            }]);


            if (Auth()->user()->hasRole(['assessor'])) {
                $query->orWhereHas('tasks', function ($query) {
                    $query->where('assessor_id', '=', Auth()->user()->id);
                });
            }

        }

        return $query;
    }
}