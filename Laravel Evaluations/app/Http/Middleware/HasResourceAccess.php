<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\Task;
use Closure;
use Illuminate\Support\Facades\Auth;

class HasResourceAccess
{
    private $user;
    private $request;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->user = Auth::user();
        $this->request = $request;

        $action = $request->route()->getAction()['controller'];
        $action = str_replace("App\Http\Controllers\\", '', $action);

        switch ($action) {
            case 'TaskController@getTaskPage':

                ### check client task access
                $this->_checkTaskAccess();

                break;

            case 'TaskController@index':

                ### check client task access
                $this->_checkProjectAccess();

                break;

            default;
                break;
        }

        return $next($request);
    }


    /**
     *  check task access
     */
    private function _checkTaskAccess()
    {

        if ($this->user->hasRole(['client', 'assessor', 'css', 'tds', 'recruiter'])) {
            $task = Task::with('project')->find($this->request->segments()[1]);
            $project = $task->project;
            $participants = $task->project->participants->pluck('user_id')->toArray();
            $followers = $task->followers->pluck('user_id')->toArray();
            if (!in_array($this->user->id, $participants) &&
                !in_array($this->user->id, $followers) &&
                $this->user->id !== $task->assessor_id &&
                $project->user_id != $this->user->id
            ) {
                return abort(404);
            }

        }
    }

    /**
     *  check project access
     */
    private function _checkProjectAccess()
    {

        if ($this->user->hasRole(['client', 'assessor', 'css', 'tds', 'recruiter'])) {

            $project = Project::with('tasks')->find($this->request->segments()[1]);
            if($project != null){
                $assessors = [];
                foreach($project->tasks as $task){
                    $assessors[] = $task->assessor_id;
                }

                $participants = $project->participants->pluck('user_id')->toArray();
                if (!in_array($this->user->id, $participants) &&
                    !in_array($this->user->id, $assessors) &&
                    $project->user_id != $this->user->id) {
                    return abort(404);
                }
            }
        }
    }
}
