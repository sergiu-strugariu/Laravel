<?php

namespace App\Http\Controllers\admin;

use App\Models\Client;
use App\Models\Group;
use App\Models\Language;
use App\Models\LanguagePaperTypes;
use App\Models\Paper;
use App\Models\PaperType;
use App\Models\ProjectParticipant;
use App\Models\Question;
use App\Models\Role;
use App\Models\Setting;
use App\Models\TaskUpdate;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\GroupRepository;
use App\Repositories\GroupUserRepository;
use App\Repositories\ModuleInterface;
use App\Repositories\ProjectParticipantsInterface;
use App\Repositories\ProjectTypeInterface;
use App\Repositories\QuestionRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRoleInterface;
use App\Repositories\PermissionInterface;
use App\Repositories\PricesRepository;
use App\Services\EmailService;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\UserInterface;
use App\Repositories\RoleInterface;
use App\Services\EmailServiceInterface;
use App\Services\ExcelServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Facades\Excel as Excel;
use Mockery\Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\VarDumper\Cloner\Data;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdministratorController extends Controller
{
    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * @var $userRoleRepository
     */
    private $userRoleRepository;

    /**
     * @var $roleRepository
     */
    private $roleRepository;

    /**
     * @var $moduleRepository
     */
    private $moduleRepository;

    /**
     * @var $projectTypeRepository
     */
    private $projectTypeRepository;

    /**
     * @var $permissionRepository
     */
    private $permissionRepository;

    /**
     * @var $groupRepository
     */
    private $groupRepository;

    /**
     * @var $groupUserRepository
     */
    private $groupUserRepository;


    /**
     * @var $projectParticipantsRepository
     */
    private $projectParticipantsRepository;

    /**
     * @var
     */
    private $pricesRepository;

    /**
     * @var $emailService
     */
    private $emailService;

    /**
     * @var $excelService
     */
    private $excelService;

    /**
     * @var $userRepository
     */
    private $questionRepository;

    /**
     * @var $settingRepository
     */
    private $settingRepository;


    /**
     * AdministratorController constructor.
     *
     * @param UserInterface $userInterface
     * @param UserRoleInterface $userRoleInterface
     * @param RoleInterface $roleRepository
     * @param PermissionInterface $permissionInterface
     * @param ProjectTypeInterface $projectTypeInterface
     * @param ProjectParticipantsInterface $projectParticipantsRepository
     * @param GroupRepository $groupRepository
     * @param GroupUserRepository $groupUserRepository
     * @param PricesRepository $pricesRepository
     * @param ModuleInterface $moduleInterface
     * @param EmailService $emailService
     * @param ExcelServiceInterface $excelService
     * @param QuestionRepository $questionRepository
     * @param SettingRepository $settingRepository
     */
    public function __construct(
        UserInterface $userInterface,
        UserRoleInterface $userRoleInterface,
        RoleInterface $roleRepository,
        PermissionInterface $permissionInterface,
        ProjectTypeInterface $projectTypeInterface,
        ProjectParticipantsInterface $projectParticipantsRepository,
        GroupRepository $groupRepository,
        GroupUserRepository $groupUserRepository,
        PricesRepository $pricesRepository,
        ModuleInterface $moduleInterface,
        EmailService $emailService,
        ExcelServiceInterface $excelService,
        QuestionRepository $questionRepository,
        SettingRepository $settingRepository
    )
    {
        $this->userRepository = $userInterface;
        $this->userRoleRepository = $userRoleInterface;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionInterface;
        $this->projectTypeRepository = $projectTypeInterface;
        $this->projectParticipantsRepository = $projectParticipantsRepository;
        $this->groupRepository = $groupRepository;
        $this->groupUserRepository = $groupUserRepository;
        $this->pricesRepository = $pricesRepository;
        $this->moduleRepository = $moduleInterface;
        $this->emailService = $emailService;
        $this->excelService = $excelService;
        $this->questionRepository = $questionRepository;
        $this->settingRepository = $settingRepository;
    }


    /**
     * Get user form for manually creation
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreateUser()
    {
        $roles = $this->roleRepository->getAll();
        $permissions = $this->permissionRepository->getAll();
        $users = $this->userRepository->getByRoleAndParentIdNull('client');
        $clientCollection = Client::all();
        $clients = $clientCollection->sortBy("name", SORT_NATURAL|SORT_FLAG_CASE);

        return view('account.createManually', compact('roles', 'permissions', 'users', 'clients'));
    }

    /**
     * Get user form for automatically creation
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreateUserAutomatically()
    {
        $roles = $this->roleRepository->getAll();
        $permissions = $this->permissionRepository->getAll();

        return view('account.createAutomatically', compact('roles', 'permissions'));
    }

    /**
     * Create an user manually.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createUserManually(Request $request, Response $response)
    {

        $password = $this->userRepository->generateRandomPassword();
        $request['password'] = $password;

        $rules = [
            'email' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ($validator->messages());
        }

        $user = $this->userRepository->create($request->except('role_id'));

        if ($user == null) {
            return response()->json(['resType' => ERROR, 'errMsg' => 'The email already exists!']);
        }


        $roleAttributes = array();
        $roleAttributes['role_id'] = $request->get('role_id');
        $roleAttributes['user_id'] = $user->id;

        if ($request->get('role_id') == 7) {
            $roleAttributes['status_id'] = 1; //active
        }
        $this->userRoleRepository->create($roleAttributes);

        $attributes = [
            'email' => $user->email,
            'password' => $password,
        ];

        $mailSent = $this->emailService->sendEmail($attributes, MAIL_WELCOME);

        if ($mailSent) {
            addLog([
                'type' => MAIL_SENT,
                'description' => 'Mail sent for creating new user'
            ]);
        }

        return response()->json(['resType' => 'Success']);
    }

    /**
     * Create users from excel file.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createUserAutomatically(Request $request)
    {
        $file = $request->file('file')->getRealPath();
        $rows = Excel::load($file, function ($reader) {
        })->get()->toArray();

        $allowedRoles = Role::all('slug')->pluck('slug')->toArray();
        $allowedClients = Client::all('name')->pluck('name')->toArray();
        foreach ($rows as $key => $row) {

            foreach ($row as $field => $value) {
                $row[$field] = trim($value);
            }

            if (!in_array($row['role'], $allowedRoles)) {
                return response()->json(['resType' => 'Error', 'resMotive' => 'Invalid role (' . $row['role'] . '), on row ' . ($key + 2)]);
            }

            if (!in_array($row['client'], $allowedClients)) {
                return response()->json(['resType' => 'Error', 'resMotive' => 'Invalid client (' . $row['client'] . '), on row ' . ($key + 2)]);
            }

            $validator = \Validator::make($row, [
                'first_name' => 'string|nullable',
                'last_name' => 'string|nullable',
                'email' => 'email|required',
                'client' => 'string|nullable',
                'role' => 'string|required',
                'status' => 'nullable|integer|max:1|min:0'
            ]);

            if (!$validator->passes()) {

                $error = $validator->messages();
                $error = $error->messages();
                $errorField = key($error);
                $errorText = reset($error);

                $errMsg = $errorText[0] . ' (' . $row[$errorField] . ', on row ' . ($key + 2) . ')';
                return response()->json(['resType' => 'Error', 'resMotive' => $errMsg]);
            }


        }

        $excelData = $this->excelService->getExcelData($rows);
        $clients = Client::all();
        $roles = Role::all();

        $clientsArray = [];
        foreach ($clients as $client) {
            $clientsArray[$client->name] = $client->id;
        }
        unset($clients);

        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray[$role->slug] = $role->id;
        }
        unset($roles);

        Log::useFiles(storage_path() . '/logs/error.log');

        foreach ($excelData as $excelRow) {

            $attributes = [];
            $attributes['first_name'] = $excelRow['first_name'];
            $attributes['last_name'] = $excelRow['last_name'];
            $attributes['email'] = $excelRow['email'];
            $attributes['password'] = $this->userRepository->generateRandomPassword();
            $attributes['status'] = in_array($excelRow['status'], [0, 1]) ? $excelRow['status'] : 1;

            if ($excelRow['client'] != '') {
                $attributes['client_id'] = $clientsArray[$excelRow['client']];
            }

            $user = $this->userRepository->create($attributes);
            if ($user) {
                try {

                    $roleAttributes = [];
                    $roleAttributes['role_id'] = $rolesArray[$excelRow['role']];
                    $roleAttributes['user_id'] = $user->id;

                    $this->userRoleRepository->create($roleAttributes);

                    if ($user != null) {
                        $mailSent = $this->emailService->sendEmail($attributes, MAIL_WELCOME);
                        if ($mailSent) {
                            addLog([
                                'type' => MAIL_SENT,
                                'description' => 'Mail sent for creating new user'
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    Log::error([$e->getMessage()]);
                }
            }
        }

        return response()->json(['resType' => 'Success']);
    }

    /**
     * Display all permissions
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPermissions()
    {
        $permissions = $this->permissionRepository->getAll();
        $roles = $this->roleRepository->getAll();

        return view('permissions.index', compact('permissions', 'roles'));
    }

    /**
     * Return data for permissions datatable
     *
     * @return mixed
     */

    public function getPermissionDatatable()
    {
        return DataTables::of(\App\Models\Permission::all())->make(true);
    }


    /**
     * Display all roles
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRoles()
    {
        $roles = $this->roleRepository->getAll();

        return view('roles.index', compact('roles'));
    }

    /**
     * Return data for roles datatable
     *
     * @return mixed
     */
    public function getRolesDatatable()
    {
        return DataTables::of(\App\Models\Role::all())->make(true);
    }

    /**
     * Display all project types
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getProjectTypes()
    {
        $projectTypes = $this->projectTypeRepository->getAll();
        $permissions = $this->permissionRepository->getAll();
        $roles = $this->roleRepository->getAll();

        return view('projectTypes.index', compact('projectTypes', 'roles', 'permissions'));
    }

    /**
     * Return data for project types datatable
     *
     * @return mixed
     */
    public function getProjectTypesDatatable()
    {
        return DataTables::of((\App\Models\ProjectTypes::all()))->make(true);
    }

    /**
     * Display all task updates
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTaskUpdates()
    {
        return view('admin.task-updates');
    }


    /**
     * Return data for project types datatable
     *
     * @return mixed
     */
    public function getTaskUpdatesDatatable()
    {
        return DataTables::of((\App\Models\TaskUpdate::with('roles')))->make(true);
    }

    /**
     * Generate update form for Ajax request.
     */
    public function getTaskUpdatesForm($id)
    {
        $taskUpdate = TaskUpdate::find($id);
        $roles = Role::pluck('name', 'id');
        return view('admin.task-updates-form', compact('taskUpdate', 'roles'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateTaskUpdate($id, Request $request)
    {
        $taskUpdate = TaskUpdate::find($id);
        $taskUpdate->name = $request->get('name');
        $taskUpdate->display_name = $request->get('display_name');

        $taskUpdate->roles()->sync($request->get('roles'));

        $taskUpdate->save();

        return ajaxResponse(SUCCESS);
    }


    /**
     * Display all tests
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTestsPage()
    {
        $tests = PaperType::all();

        return view('admin.tests.index', compact('tests'));
    }

    /**
     * Return data for project types datatable
     *
     * @return mixed
     */
    public function getTestsDatatable()
    {
        return DataTables::of((\App\Models\PaperType::all()))->make(true);
    }

    /**
     * Update test
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTest($id, Request $request)
    {

        $test = PaperType::find($id);

        if (!$test) {
            return ajaxResponse(ERROR, 'Test not found!');
        }

        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $test->name = $request->name;
        $test->save();

        return ajaxResponse(SUCCESS);

    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function updateUser(Request $request, $id)
    {

        if (!$request->has('project_participating_id')) {
            return json_encode($this->userRepository->getById($id)->update($request->toArray()));
        }

        $project_participating_ids = $request->input('project_participating_id');

        $resType = '';

        $projectParticipating = $this->projectParticipantsRepository->getByUserId($id);
        $projectParticipatingArray = array();
        foreach ($projectParticipating as $projectParticipating) {
            $projectParticipatingArray[$projectParticipating->project_id] = $projectParticipating;
        }
        unset($projectParticipating);


        foreach ($project_participating_ids as $projectParticipatingId) {
            if (in_array($projectParticipatingId, array_keys($projectParticipatingArray))) {

                unset($projectParticipatingArray[$projectParticipatingId]);
            }


            try {
                $pp = $this->projectParticipantsRepository->getByUserAndProjectId($id, $projectParticipatingId);


                $attributes = array();
                if ($pp) {
                    $attributes['project_id'] = $pp->project_id;
                    $attributes['user_id'] = $id;

                    $resType = json_encode($this->projectParticipantsRepository->update($pp->id, $attributes));
                } else {
                    $attributes['project_id'] = $projectParticipatingId;
                    $attributes['user_id'] = $id;

                    $resType = json_encode($this->projectParticipantsRepository->create($attributes));
                }
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
        }

        ### remove other projects participating
        foreach ($projectParticipatingArray as $projectPart) {
            $this->projectParticipantsRepository->delete($projectPart->id);
        }

        ### update user
        $this->userRepository->getById($id)->update($request->toArray());
        return response()->json(['type' => $resType]);

    }


    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRolesSlug($slug)
    {

        $roles = $this->roleRepository->getAll();
       // $users = $this->userRepository->getClients($slug);
        $users = Client::orderBy('name', 'asc')->get()->pluck('name', 'id');

        $groups = $this->groupRepository->getAllAssessorsInGroups();

        $assessorGroups = [];
        if ($slug == 'assessor') {
            $assessorGroups = $this->getGroupAllAssessors();
        }

        return view('account.role.' . $slug . '', compact('roles', 'users', 'groups', 'assessorGroups'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getRoleDatatable($id)
    {
        return DataTables::of((\App\Models\UserRole::where('role_id', $id)->with('user', 'role',
            'user.status', 'user.projects', 'user.projects.participants.user')))->make(true);
    }

    /**
     * @param null $filter
     * @return mixed
     */
    public function getCssRecruitersDatatable($filter = null)
    {

        $results = \App\Models\UserRole::with('user', 'role',
            'user.status', 'user.projects', 'user.projects.participants.user')->whereHas('role', function ($query) {
            $query->where('slug', 'css');
            $query->orWhere('slug', 'recruiter');
        })->whereHas('user', function ($q) use ($filter) {
            if ($filter == 0) {
                $q->whereNotNull('deleted_at');
            } elseif ($filter == 1) {
                $q->whereNull('deleted_at');
            }
        });
        return DataTables::of($results)->make(true);

    }


    /**
     * @param $id
     * @param $parentId
     * @param $filter
     * @return mixed
     */
    public function getClientParticipants($id, $parentId, $filter)
    {

        if ($filter == 0) {
            $results = User::withTrashed()->where('client_id', $parentId)
                ->with('projectsParticipating', 'projectsParticipating.project', 'projects')
                ->whereNotNull('deleted_at');
        } elseif ($filter == 1) {
            $results = User::where('client_id', $parentId)
                ->with('projectsParticipating', 'projectsParticipating.project', 'projects')
                ->whereNull('deleted_at');
        } else {
            $results = User::withTrashed()->where('client_id', $parentId)
                ->with('projectsParticipating', 'projectsParticipating.project', 'projects');
        }

        return DataTables::of($results)->make(true);

    }

    /**
     * @return array
     */
    public function getGroupAllAssessors()
    {
        $all = $this->groupUserRepository->searchALL()->get();
        $return = [];
        foreach ($all as $row){
            $return[$row->group_id][] = $row;
        }
        return $return;
    }

    /**
     * @param Group $group
     * @return mixed
     */
    public function getGroupAssessors(Group $group)
    {
        return DataTables::of($this->groupUserRepository->search($group, []))->make(true);
    }

    /**
     * @param null $filter
     * @return mixed
     */
    public function getTdsDatatable($filter = null)
    {

        $results = UserRole::with('user', 'role', 'user.status', 'user.projects',
            'user.projects.participants.user')
            ->whereHas('role', function ($query) {
                $query->where('slug', 'tds');
            })->whereHas('user', function ($q) use ($filter) {
                if ($filter == 0) {
                    $q->whereNotNull('deleted_at');
                } elseif ($filter == 1) {
                    $q->whereNull('deleted_at');
                }
            });

        return DataTables::of($results)->make(true);

    }


    /**
     * Get update creation form
     *
     * @return string
     */

    public function getUpdateUserForm($id)
    {
        $user = $this->userRepository->getById($id);
        $roles = $this->roleRepository->getAll()->pluck('name', 'id');
        $nativeArray = array('0' => 0, '1' => 1);

        return view('account/_form', compact('user', 'roles', 'nativeArray'))->render();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Support\MessageBag
     */
    public function createClientParticipant(Request $request)
    {
        $rules = [
            'email' => 'required',
            'projectsId' => 'required',
        ];

        $resType = 'Error';

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ($validator->messages());
        }

        $projectsId = $request->input('projectsId');

        $password = $this->userRepository->generateRandomPassword();
        $request['password'] = $password;


        try {

            $user = $this->userRepository->create($request->except('projects'));
            if ($user == null) {
                return response()->json(['resType' => 'Error', 'resMotive' => 'The email already exists!']);
            }
            $attributes = array();
            $attributes['password'] = $password;
            $attributes['first_name'] = $request->get('first_name');
            $attributes['last_name'] = $request->get('last_name');
            $attributes['template'] = config('mail.new_account');
            $attributes['email'] = $user->email;


            $this->emailService->sendEmail($attributes, MAIL_WELCOME);


            $roleAttributes = array();
            $roleAttributes['user_id'] = $user->id;
            $roleAttributes['role_id'] = 5;
            $this->userRoleRepository->create($roleAttributes);

            foreach ($projectsId as $projectId) {

                $projectParticipantAttributes = array();
                $projectParticipantAttributes['user_id'] = $user->id;
                $projectParticipantAttributes['project_id'] = $projectId;

                $this->projectParticipantsRepository->create($projectParticipantAttributes);
            }

            $resType = 'Success';
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return response()->json(['resType' => $resType]);
    }

    /**
     * @param $id
     * @return string
     */
    public function removeClientParticipant($id)
    {
        return json_encode($this->projectParticipantsRepository->deleteProjectParticipant($id));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Support\MessageBag
     */
    public function createUserByRoleId(Request $request)
    {

        $rules = [
            'email' => 'required',
        ];

        $resType = 'Error';

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ($validator->messages());
        }

        $password = $this->userRepository->generateRandomPassword();
        $request['password'] = $password;

        try {

            $user = $this->userRepository->create($request->except('projects'));
            if ($user == null) {
                return response()->json(['resType' => 'Error', 'resMotive' => 'The email already exists!']);
            }

            $attributes = array();
            $attributes['password'] = $password;
            $attributes['template'] = config('mail.new_account');
            $attributes['email'] = $user->email;


            $this->emailService->sendEmail($attributes, MAIL_WELCOME);


            $roleAttributes = array();
            $roleAttributes['user_id'] = $user->id;
            $roleAttributes['role_id'] = $request->input('role');
            $this->userRoleRepository->create($roleAttributes);
            $resType = 'Success';

        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return response()->json(['resType' => $resType]);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function removeUser($id)
    {
        if(!$this->userRepository->delete($id)){
            return ajaxResponse(ERROR, 'User was not removed');
        }
        return ajaxResponse(SUCCESS);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function activateUser($id)
    {
        if(!$this->userRepository->untrash($id)){
            return ajaxResponse(ERROR, 'User was not activated');
        }
        return ajaxResponse(SUCCESS);
    }


    /**
     *  Sets a user's disabled_from and disabled_to attributes
     *
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function addUserTemporaryDisabled($id, Request $request)
    {
        if(!$this->userRepository->addUserTemporaryDisabled($id, $request->all())){
            return ajaxResponse(ERROR, 'User was not disabled');
        }
        return ajaxResponse(SUCCESS);
    }

    /**
     *  Removes user's temporary disabled attributes
     *
     * @param $id
     * @return mixed
     */
    public function removeUserTemporaryDisabled($id)
    {
        if(!$this->userRepository->removeUserTemporaryDisabled($id)){
            return ajaxResponse(ERROR, 'User was not enabled');
        }
        return ajaxResponse(SUCCESS);
    }

    /**
     * @return mixed
     */
    public function getUsersPage()
    {
        $roles = $this->roleRepository->getAll()->pluck('slug', 'id');
        return view('users.crud', compact('roles'));
    }


    /**
     *  Get users collection for Datatables
     *
     * @param Request $request
     * @return mixed
     */
    public function getUsersDatatable(Request $request)
    {
        $filters = $request->input('filters');

        $results = $this->userRepository->searchUsers($filters)
            ->with([
                'roles' => function ($q) {
                    $q->select('slug');
                },
                'clients',
                'projectsParticipating.project'
            ]);

        return DataTables::of($results)->make(true);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function exportUsers(Request $request)
    {

        $filters = $request->input('filters');

        $data = $this->userRepository->searchUsers($filters)->with([
            'roles' => function ($query) {
                $query->select('slug');
            }
        ])->get();

        $results = [];

        foreach ($data as $item) {

            $role = null;
            $roles = $item->roles->toArray();
            if (!empty($roles)) {
                $role = reset($roles);
                $role = $role['slug'];
            }

            $results[] = [
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'email' => $item->email,
                'client' => $item->clients['name'],
                'role' => $role,
                'status' => is_null($item->deleted_at) ? 1 : 0,
            ];
        }


        return Excel::create('UsersListing', function ($excel) use ($results) {

            $excel->sheet('mySheet', function ($sheet) use ($results) {
                $sheet->setOrientation('portrait');
                $sheet->fromArray($results);
            });

        })->download("xlsx");

    }

    /**
     * Create client(company)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     */
    public function createClient(Request $request)
    {

        if (!auth()->user()->canAtLeast(['user.create', 'client.create'])) {
            return abort(403, 'You are not allowed to view this content!');
        }

        $rules = [
            'name' => 'required|unique:clients,name',
            'billing_contract_date' => 'required',
            'billing_contract_no' => 'required',
            'billing_capital' => 'required',
            'billing_bank' => 'required',
            'billing_iban' => 'required',
            'billing_address' => 'required',
            'billing_cif' => 'required',
            'billing_registry' => 'required',
            'billing_company_name' => 'required',
        ];

        $resType = 'Error';

        $validator = Validator::make($request->all(), $rules);


        if (!$validator->passes()) {
            return ($validator->messages());
        }

        try {

            $client = Client::withTrashed()->where('name', $request->name)->first();

            if ($client != null) {
                return response()->json(['resType' => 'Error', 'resMotive' => 'The client already exists!']);
            }

            $attributes = array(
                'name' => $request->name,
                'billing_contract_date' => $request->billing_contract_date,
                'billing_contract_no' =>  $request->billing_contract_no,
                'billing_capital' => $request->billing_capital,
                'billing_bank' => $request->billing_bank,
                'billing_iban' =>  $request->billing_iban,
                'billing_address' => $request->billing_address,
                'billing_cif' => $request->billing_cif,
                'billing_registry' => $request->billing_registry,
                'billing_company_name' => $request->billing_company_name,
                'billing_hidden' => intval($request->billing_hidden),
            );

            $client = Client::create($attributes);


            if ($request->get('custom-prices')) {
                $prices = json_decode($request->get('custom-prices'), true);
                $create = [];
                foreach ($prices['create'] as $price) {
                    $price['client_id'] = $client->id;
                    $create[] = $price;
                }

                if (!empty($create)) $this->pricesRepository->createDefaultMultiple($create);
            }

            $resType = 'Success';
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        return response()->json(['resType' => $resType, 'data' => $client]);
    }

    public function getClientDetails(Request $request)
    {
        if (!auth()->user()->canAtLeast(['project.create'])) {
            return abort(403, 'You are not allowed to get this content!');
        }

        $rules = [
            "id" => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->passes()) {
            return [$validator->messages()];
        }

        $client = Client::find($request->id);
        $response = ['client' => null];
        if ($client) {
            $response['client'] = $client;
        }

        return $response;
    }

    /**
     * Load settings page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSettings()
    {
        return view('settings.index');
    }

    /**
     * Get data for settings table
     *
     * @return mixed
     */
    public function getSettingsDatatable()
    {
        return DataTables::of(Setting::all())->make(true);
    }

    /**
     * Load setting
     *
     * @param $setting_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadSetting($setting_id)
    {
        $setting = $this->settingRepository->getById($setting_id);
        $fileExtension = '';
        if ($setting->key == 'audio_file_path' && $setting->value !== null) {
            $fileExtension = pathinfo(url('audio/' . $setting->value))['extension'];
        }
        return view('settings.modal-settings', compact('setting', 'fileExtension'));

    }

    /**
     * Update setting
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSetting($id, Request $request)
    {

        $setting = $this->settingRepository->getById($id);
        $rules = ['value' => 'required'];

        if ($setting->key == 'audio_file_path') {
            $rules = ['description' => 'required'];
        }

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        if ($setting->key == 'audio_file_path') {
            if ($request->has('value') && $request->file('value')) {


                $file = $request->file('value');

                ### check file for uploading
                $this->_checkAudioFile($file);

                $fileName = $file->getClientOriginalName();
                $rules['audio_file_path'] = $fileName;

                ### Upload file
                $destinationPath = public_path('audio');
                $file->move($destinationPath, $fileName);
                $request->value = $fileName;
            }
        }

        $updateArray = [
            'value' => $request->value,
            'description' => $request->description
        ];

        if ($setting->key == 'audio_file_path' && !$request->has('value') && !$request->file('value')) {
            unset($updateArray['value']);
        }

        $this->settingRepository->update($id, $updateArray);

        return ajaxResponse(SUCCESS);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function sendMailToLanguageAuditManager(Request $request)
    {

        $rules = [
            'subject' => 'required',
            'body' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return response()->json(['resMotive' => $validator->messages()]);
        }

        $task_id = $request->get('task_id');

        $attributes = [];
        $attributes['subject'] = $request->get('subject');
        $attributes['body'] = $request->get('body');
        $attributes['body'] .= '<br><br>Mail sent by: ' . auth()->user()->full_name;
        $attributes['body'] .= '<br>Email: ' . auth()->user()->email;
        $attributes['task_id'] = $task_id;
        $role = preg_replace('/\s+/','',$request->get('role'));
        $role = trim($role);
        if($role == 'client'){
            $attributes['to'] =  Setting::where('key', 'eucom_email')->first()->value;
            $attributes['description'] = 'Mail sent to Eucom';
        }else if($role == 'assessor'){
            $attributes['to'] = Setting::where('key', 'language_audit_manager_email')->first()->value;
            $attributes['description'] = 'Mail sent to Eucom Language Audit Manager';
        }
        if($role != 'client' && $role != 'assessor'){
            return ajaxResponse(ERROR, 'The role does not exist!');
        }


        $this->emailService->sendEmailToLanguageAuditManager($task_id, $attributes);
        addLog([
            'type' => MAIL_SENT,
            'user_id' => Auth()->user()->id,
            'task_id' => $task_id,
            'description' => $attributes['description']
        ]);

        return ajaxResponse(SUCCESS);

    }

    /**
     * Verify the size of uploaded audio file
     *
     * @param $file
     * @return \Illuminate\Http\JsonResponse
     */
    private function _checkAudioFile($file)
    {

        $fileSize = $file->getSize() / pow(1024, 2);
        $fileMime = $file->getMimeType();

        $maxSize = 50;

        ### check file size
        if ($fileSize > $maxSize) {
            return ajaxResponse(ERROR, 'The file must be smaller than ' . $maxSize . ' MB');
        }

        ### check mime type
        if (!starts_with($fileMime, 'audio')) {
            return ajaxResponse(ERROR, 'The file must be audio type');
        }

    }


    /**
     * @param $id
     * @return mixed
     */
    public function getUserDetails($id)
    {
        $user = $this->userRepository->getById($id);
        $roles = $this->roleRepository->getAll()->pluck('slug', 'id')->toArray();
        return view('users.form', compact('user', 'roles'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function updateUserDetails($id, Request $request)
    {
        if (!auth()->user()->hasRole(['master', 'administrator'])) {
            return ajaxResponse(ERROR, 'Not allowed!');
        }

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|required|unique:users,email,'.$id,
            'roles' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $this->userRepository->update($id, $request->only(['email', 'first_name', 'last_name']));

        $roles = $request->only('roles');
        UserRole::where('user_id', $id)->delete();
        foreach ($roles['roles'] as $role) {
            $this->userRoleRepository->create(['user_id' => $id, 'role_id' => $role]);
        }

        return ajaxResponse(SUCCESS);
    }

}
