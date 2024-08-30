<?php
/**
 * Repository to handle data flow for Task model
 */

namespace App\Repositories;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\DB;


class TaskRepository implements TaskInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * TaskRepository constructor.
     *
     * @param \App\Models\Task $model
     */

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    /**
     * Get all tasks.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get task by id.
     *
     * @param integer $id
     *
     * @return \App\Models\Task
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new task.
     *
     * @param array $attributes
     *
     * @return \App\Models\Task
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a task.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return bool
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an task.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->find($id)->update(['task_status_id' => TaskStatus::STATUS_CANCELED]);
    }


    /**
     * Search in table.
     *
     * @param $projects
     * @param array $filters
     *
     * @return mixed
     */
    function search($projects, $filters)
    {

        $mem_limit = ini_get('memory_limit');
        ini_set('memory_limit', -1);
        if (isset($projects[0])) {
            $project_ids = [];
            foreach ($projects as $project) {
                $project_ids[$project->id] = $project->id;
            }

            $query = $this->model->query()->whereIn('project_id', $project_ids)->with([
                'language',
                'language.groups',
                'language.groups.userGroups',
                'assessor',
                'addedBy',
                'status',
                'papers',
                'papers.type',
                'papers.task',
                'papers.status',
                'papers.report',
                'project'
            ]);
        } else {

            if ($projects[0] != null) {
                $project = $projects[0];
            } else {
                $project = $projects;
            }
            $query = $this->model->query()->where('project_id', '=', $project->id)->with([
                'language',
                'language.groups',
                'language.groups.userGroups',
                'assessor',
                'addedBy',
                'status',
                'papers',
                'papers.type',
                'papers.task',
                'papers.status',
                'papers.report',
                'project'
            ]);
        }

        if($filters and !empty($filters)) {
            foreach ($filters as $key => $value) {
                if (!empty($value)) {

                    switch ($key) {
                        case 'created_at':
                            $query->whereBetween('created_at',
                                [
                                    date('Y-m-d H:i:s', strtotime($value)),
                                    date('Y-m-d H:i:s', strtotime($value . ' + 1 day'))
                                ]);
                            break;
                        case 'date_range' :
                            $dates = explode(' - ', $value);
                            $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0]));
                            $dateTo = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                            break;
                        case 'date_range_tests' :
                            $dates = explode(' - ', $value);
                            $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0]));
                            $dateTo = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereHas('papers.report', function ($q) use ($dateFrom, $dateTo) {
                                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                            });
                            $query->with([
                                'papers' => function ($q) use ($dateFrom, $dateTo) {
                                    $q->whereHas('report', function ($q) use ($dateFrom, $dateTo) {
                                        $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                                    });
                                }
                            ]);
                            break;
                        case 'date_assessor_tests_range' :
                            $dates = explode(' - ', $value);
                            $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0]));
                            $dateTo = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereHas('papers', function ($q) use ($dateFrom, $dateTo) {
                                $q->whereIn('paper_type_id', [TEST_SPEAKING, TEST_WRITING]);

                                $q->whereHas('report', function ($q) use ($dateFrom, $dateTo) {
                                    $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                                });
                            });

                            break;
                        case 'language_id' :
                        case 'added_by_id' :
                        case 'assessor_id' :
                            $query->where($key, $value);
                            break;
                        case 'project_type' :
                            $query->whereHas('project', function ($q) use ($value) {
                                $q->where('project_type_id', $value);
                            });
                            break;
                        case 'status' :
                        case 'all' :
                            if ($value == 'active') {
                                $query->where(function ($q) {
                                    $q->where('task_status_id', TaskStatus::STATUS_ALLOCATED)
                                        ->orWhere('task_status_id', TaskStatus::STATUS_IN_PROGRESS);
                                });
                            } elseif ($value == 'archived') {
                                $query->where('task_status_id', TaskStatus::STATUS_ARCHIVED);
                            } else {
                                $query->where('task_status_id', '!=', TaskStatus::STATUS_ARCHIVED);
                            }
                            break;
                        case 'has_unbilled_tests':
                            if ($value) {
                                $query->where(function ($query) use ($value) {
                                    if ($value == "yes") {
                                        $query->whereHas('papers', function ($query) {
                                            $query->whereNull('invoice_id');
                                        });
                                    } else {
                                        $query->whereHas('papers', function ($query) {
                                            $query->whereNull('invoice_id');
                                        }, "=", 0);
                                    }
                                });
                            }
                            break;
                        default:
                            $query->where($key, 'LIKE', '%' . $value . '%');
                            break;
                    }
                }
            }
        }

        if (auth()->user()->hasOnlyRole('assessor')) {
            $query->where(['assessor_id' => auth()->user()->id]);
        }

        if (auth()->user()->hasRole(['client', 'css', 'tds', 'recruiter'])) {
            $query->where(function ($query) {
                $query->whereHas('project', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                })->orWhereHas('project.participants', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                })->orWhereHas('followers', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                });
                if (Auth()->user()->hasRole(['assessor'])) {
                    $query->orWhere('assessor_id', '=', Auth()->user()->id);
                }
            });
        }
        ini_set('memory_limit', $mem_limit);
        return $query;
    }

    /**
     * Search in table.
     *
     * @param array $filters
     *
     * @return mixed
     */
    function globalSearch($filters)
    {
        $query = $this->model->query()->with([
            'language',
            'language.groups',
            'language.groups.userGroups',
            'assessor',
            'addedBy',
            'status',
            'papers',
            'papers.type',
            'papers.task',
            'papers.status',
            'papers.report',
            'project',
            'project.user',
            'project.participants.user',
            'project.participants',
            'followers'
        ]);
        
        if (!is_null($filters['global_search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['global_search'] . '%');
                $q->orWhere('email', 'LIKE', '%' . $filters['global_search'] . '%');
                $q->orWhere('phone', 'LIKE', '%' . $filters['global_search'] . '%');
                $q->orWhere('id', $filters['global_search']);
            });
        }


        if($filters and !empty($filters)) {
            $isAssessor = (auth()->user() && auth()->user()->hasOnlyRole('assessor'));
            foreach ($filters as $key => $value) {
                if (!empty($value) && $key != 'global_search' && $key != 'q') {

                    switch ($key) {
                        case 'created_at':
                            $query->whereBetween('created_at',
                                [
                                    date('Y-m-d H:i:s', strtotime($value)),
                                    date('Y-m-d H:i:s', strtotime($value . ' + 1 day'))
                                ]);
                            break;
                        case 'date_range' :
                            $dates = explode(' - ', $value);
                            $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0]));
                            $dateTo = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                            break;
                        case 'date_range_tests' :
                            $dates = explode(' - ', $value);
                            $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0]));
                            $dateTo = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereHas('papers.report', function ($q) use ($dateFrom, $dateTo) {
                                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                            });
                            $query->with([
                                'papers' => function ($q) use ($dateFrom, $dateTo) {
                                    $q->whereHas('report', function ($q) use ($dateFrom, $dateTo) {
                                        $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                                    });
                                }
                            ]);
                            break;
                        case 'date_assessor_tests_range' :
                            $dates = explode(' - ', $value);
                            $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0]));
                            $dateTo = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereHas('papers', function ($q) use ($dateFrom, $dateTo) {
                                $q->whereIn('paper_type_id', [TEST_SPEAKING, TEST_WRITING]);

                                $q->whereHas('report', function ($q) use ($dateFrom, $dateTo) {
                                    $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                                });
                            });


                            break;
                        case 'language_id' :
                        case 'added_by_id' :
                        case 'assessor_id' :
                        case 'project_id' :
                            $query->where($key, $value);
                            break;
                        case 'project_type' :
                            $query->whereHas('project', function ($q) use ($value) {
                                $q->where('project_type_id', $value);
                            });
                            break;
                        case 'status' :
                        case 'all' :
                            if ($value == 'active') {
                                $query->where(function ($q) {
                                    $q->where('task_status_id', TaskStatus::STATUS_ALLOCATED)
                                        ->orWhere('task_status_id', TaskStatus::STATUS_IN_PROGRESS);
                                });
                            } elseif ($value == 'archived') {
                                $query->where('task_status_id', TaskStatus::STATUS_ARCHIVED);
                            } else {
                                $query->where('task_status_id', '!=', TaskStatus::STATUS_ARCHIVED);
                            }
                            break;
                        case 'task_status_id':
                            // Assessors will see papers as "DONE" if they have done all of their work on them
                            if ($value == TaskStatus::STATUS_DONE && $isAssessor) {
                                $query->where(function($query) {
                                    $query->whereHas('papers', function($query) {
                                        $query->where('status_id', 3);
                                        $query->whereIn('paper_type_id', [TEST_WRITING, TEST_SPEAKING]);
                                    });
                                    $query->orWhere('task_status_id', 'LIKE', '%' . TaskStatus::STATUS_DONE . '%');
                                });

                            }
                            // Simple find
                            else {
                                $query->where($key, 'LIKE', '%' . $value . '%');
                            }
                            break;
                        case 'has_unbilled_tests':
                            if ($value) {
                                $query->where(function ($query) use ($value) {
                                    if ($value == "yes") {
                                        $query->whereHas('papers', function ($query) {
                                            $query->whereNull('invoice_id');
                                        });
                                    } else {
                                        $query->whereHas('papers', function ($query) {
                                            $query->whereNull('invoice_id');
                                        }, "=", 0);
                                    }
                                });
                            }
                            break;
                        default:
                            $query->where($key, 'LIKE', '%' . $value . '%');
                            break;
                    }
                }
            }
        }

        if (auth()->user() && auth()->user()->hasOnlyRole('assessor')) {
            $query->where('assessor_id', auth()->user()->id);
            $query->whereHas('papers', function($q){
                $q->whereIn('paper_type_id', [TEST_SPEAKING, TEST_WRITING]);
                $q->where('status_id', '!=', CANCELED);
            });
        }

        if (auth()->user() && auth()->user()->hasRole(['client', 'css', 'tds', 'recruiter'])) {
            $query->where(function ($query) {
                $query->whereHas('project', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                })->orWhereHas('project.participants', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                })->orWhereHas('followers', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                });
                if (Auth()->user()->hasRole(['assessor'])) {
                    $query->orWhere('assessor_id', '=', Auth()->user()->id);
                }
            });
        }
        return $query;
    }

    /**
     * Verify if test-taker has taken a test on same language.
     *
     * @param integer $language_id
     * @param array $params
     *
     * @return mixed
     */
    function verifyTestTaker($language_id, $params)
    {

        $papers = array_keys($params['languagesExtra'][$language_id]['PaperTypes']);

        // search in same project tasks
        $task = $this->model->query()->with([
            'language',
            'language.groups',
            'language.groups.userGroups',
            'assessor',
            'addedBy',
            'status',
            'papers',
            'papers.type',
            'papers.task',
            'papers.status',
            'papers.report',
            'project'
        ])
            ->where([
                'language_id' => intval($language_id),
                'project_id' => $params['project_id'],
                'email' => trim($params['email'])
            ])
            ->whereHas('papers', function ($q) use ($papers) {
                $q->whereIn('paper_type_id', $papers);
            })
            ->whereHas('papers.report')
            ->orderBy('id', 'desc')->first();

        if (!empty($task)) {
            return [
                'other_project' => false,
                'task' => $task
            ];
        }

        // search in other project tasks
        $task = $this->model->query()->with([
            'language',
            'language.groups',
            'language.groups.userGroups',
            'assessor',
            'addedBy',
            'status',
            'papers',
            'papers.type',
            'papers.task',
            'papers.status',
            'papers.report',
            'project'
        ])
            ->where([
                'language_id' => intval($language_id),
                'email' => trim($params['email'])
            ])
            ->where('project_id', '!=', $params['project_id'])
            ->whereHas('papers', function ($q) use ($papers) {
                $q->whereIn('paper_type_id', $papers);
            })
            ->whereHas('papers.report')
            ->orderBy('id', 'desc')->first();

        if (!empty($task)) {
            return [
                'other_project' => true,
                'task' => $task
            ];
        }

        return [
            'other_project' => null,
            'task' => null
        ];
    }

    /**
     * Create a new task or update if exists.
     *
     * @param array $attributes
     *
     * @return \App\Models\Task
     */
    function createOrUpdate(array $attributes)
    {
        $task = $this->model->where(['id' => $attributes['id']])->first();

        if (empty($task)) {
            return $this->create($attributes);
        } else {
            $this->update($task->id, $attributes);
            return $task;
        }
    }

}