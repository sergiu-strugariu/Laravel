<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/5/2018
 * Time: 4:17 PM
 */

namespace App\Repositories;


use App\Models\Paper;
use App\Models\PaperAnswers;
use App\Models\PaperReport;
use App\Models\TaskStatus;
use Yajra\DataTables\Facades\DataTables;

class PaperReportRepository implements PaperReportInterface
{


    /**
     * @var $model
     */
    private $model;

    /**
     * PaperReportRepository constructor.
     * @param PaperReport $model
     */
    public function __construct(PaperReport $model)
    {
        $this->model = $model;
    }

    /**
     * Get all paper reports.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->with(['paper', 'paper.paper_answers', 'paper.task', 'paper.type', 'paper.type.languagePaperType'])->get();
    }

    /**
     * Get paper report by id.
     *
     * @param integer $id
     *
     * @return \App\Models\PaperReport
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new paper report.
     *
     * @param array $attributes
     *
     * @return \App\Models\PaperReport
     */
    public function create(array $attributes)
    {
        $paperReport = $this->model->create($attributes);
        $task = $paperReport->paper->task;

        PaperAnswers::where('paper_id', $paperReport->paper->id)
            ->whereNull('report_id')
            ->update(['report_id' => $paperReport->id]);

        if ($task->papers->count() == $task->completedTests()->count()) {
            $task->task_status_id = TaskStatus::STATUS_DONE;
            $task->save();
        }

        return $paperReport;
    }

    /**
     * Update a paper report.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\PaperReport
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete a paper report.
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
     * Filter the results based on filter
     *
     * @param $filters
     * @return PaperReport|\Illuminate\Database\Eloquent\Builder
     */
    public function filterResults($filters)
    {
        $query = $this->model->query();
        foreach ($filters as $key => $value) {

            if (empty($value)) {
                continue;
            }

            switch ($key) {
                case 'language':
                    $query->whereHas('paper.task.language', function ($q) use ($value) {
                        $q->where("id", $value);
                    });
                    break;

                case 'email':
                    $query->whereHas('paper.task', function ($q) use ($value) {
                        $q->where("email", "LIKE", '%' . $value . '%');
                    });
                    break;

                case 'started_at':
                    $query->whereHas('paper', function ($q) use ($value) {
                        $q->where("started_at", ">=", date('Y-m-d H:i:s',
                            strtotime($value)));
                    });
                    break;

                case 'ended_at':
                    $query->whereHas('paper', function ($q) use ($value) {
                        $q->where("ended_at", "<=", date('Y-m-d 23:59:59',
                            strtotime($value)));
                    });
                    break;

                default :
                    $query = $this->model;
                    break;
            }
        }

        return $query;
    }

}