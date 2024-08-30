<?php

namespace App\Http\Controllers;


use App\Models\Project;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\PricesRepository;
use App\Repositories\ProjectInterface;
use App\Repositories\ProjectParticipantsInterface;
use App\Repositories\ProjectTypeInterface;
use App\Repositories\UserInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Services\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Yajra\DataTables\DataTables;

class ProjectController
{
    /**
     * @var $projectRepository
     */
    private $projectRepository;

    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * @var $projectTypeRepository
     */
    private $projectTypeRepository;

    /**
     * @var $projectParticipantRepository
     */
    private $projectParticipantRepository;

    /**
     * @var
     */
    private $pricesRepository;

    /**
     * @var $emailService
     */
    private $emailService;


    /**
     * ProjectController constructor.
     * @param ProjectInterface $projectInterface
     * @param UserInterface $userInterface
     * @param ProjectTypeInterface $projectTypeInterface
     * @param ProjectParticipantsInterface $projectParticipantInterface
     * @param PricesRepository $pricesRepository
     * @param EmailServiceInterface $emailService
     */
    public function __construct(
        ProjectInterface $projectInterface,
        UserInterface $userInterface,
        ProjectTypeInterface $projectTypeInterface,
        ProjectParticipantsInterface $projectParticipantInterface,
        PricesRepository $pricesRepository,
        EmailServiceInterface $emailService
    )
    {
        $this->projectRepository = $projectInterface;
        $this->userRepository = $userInterface;
        $this->projectTypeRepository = $projectTypeInterface;
        $this->projectParticipantRepository = $projectParticipantInterface;
        $this->pricesRepository = $pricesRepository;
        $this->emailService = $emailService;
    }

    /**
     * Get project page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getProjectPage()
    {
        $participants = $this->userRepository->getAll()->pluck('first_name', 'id');

        if (Auth()->user()->hasRole(['master', 'administrator', 'css', 'recruiter'])) {
            $clientsCollection = Client::with('projects', 'projects.participants')->get();
        } else if (Auth()->user()->hasOnlyRole('assessor')) {
            $clientsCollection = Client::with('projects')->whereHas('projects.tasks', function ($query) {
                $query->where('assessor_id', Auth()->user()->id);
            })->get();
        } else {
            $clientsCollection = Client::with(['projects' => function ($q) {
                $q->whereHas('participants', function ($q) {
                    $q->where('user_id', Auth()->user()->id);
                })->orWhereHas('tasks', function ($query) {
                    $query->where('assessor_id', Auth()->user()->id);
                });
            }])->whereHas('projects.participants', function ($q) {
                $q->where('user_id', Auth()->user()->id);
            })->orWhereHas('projects.tasks', function ($query) {
                $query->where('assessor_id', Auth()->user()->id);
            })->get();
        }

        $clients = $clientsCollection->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);

        $projectTypes = $this->projectTypeRepository->getAll();

        return view('project.index', compact('participants', 'clients', 'projectTypes'));
    }

    /**
     * Get project data for datatables
     *
     * @return mixed
     * @throws \Exception
     */
    public function getProjectDatatable()
    {
        if (Auth()->user()->hasRole(['master'])) {
            return DataTables::of(Project::query()->with([
                'participants',
                'participants.user',
                'user',
            ]))->make(true);
        }

        if (Auth()->user()->hasRole(['client'])) {
            return DataTables::of(Project::query()->with([
                'participants',
                'participants.user',
                'user',
            ])->where('user_id',
                Auth()->user()->id))->make(true);
        }


        if (Auth()->user()->hasRole(['css', 'assessor', 'tds', 'administrator'])) {
            return DataTables::of(Project::query()->with('participants.user', 'user')->whereHas('participants',
                function ($query) {
                    $query->where('user_id', '=', Auth()->user()->id);
                }))->make(true);
        }

    }

    /**
     * Get project create form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createForm()
    {
        $clients = $this->userRepository->getByRole('client');
        $projectTypes = $this->projectTypeRepository->getAll()->pluck('name', 'id');

        $projectSettings = array();
        $projectSettings[0] = 'Yes';
        $projectSettings[1] = 'No';

        return view('project/_form',
            compact('clients', 'projectTypes', 'projectSettings', 'projectSettings'));
    }

    /**
     * Get project participants for the specified client
     *
     * @param $clientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParticipants($clientId, Request $request)
    {
        $clients = User::whereHas('roles', function ($query) {
            $query->where('slug', '<>', 'master')->orWhere('slug', '<>', 'administrator');
        })->with('projectsParticipating')->where('client_id', $clientId)->get();


        $admins = User::whereHas('roles', function ($query) {
            $query->where('slug', 'css')->orWhere('slug', 'recruiter')->orWhere('slug', 'tds');
        })->with('projectsParticipating')->where('id', '<>', Auth()->user()->id)->get();

        $participants = $clients->merge($admins);
        $participantsUnique = $participants->unique();
        
        return response()->json($participantsUnique);
    }


    /**
     * Create project
     *
     * @param Request $request
     * @return string
     */
    public function createProject(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_id' => 'required',
            'client_id' => 'required',
            'project_type_id' => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode(($validator->messages()));
        }


        $response = 'Error';
        $project = $this->projectRepository->create($request->toArray());
        $projectParticipants = $request->input('participants_id');

        foreach ($projectParticipants as $projectParticipant) {
            try {
                $attributes = array();

                $attributes['user_id'] = $projectParticipant;
                $attributes['project_id'] = $project->id;

                $validator = Validator::make($attributes, [
                    'user_id' => 'required',
                    'project_id' => 'required',
                ]);

                if (!$validator->passes()) {
                    return json_encode(($validator->messages()));
                }

                $this->projectParticipantRepository->create($attributes);
                $response = 'Success';
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }

        }

        try {
            if ($request->get('custom-prices')) {
                $prices = json_decode($request->get('custom-prices'), true);
                $create = [];
                foreach ($prices['create'] as $price) {
                    $price['project_id'] = $project->id;
                    $price['client_id'] = $request->get('client_id');
                    $create[] = $price;
                }

                if (!empty($create)) $this->pricesRepository->createDefaultMultiple($create);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return ajaxResponse(SUCCESS, null, $project);
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserRoleRepository $userRoleRepository
     * @return mixed
     */
    public function createClientByEmail(Request $request, UserRepository $userRepository, UserRoleRepository $userRoleRepository){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $password = $this->userRepository->generateRandomPassword();
        $request->request->add(['password' => $password]);

        $user = $userRepository->create($request->all());

        $userRoleRepository->create([
            'role_id' => Role::ROLE_CLIENT,
            'user_id' => $user->id
        ]);

        $attributes = [
            'email' => $user->email,
            'password' => $password,
        ];

        $this->emailService->sendEmail($attributes, MAIL_WELCOME);

        return ajaxResponse(SUCCESS, null, $user);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateClient($id, Request $request){

        if (!auth()->user()->canAtLeast(['project.create'])) {
            return ajaxResponse(ERROR, 'No access here!');
        }

        if (!$client = Client::find($id)){
            return ajaxResponse(ERROR, 'Client not found!');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:clients,name,'.$id,
            'billing_contract_date' => 'required',
            'billing_contract_no' => 'required',
            'billing_capital' => 'required',
            'billing_bank' => 'required',
            'billing_iban' => 'required',
            'billing_address' => 'required',
            'billing_cif' => 'required',
            'billing_registry' => 'required',
            'billing_company_name' => 'required',
        ]);

        if ($validator->fails()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $client->name = $request->get('name');
        $client->billing_contract_date = $request->get('billing_contract_date');
        $client->billing_contract_no = $request->get('billing_contract_no');
        $client->billing_capital = $request->get('billing_capital');
        $client->billing_bank = $request->get('billing_bank');
        $client->billing_iban = $request->get('billing_iban');
        $client->billing_address = $request->get('billing_address');
        $client->billing_cif = $request->get('billing_cif');
        $client->billing_registry = $request->get('billing_registry');
        $client->billing_company_name = $request->get('billing_company_name');
        $client->billing_hidden = intval($request->get('billing_hidden'));

        $client->save();

        try {
            if ($request->get('custom-prices')) {
                $prices = json_decode($request->get('custom-prices'), true);
                $create = [];
                foreach ($prices['create'] as $price) {
                    $price['client_id'] = $client->id;
                    $price['level'] = 1;
                    $create[] = $price;
                }

                if (!empty($create)) $this->pricesRepository->createDefaultMultiple($create);
                if (!empty($prices['update'])) $this->pricesRepository->insertOrUpdate($prices['update']);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return ajaxResponse(SUCCESS);

    }

    /**
     * Get projects by client id
     *
     * @param $clientId
     * @return \Illuminate\Http\JsonRespons
     */
    public function getClientProjects($clientId)
    {
        $projects = Project::where('client_id', $clientId)->get();

        return response()->json($projects);
    }


    /**
     * Get project by id
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjectById($id)
    {
        $project = Project::find($id);

        return response()->json($project);
    }

    /**
     * Update project by project id
     *
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateProject($id, Request $request)
    {

        $this->projectRepository->update($id, $request->all());

        $projectsParticipating = $this->projectParticipantRepository->getByProjectId($id);
        foreach ($projectsParticipating as $participant) {
            $participant->delete();
        }

        $project_participating_ids = $request->input('participants_id');

        foreach ($project_participating_ids as $user_id ){
            $this->projectParticipantRepository->create([
                'project_id' => $id,
                'user_id' => $user_id
            ]);
        }

        if ($request->get('custom-prices')) {

            $pricingData = json_decode($request->get('custom-prices'), true);
            if (!empty($pricingData['create'])) $this->pricesRepository->createDefaultMultiple($pricingData['create']);
            if (!empty($pricingData['update'])) $this->pricesRepository->insertOrUpdate($pricingData['update']);
        }

        return ajaxResponse(SUCCESS);

    }

    /**
     * Delete project by id
     *
     * @param $id
     * @return string
     */
    public function deleteProject($id)
    {
        $project = $this->projectRepository->getById($id);

        if (count($project->tasks) == 0) {
            return json_encode($this->projectRepository->delete($id));
        } else {
            $resType = 'This project has tasks! You cannot delete it!';
            return json_encode($resType);
        }

    }

    /**
     * Get participating projects of specified user
     *
     * @param $id
     * @return string
     */
    public function getParticipantsProjects($id, Request $request)
    {

        $projectsParticipating = $this->projectParticipantRepository->getByUserId($id);
        return json_encode($projectsParticipating);

    }

    /**
     * Get projects of specific client and where user is participant
     *
     * @param $user_id
     * @param $client_id
     * @return string
     */
    public function getProjectsParticipatings($user_id, $client_id)
    {
        $projectsParticipating = $this->projectParticipantRepository->getByUserId($user_id);
        $projects = $this->projectRepository->getByClient($client_id);
        $projectsArray = array();
        foreach ($projectsParticipating as $projectParticipating) {
            $projectsArray[$projectParticipating->project_id] = $projectParticipating->project;

        }
        unset($projectsParticipating);
        foreach ($projectsArray as $project) {
            $project['participating'] = true;
        }
        foreach ($projects as $project) {

            if (!in_array($project->id, array_keys($projectsArray))) {
                $project['participating'] = false;
                $projectsArray[$project->id] = $project;
            }
        }
        return json_encode($projectsArray);
    }

    /**
     * Update client projects participating
     *
     * @param $user_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProjectParticipants($user_id, $project_id, Request $request)
    {

        $arrayProjectsParticipating = array();
        $projectParticipatingArrayCreate = array();
        $projectsParticipating = $this->projectParticipantRepository->getByUserId($user_id);
        foreach ($projectsParticipating as $projectParticipating) {
            $arrayProjectsParticipating[$projectParticipating->project_id] = $projectParticipating;
            $projectParticipatingArrayCreate[$projectParticipating->project_id] = $projectParticipating;
        }
        unset($projectsParticipating);

        $resType = 'Error';
        $projectIds = $request->input('projects');

        if (empty($projectIds)) {
            $response = $this->projectParticipantRepository->deleteProjectParticipant($user_id);
            if ($response == 1) {
                $resType = 'Success';
            }
            return response()->json(['resType' => $resType]);
        } else {

            foreach ($projectIds as $project_participating_id) {

                if (count($projectIds) > count($arrayProjectsParticipating)) {

                    $projectParticipatingRes = $this->projectParticipantRepository->getByUserAndProjectId($user_id,
                        $project_participating_id);
                    if (empty($projectParticipatingRes)) {
                        $attributes = array();
                        $attributes['project_id'] = $project_participating_id;
                        $attributes['user_id'] = $user_id;
                        $this->projectParticipantRepository->create($attributes);
                    }

                }

                if (count($projectIds) < count($arrayProjectsParticipating)) {
                    if (in_array($project_participating_id, array_keys($arrayProjectsParticipating))) {
                        unset($arrayProjectsParticipating[$project_participating_id]);
                    }

                    foreach ($arrayProjectsParticipating as $item) {
                        $this->projectParticipantRepository->delete($item->id);
                    }
                }
            }
        }
        $resType = 'Success';
        return response()->json(['resType' => $resType]);

    }

}