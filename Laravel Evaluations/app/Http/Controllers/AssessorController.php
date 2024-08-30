<?php

namespace App\Http\Controllers;


use App\Models\AssessorInactivity;
use App\Models\User;
use App\Repositories\ModuleInterface;
use App\Repositories\PermissionInterface;
use App\Repositories\ProjectTypeInterface;
use App\Repositories\RoleInterface;
use App\Repositories\TaskInterface;
use App\Repositories\TaskRepository;
use App\Repositories\UserInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssessorController extends Controller
{


    /**
     * @param $id
     * @return mixed
     */
    public function getInactivity($id)
    {
        $user = User::with('inactivities')->find($id);
        if (!$user->hasRole('assessor')) {
            return ajaxResponse(ERROR, 'bad request');
        }
        return ajaxResponse(SUCCESS, null, $user->inactivities);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateInactivity($id, Request $request)
    {

        $dates = [];

        if ($request->has('dates')) {
            foreach ($request->get('dates') as $range) {
                $dates[] = [
                    'user_id' => $id,
                    'date_from' => $range['date_from'],
                    'date_to' => $range['date_to'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        
        AssessorInactivity::where('user_id', $id)->delete();

        if (!empty($dates))
            AssessorInactivity::insert($dates);
        
        return ajaxResponse(SUCCESS);
    }


    /**
     * Get assessor page
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAssessorPage(Request $request)
    {
        $projectTypes = $this->projectTypeRepository->getAll();
        $permissions = $this->permissionRepository->getAll();
        $roles = $this->roleRepository->getAll();

        return view('account.assessors.index', compact('roles', 'permissions', 'projectTypes'));
    }

    /**
     * Refuse task
     *
     * @param $assessorId
     * @param $taskId
     * @return void
     */
    public function refuseTask($assessorId, $taskId)
    {
        $assessor = $this->userRepository->getByRole('assessor', $assessorId)->random(1);
        $attributes = array();
        $attributes['assessor_id'] = $assessor->id;
        $this->taskRepository->update($taskId, $attributes);
    }
}