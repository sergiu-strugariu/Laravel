<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Repositories\GroupRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\RoleInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    private $groupRepository;
    private $languageRepository;
    private $roleRepository;

    /**
     * GroupController constructor.
     *
     * @param \App\Repositories\GroupRepository $groupRepository
     * @param \App\Repositories\LanguageRepository $languageRepository
     * @param \App\Repositories\RoleInterface $roleRepository
     */
    public function __construct(GroupRepository $groupRepository, LanguageRepository $languageRepository, RoleInterface $roleRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->languageRepository = $languageRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of groups.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = $this->roleRepository->getAll();
        return view('groups/index', compact('roles'));
    }

    /**
     * Ajax function to populate data table.
     */
    public function getTableData()
    {
        return DataTables::of(Group::query()->with('language'))->make(true);
    }

    /**
     * Generate create form for Ajax request.
     */
    public function createForm()
    {
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');

        return view('groups/_form', compact('languages'))->render();
    }

    /**
     * Create a new group.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Group
     */
    public function create(Request $request)
    {
        return json_encode($this->groupRepository->create($request->toArray()));
    }

    /**
     * Display the specified group.
     *
     * @param  int $id
     * @return Group
     */
    public function view($id)
    {
        return $this->groupRepository->getById($id);
    }

    /**
     * Display the specified group users.
     *
     * @param  int $id
     * @return array
     */
    public function viewUsers($id)
    {
        $group = $this->groupRepository->getById($id);
        $groupUsers = $group->userGroups()->get();

        $result['title'] = 'Users in Group ' . $group->language()->first()->name;
        $result['html'] = view('groups/users', compact('groupUsers'))->render();
        return $result;
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updateForm($id)
    {
        $group = $this->groupRepository->getById($id);
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');

        return view('groups/_form', compact('group', 'languages'))->render();
    }

    /**
     * Update the specified group.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function update(Request $request, $id)
    {
        return json_encode($this->groupRepository->getById($id)->update($request->toArray()));
    }

    /**
     * Remove the specified group.
     *
     * @param  int $id
     * @return bool|null
     */
    public function delete($id)
    {
        return json_encode($this->groupRepository->getById($id)->delete());
    }
}
