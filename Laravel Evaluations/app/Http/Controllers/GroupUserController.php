<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Role;
use App\Models\UserGroup;
use App\Models\UserRole;
use App\Repositories\GroupRepository;
use App\Repositories\GroupUserRepository;
use App\Repositories\UserRoleRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GroupUserController extends Controller
{
    private $groupUserRepository;
    private $groupRepository;
    private $userRoleRepository;

    /**
     * GroupUserController constructor.
     *
     * @param \App\Repositories\GroupUserRepository $groupUserRepository
     * @param \App\Repositories\GroupRepository $groupRepository
     * @param \App\Repositories\UserRoleRepository $userRoleRepository
     */
    public function __construct(
        GroupUserRepository $groupUserRepository,
        GroupRepository $groupRepository,
        UserRoleRepository $userRoleRepository
    )
    {
        $this->groupUserRepository = $groupUserRepository;
        $this->groupRepository = $groupRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * Display a listing of users in group.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Group $group)
    {
        $assessors = $this->userRoleRepository->getAssessorUsers()->pluck('full_name_email', 'id');

        return view('groups.users/index', ['group' => $group, 'assessors' => $assessors]);
    }

    /**
     * Ajax function to populate data table.
     */
    public function getTableData(Request $request, Group $group)
    {
        return DataTables::of($this->groupUserRepository->search($group, $request->input('filters')))->make(true);
    }

    /**
     * Generate create form for Ajax request.
     */
    public function createForm(Group $group)
    {
        $assessors = $this->userRoleRepository->getAssessorUsers();

        return view('groups.users/_form', [
            'group' => $group,
            'assessors' => empty($assessors) ? array() : $assessors->pluck('full_name_email', 'id')
        ])->render();
    }

    /**
     * Add a new user in group.
     *
     * @param  \Illuminate\Http\Request $request
     * @return UserGroup
     */
    public function create(Request $request, Group $group)
    {
        $params = $request->toArray();

        $params['group_id'] = $group->id;

        $groupUser = $this->groupUserRepository->createOrUpdate($params);

        return json_encode($groupUser);
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updateForm(Group $group, $id)
    {
        $groupUser = $this->groupUserRepository->getById($id);
        $assessors = $this->userRoleRepository->getAssessorUsers();

        return view('groups.users/_form', [
            'group' => $group,
            'groupUser' => $groupUser,
            'assessors' => empty($assessors) ? array() : $assessors->pluck('full_name_email', 'id')
        ])->render();
    }

    /**
     * Update the specified user in group.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function update(Request $request, Group $group, $id)
    {
        $params = $request->toArray();

        return json_encode($this->groupUserRepository->update($id, $params));
    }

    /**
     * Remove the specified user from group.
     *
     * @param  int $id
     * @return bool|null
     */
    public function delete(Group $group, $id)
    {
        return json_encode($this->groupUserRepository->deleteUserFromGroup(['user_id' => $id, 'group_id' => $group->id]));
    }

    /**
     * Get assessors
     */
    public function getAssessors()
    {
        return json_encode($this->userRoleRepository->getAssessorUsers()->pluck('full_name_email', 'id'));
    }
}
