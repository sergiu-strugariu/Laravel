<?php
/**
 * Repository to handle data flow for Paper model
 */

namespace App\Repositories;

use App\Models\Paper;
use App\Models\TaskStatus;


class PaperRepository implements PaperInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * PaperRepository constructor.
     *
     * @param \App\Models\Paper $model
     */

    public function __construct(Paper $model)
    {
        $this->model = $model;
    }

    /**
     * Get all papers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get paper by id.
     *
     * @param integer $id
     *
     * @return \App\Models\Paper
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new paper.
     *
     * @param array $attributes
     *
     * @return \App\Models\Paper
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a paper.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\Paper
     */
    public function update($id, array $attributes)
    {
        if (isset($attributes['done']) && $attributes['done'] == true) {
            $attributes['status_id'] = TaskStatus::STATUS_DONE;
        }
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an paper.
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
     * Get papers by tasks.
     *
     * @param integer $id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function getAllTaskPapers($id)
    {
        return $this->model->newQuery()->where('task_id', '=', $id)->with('type')->get();
    }

    /**
     * Create a new paper or skip if exists.
     *
     * @param array $attributes
     *
     * @return \App\Models\Paper
     */
    function createOrSkip(array $attributes)
    {
        $paper = $this->model->newQuery()->where(['task_id' => $attributes['task_id'], 'paper_type_id' => $attributes['paper_type_id']])->first();

        if (empty($paper)) {
            return $this->create([
                'paper_type_id' => $attributes['paper_type_id'],
                'task_id' => $attributes['task_id'],
                'cost' => $attributes['cost']
            ]);
        } else {
            return $paper;
        }
    }
    
    function cancelAllPapers($task_id)
    {
        $this->model->where('task_id', $task_id)->update(['status_id' => CANCELED]);
    }

}