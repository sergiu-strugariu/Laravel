<?php
/**
 * Repository to handle data flow for Group model
 */

namespace App\Repositories;

use App\Models\Group;
use App\Models\Task;
use App\Models\TaskAssessorHistory;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\DB;


class GroupRepository implements GroupInterface
{
    private $model;

    /**
     * GroupRepository constructor.
     *
     * @param \App\Models\Group $model
     */

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    /**
     * Get all groups.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get group by id.
     *
     * @param integer $id
     *
     * @return \App\Models\Group
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new group.
     *
     * @param array $attributes
     *
     * @return \App\Models\Group
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update an group.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\Group
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an group.
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
     * Get all assessors from language group.
     *
     * @param $language_id
     * @param int $user_id
     * @param bool $native
     * @param string $deadline
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function getAssessorsFromLanguageGroup($language_id, $user_id = 0, $native = false, $deadline = null)
    {

        $query = $this->model->newQuery()
            ->with([
                    'userGroups' => function($q) use ($native) {
                        $q->where('native', $native);
                    },
                    'userGroups.user' => function ($q) use ($deadline){
                        if ($deadline) {
                            $q->whereDoesntHave('inactivities', function($q) use ($deadline) {
                                $q->whereRaw("(? BETWEEN date_from AND date_to)", [$deadline]);
                            });

                        }
                        $q->active();
                    }
                ])
            ->where(['language_id' => $language_id])
            ->get()
            ->pluck('userGroups');


        if ($query->isNotEmpty()) {
            return $query->first()->pluck('user')
                // remove inactive users
                ->reject(function ($value, $key) {
                    return $value == null;
                })
                ->where('id', '<>', $user_id);
        }

        return $query;
    }

    /**
     * Get all assessors by language and native.
     *
     * @param $language_id
     * @param $native
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function getAssessorsByLanguageAndNative($language_id, $native)
    {
        $query = $this->model->newQuery()->with([
            'userGroups' => function ($query) use ($native) {
                $query->where('native', $native);
            },
            'userGroups.user' => function ($q) {
                $q->active();
            }])->where(['language_id' => $language_id])->get()->pluck('userGroups');

        if ($query->isNotEmpty()) {
            return $query->first()->pluck('user')
                // remove inactive users
                ->reject(function ($value, $key) {
                    return $value == null;
                });
        }

        return $query;
    }

    /**
     * Get all  from language group.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function getAllAssessorsInGroups()
    {
        return $this->model->with(['language', 'userGroups', 'userGroups.user'])->get();
    }

    /**
     * @param $language_id
     * @param int $native
     * @return bool
     */
    public function existsNativeAssessors($language_id, $native = 1)
    {
        $group = UserGroup::where('native', $native)
            ->whereHas('group', function ($q) use ($language_id) {
                $q->where('language_id', $language_id);
                $q->whereHas('userGroups.user', function ($q) {
                    $q->active();
                });
            })->count();

        return $group > 0;
    }

    /**
     * Get random assessors from language group.
     *
     * @param $language_id
     * @param int $user_id
     * @param string $native
     * @param string $outputLog
     * @return \App\Models\User|null
     */
    function getRandomAssessor($language_id, $user_id = 0, $native = '', &$outputLog = '', $deadline = null)
    {

        $outputLog .= 'Params: ' . $language_id .','. $user_id .','. $native  . "\n";

        ### check if assessor is native on this lang
        if ($user_id > 0 && $native === ''){ //if we have user id and we don't have native, determine native
            $group = UserGroup::where('user_id', $user_id)
                ->where('native', 1)
                ->with(['group' => function ($q) use ($language_id) {
                    $q->where('language_id', $language_id);
                }])
                ->count();

            $native = $group > 0;
        }

        $outputLog .= '$native: ' . json_encode($native)  . "\n";

        $assessors = $this->getAssessorsFromLanguageGroup($language_id, $user_id, $native, $deadline);

        $outputLog .= '$assessors: ' . json_encode($assessors)  . "\n";

        $assessorNames = $assessors->pluck('full_name', 'id')->toArray();

        if ($assessors->isNotEmpty()) {
        
            $assessorsIds = $assessors->pluck('id')->toArray();

            // Get assessors with no tasks
            $assessorsWithNoTasks = User::whereIn('id', $assessorsIds)
                ->doesntHave("tasks")
                ->get();

            if ($assessorsWithNoTasks->count() > 0) {
                return $assessorsWithNoTasks->random(1)->first();
            }

            $tasksAndAssessors = Task::whereIn('assessor_id', $assessorsIds)
                ->select("*", DB::raw('MAX(created_at) as last_task_created_at'))
                ->groupBy("assessor_id")
                ->orderBy("last_task_created_at", "ASC")
                ->with('assessor')
                ->get();

            return $tasksAndAssessors->first()->assessor;
        } else {
            if ($user_id > 0) {
                return User::find($user_id);
            }
        }

        return null;
    }

    /**
     * Get random assessors from language group - refuse
     *
     * @param $task
     * @return \App\Models\User|null
     */
    function getRandomAssessorRefuse($task)
    {

        // old assessors from task history
        $oldAssessorsIds = TaskAssessorHistory::where('task_id', $task->id)->pluck('assessor_id')->toArray();
        $oldAssessorsIds = array_unique($oldAssessorsIds);

        // get all assessors without current assessor
        $isNative =  $task->native == 1;
        $assessors = $this->getAssessorsFromLanguageGroup($task->language_id, $task->assessor_id, $isNative);


        // remove old assessors from assessors
        $newAssessorsIds = array_diff($assessors->pluck('id')->toArray(), $oldAssessorsIds);

        // case 1 - we have other assessors
        if (!empty($newAssessorsIds)) {
            $assessorsToPick = $newAssessorsIds;
        // case 2 - we don't have new assessors, get one from old assessors
        } else {
            $assessorsToPick = $oldAssessorsIds;
        }

        // get total tasks assigned
        $totals = Task::selectRaw('assessor_id, COUNT(*) as total')
            ->whereIn('assessor_id', $assessorsToPick)
            ->whereIn('task_status_id', [TaskStatus::STATUS_ALLOCATED, TaskStatus::STATUS_IN_PROGRESS])
            ->where(function($q){
                $q->whereHas('papers', function($q){
                    $q->where('status_id', '!=', CANCELED);
                    $q->where('status_id', '!=', DONE);
                    $q->whereIn('paper_type_id', [TEST_WRITING, TEST_SPEAKING]);
                    $q->whereDoesntHave('report');
                });
            })
            ->groupBy('assessor_id')
            ->orderBy('total')->get()->pluck('total', 'assessor_id')->toArray();

        // add rest of the assessors with no tasks
        foreach ($assessorsToPick as $assessor) {
            if (!isset($totals[$assessor])) {
                $totals[$assessor] = 0;
            }
        }

        // sort by totals
        asort($totals);


        $totals = array_flip($totals);
        $firstId = reset($totals);

        if ($firstId == $task->assessor_id) {
            array_shift($totals);
            $firstId = reset($totals);
        }

        $firstAssessor = $assessors->filter(function($item) use ($firstId) {
            return $item->id == $firstId;
        })->first();

        return $firstAssessor;

    }

}
