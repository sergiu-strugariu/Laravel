<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Client;
use App\Models\Language;
use App\Models\LanguagePaperTypes;
use App\Models\Paper;
use App\Models\Log;
use App\Models\PaperReport;
use App\Models\PaperType;
use App\Models\Project;
use App\Models\ProjectParticipant;
use App\Models\ProjectTypes;
use App\Models\PricingType;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Task;
use App\Models\TaskFollower;
use App\Models\TaskStatus;
use App\Models\TaskUpdate;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\PaperRepository;
use App\Repositories\PaperTypeRepository;
use App\Repositories\PricesRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ReferenceRepository;
use App\Repositories\TaskFollowersRepository;
use App\Repositories\TaskRepository;
use App\Repositories\TaskStatusRepository;
use App\Repositories\UserRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\PaperReportRepository;
use App\Repositories\PaperAnswerRepository;
use App\Services\EmailService;
use App\Services\EventService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Spatie\CalendarLinks\Link;
use View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel as Excel;
use App\Services\ExcelServiceInterface;
use Knp\Snappy\Pdf;
use Jenssegers\Agent\Agent;

class TaskController extends Controller
{
    private $taskRepository;
    private $languageRepository;
    private $userRepository;
    private $taskStatusRepository;
    private $taskFollowerRepository;
    private $paperTypeRepository;
    private $groupRepository;
    private $paperRepository;
    private $attachmentRepository;
    private $projectRepository;
    private $referenceRepository;
    private $paperReportRepository;
    private $paperAnswerRepository;
    private $pricesRepository;
    private $emailService;
    private $excelService;
    private $pdf;
    private $eventService;
    private $skilsAssessments = [
        'grades' => ['Pre-A1', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'],
        'speaking-grades' => ['Pre-A1', 'A1', 'A2', 'A2+', 'B1', 'B1+', 'B2', 'B2+', 'C1', 'C2'],
        'writing-grades' => ['Pre-A1', 'A1', 'A2', 'A2+', 'B1', 'B1+', 'B2', 'B2+', 'C1', 'C2'],
        'speaking' => [
            ['name' => 'interaction', 'title' => 'Interaction'],
            ['name' => 'phonology', 'title' => 'Phonology'],
            ['name' => 'range', 'title' => 'Range'],
            ['name' => 'fluency', 'title' => 'Fluency'],
            ['name' => 'accuracy', 'title' => 'Accuracy'],
            ['name' => 'coherence', 'title' => 'Coherence'],
        ],
        'writing' => [
            ['name' => 'argument', 'title' => 'Argument'],
            ['name' => 'coherence', 'title' => 'Coherence'],
            ['name' => 'description', 'title' => 'Description'],
            ['name' => 'accuracy', 'title' => 'Accuracy'],
            ['name' => 'range', 'title' => 'Range'],
        ],
        'accents' => [
            'Strong Mother tongue influence' => 'Strong Mother tongue influence',
            'Strong influence of another language' => 'Strong influence of another language',
            'Slight Mother tongue influence' => 'Slight Mother tongue influence',
            'Slight influence of another language' => 'Slight influence of another language',
            // [LoW] Only the label has changed in order for us to not have to change all of the entries in the database.
            'No Mother tongue influence (neutral accent)' => 'No influence from other languages (neutral accent)',
            'Close to native accent' => 'Close to native accent',
            'Native accent' => 'Native accent',
        ],
    ];

    /**
     * TaskController constructor.
     *
     * @param \App\Repositories\TaskRepository $taskRepository
     * @param \App\Repositories\TaskStatusRepository $taskStatusRepository
     * @param \App\Repositories\LanguageRepository $languageRepository
     * @param \App\Repositories\UserRepository $userRepository
     * @param \App\Repositories\TaskFollowersRepository $taskFollowerRepository
     * @param \App\Repositories\PaperTypeRepository $paperTypeRepository
     * @param \App\Repositories\GroupRepository $groupRepository
     * @param \App\Repositories\PaperRepository $paperRepository
     * @param \App\Repositories\AttachmentRepository $attachmentRepository
     * @param \App\Repositories\ProjectRepository $projectRepository
     * @param \App\Repositories\ReferenceRepository $referenceRepository
     * @param \App\Repositories\PaperReportRepository $paperReportRepository
     * @param \App\Repositories\PaperAnswerRepository $paperAnswerRepository
     * @param EmailService $emailService
     * @param ExcelServiceInterface $excelService
     * @param Pdf $pdf
     * @param EventService $eventService
     */
    public function __construct(
        TaskRepository $taskRepository,
        TaskStatusRepository $taskStatusRepository,
        LanguageRepository $languageRepository,
        UserRepository $userRepository,
        TaskFollowersRepository $taskFollowerRepository,
        PaperTypeRepository $paperTypeRepository,
        GroupRepository $groupRepository,
        PaperRepository $paperRepository,
        AttachmentRepository $attachmentRepository,
        ProjectRepository $projectRepository,
        ReferenceRepository $referenceRepository,
        PaperReportRepository $paperReportRepository,
        PaperAnswerRepository $paperAnswerRepository,
        PricesRepository $pricesRepository,
        EmailService $emailService,
        ExcelServiceInterface $excelService,
        Pdf $pdf,
        EventService $eventService
    )
    {
        $this->taskRepository = $taskRepository;
        $this->languageRepository = $languageRepository;
        $this->userRepository = $userRepository;
        $this->taskStatusRepository = $taskStatusRepository;
        $this->taskFollowerRepository = $taskFollowerRepository;
        $this->paperTypeRepository = $paperTypeRepository;
        $this->groupRepository = $groupRepository;
        $this->paperRepository = $paperRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->projectRepository = $projectRepository;
        $this->referenceRepository = $referenceRepository;
        $this->paperReportRepository = $paperReportRepository;
        $this->paperAnswerRepository = $paperAnswerRepository;
        $this->pricesRepository = $pricesRepository;
        $this->emailService = $emailService;
        $this->excelService = $excelService;
        $this->pdf = $pdf;
        $this->eventService = $eventService;

        $this->middleware('hasResourceAccess')->only([
            'getTaskPage',
            'index'
        ]);

    }

    /**
     * Display a listing of tasks.
     *
     * @param Request $request
     * @param Project|null $project
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Project $project = null)
    {
        $client = null;
        if (!$project) {
            /*
            $projects = Project::with([
                'tasks',
                'participants' => function ($q) {
                    $q->whereHas('user')->with('user');
                }
            ])->get();
            foreach ($projects as $project) {
                foreach ($project->tasks as $task) {
                    $tasks[$task->name] = $task->name;
                }
                foreach ($project->participants as $participant) {
                    $projectParticipants[$participant->user->full_name] = $participant->user->id;
                }
            }*/
            $tasks = [];
            $projectParticipants = [];
            $project = null;
        } else {
            $tasks = $project->tasks()->get()->pluck('name', 'name');
            $projectParticipants = $project->participants()->whereHas('user')->with('user')->get()->pluck('user.full_name',
                'user.id');

            $client = Client::find($project->client_id);
        }

        $languages = Language::whereHas('language_paper_type')->pluck('name', 'id');
        $taskStatuses = $this->taskStatusRepository->getAll()->pluck('name', 'id');
        $paperTypes = $this->paperTypeRepository->getAll()->pluck('name', 'id');
        $viewAssesorPermission = auth()->user()->canAtLeast(['assessor.view_name']);
        $assessors = $this->userRepository->getByRole('assessor');
        $allProjects = $this->projectRepository->search([])->orderBy('name', 'asc')->get();
        $addedByUsers = $this->userRepository->getByRole('master')->toArray() +
            $this->userRepository->getByRole('administrator')->toArray() +
            $this->userRepository->getByRole('client')->toArray();

        $permanentFilters = [];
        if ($request->has('project_type')) {
            $permanentFilters[] = 'project_type=' . $request->get('project_type');
        }
        if ($request->has('all')) {
            $permanentFilters[] = 'all=' . $request->get('all');
        }

        if ($request->has('all') && $request->get('all') == 'active') {
            unset($taskStatuses[DONE]);
            unset($taskStatuses[ISSUE]);
            unset($taskStatuses[CANCELED]);
            unset($taskStatuses[ARCHIVED]);
        }
        $permanentFilters = implode('&', $permanentFilters);

        $projectTypeName = false;
        if ($request->has('project_type')) {
            $permanentFilters[] = 'project_type=' . $request->get('project_type');
            $projectTypeName = ProjectTypes::find($request->get('project_type'))->name;
        }

        $route = isset($project) ? $project->id . '/task' : 'all/tasks';
        return view('task/index', [
            'project' => $project,
            'allProjects' => $allProjects,
            'addedByUsers' => $addedByUsers,
            'languages' => $languages,
            'assessors' => $assessors,
            'tasks' => $tasks,
            'taskStatuses' => $taskStatuses,
            'paperTypes' => $paperTypes,
            'projectParticipants' => $projectParticipants,
            'viewAssesorPermission' => $viewAssesorPermission,
            'global_search' => $request->input('q'),
            'all_tasks' => $request->input('all'),
            'project_type' => $request->get('project_type'),
            'permanentFilters' => $permanentFilters,
            'projectTypeName' => $projectTypeName,
            'request_all' => $request->all(),
            'route' => $route,
            'sidebarCollapsed' => true,
            'hidePrices' => $client ? $client->billing_hidden : false,
        ]);
    }

    /**
     * Display a listing of tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function tasks(Request $request)
    {
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');

        $taskStatuses = $this->taskStatusRepository->getAll()->pluck('name', 'id');
        $paperTypes = $this->paperTypeRepository->getAll()->pluck('name', 'id');

        $assessors = $this->userRepository->getByRole('assessor');
        $projectParticipants = ProjectParticipant::with('user')->get()->pluck('user.full_name', 'user.id');

        $viewAssesorPermission = auth()->user()->canAtLeast(['assessor.view_name']);

        $allProjects = $this->projectRepository->search([])->orderBy('name', 'asc')->get();
        $addedByUsers = $this->userRepository->getByRole('master')->toArray() +
            $this->userRepository->getByRole('administrator')->toArray() +
            $this->userRepository->getByRole('client')->toArray();

        $project = null;
        $route = isset($project) ? $project->id . '/task' : 'all/tasks';

        $permanentFilters = [];
        $projectTypeName = false;
        if ($request->has('project_type')) {
            $permanentFilters[] = 'project_type=' . $request->get('project_type');
            $projectTypeName = ProjectTypes::find($request->get('project_type'))->name;
        }
        if ($request->has('all')) {
            $permanentFilters[] = 'all=' . $request->get('all');
        }

        if ($request->has('all') && $request->get('all') == 'active') {
            unset($taskStatuses[DONE]);
            unset($taskStatuses[ISSUE]);
            unset($taskStatuses[CANCELED]);
            unset($taskStatuses[ARCHIVED]);
        }

        $permanentFilters = implode('&', $permanentFilters);

        return view('task/index', [
            'project' => $project,
            'allProjects' => $allProjects,
            'addedByUsers' => $addedByUsers,
            'languages' => $languages,
            'assessors' => $assessors,
            'taskStatuses' => $taskStatuses,
            'paperTypes' => $paperTypes,
            'projectParticipants' => $projectParticipants,
            'viewAssesorPermission' => $viewAssesorPermission,
            'global_search' => $request->input('q'),
            'all_tasks' => $request->input('all'),
            'project_type' => $request->get('project_type'),
            'route' => $route,
            'permanentFilters' => $permanentFilters,
            'request_all' => $request->all(),
            'projectTypeName' => $projectTypeName,
            'sidebarCollapsed' => true,
            "hidePrices" => false,
        ]);
    }

    /**
     * Ajax function to populate data table.
     * @param Request $request
     * @param Project $project
     * @return
     */
    public function getTableData(Request $request, Project $project)
    {
        $filters = $request->input('filters');
        if ($request->has('project_type')) {
            $filters['project_type'] = $request->get('project_type');

        }
        $filters['status'] = 'all';
        if ($request->has('all')) {
            $filters['status'] = $request->get('all');

        }
        if (empty($request->input('filters.global_search')) && empty($request->input('filters.all_tasks')) || $project) {
            return DataTables::of($this->taskRepository->search($project, $filters))->make(true);
        } else {
            return DataTables::of($this->taskRepository->globalSearch($filters))->make(true);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getTableDataSearch(Request $request)
    {
        $filters = $request->input('filters');
        if ($request->has('project_type')) {
            $filters['project_type'] = $request->get('project_type');

        }
        if ($request->has('all')) {
            $filters['status'] = $request->get('all');

        }
//        $q = $this->taskRepository->globalSearch($filters);
//        return getSql($q);
        return DataTables::of($this->taskRepository->globalSearch($filters))->make(true);
    }

    /**
     * Ajax - delete batch
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function deleteBatch(Request $request)
    {
        if (!Auth::user()->hasRole(['administrator', 'master', 'client', 'css', 'recruiter'])) {
            return ajaxResponse(ERROR, 'Sorry, no access here!');
        }

        $taskIds = json_decode($request->get('task_ids'), true);
        $tasks = Task::with(['papers', 'papers.type'])->whereIn('id', $taskIds)->get();

        if (Auth::user()->hasRole('client')) {
            foreach ($tasks as $task) {
                if ($task->task_status_id != TaskStatus::STATUS_ALLOCATED) {
                    ### deny action
                    return ajaxResponse(ERROR, 'You can cancel a task only when the status is Allocated');
                }
            }
        }

        if (!empty($taskIds)) {
            Task::whereIn('id', $taskIds)->delete();

            foreach ($tasks as $task) {

                $paperTypes = $task->papers->pluck('type.id')->toArray();
                if (in_array(TEST_SPEAKING, $paperTypes) && $task->assessor) {

                    ### 11. Assessment cancelled
                    ### Send email to assessor
                    $this->emailService->sendEmail([
                        'email' => $task->assessor->email,
                        'name' => $task->name
                    ], MAIL_ASSESSMENT_CANCELED);

                }
            }
        }

        return ajaxResponse(SUCCESS);

    }

    /**
     * Ajax - update batch
     * @param Request $request
     * @return mixed
     */
    public function updateBatch(Request $request)
    {
        if (!Auth::user()->hasRole(['administrator', 'master', 'client', 'css', 'recruiter'])) {
            return ajaxResponse(ERROR, 'Sorry, no access here!');
        }

        $rules = [
            'task_ids' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $taskIds = json_decode($request->get('task_ids'), true);

        $updateFields = array_filter($request->only([
            'assessor_id',
            'bill_client',
            'additional_cost',
            'pay_assessor',
            'task_status_id',
            'language_id',
            'tests'
        ]),
            function ($val) {
                return $val !== null;
            });

        if (!empty($updateFields)) {

            $allTasks = Task::whereIn('id', $taskIds);
            unset($updateFields['tests']);
            $allTasks->update($updateFields);

            ### STATUS
            if (isset($updateFields['task_status_id']) && $updateFields['task_status_id'] == TaskStatus::STATUS_CANCELED) {
                $tasks = Task::with([
                    'assessor',
                    'papers',
                    'papers.type',
                    'project',
                    'project.owner',
                    'language'
                ])->whereIn('id',
                    $taskIds)->get();
                foreach ($tasks as $task) {

                    $paperTypes = $task->papers->pluck('type.id')->toArray();
                    if (in_array(TEST_SPEAKING, $paperTypes) && $task->assessor) {

                        ### 11. Assessment cancelled
                        ### Send email to assessor
                        $this->emailService->sendEmail([
                            'email' => $task->assessor->email,
                            'name' => $task->name
                        ], MAIL_ASSESSMENT_CANCELED);

                    }
                }
            }

            ### LANGUAGE
            if (isset($updateFields['language_id'])) {
                $oldTasks = $allTasks->get()->keyBy('id');
                $tasks = Task::with(['assessor'])->whereIn('id', $taskIds)->get();
                foreach ($tasks as $task) {
                    $this->eventService->handleTaskLanguageChange($task, $oldTasks[$task->id]);
                }
            }

            ### TEST TYPE
            if ($request->has('tests') && !empty($request->get('tests'))) {
                $tasks = $allTasks->get();
                foreach ($tasks as $task) {
                    $this->eventService->handleTaskTestAdd($task, $request->get('tests'));
                }
            }

            ### ASSESSOR
            if (isset($updateFields['assessor_id'])) {
                $tasks = Task::with(['papers', 'papers.type', 'project', 'project.owner', 'language'])->whereIn('id',
                    $taskIds)->get();
                $assessor = $this->userRepository->getById($request->assessor_id);
                foreach ($tasks as $task) {

                    //check task needs assessor
                    $testTypes = $task->papers->pluck('paper_type_id')->toArray();
                    if (in_array(TEST_SPEAKING, $testTypes)) {
                        $this->emailService->sendAssessorMail($assessor, $task);
                    }
                }
            }
        }

        return ajaxResponse(SUCCESS);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getBatchTestTypes(Request $request)
    {
        $ids = $request->get('ids');

        if (empty($ids)) {
            return ajaxResponse(ERROR);
        }

        $language_ids = Task::whereIn('id', $ids)->pluck('language_id')->toArray();
        $tests = LanguagePaperTypes::with('paperTypes')->whereIn('language_id', $language_ids)->get()->pluck('paperTypes.name', 'paperTypes.id')->toArray();

        return ajaxResponse(SUCCESS, null, ['test_types' => $tests]);
    }

    /**
     * Generate create form for Ajax request.
     */
    public function createForm(Project $project)
    {
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');
        $assessors = $this->userRepository->getAll()->pluck('full_name_email', 'id');
        $paperTypes = $this->paperTypeRepository->getAll()->pluck('name', 'id');

        return view('task/_form', [
            'project' => $project,
            'languages' => $languages,
            'assessors' => $assessors,
            'paperTypes' => $paperTypes,
        ])->render();
    }

    /**
     * Create a new task.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Project $project
     * @return array
     */
    public function create(Request $request, Project $project)
    {
        $rules = [
            'name' => 'required',
            'email' => 'email|required',
            'phone' => 'required',
            'languages' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return response()->json(['resMotive' => $validator->messages()]);
        }

        $outputLog = '';

        $languages = $request->input('languages');
        $params = $request->toArray();
        foreach ($languages as $language) {
            if (empty($params['languagesExtra'][$language]['PaperTypes'])) {
                return response()->json(['resMotive' => [['You need to select at least one test for each language.']]]);
            }
        }

        $outputLog .= 'POST: ' . var_export($params, true) . "\n";
        $outputLog .= 'User: ' . var_export(auth()->user()->attributesToArray(), true) . "\n";

        $projectId = $project->id;
        $clientId = $project->client_id;
        $prices = $this->pricesRepository->getClientPricesGrouped($clientId, $projectId);

        foreach ($languages as $language) {

            $params = array_map(function ($elem) {
                return is_string($elem) ? trim($elem) : $elem;
            }, $params);

            $params['language_id'] = $language;
            $params['added_by_id'] = \Auth::id();
            $params['phone'] = preg_replace("/[^0-9]/", "", str_replace('+', '00', $params['phone']));

            if (isset($params['assessor_id'])) {
                unset($params['assessor_id']);
            }

            if (isset($params['languagesExtra'][$language]['has_custom_period']) && $params['languagesExtra'][$language]['has_custom_period'] === "on") {
                if (!empty($prices[$language]) && !empty($prices[$language][PricingType::CUSTOM_PERIOD_SPEAKING])) {
                    $params['custom_period'] = 1;
                    $params['custom_period_cost'] = $prices[$language][PricingType::CUSTOM_PERIOD_SPEAKING]['price'];
                }
            }

            $testTypes = array_keys($params['languagesExtra'][$language]['PaperTypes']);
            $taskRequiresAssessor = false;
            $assessor = null;

            $native = isset($params['languagesExtra'][$language]['native']) && $params['languagesExtra'][$language]['native'] == 1;

            // if this param is received, do not assign assessor
            $forceNoAssessor = isset($params['no_assessor']) && $params['no_assessor'] === 'true'; //string compare


            ### set availability interval
            $params['deadline'] = isset($params['languagesExtra'][$language]['deadline']) ? date('Y-m-d H:i:s',
                strtotime($params['languagesExtra'][$language]['deadline'])) : null;

            ### if it has speaking or writing test to take
            if ((in_array(TEST_SPEAKING, $testTypes) || in_array(TEST_WRITING, $testTypes)) && ((isset($params['no_assessor']) && $params['no_assessor'] === 'false') || !isset($params['no_assessor']))) {

                $taskRequiresAssessor = true;

                ### @TODO: add random assessor
                ### check assessor
                if (isset($params['languagesExtra'][$language]['assessor_id']) && $params['languagesExtra'][$language]['assessor_id']) {
                    $params['assessor_id'] = $params['languagesExtra'][$language]['assessor_id'];
                    $assessor = $this->userRepository->getById($params['assessor_id']);
                } else {

                    $user_id_param = 0;
                    if (isset($params['previous_task_id'])) {
                        $prevTask = $this->taskRepository->getById($params['previous_task_id']);
                        $user_id_param = $prevTask->assessor_id;
                    }

                    $assessor = $this->groupRepository->getRandomAssessor(intval($language), $user_id_param, $native, $outputLog, $params['deadline']);
                    $params['assessor_id'] = empty($assessor) ? null : $assessor->id;
                }

            } else {
                $params['assessor_id'] = null;
            }

            $availability_date = isset($params['languagesExtra'][$language]['availability_from']) ?
                date('Y-m-d', strtotime($params['languagesExtra'][$language]['availability_from'])) : null;

            $params['availability_from'] = isset($params['languagesExtra'][$language]['from_date']) ?
                date($availability_date . ' H:i:s',
                    strtotime($params['languagesExtra'][$language]['from_date'])) : null;
            $params['availability_to'] = isset($params['languagesExtra'][$language]['to_date']) ?
                date($availability_date . ' H:i:s', strtotime($params['languagesExtra'][$language]['to_date'])) : null;

            if ($availability_date != null && !isset($params['languagesExtra'][$language]['from_date'])) {
                $params['availability_from'] = Carbon::parse($availability_date)->addHours(9);
                $params['availability_to'] = Carbon::parse($availability_date)->addHours(18);
            }

            if (isset($native)) {
                $params['native'] = intval($native);
            }

            $params['task_status_id'] = TaskStatus::STATUS_ALLOCATED;

            if ($taskRequiresAssessor && $assessor == null) {
                $params['task_status_id'] = TaskStatus::STATUS_ISSUE;
            }

            $params['bill_client'] = $project->default_bill_client;
            $params['pay_assessor'] = $project->default_pay_assessor;

            $outputLog .= '$params: ' . var_export($params, true) . "\n";
            $outputLog .= '$taskRequiresAssessor: ' . var_export($taskRequiresAssessor, true) . "\n";
            $outputLog .= '$forceNoAssessor: ' . var_export($forceNoAssessor, true) . "\n";
            $outputLog .= '$testTypes: ' . var_export($testTypes, true) . "\n";

            $task = $this->taskRepository->create($params);

            $outputLog .= '$task: ' . var_export($task, true) . "\n";

            if (isset($params['send_mail_admin']) && $params['send_mail_admin'] == 'true') {
                $verifyTask = $this->taskRepository->getById($params['previous_task_id']);
                $outputLog .= '$verifyTask: ' . var_export($verifyTask, true) . "\n";
                $this->userRepository->sendTaskMailToAdmins($this->emailService, $task, $verifyTask, $params);
                addLog([
                    'type' => MAIL_SENT,
                    'task_id' => $task->id,
                    'description' => 'Mail sent to admin because the test taker was already tested on that language'
                ]);
            }

            ### taks activity log
            if (!empty($task->availability_from) AND !empty($task->availability_to)) {
                addLog([
                    'type' => TASK_UPDATE,
                    'description' => 'Scheduled for ' .
                        date('d M Y', strtotime($task->availability_from)) . ' from ' .
                        date('H:i', strtotime($task->availability_from)) . ' to ' .
                        date('H:i', strtotime($task->availability_to)),
                    'task_id' => $task->id,
                    'user_id' => auth()->user()->id
                ]);
            }

            ### create papers foreach test type
            $params['languagesExtra'][$language]['PaperTypes'] = isset($params['languagesExtra'][$language]['PaperTypes']) ? $params['languagesExtra'][$language]['PaperTypes'] : array();
            foreach ($params['languagesExtra'][$language]['PaperTypes'] as $key => $paperType) {
                $cost = "0.00";

                if ($key == TEST_SPEAKING && $native) {
                    $pricingTypeId = PricingType::SPEAKING_NATIVE;
                } else if ($key == TEST_WRITING && $native) {
                    $pricingTypeId = PricingType::WRITING_NATIVE;
                } else {
                    $pricingTypeId = $this->pricesRepository->getTestTypeByPaperType($key);
                }

                if (!empty($prices[$language]) && !empty($prices[$language][$pricingTypeId])) {
                    $cost = $prices[$language][$pricingTypeId]['price'];
                }

                if ($cost == "0.00") {
                    continue;
                }

                $this->paperRepository->createOrSkip([
                    'paper_type_id' => $key,
                    'task_id' => $task->id,
                    'cost' => $cost
                ]);
            }

            $task = Task::with('project', 'project.owner', 'language', 'papers.type')->where('id', $task->id)->first();

            ### if has assessor
            if ($taskRequiresAssessor && !$forceNoAssessor) {

                if (isset($assessor) && $assessor !== null) {
                    ### send mail to assessor
                    $this->emailService->sendAssessorMail($assessor, $task);

                    addAssessorHistory($task->id, $assessor->id, 'create');

                    addLog([
                        'type' => MAIL_SENT,
                        'user_id' => $assessor->id,
                        'task_id' => $task->id,
                        'description' => 'Mail sent to assessor for creating new task'
                    ]);

                } else { ### language group doesn't have any assessors

                    $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService, $task, $params);
                    addLog([
                        'type' => MAIL_SENT,
                        'task_id' => $task->id,
                        'description' => 'Mail sent to admin because group is empty'
                    ]);

                }
            }

            $testIds = $task->papers->pluck('paper_type_id')->toArray();
            if (count($testIds) == 1 && reset($testIds) == TEST_SPEAKING) {
                $link = false;
            } else {
                $link = url('test/instructions/' . $task->link);
            }

            $deadline = isset($task->deadline) ? Carbon::parse($task->deadline)->format('d M Y, H:i') : null;

            ###  Send email to test taker
            $mailSent = $this->emailService->sendEmail([
                'email' => $task->email,
                'name' => $task->name,
                'link' => $link,
                'company' => $task->project->owner->name,
                'language' => $task->language->name,
                'deadline' => $deadline,
                'language_use_new_link' => (string) View::make('emails.partials.button', ['text' => 'Take Language Use Test', 'href' => $link . "/1"]),
                'speaking_link' => (string) View::make('emails.partials.button', ['text' => 'Take Speaking Test', 'href' => $link . "/2"]),
                'writing_link' => (string) View::make('emails.partials.button', ['text' => 'Take Writing Test', 'href' => $link . "/3"]),
                'listening_link' => (string) View::make('emails.partials.button', ['text' => 'Take Listening Test', 'href' => $link . "/4"]),
                'reading_link' => (string) View::make('emails.partials.button', ['text' => 'Take Reading Test', 'href' => $link . "/5"]),
                'language_use_link' => (string) View::make('emails.partials.button', ['text' => 'Take Language Use Test', 'href' => $link . "/6"]),
                'testList' => [
                    'language_use_new' => in_array(TEST_LANGUAGE_USE_NEW, $testIds),
                    'speaking' => in_array(TEST_SPEAKING, $testIds),
                    'writing' => in_array(TEST_WRITING, $testIds),
                    'listening' => in_array(TEST_LISTENING, $testIds),
                    'reading' => in_array(TEST_READING, $testIds),
                    'language_use' => in_array(TEST_LANGUAGE_USE, $testIds),
                    'online' => !!$deadline,
                ]
            ], MAIL_TEST_TAKE_MULTIPLE);

            if ($mailSent) {
                addLog([
                    'type' => MAIL_SENT,
                    'task_id' => $task->id,
                    'description' => 'Mail sent to test taker for creating new task'
                ]);
            }


            ### check has followers
            if (isset($params['followers'])) {
                foreach ($params['followers'] as $follower) {

                    $followerModel = $this->taskFollowerRepository->createOrSkip([
                        'user_id' => $follower,
                        'task_id' => $task->id,
                    ]);

                }
            }

            $logFilename = date('Y-m-d-H-i-s') . ' - task id ' . $task->id . '.txt';
            file_put_contents(storage_path() . DIRECTORY_SEPARATOR . $logFilename, $outputLog);

            ### upload attachment
            if (isset($params['attachment'])) {
                $this->attachmentRepository->createTaskAttachment($task, request()->attachment);
            }
        }


        return response()->json(['resType' => 'Success']);
    }

    /**
     * Display the specified task.
     *
     * @param  Project $project
     * @param  int $id
     * @return Task
     */
    public function view(Project $project, $id)
    {
        return $this->taskRepository->getById($id);
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updateForm(Project $project, $id)
    {
        $task = $this->taskRepository->getById($id);
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');
        $paperTypes = $this->paperTypeRepository->getAll()->pluck('name', 'id');
        $assessors = $this->groupRepository->getAssessorsFromLanguageGroup($task->language_id);
        $papers = $this->paperRepository->getAllTaskPapers($task->id);

        return view('task/_form', [
            'project' => $project,
            'task' => $task,
            'languages' => $languages,
            'assessors' => empty($assessors) ? array() : $assessors->pluck('full_name_email', 'id'),
            'paperTypes' => $paperTypes,
            'papers' => empty($papers) ? array() : $papers->pluck('id', 'paper_type_id'),
        ])->render();
    }

    /**
     * Generate update form data for Ajax request.
     */
    public function updateFormData(Project $project, $id)
    {
        $task = $this->taskRepository->getById($id);
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');
        $paperTypes = $this->paperTypeRepository->getAll()->pluck('name', 'id');
        $assessors = $this->groupRepository->getAssessorsFromLanguageGroup($task->language_id);
        $nativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative($task->language_id, 1);
        $assessorsIds = empty($assessors) ? array() : $assessors->pluck('full_name_email', 'id');
        $nativeAssessorsIds = empty($nativeAssessors) ? array() : $nativeAssessors->pluck('id');

        $papers = $this->paperRepository->getAllTaskPapers($task->id);

        $task->deadline = empty($task->deadline) ? null : date("m/d/Y h:i a", strtotime($task->deadline));
        $task->from_date = empty($task->availability_from) ? null : date("H:i", strtotime($task->availability_from));
        $task->to_date = empty($task->availability_to) ? null : date("H:i", strtotime($task->availability_to));
        $task->availability_from = empty($task->availability_from) ? null : date("m/d/Y",
            strtotime($task->availability_from));
        $task->availability_to = empty($task->availability_to) ? null : date("m/d/Y",
            strtotime($task->availability_to));
        $attachments = $task->attachments();
        $followers = $task->followers()->with('user')->get()->pluck('user.id');

        $notNativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative($task->language_id, false);
        $nativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative($task->language_id, true);

        $taskUpdatesUsersCollection = $task->taskUpdates()->pluck('user');
        $taskUpdatesUsers = []; //$taskUpdates->pluck('user.full_name', 'user.id');

        foreach ($taskUpdatesUsersCollection as $user) {
            if(is_null($user)) {
                continue;
            }
            $name = $user->full_name;
            if (auth()->user()->hasRole('client') && $user->hasRole('assessor')) {
                $name = 'Assessor';
            }
            $taskUpdatesUsers[$user->id] = $name;
        }

        $taskUpdatesUsers['null'] = 'System';

        $response = [
            'project' => $project,
            'task' => $task,
            'followers' => $followers,
            'languages' => $languages,
            'assessors' => $assessorsIds,
            'nativeAssessorsIds' => $nativeAssessorsIds,
            'nativeButton' => !(!$notNativeAssessors->count() || !$nativeAssessors->count()),
            'nativeDisabled' => !$notNativeAssessors->count() || !$nativeAssessors->count(),
            'nativeState' => $nativeAssessors->count() && !$notNativeAssessors->count(),
            'paperTypes' => $paperTypes,
            'papers' => empty($papers) ? array() : $papers->pluck('id', 'paper_type_id'),
            'attachments' => $attachments,
            'taskUpdates' => $task->taskUpdates(),
            'taskUpdatesUsers' => $taskUpdatesUsers,
        ];

        return json_encode($response);
    }

    /**
     * Update the specified task.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return array
     */
    public function update(Request $request, Project $project, $id)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'languages' => 'required',
            'PaperTypes' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return response()->json(['resMotive' => $validator->messages()]);
        }

        $task = Task::with('project', 'project.owner', 'language')->where('id', $id)->first();
        $languages = $request->input('languages');
        if (($task->email != $request->input('email'))) {
            $this->resendMail($task, $request->input('email')); //to test taker
        }


        ### 9. Phone number/skype ID updated
        ### Send email to assessor
        if (($task->phone != $request->phone || $task->skype != $request->skype) && $task->assessor) {
            $this->emailService->sendEmail([
                'email' => $task->assessor->email,
                'name' => $task->name,
                'skype' => $task->skype != $request->skype ? $request->skype : false,
                'phone' => $task->phone != $request->phone ? $request->phone : false,
                'link' => url('task/' . $task->id)
            ], MAIL_CONTACTS_UPDATED);
        }


        foreach ($languages as $language) {
            $params = $request->toArray();
            $params['language_id'] = $language;
            $params['phone'] = preg_replace("/[^0-9]/", "", str_replace('+', '00', $params['phone']));
            $params['deadline'] = isset($params['deadline']) ? date('Y-m-d H:i:s',
                strtotime($params['deadline'])) : null;

            if (isset($params['availability_from'])) {
                $date_from = date('Y-m-d H:i:s', strtotime($params['availability_from'] . ' ' . $params['from_date']));
                $date_to = date('Y-m-d H:i:s', strtotime($params['availability_from'] . ' ' . $params['to_date']));

                $params['availability_from'] = $date_from;
                $params['availability_to'] = $date_to;
            }

            $task = Task::with('project', 'project.owner', 'language', 'papers.type')->where('id', $id)->first();

            if (isset($params['native'])) {
                $params['native'] = 1;
                $native = true;
            } else {
                $native = false;
                $params['native'] = 0;
            }


            if (isset($params['assessor_id']) && !empty($params['assessor_id'])) {
                // assessor was changed
                if ($task->assessor_id != intval($params['assessor_id'])) {
                    $newAssessor = $this->userRepository->getById(intval($params['assessor_id']));

                    ### Send mail to new assessor
                    $this->emailService->sendAssessorMail($newAssessor, $task);

                    if ($task->assessor) {
                        ### 11. Assessment cancelled
                        ### Send email to old assessor
                        $this->emailService->sendEmail([
                            'email' => $task->assessor->email,
                            'name' => $task->name
                        ], MAIL_ASSESSMENT_CANCELED);
                        $description = 'Task assessor was changed from ' . $task->assessor->full_name . ' to ' . $newAssessor->full_name;
                    } else {
                        $description = 'Task assessor was set to ' . $newAssessor->full_name;
                    }

                    addAssessorHistory($task->id, $newAssessor->id, 'manual');

                    addLog([
                        'type' => TASK_HISTORY,
                        'description' => $description,
                        'task_id' => $task->id,
                        'user_id' => auth()->user()->id
                    ]);

                }
            } else {
                $assessor = $this->groupRepository->getRandomAssessor(intval($language), $task->assessor_id, $native);
                if (!empty($assessor)) {
                    $params['assessor_id'] = $assessor->id;
                    $this->emailService->sendAssessorMail($assessor, $task);
                } else {
                    // language group doesn't have any assessors
                    $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService, $task, $params);
                }

                ### is assessor was changed or no assessor was found
                ### send to old assessor
                if ($task->assessor && (empty($assessor) || (!empty($assessor) && $task->assessor_id != $assessor->id))) {
                    ### 11. Assessment cancelled
                    ### Send email to old assessor
                    $this->emailService->sendEmail([
                        'email' => $task->assessor->email,
                        'name' => $task->name
                    ], MAIL_ASSESSMENT_CANCELED);
                }
            }

            $this->taskRepository->update($params['task_id'], $params);

            $papers = $this->paperRepository->getAllTaskPapers($id);

            $params['PaperTypes'] = isset($params['PaperTypes']) ? $params['PaperTypes'] : array();

            $removedPaper = false;

            foreach ($papers as $paper) {
                if (!array_key_exists($paper->paper_type_id, $params['PaperTypes'])) {
                    $this->paperRepository->delete($paper->id);
                    $removedPaper = true;
                }
            }

            foreach ($params['PaperTypes'] as $key => $paperType) {
                $this->paperRepository->createOrSkip([
                    'paper_type_id' => $key,
                    'task_id' => $id,
                ]);
            }

            ### get all un done papers
            $allPapers = Paper::selectRaw('done, COUNT(*) as total')
                ->where('task_id', $task->id)
                ->groupBy('done')
                ->pluck('total', 'done')
                ->all();

            $totalDone = isset($allPapers[1]) ? $allPapers[1] : 0;
            $totalUndone = isset($allPapers[0]) ? $allPapers[0] : 0;

//            ### check user has unfinished tests
//            if ($totalUndone > 0) {
//                $this->resendMail($task); //to test taker
//            }

            ### check if user removed some paper type
            ### and all papers are done
            if ($removedPaper && $totalDone == count($params['PaperTypes'])) {
                ### set status of task = done
                $this->taskRepository->update($task->id, ['task_status_id' => TaskStatus::STATUS_DONE]);
            }

            $task = $this->taskRepository->getById($id);
            $followers = $task->followers()->get();

            foreach ($followers as $follower) {
                if (isset($params['followers'])) {
                    if (!in_array($follower->user_id, $params['followers'])) {
                        $this->taskFollowerRepository->deleteFollowerFromTask([
                            'user_id' => $follower->user_id,
                            'task_id' => $task->id,
                        ]);

                        $this->emailService->sendTaskMailToFollower($task, $follower->user, 'removed_task_follower',
                            $params);
                    }
                } else {
                    $this->taskFollowerRepository->deleteFollowerFromTask([
                        'user_id' => $follower->user_id,
                        'task_id' => $task->id,
                    ]);

                    $this->emailService->sendTaskMailToFollower($task, $follower->user, 'removed_task_follower',
                        $params);
                }
            }

            if (isset($params['followers'])) {
                foreach ($params['followers'] as $follower) {
                    if (empty($this->taskFollowerRepository->getByUserAndTask([
                        'user_id' => $follower,
                        'task_id' => $task->id,
                    ]))
                    ) {
                        $model = $this->taskFollowerRepository->createOrSkip([
                            'user_id' => $follower,
                            'task_id' => $task->id,
                        ]);

//                        $this->emailService->sendTaskMailToFollower($task, $model->user, 'new_task_follower', $params);
                    }

                }
            }

            if (isset($params['attachment'])) {
                $this->attachmentRepository->createTaskAttachment($task, request()->attachment);
            }
        }

        return response()->json(['resType' => 'Success']);
    }

    /**
     * @param Task $task
     * @param Request $request
     * @return mixed
     */
    public function uploadAttachment(Task $task, Request $request)
    {
        if ($request->has('attachment')) {
            $this->attachmentRepository->createTaskAttachment($task, $request->file('attachment'));
            return ajaxResponse(SUCCESS, null);
        }
    }

    /**
     * @param Attachment $attachment
     * @param Request $request
     * @return mixed
     */
    public function deleteAttachment(Attachment $attachment, Request $request)
    {
        if ($this->attachmentRepository->delete($attachment->id)) {
            return ajaxResponse(SUCCESS, null);
        }
        return ajaxResponse(ERROR, 'Can\'t delete the attachment!');
    }


    /**
     * @param $paper
     * @return mixed
     */
    public function deleteTest(Paper $paper)
    {
        if (!auth()->user()->canAtLeast(['task.update'])) {
            return ajaxResponse(ERROR, 'no permission');
        }

        if ($paper->done || $paper->status_id == DONE) {
            return ajaxResponse(ERROR, 'Can\'t delete a Done test!');
        }
        $task = $this->taskRepository->getById($paper->task_id);


        if ($this->paperRepository->delete($paper->id)) {
            ### 26. Cancelled test
            ### Send email to assessor
            if ($task->assessor && in_array($paper->type->id,[TEST_SPEAKING, TEST_WRITING])) {
                $this->emailService->sendEmail([
                    'email' => $task->assessor->email,
                    'name' => $task->name,
                    'link' => url('task/' . $task->id),
                    'test_type' => $paper->type->name
                ], MAIL_CANCELLED_TEST);
            }
            addLog([
                'type' => TASK_HISTORY,
                'task_id' => $paper->task_id,
                'user_id' => auth()->user()->id,
                'description' => 'Test ' . $paper->type->name . ' was deleted'
            ]);

            return ajaxResponse(SUCCESS);
        }

    }

    /**
     * @param $log_id
     * @return mixed
     */
    public function deleteLog($log_id)
    {
        if (!auth()->user()->hasRole(['master', 'administrator'])) {
            return ajaxResponse(ERROR, 'no permission');
        }

        $log = Log::find($log_id);

        if (!$log) {
            return ajaxResponse(ERROR, 'Entry not found');
        }

        if ($log->delete()) {
            return ajaxResponse(SUCCESS);
        }

    }

    /**
     * Remove the specified task.
     *
     * @param  Project $project
     * @param  Task $task
     * @return bool|null
     */
    public function delete(Project $project, Task $task)
    {

        ### check current user is client
        ### and the status is other than allocated
        if (Auth::user()->hasRole('client') && $task->task_status_id != TaskStatus::STATUS_ALLOCATED) {
            ### deny action
            return ajaxResponse(ERROR, 'You can cancel a task only when the status is Allocated');
        }

        if ($task->assessor) {
            $attributes['template'] = config('mail.task_info');
            $attributes['name'] = $task->name;
            $attributes['link'] = '[Here will be the link to the test page]';
            $attributes['taskInfo'] = 'task_canceled';
            $attributes['email'] = $task->assessor->email;
            $attributes['task'] = $task;
            $mailSent = $this->emailService->sendEmail($attributes, MAIL_ASSESSMENT_CANCELED);

            if ($mailSent) {
                addLog([
                    'type' => MAIL_SENT,
                    'task_id' => $task->id,
                    'description' => 'Mail sent to test taker for canceled task'
                ]);
            }
        }

        foreach ($task->followers as $follower) {
            $attributes['template'] = config('mail.task_info');
            $attributes['name'] = $task->name;
            $attributes['link'] = '[Here will be the link to the test page]';
            $attributes['taskInfo'] = 'task_canceled';
            $attributes['email'] = $follower->user->email;
            $attributes['user'] = $follower->user;
            $attributes['task'] = $task;
            $mailSent = $this->emailService->sendEmail($attributes, MAIL_ASSESSMENT_CANCELED);
            if ($mailSent) {
                addLog([
                    'type' => MAIL_SENT,
                    'task_id' => $task->id,
                    'description' => 'Mail sent to task followers for canceled task'
                ]);
            }
        }

        $this->paperRepository->cancelAllPapers($task->id);
        $deleted = $this->taskRepository->delete($task->id);

        if ($deleted) {
            addLog([
                'type' => TASK_HISTORY,
                'description' => 'Task status was changed from ' . $task->status->name . ' to Canceled',
                'task_id' => $task->id,
                'user_id' => auth()->user()->id
            ]);
            return ajaxResponse(SUCCESS);
        }

        return ajaxResponse(ERROR, 'Sorry! Could not cancel the task!');

    }

    /**
     * Get assessors by language selected.
     * @param int $projectType
     * @param $language_id
     * @param int $native
     * @return string
     */
    public function assessorsByLanguage(Request $request, $projectType = Project::PROJECT_TYPE_AUDIT, $language_id, $native = 0)
    {

        $projectId = $request->get('project_id');
        $project = $this->projectRepository->getProjectById($projectId);
        $clientId = $project->client_id;

        $prices = $this->pricesRepository->getClientPrices($clientId, $projectId);

        $groupedPrices = [];

        foreach ($prices as $price) {
            if (empty($groupedPrices[$price->language_id])) {
                $groupedPrices[$price->language_id] = [];
            }

            if (empty($groupedPrices[$price->language_id][$price->pricing_type_id])) {
                $groupedPrices[$price->language_id][$price->pricing_type_id] = [];
            }

            $groupedPrices[$price->language_id][$price->pricing_type_id] = [
                'id' => $price->id,
                'price' => $price->price,
            ];
        }

        $paperTypeToPricingType = $this->pricesRepository->getTestTypeMapping();

        $languagePaperTypes = LanguagePaperTypes::where('language_id', $language_id)->with('paperTypes')->get();

        if ($projectType == Project::PROJECT_TYPE_COURSES || $projectType == Project::PROJECT_TYPE_RECRUITING) {
            $assessors = $this->userRepository->getAllAssessors();
            return json_encode([
                'nativeButton' => false,
                'nativeDisabled' => false,
                'nativeState' => true,
                'assessors' => empty($assessors) ? null : $assessors->toArray(),
                'languagePaperTypes' => $languagePaperTypes,
                'prices' => $groupedPrices,
                'paperTypeToPricingType' => $paperTypeToPricingType
            ]);
        }

        //$assessors = $this->groupRepository->getAssessorsFromLanguageGroup(intval($language_id));

        $notNativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative(intval($language_id), false);
        $nativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative(intval($language_id), true);

        if (($nativeAssessors->count() && !$notNativeAssessors->count()) || $native == 1) {
            $assessors = $nativeAssessors->pluck('full_name_email', 'id');
        } else {
            $assessors = $notNativeAssessors->pluck('full_name_email', 'id');
        }


        return json_encode([
            'nativeButton' => !(!$notNativeAssessors->count() || !$nativeAssessors->count()),
            'nativeDisabled' => !$notNativeAssessors->count() || !$nativeAssessors->count(),
            'nativeState' => $nativeAssessors->count() && !$notNativeAssessors->count(),
            'assessors' => $assessors,
            'languagePaperTypes' => $languagePaperTypes,
            'prices' => $groupedPrices,
            'paperTypeToPricingType' => $paperTypeToPricingType
        ]);
    }

    /**
     * Get assessors by language and native selected.
     * @param int $projectType
     * @param $language_id
     * @param $native
     * @return string
     */
    public function assessorsByLanguageAndNative($projectType = 1, $language_id, $native)
    {
        if ($projectType == Project::PROJECT_TYPE_COURSES || $projectType == Project::PROJECT_TYPE_RECRUITING) {

            $notNativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative(intval($language_id), false);
            $nativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative(intval($language_id), true);

            $assessors = $this->userRepository->getAllAssessors();
            return json_encode([
                'nativeButton' => !(!$notNativeAssessors->count() || !$nativeAssessors->count()),
                'nativeDisabled' => !$notNativeAssessors->count() || !$nativeAssessors->count(),
                'nativeState' => $nativeAssessors->count() && !$notNativeAssessors->count(),
                'assessors' => empty($assessors) ? null : $assessors->toArray(),
            ]);
        }

        $assessors = $this->groupRepository->getAssessorsByLanguageAndNative(intval($language_id), intval($native));

        return json_encode([
            'assessors' => empty($assessors) ? null : $assessors->pluck('full_name_email', 'id')
        ]);
    }

    /**
     * Filter projects by name.
     */
    public function projectsByName(Project $project, $name)
    {
        $name = $name == 'null' ? '' : $name;
        $projects = $this->projectRepository->search(['name' => $name])->get();

        return json_encode([
            'projects' => empty($projects) ? null : $projects->pluck('name', 'id')
        ]);
    }


    /**
     * Filter taks by name.
     */
    public function tasksByName(Project $project, $name)
    {
        $name = $name == 'null' ? '' : $name;
        $tasks = $this->taskRepository->globalSearch(['global_search' => $name])->where(['project_id' => $project->id]);

        return json_encode([
            'tasks' => empty($tasks) ? null : $tasks->pluck('name', 'id')
        ]);
    }

    /**
     * @param $project_id
     * @param Request $request
     * @return mixed
     */
    public function exportTasksXLS($project_id, Request $request)
    {
        ini_set("memory_limit", -1);
        ini_set("max_execution_time", 120);

        $auth_user = Auth::user();

        if ($project_id != 0) { // on project page
            $projects = $this->projectRepository->getById($project_id);

        } else { // tasks page

            if ($auth_user->hasRole(['administrator', 'master'])) {
                $projects = Project::with('participants')->get();
            } else {
                $projects = Project::whereHas('participants', function ($q) use ($auth_user) {
                    $q->where('user_id', $auth_user->id);
                })->orWhereHas('tasks', function ($q) use ($auth_user) {
                    $q->where('assessor_id', $auth_user->id);
                })->orWhereHas('tasks.followers', function ($q) use ($auth_user) {
                    $q->where('user_id', $auth_user->id);
                })->orWhere('user_id', $auth_user->id)->get();
            }


        }

//        $hideMarkAndDepartment = false;
//        if (isset($projects[0])) {
//            foreach ($projects as $project) {
//                if ($project->project_type_id == 1) {
//                    $hideMarkAndDepartment = true;
//                    break;
//                }
//            }
//        } else {
//            if ($projects->project_type_id == 1) {
//                $hideMarkAndDepartment = true;
//            }
//        }

        $headers = [
            'project' => 'Project',
            'additional_cost' => 'Additional Cost',
            'id' => 'Task id',
            'task_status_id' => 'Status',
            'name' => 'Name',
            'language' => 'Language',
            'test_lu' => 'Language Use',
            'date_lu' => 'Date LU',
            'test_s' => 'Speaking',
            'date_s' => 'Date S',
            'test_w' => 'Writing',
            'date_w' => 'Date W',
            'test_l' => 'Listening',
            'date_l' => 'Date L',
            'test_r' => 'Reading',
            'date_r' => 'Date R',
            'added_by' => 'Added By',
            'created_at' => 'Date added',
            'assessor' => 'Assessor',
            'native' => 'Native assessor',
            'invoice_client' => 'Invoice client yes/no/half price',
            'pay_assessor' => 'Pay assessor yes/no',
            'price_client' => 'Price for client',
            'rate_assessor' => 'Rate for assessor',
        ];

        if ($auth_user->hasRole('client')) {
            unset($headers['assessor']);
            unset($headers['invoice_client']);
            unset($headers['pay_assessor']);
            unset($headers['price_client']);
            unset($headers['rate_assessor']);
        }

        if ($auth_user->hasRole('assessor')) {
            unset($headers['assessor']);
            unset($headers['invoice_client']);
            unset($headers['price_client']);
            unset($headers['rate_assessor']);

            unset($headers['test_lu']);
            unset($headers['date_lu']);
            unset($headers['test_l']);
            unset($headers['date_l']);
            unset($headers['test_r']);
            unset($headers['date_r']);
            unset($headers['additional_cost']);

        }

        if ($auth_user->hasRole(['css', 'recruiter'])) {
            unset($headers['price_client']);
            unset($headers['rate_assessor']);
        }

        if ($auth_user->hasRole('tds')) {
            unset($headers['invoice_client']);
            unset($headers['pay_assessor']);
            unset($headers['price_client']);
            unset($headers['rate_assessor']);
            unset($headers['additional_cost']);
        }

        $template = [];
        foreach ($headers as $key => $name) {
            $template[$name] = '';
        }

        ### TEMPLATE
        if ($request->has('template')) {
            return Excel::create('Project-Tasks-Template', function ($excel) use ($template) {
                $excel->sheet('Tasks', function ($sheet) use ($template) {
                    $sheet->setOrientation('portrait');
                    $sheet->fromArray([$template]);
                });

            })->download("xlsx");

            ### RESULTS
        } else {

            $filters = $request->input('filters');

            $data = [];
            $task_ids = [];
            $papers = [];
            $reports = [];
            $languages = [];
            $project_names = [];
            $user_names = [];

            if ($request->has('filters.global_search') && $filters['global_search'] !== null) {
                foreach ($this->taskRepository->globalSearch($filters)->cursor() as $data_item) {
                    $data[] = $data_item;
                }
                foreach ($data as $index => $item) {
                    $task_ids[] = $item->id;
                }
                if (!empty($task_ids)) {
                    $queryIn = sprintf("task_id IN (%s)", implode(",", $task_ids));
                    foreach (Paper::whereRaw($queryIn)->cursor() as $paper_item) {
                        $papers[$paper_item->task_id][] = $paper_item;
                    }
                    $queryIn = sprintf("paper_id IN (SELECT id from papers where task_id in (%s))", implode(",", $task_ids));
                    foreach (PaperReport::whereRaw($queryIn)->cursor() as $report_item) {
                        $reports[$report_item->paper_id] = $report_item;
                    }
                    foreach (Language::all() as $language_item) {
                        $languages[$language_item->id] = $language_item->name;
                    }
                    foreach (Project::withTrashed()->get() as $project_item) {
                        $project_names[$project_item->id] = $project_item->name;
                    }
                    foreach (User::withTrashed()->get() as $user_item) {
                        $user_names[$user_item->id] = $user_item->first_name . " " . $user_item->last_name;
                    }
                }
            } else {
                foreach ($this->taskRepository->search($projects, $filters)->cursor() as $data_item) {
                    $data[] = $data_item;
                }
                foreach ($data as $index => $item)  {
                    $task_ids[] = $item->id;
                }
                if (isset($projects[0])) { // on tasks page
                    foreach ($projects as $project) {
                        $project_ids[] = $project->id;
                    }
                } else { // on project page
                    $project_ids[] = $projects->id;
                }
                if (!empty($project_ids) && !empty($task_ids)) {
                    $queryIn = sprintf("task_id IN (SELECT id from tasks where project_id in (%s))", implode(",", $project_ids));
                    foreach (Paper::whereRaw($queryIn)->cursor() as $paper_item) {
                        $papers[$paper_item->task_id][] = $paper_item;
                    }
                    $queryIn = sprintf("paper_id IN (SELECT id from papers where task_id in (%s))", implode(",", $task_ids));
                    foreach (PaperReport::whereRaw($queryIn)->cursor() as $report_item) {
                        $reports[$report_item->paper_id] = $report_item;
                    }
                    foreach (Language::all() as $language_item) {
                        $languages[$language_item->id] = $language_item->name;
                    }
                    foreach (Project::withTrashed()->get() as $project_item) {
                        $project_names[$project_item->id] = $project_item->name;
                    }
                    foreach (User::withTrashed()->get() as $user_item) {
                        $user_names[$user_item->id] = $user_item->first_name . " " . $user_item->last_name;
                    }
                }
            }

            $results = [];

            foreach ($data as $index => $item) {

                $newRow = [];
                foreach ($headers as $key => $name) {
                    switch ($key) {
                        case 'task_status_id':
                            $newRow[$name] = TaskStatus::STATUSES[$item->task_status_id];
                            break;
                        case 'project':
                            $newRow[$name] = $project_names[$item->project_id];
                            break;
                        case 'language':
                            $newRow[$name] = $languages[$item->language_id];
                            break;
                        case 'test_lu':

                            $newRow[$name] = '';

                            if (!empty($papers[$item->id])) {
                                foreach ($papers[$item->id] as $paper) {
                                    if (in_array($paper->paper_type_id, [TEST_LANGUAGE_USE, TEST_LANGUAGE_USE_NEW]) && isset($reports[$paper->id])) {
                                        $newRow[$name] = $reports[$paper->id]->grade;
                                        $newRow[$headers['date_lu']] = $reports[$paper->id]->created_at->format('d.M.y');
                                        break;
                                    }
                                }
                            }

                            break;
                        case 'test_s':

                            $newRow[$name] = '';

                            if (!empty($papers[$item->id])) {
                                foreach ($papers[$item->id] as $paper) {
                                     if ($paper->paper_type_id == TEST_SPEAKING && isset($reports[$paper->id])) {
                                        $newRow[$name] = $reports[$paper->id]->grade;
                                        $newRow[$headers['date_s']] = $reports[$paper->id]->created_at->format('d.M.y');
                                        break;
                                    }
                                }
                            }

                            break;
                        case 'test_w':

                            $newRow[$name] = '';

                            if (!empty($papers[$item->id])){
                                foreach ($papers[$item->id] as $paper) {
                                    if ($paper->paper_type_id == TEST_WRITING && isset($reports[$paper->id])) {
                                        $newRow[$name] = $reports[$paper->id]->grade;
                                        $newRow[$headers['date_w']] = $reports[$paper->id]->created_at->format('d.M.y');
                                        break;
                                    }
                                }
                            }

                            break;
                        case 'test_l':

                            $newRow[$name] = '';

                            if (!empty($papers[$item->id])) {
                                foreach ($papers[$item->id] as $paper) {
                                    if ($paper->paper_type_id == TEST_LISTENING && isset($reports[$paper->id])) {
                                        $newRow[$name] = $reports[$paper->id]->grade;
                                        $newRow[$headers['date_l']] = $reports[$paper->id]->created_at->format('d.M.y');
                                        break;
                                    }
                                }
                            }

                            break;
                        case 'test_r':

                            $newRow[$name] = '';

                            if (!empty($papers[$item->id])) {
                                foreach ($papers[$item->id] as $paper) {
                                    if ($paper->paper_type_id == TEST_READING && isset($reports[$paper->id])) {
                                        $newRow[$name] = $reports[$paper->id]->grade;
                                        $newRow[$headers['date_r']] = $reports[$paper->id]->created_at->format('d.M.y');
                                        break;
                                    }
                                }
                            }

                            break;
                        case 'added_by':
                            $newRow[$name] = $item->added_by_id !== null ? $user_names[$item->added_by_id] : '';
                            break;
                        case 'created_at':
                            $newRow[$name] = $item->created_at->format('d.M.y');
                            break;
                        case 'assessor':
                            if ($auth_user->hasRole('client')) {
                                $newRow[$name] = '';
                                break;
                            }
                            $newRow[$name] = $item->assessor_id !== null ? $user_names[$item->assessor_id] : '';
                            break;
                        case 'native':
                            $newRow[$name] = $item->native ? 'yes' : 'no';
                            break;
                        case 'invoice_client':

                            if ($auth_user->hasRole('client')) {
                                $newRow[$name] = '';
                                break;
                            }

                            if ($item->bill_client == 2) {
                                $newRow[$name] = 'half price';
                            } else {
                                $newRow[$name] = $item->bill_client == 1 ? 'yes' : 'no';
                            }

                            break;
                        case 'pay_assessor':

                            if ($auth_user->hasRole('client')) {
                                $newRow[$name] = '';
                                break;
                            }

                            $newRow[$name] = $item->pay_assessor ? 'yes' : 'no';
                            break;
                        case 'date_lu':
                        case 'date_s':
                        case 'date_w':
                        case 'date_l':
                        case 'date_r':
                            if (!isset($newRow[$name])) {
                                $newRow[$name] = '';
                            }
                            break;

                        default:
                            $newRow[$name] = isset($item->{$key}) ? $item->{$key} : '';
                            break;
                    }
                }

                $results[$index] = $newRow;

            }
            if ($projects[0] != null) {
                $name = count($projects) > 1 ? 'All' : $projects[0]->name;
            } else {
                $name = count($projects) > 1 ? 'All' : $projects->name;
            }

            return Excel::create('Project-' . $name . '-tasks-' . date('d-m-Y-H:i', time()),
                function ($excel) use ($results) {
                    $excel->sheet('Tasks', function ($sheet) use ($results) {
                        $sheet->setOrientation('portrait');
                        $sheet->fromArray($results);
                    });

                })->download("xlsx");
        }
    }

    /**
     * @param $project_id
     * @param Request $request
     * @return mixed
     */
    public function exportGradesCSV($project_id, Request $request)
    {
        $auth_user = Auth::user();

        if ($project_id != 0) { // on project page
            $projects = $this->projectRepository->getById($project_id);

        } else { // tasks page

            if ($auth_user->hasRole(['administrator', 'master'])) {
                $projects = Project::with('tasks', 'participants')->get();
            } else {
                $projects = Project::whereHas('participants', function ($q) use ($auth_user) {
                    $q->where('user_id', $auth_user->id);
                })->orWhereHas('tasks', function ($q) use ($auth_user) {
                    $q->where('assessor_id', $auth_user->id);
                })->orWhereHas('tasks.followers', function ($q) use ($auth_user) {
                    $q->where('user_id', $auth_user->id);
                })->orWhere('user_id', $auth_user->id)->get();
            }
        }

        $headers = [
            'email' => 'Email',
            'test_lu' => 'Language Use level',
            'test_s' => 'Speaking level'
        ];

        $template = [];
        foreach ($headers as $key => $name) {
            $template[$name] = '';
        }

        $filters = $request->input('filters');

        if ($request->has('filters.global_search') && $filters['global_search'] !== null) {
            $data = $this->taskRepository->globalSearch($filters)->get();
        } else {
            $data = $this->taskRepository->search($projects, $filters)->get();
        }

        $results = [];

        foreach ($data as $index => $item) {

            $newRow = [];
            foreach ($headers as $key => $name) {
                switch ($key) {
                    case 'test_lu':

                        $newRow[$name] = '';

                        foreach ($item->papers as $paper) {
                            if (in_array($paper->type->id, [TEST_LANGUAGE_USE, TEST_LANGUAGE_USE_NEW]) && $paper->report !== null
                            ) {
                                $newRow[$name] = $paper->report->grade;
                                break;
                            }
                        }

                        break;
                    case 'test_s':

                        $newRow[$name] = '';

                        foreach ($item->papers as $paper) {
                            if ($paper->type->id == TEST_SPEAKING && $paper->report !== null) {
                                $newRow[$name] = $paper->report->grade;
                                break;
                            }
                        }

                        break;
                    default:
                        $newRow[$name] = isset($item->{$key}) ? $item->{$key} : '';
                        break;
                }
            }

            $results[$index] = $newRow;
        }
        if ($projects[0] != null) {
            $name = count($projects) > 1 ? 'All' : $projects[0]->name;
        } else {
            $name = count($projects) > 1 ? 'All' : $projects->name;
        }

        return Excel::create('Project-' . $name . '-grades-' . date('d-m-Y-H:i', time()), function ($excel) use ($results) {
                    $excel->sheet('Tasks', function ($sheet) use ($results) {
                        $sheet->setOrientation('portrait');
                        $sheet->fromArray($results);
                    });
                })->download("csv");
    }


    /**
     * @param Project $project
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importTasksXLS(Project $project, Request $request)
    {
        $file = $request->file('import-file')->getRealPath();
        $rows = Excel::load($file, function ($reader) {
            $reader->ignoreEmpty();
        })->get()->toArray();

        foreach ($rows as $key => $row) {
            if (empty($row) || !isset($row['name'])) {
                unset($rows[$key]);
            }
        }

        $prices = $this->pricesRepository->getClientPricesGrouped($project->client_id, $project->id);

        $assessor_xls = [];
        $languages_xls = [];

        $availableTestsByLang = [];
        $languages = Language::with('language_paper_type')->get();
        foreach ($languages as $item) {
            $availableTestsByLang[$item->id] = $item->language_paper_type->pluck('paper_type_id')->toArray();
        }
        $languagesNames = $languages->pluck('id', 'name')->toArray();
        $testNames = PaperType::all()->pluck('name', 'id')->toArray();

        $errorsOutput = [];

        foreach ($rows as $key => $row) {
            $validator = \Validator::make($row, [
                'name' => 'required',
                'e_mail' => 'required',
                'phone' => 'required',
                'language' => 'required',
            ]);

            if (!$validator->passes()) {
                $error = $validator->messages();
                $error = $error->messages();
                $errorField = key($error);
                $errorText = reset($error);

                $errorsOutput[] = 'Row ' . ($key + 2) . ': ' . $errorText[0] . ' ' . $row[$errorField];
            }

            if (isset($row['assessor'])) {
                $assessor_xls[] = trim($row['assessor']);
            }

            $languages_xls[] = trim($row['language']);

            ### check test types
            ### get available test types for this lang
            $availableTests = $availableTestsByLang[$languagesNames[$row['language']]];
            $testsToAdd = [
                TEST_LANGUAGE_USE_NEW => isset($row['language_use']) && strtolower($row['language_use']) == 'yes',
                TEST_SPEAKING => isset($row['speaking']) && strtolower($row['speaking']) == 'yes',
                TEST_WRITING => isset($row['writing']) && strtolower($row['writing']) == 'yes',
                TEST_LISTENING => isset($row['listening']) && strtolower($row['listening']) == 'yes',
                TEST_READING => isset($row['reading']) && strtolower($row['reading']) == 'yes',
                TEST_LANGUAGE_USE => isset($row['language_use']) && strtolower($row['language_use']) == 'yes',
            ];
            $testsToAdd = array_filter($testsToAdd, function ($test) {
                return $test === true;
            });

            foreach ($testsToAdd as $test => $v) {
                if ($test == TEST_LANGUAGE_USE || $test == TEST_LANGUAGE_USE_NEW) {
                    if (!in_array(TEST_LANGUAGE_USE, $availableTests) && !in_array(TEST_LANGUAGE_USE_NEW,
                            $availableTests)
                    ) {
                        $errorsOutput[] = 'Row ' . ($key + 2) . ': ' . $row['language'] . ' ' . $testNames[TEST_LANGUAGE_USE] . ' test is not available';
                    }
                } else {
                    if (!in_array($test, $availableTests)) {
                        $errorsOutput[] = 'Row ' . ($key + 2) . ': ' . $row['language'] . ' ' . $testNames[$test] . ' test is not available';
                    }
                }

            }

            ### deadline
            if (isset($row['deadline_for_online_tests']) && $row['deadline_for_online_tests'] != null) {

                if (date('H:i:s', strtotime($row['deadline_for_online_tests'])) == '00:00:00') {
                    $rows[$key]['deadline_for_online_tests'] = date('Y-m-d', strtotime($row['deadline_for_online_tests'])) . ' 23:59:59';
                    $row['deadline_for_online_tests'] = $rows[$key]['deadline_for_online_tests'];
                }
                if (strtotime($row['deadline_for_online_tests']) < time()) {
                    $errorsOutput[] = 'Row ' . ($key + 2) . ': Date for deadline must be greater than today';
                }
            }

            ### availability day
            if (isset($row['availability_for_speaking_test_day'])
                && $row['availability_for_speaking_test_day'] != null
                && strtotime($row['availability_for_speaking_test_day']) < strtotime(date('Y-m-d 00:00:00'))
            ) {
                $errorsOutput[] = 'Row ' . ($key + 2) . ': Date for speaking availability (day) must be greater than today';
            }

            ### availability greater
            if (isset($row['availability_for_speaking_test_day']) && $row['availability_for_speaking_test_day'] != null) {
                $avb_day = date('Y-m-d', strtotime($row['availability_for_speaking_test_day']));
                $avb_from = $avb_day;
                $avb_to = $avb_day;
                if (isset($row['availability_for_speaking_test_time_from']) && $row['availability_for_speaking_test_time_from'] != null) {
                    $avb_from .= ' ' . date('H:i:s', strtotime($row['availability_for_speaking_test_time_from']));
                } else {
                    $avb_from .= ' 09:00';
                }

                if (isset($row['availability_for_speaking_test_time_to']) && $row['availability_for_speaking_test_time_to'] != null) {
                    $avb_to .= ' ' . date('H:i:s', strtotime($row['availability_for_speaking_test_time_to']));
                } else {
                    $avb_to .= ' 18:00';
                }

                if (strtotime($avb_from) > strtotime($avb_to)) {
                    $errorsOutput[] = 'Row ' . ($key + 2) . ': Speaking availability time to must be greater than availability time from';
                }
            }

        }

        // find assessors
        $assessors = User::select('email', 'id')->whereIn('email', $assessor_xls)->whereHas('allRoles', function ($q) {
            $q->where('role_id', Role::ROLE_ASSESSOR);
        })->get();

        // find languages
        $languages = Language::whereIn('name', $languages_xls)->pluck('name', 'id')->toArray();
        $languageNatives = [];
        foreach ($languages as $language_id => $name) {
            $languageNatives[$name] = [
                'native' => $this->groupRepository->existsNativeAssessors($language_id),
                'non_native' => $this->groupRepository->existsNativeAssessors($language_id, 0)
            ];
        }

        $dbAssessors = $assessors->pluck('email')->toArray();
        $notFoundAssessors = array_diff($assessor_xls, $dbAssessors);

        if (count($notFoundAssessors)) {
            $errorsOutput[] = 'Error: The following assessors are not found: ' . implode(', ', $notFoundAssessors);
        }

        if (!empty($errorsOutput)) {
            $errorsOutput = array_map(function ($elem) {
                return '<li>' . $elem . '</li>';
            }, $errorsOutput);
            return ajaxResponse(ERROR, implode(' ', $errorsOutput));
        }

        $assessorsEmails = $assessors->pluck('id', 'email')->toArray();
        $addedBy = \Auth::id();

        $excelData = $this->excelService->getExcelData($rows);

        foreach ($excelData as $excelRow) {

            $excelRow = array_map(function ($elem) {
                return trim($elem);
            }, $excelRow);

            $avb_date_from = null;
            $avb_date_to = null;

            // set availability
            if (isset($excelRow['availability_for_speaking_test_day']) && $excelRow['availability_for_speaking_test_day'] != null) {
                $avb_day = date('Y-m-d', strtotime($excelRow['availability_for_speaking_test_day']));
                $avb_from = $avb_day;
                $avb_to = $avb_day;
                if (isset($excelRow['availability_for_speaking_test_time_from']) && $excelRow['availability_for_speaking_test_time_from'] != null) {
                    $avb_from .= ' ' . date('H:i:s', strtotime($excelRow['availability_for_speaking_test_time_from']));
                } else {
                    $avb_from .= ' 09:00';
                }

                if (isset($excelRow['availability_for_speaking_test_time_to']) && $excelRow['availability_for_speaking_test_time_to'] != null) {
                    $avb_to .= ' ' . date('H:i:s', strtotime($excelRow['availability_for_speaking_test_time_to']));
                } else {
                    $avb_to .= ' 18:00';
                }
                $avb_date_from = date('Y-m-d H:i:s', strtotime($avb_from));
                $avb_date_to = date('Y-m-d H:i:s', strtotime($avb_to));
            }

            // native
            if(isset($excelRow['native_assessor'])){
                if( strtolower($excelRow['native_assessor']) == 'yes' && $languageNatives[$excelRow['language']]['native'] == true) {
                    $native = true;
                } elseif( strtolower($excelRow['native_assessor']) == 'no' && $languageNatives[$excelRow['language']]['non_native'] == true) {
                    $native = false;
                } else {
                    if ($languageNatives[$excelRow['language']]['non_native'] == true) {
                        $native = false;
                    } else {
                        $native = true;
                    }
                }
            } else {
                if ($languageNatives[$excelRow['language']]['non_native'] == true) {
                    $native = false;
                } else {
                    $native = true;
                }
            }

            $newTask = [
                'project_id' => $project->id,
                'assessor_id' => isset($excelRow['assessor']) && $excelRow['assessor'] != null ? $assessorsEmails[$excelRow['assessor']] : null,
                'language_id' => $languagesNames[$excelRow['language']],
                'task_status_id' => TaskStatus::STATUS_ALLOCATED,
                'added_by_id' => $addedBy,
                'name' => $excelRow['name'],
                'skype' => isset($excelRow['skype']) ? $excelRow['skype'] : null,
                'email' => $excelRow['e_mail'],
                'phone' => preg_replace("/[^0-9+]/", "", $excelRow['phone']),
                'deadline' => isset($excelRow['deadline_for_online_tests']) && $excelRow['deadline_for_online_tests'] != null ? $excelRow['deadline_for_online_tests'] : null,
                'availability_from' => $avb_date_from,
                'availability_to' => $avb_date_to,
                'native' => $native,
                'bill_client' => $project->default_bill_client,
                'pay_assessor' => $project->default_pay_assessor,
            ];

            if (auth()->user()->hasRole('cilent')) {
                $newTask['assessor_id'] = null;
            }

            ### get available test types for this lang
            $availableTests = $availableTestsByLang[$newTask['language_id']];

            ### get test types
            $testsToAdd = [
                TEST_LANGUAGE_USE_NEW => isset($excelRow['language_use']) && strtolower($excelRow['language_use']) == 'yes' && in_array(TEST_LANGUAGE_USE_NEW, $availableTests),
                TEST_SPEAKING => isset($excelRow['speaking']) && strtolower($excelRow['speaking']) == 'yes' && in_array(TEST_SPEAKING, $availableTests),
                TEST_WRITING => isset($excelRow['writing']) && strtolower($excelRow['writing']) == 'yes' && in_array(TEST_WRITING, $availableTests),
                TEST_LISTENING => isset($excelRow['listening']) && strtolower($excelRow['listening']) == 'yes' && in_array(TEST_LISTENING, $availableTests),
                TEST_READING => isset($excelRow['reading']) && strtolower($excelRow['reading']) == 'yes' && in_array(TEST_READING, $availableTests),
                TEST_LANGUAGE_USE => isset($excelRow['language_use']) && strtolower($excelRow['language_use']) == 'yes' && in_array(TEST_LANGUAGE_USE, $availableTests),
            ];

            $papers = array_filter($testsToAdd, function ($test) {
                return $test === true;
            });

            ### check task needs assessor
            $assessorNeeded = array_key_exists(TEST_SPEAKING, $papers);


            if ($newTask['assessor_id'] === null && $assessorNeeded) {
                $randAssessor = $this->groupRepository->getRandomAssessor($newTask['language_id'], 0, $native);
                $newTask['assessor_id'] = $randAssessor !== null ? $randAssessor->id : null;
            }

            ### CREATE TASK
            $task = $this->taskRepository->create($newTask);

            ### CREATE PAPERS
            foreach ($papers as $paper_type_id => $val) {

                ### SET PRICING
                $cost = "0.00";
                $key = $paper_type_id;
                $language = $newTask['language_id'];

                if ($key == TEST_SPEAKING && $native) {
                    $pricingTypeId = PricingType::SPEAKING_NATIVE;
                } else if ($key == TEST_WRITING && $native) {
                    $pricingTypeId = PricingType::WRITING_NATIVE;
                } else {
                    $pricingTypeId = $this->pricesRepository->getTestTypeByPaperType($key);
                }

                if (!empty($prices[$language]) && !empty($prices[$language][$pricingTypeId])) {
                    $cost = $prices[$language][$pricingTypeId]['price'];
                }

                $this->paperRepository->createOrSkip([
                    'paper_type_id' => $paper_type_id,
                    'task_id' => $task->id,
                    'cost' => $cost,
                ]);
            }

            ### get task all details
            $task = Task::with('project', 'project.owner', 'language', 'papers', 'papers.type')->where('id',
                $task->id)->first();

            ### send mail to assessor if we have one
            if ($task->assessor_id !== null && $assessorNeeded) {
                $assessor = $this->userRepository->getById($task->assessor_id);
                $this->emailService->sendAssessorMail($assessor, $task);
                addAssessorHistory($task->id, $assessor->id, 'create');
                addLog([
                    'type' => MAIL_SENT,
                    'user_id' => $assessor->id,
                    'task_id' => $task->id,
                    'description' => 'Mail sent to assessor for creating new task'
                ]);
            } elseif ($task->assessor_id === null && $assessorNeeded) {
                //language group doesn't have any assessors
                $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService, $task, $excelRow);
                addLog([
                    'type' => MAIL_SENT,
                    'task_id' => $task->id,
                    'description' => 'Mail sent to admin because group is empty'
                ]);
            }

            $testTypes = Paper::where('task_id', $task->id)->where(function ($query) {
                $query->where('done', 0)
                    ->where('status_id', '!=', CANCELED);
//                    ->where('paper_type_id', '!=', TEST_SPEAKING);
                $query->whereDoesntHave('report');
            })->with('type')
                ->orderByRaw("FIELD(papers.paper_type_id, '3', '5', '4', '1', '6') ASC")
                ->get()->pluck('type.name')->toArray();
            $testIds = $task->papers->pluck('paper_type_id')->toArray();

            if (count($testIds) == 1 && reset($testIds) == TEST_SPEAKING) {
                $link = false;
            } else {
                $link = $task->link;
            }

            ###  Send email to test taker
            $mailSent = $this->emailService->sendEmail([
                'email' => $task->email,
                'name' => $task->name,
                'link' => $link,
                'company' => $task->project->owner->name,
                'language' => $task->language->name,
                'availability_from' => $task->availability_from,
                'availability_to' => $task->availability_to,
                'deadline' => isset($task->deadline) ? Carbon::parse($task->deadline)->format('d M Y, H:i') : null,
                'tests' => implode(', ', $testTypes)
            ], MAIL_TEST_TAKE);

            if ($mailSent) {
                addLog([
                    'type' => MAIL_SENT,
                    'task_id' => $task->id,
                    'description' => 'Mail sent to test taker for creating new task'
                ]);
            }
        }

        return ajaxResponse(SUCCESS);
    }

    /**
     * Get task page
     *
     * @param $task
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTaskPage(Task $task)
    {

        if (Auth()->user()->hasOnlyRole('assessor') && $task->assessor->id != Auth()->user()->id) {
            return abort(404);
        }

        if (Auth()->user()->hasOnlyRole('assessor') && $task->task_status_id == TaskStatus::STATUS_CANCELED) {
            session()->flash('flash_info', 'Task is Canceled!');
            return redirect()->back();
        }

        $results = $this->getTask($task);
        $taskStatusesAssessor = [];
        $taskStatusesClient = [];
        $label = [];
        $taskStatusId = [];

        foreach ($task->papers as $paper) {
            $label[] = $paper;
            $taskStatusId[] = $paper->status_id;
        }
        usort($label, function ($a, $b) {
            if ($a->paper_type_id == $b->paper_type_id) {
                return 0;
            }
            return ($a->paper_type_id < $b->paper_type_id) ? -1 : 1;
        });

        foreach ($results['taskStatuses'] as $key => $taskStatus) {
            if ($key == ALLOCATED || $key == ISSUE) {
                $taskStatusesAssessor[$key] = $taskStatus;
            }
        }

        foreach ($results['taskStatuses'] as $key => $taskStatus) {
            if ($key == $task->task_status_id || $key == ALLOCATED || $key == CANCELED) {
                $taskStatusesClient[$key] = $taskStatus;
            }
        }

        if ($task->first_name == null && $task->last_name == null) {
            $task->full_name = $task->email;
        }

        ### get all reports not null
        $reportsArray = $task->papers->pluck('report')->toArray();
        $filteredReportArray = array_filter($reportsArray, function ($elem) {
            return $elem !== null;
        });

        ### followers and participants
        $followers = $task->followers()->with('user')->get()->pluck('user.id');
        $projects = Project::with([
            'participants' => function ($q) {
                $q->whereHas('user')->with('user');
            }
        ])->get();
        $projectParticipants = [];
        foreach ($projects as $project) {
            foreach ($project->participants as $participant) {
                $projectParticipants[$participant->user->id] = $participant->user->full_name;
            }
        }

        ### rest paper types
        $alreadyPaperTypes = $task->papers->pluck('paper_type_id')->toArray();
        $restPaperTypes = $languagePaperTypes = LanguagePaperTypes::where('language_id', $task->language_id)
            ->whereNotIn('paper_type_id', $alreadyPaperTypes)
            ->with('paperTypes')->get()
            ->pluck('paperTypes.name', 'paperTypes.id')->toArray();

        if (in_array(TEST_LANGUAGE_USE_NEW, $alreadyPaperTypes) && isset($restPaperTypes[TEST_LANGUAGE_USE])) {
            unset($restPaperTypes[TEST_LANGUAGE_USE]);
        }

        if (in_array(TEST_LANGUAGE_USE, $alreadyPaperTypes) && isset($restPaperTypes[TEST_LANGUAGE_USE_NEW])) {
            unset($restPaperTypes[TEST_LANGUAGE_USE_NEW]);
        }

        ### native options
        $nativeOptions = [
            0 => 'No',
            1 => 'Yes'
        ];

        $notNativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative($task->language_id, false);
        $nativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative($task->language_id, true);

        if ($task->native == 0 && $notNativeAssessors->count() == 0 || $task->native == 1 && $nativeAssessors->count() == 0) {

        } else {
            if(!$nativeAssessors->count() && !$notNativeAssessors->count()) {
                // do nothing
            } else {
                if (!$notNativeAssessors->count()) {
                    unset($nativeOptions[0]);
                }
                if (!$nativeAssessors->count()) {
                    unset($nativeOptions[1]);
                }
            }
        }



        $speakingReport = null;
        $writingReport = null;
        foreach ($task->papers as $paper) {
            if ($paper->paper_type_id == TEST_SPEAKING && $paper->report) {
                $report = $paper->report->attributesToArray();
                $report['assessments'] = json_decode($report['assessments'], true);
                $speakingReport = $report;
            }
            if ($paper->paper_type_id == TEST_WRITING && $paper->report) {
                $report = $paper->report->attributesToArray();
                $report['assessments'] = json_decode($report['assessments'], true);
                $writingReport = $report;
            }
        }

        $viewName = 'task.view';


        $calendarLinks = [];
        if (auth()->user()->hasOnlyRole('assessor')) {
            $agent = new Agent();
            if ($agent->isMobile()) {
                $viewName = 'task.mobile.view-assessor';
            } else {
                $viewName = 'task.view-assessor';
            }
            ### if availability is set in the task
            if ($task->availability_from != null) {
                // Format so that the the date will be on one line
                if ($task->availability_from == $task->availability_to) {
                    $from = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_from);
                    $to = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_from);
                }
                // Else if there are both set, concat them using "from - to"
                else {
                    $from = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_from);
                    $to = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_to);
                }

                $link = Link::create('[EUCOM] ' . $task->name, $from, $to);

                // Removed ics because sendgrid screws up the data/calendar link
                foreach(['google', 'yahoo', 'webOutlook'] as $service) {
                    $calendarLinks[] = [
                        "serviceName" => $service,
                        "serviceLink" => call_user_func([$link, $service])
                    ];
                }
            }
        }

        $client = Client::find($task->project->client_id);

        return view($viewName, [
            'task' => $results['task'],
            'taskStatuses' => $results['taskStatuses'],
            'taskStatusesAssessor' => $taskStatusesAssessor,
            'taskStatusesClient' => $taskStatusesClient,
            'calendarLinks' => $calendarLinks,
            'followers' => $followers,
            'restPaperTypes' => $restPaperTypes,
            'projectParticipants' => $projectParticipants,
            'label' => $task->papers,
            'taskStatusesColor' => $results['taskStatusesColor'],
            'skilsAssessments' => $results['skilsAssessments'],
            'defaultSkillAssessment' => $this->skilsAssessments,
            'updateActions' => $results['updateActions'],
            'languages' => $results['languages'],
            'assessors' => $results['assessors'],
            'nativeOptions' => $nativeOptions,
            'user_abilities' => $results['user_abilities'],
            'showDownloadPdfButton' => !empty($filteredReportArray),
            'speakingReport' => $speakingReport,
            'writingReport' => $writingReport,
            'sidebarCollapsed' => true,
            'hidePrices' => $client->billing_hidden
        ]);
    }

    /**
     * Update the specified task field.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Task $task
     * @return array
     */
    public function updateField(Request $request, Task $task)
    {

        $reloadPage = false;

        $oldTask = $task;
        ### is changing task
        if (empty($request->input('paper'))) {


            if ($request->has('deadline')) {
                $request->request->add(['deadline' => Carbon::parse($request->deadline)]);
            }

            if ($request->has('phone')) {
                $phone = preg_replace("/[^0-9]/", "", str_replace('+', '00', $request->get('phone')));
                $request->request->add(['phone' => $phone]);
            }

            $this->taskRepository->update($task->id, $request->toArray());

            $updatedTask = $this->taskRepository->getById($task->id);

            $params = $request->toArray();

            foreach ($params as $key => $value) {
                if ($key != '_token' && $task->{$key} != $value) {

                    switch ($key) {
                        case 'task_status_id':
                            $description = 'Task status was changed from ' . $task->status->name . ' to ' . $updatedTask->status->name;
                            if ($updatedTask->status->name == 'Canceled') {

                                if ($updatedTask->assessor) {
                                    ### 11. Assessment cancelled
                                    ### Send email to assessor
                                    $this->emailService->sendEmail([
                                        'email' => $updatedTask->assessor->email,
                                        'name' => $updatedTask->name
                                    ], MAIL_ASSESSMENT_CANCELED);
                                }

                                $this->paperRepository->cancelAllPapers($updatedTask->id);

                                addLog([
                                    'type' => TASK_HISTORY,
                                    'description' => $description,
                                    'task_id' => $task->id,
                                    'user_id' => auth()->user()->id
                                ]);
                                return response()->json([
                                    'resType' => 'Success',
                                    'task' => $updatedTask,
                                    'taskStatus' => $updatedTask->status,
                                    'log' => $updatedTask->logs->last(),
                                    'user' => auth()->user(),
                                    'reloadPage' => true
                                ]);
                            } elseif (Auth::user()->hasRole('client') && $updatedTask->task_status_id == TaskStatus::STATUS_ALLOCATED && $updatedTask->assessor) {
                                $this->emailService->sendAssessorMail($updatedTask->assessor, $updatedTask);
                            } elseif (empty($updatedTask->link)) {
                                $this->taskRepository->update($updatedTask->id, [
                                    'link' => md5($updatedTask->id . time()),
                                    'link_expires_at' => Carbon::now()->addMonths(1),
                                ]);
                            }

                            break;
                        case 'assessor_id':

                            $reloadPage = true;

                            if (empty($task->assessor)) {
                                $description = 'Task assessor was set for ' . $updatedTask->assessor->full_name;

                                ### Send mail to new assessor
                                addAssessorHistory($task->id, $updatedTask->assessor->id, 'manual');
                                $this->emailService->sendAssessorMail($updatedTask->assessor, $task);

                            } else {
                                $description = 'Task assessor was changed from ' . $task->assessor->full_name . ' to ' . $updatedTask->assessor->full_name;

                                $newAssessor = $this->userRepository->getById(intval($params['assessor_id']));

                                addAssessorHistory($task->id, $newAssessor->id, 'manual');

                                ### Send mail to new assessor
                                $this->emailService->sendAssessorMail($newAssessor, $task);

                                ### 11. Assessment cancelled
                                ### Send email to old assessor
                                $this->emailService->sendEmail([
                                    'email' => $task->assessor->email,
                                    'name' => $task->name
                                ], MAIL_ASSESSMENT_CANCELED);
                            }

                            $clientId = $task->project->client_id;
                            $projectId = $task->project_id;
                            $languageId = $task->language_id;
                            $prices = $this->pricesRepository->getClientPricesGrouped($clientId, $projectId);
                            $pricingTypeId = $params['native'] == 1 ? PricingType::SPEAKING_NATIVE : PricingType::SPEAKING;

                            $cost = $prices[$languageId][$pricingTypeId]['price'];

                            foreach ($task->papers as $paper) {
                                if ($paper->paper_type_id == TEST_SPEAKING && $paper->cost != $cost) {
                                    $oldCost = number_format($paper->cost, 2, '.', '');
                                    $paper->cost = $cost;
                                    $paper->save();
                                    addLog([
                                        'type' => TASK_HISTORY,
                                        'description' => 'The price for ' . $paper->type->name . ' was changed from ' . $oldCost . ' euro to ' . $cost . ' euro.',
                                        'task_id' => $task->id,
                                        'user_id' => auth()->user()->id
                                    ]);
                                }
                            }



//                            if ($task->assessor_id != $updatedTask->assessor_id) {
//                                $assessor = $this->groupRepository->getRandomAssessor(intval($updatedTask->language_id),
//                                    $updatedTask->assessor_id);
//                                if (!empty($assessor)) {
//                                    $this->emailService->sendTaskMailToAssessor($updatedTask, $assessor, $params);
//                                    addAssessorHistory($task->id, $assessor->id, 'random');
//                                } else {
//                                    // language group doesn't have any assessors
//                                    $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService,
//                                        $updatedTask, $params);
//                                }
//                            } //comm vineri 13.04.2018
                            break;
                        case 'language_id':

                            $reloadPage = true;

                            $this->eventService->handleTaskLanguageChange($updatedTask, $oldTask);


                            $description = 'Task language was changed from ' . $oldTask->language->name . ' to ' . $updatedTask->language->name;
                            break;
                        case 'email':
                            $email = $task->email;
                            if ($task->email != $updatedTask->email) {

                                ### get task again to have papers
                                $task = Task::with('project', 'project.owner', 'language', 'papers.type')->where('id',
                                    $task->id)->first();

                                $testTypes = Paper::where('task_id', $task->id)->where(function ($query) {
                                    $query->where('done', 0)
                                        ->where('status_id', '!=', CANCELED);
//                                        ->where('paper_type_id', '!=', TEST_SPEAKING);
                                    $query->whereDoesntHave('report');
                                })->with('type')
                                    ->orderByRaw("FIELD(papers.paper_type_id, '3', '5', '4', '1', '6') ASC")
                                    ->get()->pluck('type.name')->toArray();

                                $testIds = $task->papers->pluck('paper_type_id')->toArray();
                                if (count($testIds) == 1 && reset($testIds) == TEST_SPEAKING) {
                                    $link = false;
                                } else {
                                    $link = $updatedTask->link;
                                }

                                ###  Send email to test taker
                                $mailSent = $this->emailService->sendEmail([
                                    'email' => $updatedTask->email,
                                    'name' => $updatedTask->name,
                                    'link' => $link,
                                    'company' => $updatedTask->project->owner->name,
                                    'language' => $updatedTask->language->name,
                                    'availability_from' => $task->availability_from,
                                    'availability_to' => $task->availability_to,
                                    'deadline' => isset($task->deadline) ? Carbon::parse($task->deadline)->format('d M Y, H:i') : null,
                                    'tests' => implode(', ', $testTypes)
                                ], MAIL_TEST_TAKE);
                                if ($mailSent) {
                                    addLog([
                                        'type' => TASK_HISTORY,
                                        'task_id' => $updatedTask->id,
                                        'description' => 'Mail resent to test taker for task id ' . $task->id
                                    ]);
                                }
                            }
                            $description = 'Task ' . $key . ' was changed from ' . $email . ' to ' . $value;
                            break;
                        case 'phone':
                        case 'skype':

                            ### 9. Phone number/skype ID updated
                            ### Send email to assessor
                            if (($task->phone != $updatedTask->phone || $task->skype != $updatedTask->skype) && $task->assessor) {
                                $this->emailService->sendEmail([
                                    'email' => $task->assessor->email,
                                    'name' => $task->name,
                                    'skype' => $task->skype != $updatedTask->skype ? $updatedTask->skype : false,
                                    'phone' => $task->phone != $updatedTask->phone ? $updatedTask->phone : false,
                                    'link' => url('task/' . $task->id)
                                ], MAIL_CONTACTS_UPDATED);
                            }

                            $description = 'Task ' . $key . ' was changed from ' . $task->{$key} . ' to ' . $value;
                            break;
                        case 'native':
                            $reloadPage = true;
                            $description = 'Task Native assessor was changed from ' . ($task->native == 1 ? 'Yes' : 'No') . ' to ' . ($value == 1 ? 'Yes' : 'No');
                            break;
                        case 'deadline':
                            $updatedTask->deadline = Carbon::parse($updatedTask->deadline)->format('d M Y \a\t H:i');
                            $description = 'Task Deadline was changed from ' . Carbon::parse($task->deadline)->format('d M Y \a\t H:i')
                                . ' to ' . $updatedTask->deadline;
                            break;
                        case 'followers':

                            $value = array_filter($value, function ($elem) {
                                return $elem !== null;
                            });

                            TaskFollower::where('task_id', $task->id)->delete();

                            foreach ($value as $index => $follower_id) {

                                $this->taskFollowerRepository->createOrSkip([
                                    'user_id' => $follower_id,
                                    'task_id' => $task->id,
                                ]);

                            }

                            return ajaxResponse(SUCCESS, null, [
                                'followers' => $task->followers()->with('user')->get()->pluck('user.first_name')->toArray()
                            ]);

                            break;
                        case 'test': // add new test type
                            $cost = "0.00";

                            $clientId = $task->project->client_id;
                            $projectId = $task->project_id;
                            $languageId = $task->language_id;
                            $prices = $this->pricesRepository->getClientPricesGrouped($clientId, $projectId);

                            $updateArray = [];

                            foreach ($value['paper_type_id'] as $paper_type_id) {
                                $cost = "0.00";
                                $pricingTypeId = $this->pricesRepository->getTestTypeByPaperType($paper_type_id);

                                if (!empty($prices[$languageId]) && !empty($prices[$languageId][$pricingTypeId])) {
                                    $cost = $prices[$languageId][$pricingTypeId]['price'];
                                }

                                if ($cost == "0.00") {
                                    continue;
                                }

                                $this->paperRepository->createOrSkip([
                                    'paper_type_id' => $paper_type_id,
                                    'task_id' => $task->id,
                                    'cost' => $cost
                                ]);

                                ### get task again to have papers
                                $task = Task::with('project', 'project.owner', 'language', 'papers.type')->where('id',
                                    $task->id)->first();

                                $testTypes = Paper::where('task_id', $task->id)->where(function ($query) {
                                    $query->where('done', 0)
                                        ->where('status_id', '!=', CANCELED);
                                    $query->whereDoesntHave('report');
                                })->with('type')
                                    ->orderByRaw("FIELD(papers.paper_type_id, '3', '5', '4', '1', '6') ASC")
                                    ->get()->pluck('type.name')->toArray();

                                $testIds = $task->papers->pluck('paper_type_id')->toArray();
                                if (count($testIds) == 1 && reset($testIds) == TEST_SPEAKING) {
                                    $link = false;
                                } else {
                                    $link = $updatedTask->link;
                                }

                                ###  Send email to test taker
                                $mailSent = $this->emailService->sendEmail([
                                    'email' => $updatedTask->email,
                                    'name' => $updatedTask->name,
                                    'link' => $link,
                                    'company' => $updatedTask->project->owner->name,
                                    'language' => $updatedTask->language->name,
                                    'availability_from' => $task->availability_from,
                                    'availability_to' => $task->availability_to,
                                    'deadline' => isset($task->deadline) ? Carbon::parse($task->deadline)->format('d M Y, H:i') : null,
                                    'tests' => implode(', ', $testTypes)
                                ], MAIL_TEST_TAKE);
                                if ($mailSent) {
                                    addLog([
                                        'type' => TASK_HISTORY,
                                        'task_id' => $updatedTask->id,
                                        'description' => 'Mail resent to test taker for task id ' . $task->id
                                    ]);
                                }


                                if (($paper_type_id == TEST_SPEAKING || $paper_type_id == TEST_WRITING) && !isset($params['assessor_id'])) {
                                    $assessor = $this->groupRepository->getRandomAssessor($task->language_id, 0,
                                        $params['native']);
                                    if ($assessor !== null) {

                                        $updateArray['assessor_id'] = $assessor->id;
                                        ### send mail to assessor
                                        $this->emailService->sendAssessorMail($assessor, $task);

                                        addAssessorHistory($task->id, $assessor->id, 'create');

                                        addLog([
                                            'type' => TASK_HISTORY,
                                            'task_id' => $task->id,
                                            'description' => 'Mail sent to assessor for adding ' . ($paper_type_id == TEST_SPEAKING ? 'Speaking' : 'Writing') . ' Test'
                                        ]);

                                    } else { ### language group doesn't have any assessors

                                        $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService,
                                            $task, $params);
                                        addLog([
                                            'type' => MAIL_SENT,
                                            'task_id' => $task->id,
                                            'user_id' => auth()->user()->id,
                                            'description' => 'Mail sent to admin because group is empty'
                                        ]);

                                    }
                                } elseif (($paper_type_id == TEST_SPEAKING || $paper_type_id == TEST_WRITING) && isset($params['assessor_id'])) {
                                    $assessor = $this->userRepository->getById($params['assessor_id']);
                                    $this->emailService->sendAssessorMail($assessor, $task);
                                    addAssessorHistory($task->id, $assessor->id, 'manual');
                                    addLog([
                                        'type' => TASK_HISTORY,
                                        'task_id' => $task->id,
                                        'description' => 'Mail sent to assessor for adding ' . ($paper_type_id == TEST_SPEAKING ? 'Speaking' : 'Writing') . ' Test'
                                    ]);
                                }

                            }

                            ### deadline
                            if ($value['deadline'] !== null) {
                                $updateArray['deadline'] = Carbon::parse($value['deadline']);
                            }

                            ### availability
                            $availability_date = $value['availability_from'] !== null ? date('Y-m-d',
                                strtotime($value['availability_from'])) : null;
                            $updateArray['availability_from'] = $value['from_date'] !== null ?
                                date($availability_date . ' H:i:s', strtotime($value['from_date'])) : null;
                            $updateArray['availability_to'] = $value['to_date'] !== null ?
                                date($availability_date . ' H:i:s', strtotime($value['to_date'])) : null;

                            if ($availability_date !== null && $updateArray['availability_from'] === null) {
                                $updateArray['availability_from'] = Carbon::parse($availability_date)->addHours(9);
                                $updateArray['availability_to'] = Carbon::parse($availability_date)->addHours(18);
                            }

                            if (!empty($updateArray)) {
                                $this->taskRepository->update($task->id, $updateArray);
                                $updatedTask = $this->taskRepository->getById($task->id);
                            }

                            ### task activity log
                            if (!empty($updatedTask->availability_from) && !empty($updatedTask->availability_to)) {
                                addLog([
                                    'type' => TASK_UPDATE,
                                    'description' => 'Scheduled for ' .
                                        date('d M Y', strtotime($updatedTask->availability_from)) . ' from ' .
                                        date('H:i', strtotime($updatedTask->availability_from)) . ' to ' .
                                        date('H:i', strtotime($updatedTask->availability_to)),
                                    'task_id' => $updatedTask->id,
                                    'user_id' => auth()->user()->id
                                ]);
                            }

                            $reloadPage = true;
                            $testTypes = PaperType::whereIn('id', $value['paper_type_id'])->pluck('name')->toArray();
                            $description = 'Test ' . implode(', ',
                                    $testTypes) . ' ' . (count($testTypes) > 1 ? 'were' : 'was') . ' added';

                            break;
                        default:

                            $fromVal = $task->{$key};
                            $toVal = $value;

                            if (in_array($key, ['bill_client', 'pay_assessor'])) {
                                $fromVal = $fromVal == 0 ? 'No' : 'Yes';
                                $toVal = $toVal == 0 ? 'No' : 'Yes';
                                $key = ucwords(str_replace('_', ' ', $key));
                            } else if ($key == "additional_cost") {
                                $key = ucwords(str_replace('_', ' ', $key));
                            }

                            $description = 'Task ' . $key . ' was changed from ' . $fromVal . ' to ' . $toVal;
                            break;
                    }
                    addLog([
                        'type' => TASK_HISTORY,
                        'description' => $description,
                        'task_id' => $task->id,
                        'user_id' => auth()->user()->id
                    ]);

                    $log_user = $updatedTask->logs->last()->user;
                    if (is_null($log_user)) {
                        $log_user = auth()->user();
                    }

                    return response()->json([
                        'resType' => 'Success',
                        'task' => $updatedTask,
                        'taskStatus' => $updatedTask->status,
                        'log' => $updatedTask->logs->last(),
                        'user' => $log_user,
                        'reloadPage' => $reloadPage
                    ]);
                }
            }

            $log_user = $updatedTask->logs->last()->user;
            if (is_null($log_user)) {
                $log_user = auth()->user();
            }

            return response()->json([
                'resType' => 'Success',
                'task' => $updatedTask,
                'taskStatus' => $updatedTask->status,
                'log' => $updatedTask->logs->last(),
                'user' => $log_user,
                'reloadPage' => $reloadPage
            ]);

        } else { ### is changing paper
            $params = $request->input('paper');

            if (Auth::user()->hasRole('client')) {
                $paper = $this->paperRepository->getById($params['id']);
                if ( !in_array($paper->status_id, [ALLOCATED, CANCELED])) {
                    return response()->json([
                        'resType' => 'error',
                        'resMotive' => ['error' => ['You can\'t update the current test']]
                    ]);
                }
            }

            $this->paperRepository->update($params['id'], $params);
            $paper = $this->paperRepository->getById($params['id']);

            ### get all paper statuses
            $papersStatuses = $this->paperRepository->getAllTaskPapers($task->id)->pluck('status_id')->toArray();

            $statusesNumber = count(array_unique($papersStatuses));
            $filppedStatuses = array_flip($papersStatuses);

            // If all tests have the same status
            if ($statusesNumber === 1) {
                $task->task_status_id = reset($papersStatuses);
                $task->save();
                $reloadPage = true;
            // If all the tests have cancelled or done
            } elseif ($statusesNumber == 2 && isset($filppedStatuses[DONE]) && isset($filppedStatuses[CANCELED])) {
                $task->task_status_id = TaskStatus::STATUS_DONE;
                $task->save();
                $reloadPage = true;
            } elseif ($task->task_status_id == ALLOCATED && $params['status_id'] == DONE) {
                $task->task_status_id = TaskStatus::STATUS_IN_PROGRESS;
                $task->save();
            } elseif ($params['status_id'] == CANCELED && $statusesNumber == 2 && isset($filppedStatuses[DONE])) {
                $task->task_status_id = TaskStatus::STATUS_DONE;
                $task->save();
                $reloadPage = true;
            }

            ### if paper status is Canceled and is Writing or Speaking
            if (isset($params['status_id']) && $params['status_id'] == CANCELED && ($paper->paper_type_id == TEST_SPEAKING || $paper->paper_type_id == TEST_WRITING)) {

                $task = $paper->task;

                ### 11. Assessment cancelled
                ### Send email to assessor
                $this->emailService->sendEmail([
                    'email' => $task->assessor->email,
                    'name' => $task->name
                ], MAIL_ASSESSMENT_CANCELED);

            }

            ### if paper status is CancelALLOCATEDed and is Speaking
            ### @TODO [LOW] Might be a bug
            if (isset($params['status_id']) && $params['status_id'] == ALLOCATED && $paper->paper_type_id == TEST_SPEAKING) {

                $task = $paper->task;

                ### 11. Assessment cancelled
                ### Send email to assessor
                $this->emailService->sendAssessorMail($task->assessor, $task);

            }


            ### if paper status is Canceled
            if (isset($params['status_id'])) {
                addLog([
                    'type' => TASK_HISTORY,
                    'description' => 'Test ' . $paper->type->name . ' status has been changed to ' . $paper->status->name . '.',
                    'task_id' => $task->id,
                    'user_id' => auth()->user()->id
                ]);
                return response()->json([
                    'resType' => 'Success',
                    'log' => $task->logs->last(),
                    'user' => auth()->user(),
                    'reloadPage' => $reloadPage
                ]);
            }

            return response()->json([
                'resType' => 'Success',
                'reloadPage' => $reloadPage
            ]);
        }
    }

    /**
     * Refuse the specified task.
     *
     * @param  Task $task
     * @param Request $request
     * @return array
     */
    public function refuse(Task $task, Request $request)
    {

        $taskCanRefuse = $task->canRefuse();

        if (!$request->ajax() && !$taskCanRefuse) {
            //abort(403, 'You cannot refuse this task!');
            $request->session()->flash('flash_info', 'You cannot refuse this task!');
            return redirect('/tasks?all=true');
        }

        if (!$taskCanRefuse) {
            return ajaxResponse(ERROR, 'You cannot refuse this task!');
        }

        if ($task->task_status_id == TaskStatus::STATUS_CANCELED) {
            $request->session()->flash('flash_info', 'Task is canceled!');
            return redirect('/tasks?all=true');
        }

        $oldAssesor = $task->assessor;

        $assessor = $this->groupRepository->getRandomAssessorRefuse($task);

        if (!empty($assessor)) {
            $task->assessor_id = $assessor->id;
            $this->emailService->sendAssessorMail($assessor, $task);
            addAssessorHistory($task->id, $assessor->id, 'random');
        } else {
            // language group doesn't have any assessors
            $task->assessor_id = null;
            $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService, $task, $task->toArray());
        }

        $task->save();

        if (!$assessor) {
            $description = 'The assessor ' . $oldAssesor->first_name . ' ' . $oldAssesor->last_name . ' refused this task! No available assessor was found!';
        } else {
            $description = 'The assessor ' . $oldAssesor->first_name . ' ' . $oldAssesor->last_name . ' refused this task! ' . 'The assessor was changed to ' . $assessor->first_name . ' ' . $assessor->last_name . '.';
        }


        addLog([
            'type' => TASK_HISTORY,
            'description' => $description,
            'task_id' => $task->id,
            'user_id' => auth()->user()->id
        ]);

        if ($request->ajax()) {
            return ajaxResponse(SUCCESS, null, [
                'assessor' => $task->assessor
            ]);
        } else {
            $request->session()->flash('flash_success', 'Task was successfully refused!');
            return redirect('/tasks?all=true');
        }

    }

    /**
     * Update the specified task field.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Task $task
     * @return array
     * @throws \Throwable
     */
    public function assessments(Request $request, Task $task)
    {
        $params = $request->toArray();
        $not_done = 0;

        if (!empty($request->input('native'))) {
            // speaking test native speaker report
            $paper = $task->speakingTest(false);

            ### if is an edit
            if (isset($params['paper_id'])) {
                $paper = Paper::find($params['paper_id']);
            }

            $ability = 7;
            $grade = 'N';
            $test_type = 'Speaking';

            $attributes = [
                'paper_id' => $paper->id,
                'assessor_id' => $task->assessor_id,
                'ability' => $ability,
                'grade' => $grade,
                'assessments' => json_encode($request->only(['speaking'])),
            ];

            ### if is an edit
            if (isset($params['report_id'])) {
                unset($attributes['paper_id']);
                unset($attributes['assessor_id']);
                $paperReport = $this->paperReportRepository->update($params['report_id'], $attributes);
            } else {
                $paperReport = $this->paperReportRepository->create($attributes);
            }

        } else {
            $reportType = isset($params['speaking']) ? 'speaking' : 'writing';

            unset($params['_token']);

            if ($reportType == 'speaking') {
                $paper = $task->{$reportType . 'Test'}(false);
            } else {
                $paper = $task->{$reportType . 'Test'}();
            }


            $abilities = [
                'Pre-A1' => 0,
                'A1' => 1,
                'A2' => 2,
                'A2+' => 2.5,
                'B1' => 3,
                'B1+' => 3.5,
                'B2' => 4,
                'B2+' => 4.5,
                'C1' => 5,
                'C2' => 6
            ];

            $indexes = [
                'Pre-A1' => 0,
                'A1' => 1,
                'A2' => 2,
                'A2+' => 3,
                'B1' => 4,
                'B1+' => 5,
                'B2' => 6,
                'B2+' => 7,
                'C1' => 8,
                'C2' => 9
            ];

            $reverseIndexes = array_flip($indexes);


            $grade = (string)$params['general_descriptors'];
            $index = $indexes[$grade] - 1;
            if ($index < 0) {
                $index = 0;
            }

            if (in_array($index, [3, 5, 7])) {
                $index--;
            }

            $prevIndex = $reverseIndexes[$index];
            $sliderOneValue = $params[$reportType]['ability-aquired'] / 100;


            $sliderTwoValue = 0;
            if ($sliderOneValue == 1 || $grade == PRE_A1) { // if slider 1 is 100%
                $sliderTwoValue = $params[$reportType]['ability-next'] / 100;
            }

            if ($grade == PRE_A1) {
                $sliderOneValue = 0;
            }

            $paperAbility = $abilities[$prevIndex] + $sliderOneValue + $sliderTwoValue;

            if (substr($grade, -1) === '+') {
                $paperAbility = $abilities[$grade];
            }

            //$paperAbility = $paperAbility > 6 ? 6 : $paperAbility;
            $ability = $paperAbility;
//            $grade = str_replace('+', '', $params['general_descriptors']);

            $test_type = ucwords($reportType);

            ### if is an edit
            if (isset($params['paper_id'])) {
                $paper = Paper::find($params['paper_id']);
            }


            $attributes = [
                'paper_id' => $paper->id,
                'assessor_id' => $task->assessor_id,
                'ability' => $paperAbility,
                'grade' => $grade,
                'assessments' => json_encode($params),
            ];

            ### if is an edit
            if (isset($params['report_id'])) {
                unset($attributes['paper_id']);
                unset($attributes['assessor_id']);
                $paperReport = $this->paperReportRepository->update($params['report_id'], $attributes);
            } else {
                $paperReport = $this->paperReportRepository->create($attributes);
            }

            ### 5. Feedback request
            if ($reportType == 'speaking') {

//                disabled this for the moment
//                $this->emailService->sendEmail([
//                    'email' => $task->email,
//                    'name' => $task->name,
//                    'assessor_first_name' => $task->assessor->first_name,
//                    'language' => $task->language->name
//                ], MAIL_FEEDBACK_REQ);

            }

        }

        $paper->done = 1;
        $paper->status_id = DONE;
        $paper->save();

        $papers_status = [];
        foreach ($task->papers as $paper) {
            $papers_status[] = $paper->done;
        }

        if (count($task->papers->toArray()) == 1 || !in_array($not_done, $papers_status)) {
            $this->taskRepository->update($task->id, ['task_status_id' => DONE]);
        }

        if (count($task->papers->toArray()) > 1 && in_array($not_done, $papers_status)) {
            $this->taskRepository->update($task->id, ['task_status_id' => IN_PROGRESS]);
        }

        if (Task::allTestsAreDoneAndHaveReports($task->id)) {

            $taskItem = Task::where('id', $task->id)->with([
                'papers' => function ($q) {
                    $q->with('report');
                }
            ])->first();

            $this->sendMailTaskDone($taskItem);
        } else {
            $this->emailService->sendEmailOneTestFinished($task, $ability, $grade, $test_type);
        }

        addLog([
            'description' => sprintf("The assessor uploaded the %s report on %s", $test_type, date('d.m.Y, H:i')),
            'type' => TASK_HISTORY,
            'task_id' => $task->id,
            'user_id' => auth()->user()->id
        ]);


        return response()->json([
            'resType' => 'Success',
            'paperReport' => $paperReport,
            'paper' => $this->paperRepository->getById($paper->id)
        ]);
    }

    /**
     * @param $task
     */
    public function sendMailTaskDone($task)
    {


        $attributes = [];
        $attributes['email'] = $task->addedBy->email;
        $attributes['test_results'] = '';

        $testResults = [];

        foreach ($task->papers as $paper) {
            $tr = $paper->type->name;
            if(isset($paper->report->grade)){
                $tr .= ' (' . $paper->report->grade . ')';
            }
            $testResults[] = $tr;
        }

        $attributes['test_results'] = implode('<br>', $testResults);

        $showAssessorInPDF = true;

        if ($task->addedBy->hasRole('client')) {
            $showAssessorInPDF = false;
        }

        $path = storage_path($task->name . '_' . $task->language->name . '_Eucom language assessment.pdf');
        if (file_exists($path)) {
            unlink($path);
        }

        $pdf = $this->generate($task, $showAssessorInPDF);

        try {
            $pdf->save($path);
            $pfd_ok = true;
        } catch (\Exception $e) {
            $pfd_ok = false;
//            addLog([
//                'type' => 'Error',
//                'description' => 'Could not generate pdf report for task id ' . $task->id
//            ]);
        }

        if ($pfd_ok) {
            $attributes['attachment'] = $path;
        }
        $attributes['name'] = $task->name;
        $attributes['link'] = url('task/' . $task->id);

        $mail_sent = $this->emailService->sendEmail($attributes, MAIL_TASK_DONE);

        if ($mail_sent === false) {
            addLog([
                'type' => 'Error',
                'description' => 'Could not send email to task owner for TASK_DONE (task id ' . $task->id . ')'
            ]);
        }

        $followers = $task->followers;
        if (!empty($followers)) {
            foreach ($followers as $follower) {

                $showAssessorInPDF = !$follower->user->hasRole('client');

                $path = storage_path('TaskReport-' . $task->id . '.pdf');
                if (file_exists($path)) {
                    unlink($path);
                }

                $pdf = $this->generate($task, $showAssessorInPDF);

                try {
                    $pdf->save($path);
                    $pfd_ok = true;
                } catch (\Exception $e) {
                    $pfd_ok = false;
//                    addLog([
//                        'type' => 'Error',
//                        'description' => 'Could not generate pdf report for task id ' . $task->id
//                    ]);
                }

                $attributesFollower = [
                    'email' => $follower->user->email,
                    'name' => $task->name,
                    'link' => url('task/' . $task->id)
                ];

                if ($pfd_ok) {
                    $attributes['attachment'] = $path;
                }


                $mail_sent = $this->emailService->sendEmail($attributesFollower, MAIL_TASK_DONE);

                if ($mail_sent === false) {
                    addLog([
                        'type' => 'Error',
                        'description' => 'Could not send email to follower for TASK_DONE (task id ' . $task->id . ')'
                    ]);
                }
            }
        }

        @unlink($path);
    }


    /**
     * Update the specified task field.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Task $task
     * @return array
     */
    public function requestUpdates(Request $request, Task $task)
    {
        $description = $request->input('description');
        $reloadPage = false;

        ### get update by slug
        $taskUpdate = TaskUpdate::where('slug', $request->status_slug)->first();

        ### If you want the status to not change, set a value to this variable
        $changeTaskStatus = null;

        ### 8.Status update request from client

        if ($request->status_slug == 'status-update') {
            $description = $taskUpdate->display_name;
            $changeTaskStatus = TaskStatus::STATUS_ISSUE;
        }

        if (Auth::user()->hasRole('client') &&
            !is_null($task->assessor) &&
            ($task->assessor->deleted_at || !$task->assessor->is_active_now)
        ) {
            $this->emailService->sendEmail([
                'email' => Setting::where('key', 'eucom_email')->first()->value,
                'task_id' => $task->id,
                'language' => $task->language->name,
                'link' => url('task/' . $task->id),
            ], MAIL_ASSESSOR_IS_INACTIVE);
        }

        # if the current user is the user who created the task and is client and status is status-update
        if ($task->added_by_id == Auth::user()->id && Auth::user()->hasRole('client') && $request->status_slug == 'status-update') {

            if (!is_null($task->assessor)) {
                ### send email to assesor
                $this->emailService->sendEmail([
                    'email' => $task->assessor->email,
                    'name' => $task->name,
                    'link' => url('task/' . $task->id)
                ], MAIL_CLIENT_UPDATE_REQ);
            }

            ### check task already had this status
            $logs = Log::where('task_id', $task->id)
                ->where('description', $description)
                ->where('type', TASK_UPDATE)->count();

            if ($logs > 0) {

                ### send email to project owner
                $this->emailService->sendEmail([
                    'email' => $task->project->user->email,
                    'name' => $task->name,
                    'link' => url('task/' . $task->id)
                ], MAIL_CLIENT_UPDATE_REQ);
            }

        }

        ### 10. Candidate (re)scheduled by client
        ### Send email to assessor when a reschedule is made

        if ($request->has('reschedule')) {

            ## Get task followers
            $followers = $task->followers()->pluck('user_id')->toArray();

            ## if the current user is the user who created the task or current user is a follower
            if ($task->added_by_id == Auth::user()->id
                || in_array(Auth::user()->id, $followers)
                || in_array(auth()->user()->id, $task->project->participants->pluck('user_id')->toArray())
                || auth()->user()->hasRole(['master', 'administrator'])) {

                if ($task->task_status_id == TaskStatus::STATUS_CANCELED) {
                    $task->task_status_id = TaskStatus::STATUS_ALLOCATED;
                    $task->save();
                    Paper::where('task_id', $task->id)
                        ->where('paper_type_id', TEST_SPEAKING)
                        ->update(['status_id' => ALLOCATED]);
                    addLog([
                        'type' => TASK_HISTORY,
                        'description' => 'Task and Speaking Test statuses were changed automatically to Allocated',
                        'task_id' => $task->id,
                        'user_id' => auth()->user()->id
                    ]);
                    $reloadPage = true;
                }

                if ($task->task_status_id == TaskStatus::STATUS_ISSUE) {
                    $task->task_status_id = TaskStatus::STATUS_IN_PROGRESS;
                    $task->save();
                    Paper::where('task_id', $task->id)
                        ->where('paper_type_id', TEST_SPEAKING)
                        ->update(['status_id' => IN_PROGRESS]);
                    addLog([
                        'type' => TASK_HISTORY,
                        'description' => 'Task and Speaking Test statuses were changed automatically to In Progress',
                        'task_id' => $task->id,
                        'user_id' => auth()->user()->id
                    ]);
                    $reloadPage = true;
                }


                ### set availability
                $reschedule = $request->get('reschedule');
                $avb_from = $reschedule['on'] . ' ' . $reschedule['from'];
                $avb_to = $reschedule['on'] . ' ' . $reschedule['to'];

                if ($avb_from == $avb_to) {
                    $availability = Carbon::parse($avb_from)->format('d M Y, H:i');
                } else {
                    $availability = Carbon::parse($avb_from)->format('d M Y') . ', from ' .
                        Carbon::parse($avb_from)->format('H:i') . ' to ' . Carbon::parse($avb_to)->format('H:i');
                }

                if ($task->assessor) {
                    $this->emailService->sendEmail([
                        'email' => $task->assessor->email,
                        'link' => url('task/' . $task->id),
                        'name' => $task->name,
                        'availability' => $availability,
                    ], MAIL_CANDIDATE_RESCHEDULED);
                }
            }
        }

        ### 14. Candidate no longer interested.
        ### Send email to task owner and followers when
        ### assessor requests updates status with candidate-refused
        if ($request->status_slug == 'candidate-no-longer' && Auth::user()->id == $task->assessor_id) {

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_CANDIDATE_UNINTERESTED);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_CANDIDATE_UNINTERESTED);
                }
            }

        }


        ### 15. Candidate refused to be assessed.
        ### Send email to task owner and followers when
        ### assessor requests updates status with candidate-refused
        if ($request->status_slug == 'candidate-refused' && Auth::user()->id == $task->assessor_id) {

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_CANDIDATE_REFUSED);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_CANDIDATE_REFUSED);
                }
            }

        }

        ### 16. Candidate indicated that he/she should be assessed for a different language.
        ### Send email to task owner and followers when
        ### assessor requests updates status with candidate-different-language
        if ($request->status_slug == 'candidate-different-language' && Auth::user()->id == $task->assessor_id) {

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_CANDIDATE_DIFFERENT_LANG);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_CANDIDATE_DIFFERENT_LANG);
                }
            }

        }


        ### 17. Check candidate phone number
        ### Send email to task owner and followers when
        ### assessor requests updates status with client-request-phone
        if ($request->status_slug == 'client-request-phone') {

            $description = $taskUpdate->display_name;

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_CANDIDATE_CHECK_PHONE);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_CANDIDATE_CHECK_PHONE);
                }
            }


        }

        ### 18. Wrong number. Someone else answered
        ### Send email to task owner and followers when
        ### someone requests updates status with wrong number
        if ($request->status_slug == 'wrong-number') {

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_WRONG_NUMBER);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_WRONG_NUMBER);
                }
            }

        }

        ### 19. Add candidate skype ID
        ### Send email to task owner and followers when
        ### assessor requests to change skype ID
        if ($request->status_slug == 'client-request-skype') {

            $description = $taskUpdate->display_name;

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_CANDIDATE_SKYPE_ADD);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_CANDIDATE_SKYPE_ADD);
                }
            }

        }

        ### 21. Bad reception. The candidate will call back later
        ### Send email to task owner and followers when
        if ($request->status_slug == 'bad-reception' && Auth::user()->id == $task->assessor_id) {

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_BAD_RECEPTION);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_BAD_RECEPTION);
                }
            }

        }

        ### 22. Candidate had issues during online test. Test was reset
        ### Send email to task owner and followers when
        if ($request->status_slug == 'test-was-reset') {

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_TEST_WAS_RESET);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_TEST_WAS_RESET);
                }
            }

        }

        ### 23. Candidate did not answer at scheduled time
        ### Send email to task owner and followers when
        if ($request->status_slug == 'candidate-did-not-answer' && Auth::user()->id == $task->assessor_id) {

            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
                'name' => $task->name
            ], MAIL_CANDIDATE_DID_NOT_ANSWER);

            ### Send to followers
            $followers = $task->followers;
            if (!empty($followers)) {
                foreach ($followers as $follower) {
                    $this->emailService->sendEmail([
                        'email' => $follower->user->email,
                        'name' => $task->name,
                        'link' => url('task/' . $task->id)
                    ], MAIL_CANDIDATE_DID_NOT_ANSWER);
                }
            }

        }

        ### 27. Candidate was called
        if ($request->status_slug == MAIL_CANDIDATE_CANCELLED_CALL_BACK) {
            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
            ], MAIL_CANDIDATE_CANCELLED_CALL_BACK);
            $changeTaskStatus = $task->task_status_id;
        }

        ### 28. Candidate did not pass the Identity validation step
        if ($request->status_slug == MAIL_CANDIDATE_NOT_VALIDATED) {
            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
            ], MAIL_CANDIDATE_NOT_VALIDATED);
            $this->taskRepository->update($task->id, ['task_status_id' => TaskStatus::STATUS_ISSUE]);
        }

        ### 29. Candidate said he had issues during online test, asked to take it again
        if ($request->status_slug == MAIL_CANDIDATE_ISSUES_DURING_TEST) {
            $this->emailService->sendEmail([
                'email' => Setting::where('key', 'eucom_email')->first()->value,
                'link' => url('task/' . $task->id),
            ], MAIL_CANDIDATE_ISSUES_DURING_TEST);
            $changeTaskStatus = $task->task_status_id;
        }

        ### 30. Candidate was called several times but line was engaged each time. SMS text sent
        if ($request->status_slug == MAIL_CANDIDATE_BUSY_SMS_SENT) {
            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'link' => url('task/' . $task->id),
            ], MAIL_CANDIDATE_BUSY_SMS_SENT);
            $this->taskRepository->update($task->id, ['task_status_id' => TaskStatus::STATUS_ISSUE]);
        }

        ### 31. Please contact candidate via Whatsapp
        if ($request->status_slug == MAIL_CONTACT_VIA_WHATSAPP && $task->assessor) {
            $this->emailService->sendEmail([
                'email' => $task->assessor->email,
                'link' => url('task/' . $task->id),
            ], MAIL_CONTACT_VIA_WHATSAPP);
        }

        ### 32. Please contact candidate via Skype
        if ($request->status_slug == MAIL_CONTACT_VIA_SKYPE && $task->assessor) {
            $this->emailService->sendEmail([
                'email' => $task->assessor->email,
                'link' => url('task/' . $task->id),
            ], MAIL_CONTACT_VIA_SKYPE);
        }


        ### change status to issue
        if ($request->status_slug == 'wrong-number') {
            $this->taskRepository->update($task->id, ['task_status_id' => TaskStatus::STATUS_ISSUE]);
        }

        if (!empty($request->input('reschedule'))) {
            $reschedule = $request->input('reschedule');
            $this->taskRepository->update($task->id, [
                'availability_from' => date('Y-m-d H:i:s', strtotime($reschedule['on'] . ' ' . $reschedule['from'])),
                'availability_to' => date('Y-m-d H:i:s', strtotime($reschedule['on'] . ' ' . $reschedule['to']))
            ]);
            // if assessor reschedules, send the reminder again
            Paper::where('task_id', $task->id)->where('paper_type_id', TEST_SPEAKING)->update(['reminder_update_sent' => 0]);
        } else  {
            if ($request->status_slug == 'custom' && Auth::user()->hasRole('assessor')) {
                $reloadPage = false;
            } else if ($changeTaskStatus == TaskStatus::STATUS_ISSUE || is_null($changeTaskStatus)) {
                $reloadPage = true;
                addLog([
                    'type' => TASK_HISTORY,
                    'description' => 'Task status was changed automatically to Issue',
                    'task_id' => $task->id,
                    'user_id' => auth()->user()->id
                ]);
                $this->taskRepository->update($task->id, ['task_status_id' => TaskStatus::STATUS_ISSUE]);
            }
        }


        if ($request->status_slug == "custom") {
            $description = "__custom__" . $description;
        }

        addLog([
            'type' => TASK_UPDATE,
            'description' => $description,
            'task_id' => $task->id,
            'user_id' => auth()->user()->id
        ]);

        $log = $task->logs->last();
        $task = $this->taskRepository->getById($task->id);

        return response()->json([
            'resType' => 'Success',
            'log' => $log,
            'user' => $log->user,
            'task' => $task,
            'reloadPage' => true
        ]);
    }

    /**
     * Resend invitation mail to the test-taker
     *
     * @param  Task $task
     * @param bool $toEmail
     * @return array
     */
    public function resendMail(Task $task, $toEmail = false)
    {

        $task = Task::with('project', 'project.owner', 'language')->where('id', $task->id)->first();

        $testIds = $task->papers->pluck('paper_type_id')->toArray();
        if (count($testIds) == 1 && reset($testIds) == TEST_SPEAKING) {
            $link = false;
        } else {
            $link = url('test/instructions/' . $task->link);
        }

        $deadline = isset($task->deadline) ? Carbon::parse($task->deadline)->format('d M Y, H:i') : null;

        ###  Send email to test taker
        $mailSent = $this->emailService->sendEmail([
            'email' => $task->email,
            'name' => $task->name,
            'link' => $link,
            'company' => $task->project->owner->name,
            'language' => $task->language->name,
            'deadline' => $deadline,
            'language_use_new_link' => (string) View::make('emails.partials.button', ['text' => 'Take Language Use Test', 'href' => $link . "/1"]),
            'speaking_link' => (string) View::make('emails.partials.button', ['text' => 'Take Speaking Test', 'href' => $link . "/2"]),
            'writing_link' => (string) View::make('emails.partials.button', ['text' => 'Take Writing Test', 'href' => $link . "/3"]),
            'listening_link' => (string) View::make('emails.partials.button', ['text' => 'Take Listening Test', 'href' => $link . "/4"]),
            'reading_link' => (string) View::make('emails.partials.button', ['text' => 'Take Reading Test', 'href' => $link . "/5"]),
            'language_use_link' => (string) View::make('emails.partials.button', ['text' => 'Take Language Use Test', 'href' => $link . "/6"]),
            'testList' => [
                'language_use_new' => in_array(TEST_LANGUAGE_USE_NEW, $testIds),
                'speaking' => in_array(TEST_SPEAKING, $testIds),
                'writing' => in_array(TEST_WRITING, $testIds),
                'listening' => in_array(TEST_LISTENING, $testIds),
                'reading' => in_array(TEST_READING, $testIds),
                'language_use' => in_array(TEST_LANGUAGE_USE, $testIds),
                'online' => !!$deadline,
            ]
        ], MAIL_TEST_TAKE_MULTIPLE, ['resent_notice' => true]);

        if ($mailSent) {
            addLog([
                'type' => TASK_HISTORY,
                'description' => 'Resent mail to test taker for task id ' . $task->id,
                'task_id' => $task->id,
                'user_id' => auth()->user()->id
            ]);
        }

        return ajaxResponse(SUCCESS, null, [
            'link_expires_at' => Carbon::parse($task->link_expires_at)->format('d M Y H:i'),
            'log' => $task->logs->last(),
            'user' => auth()->user()
        ]);
    }

    /**
     * Reset online tests for task
     *
     * @param  Task $task
     * @return array
     */
    public function reset(Task $task)
    {

        $onlineTests = $task->getOnlineTests();

        $paperIds = $onlineTests->pluck('id');
        PaperReport::whereIn('paper_id', $paperIds)->delete();

        foreach ($onlineTests as $paper) {

            if (!empty($paper->paper_answers)) {
                foreach ($paper->paper_answers as $answer) {
                    $answer->delete();
                }
            }
            $this->taskRepository->update($task->id, [
                'task_status_id' => TaskStatus::STATUS_IN_PROGRESS
            ]);

            $this->paperRepository->update($paper->id, [
                'done' => false,
                'status_id' => TaskStatus::STATUS_ALLOCATED,
                'current_question_id' => null,
                'current_choices' => null,
                'current_audio_time' => null,
                'question_current_time' => null,
                'started_at' => null,
                'ended_at' => null,
            ]);
        }

        $task = Task::with('project', 'project.owner', 'language')->where('id', $task->id)->first();

        $this->resendMail($task);

        addLog([
            'type' => TASK_HISTORY,
            'description' => 'Online tests were reset.',
            'task_id' => $task->id,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'resType' => 'Success'
        ]);
    }

    /**
     * Reset link for tests
     *
     * @param  Task $task
     * @return array
     */
    public function resetLink(Task $task)
    {
        $task->link = md5($task->id . time());
        $task->link_expires_at = Carbon::now()->addMonths(1);
        $task->save();

        return ajaxResponse(SUCCESS);
    }

    /**
     * Reset report
     *
     * @param  Paper $paper
     * @return array
     */
    public function resetReport(Paper $paper)
    {
        $sendReportMail = false;
        $oldGrade = "none";
        $oldType = "none";
        if ($paper->report) {
            $oldGrade = $paper->report->grade;
            $oldType = $paper->report->type;
            $sendReportMail = true;
        }

        ### delete reports
        $paper->report()->delete();

        ### get task details
        $task = Task::with('project', 'project.owner', 'language')->where('id', $paper->task->id)->first();
        $task->task_status_id = TaskStatus::STATUS_IN_PROGRESS;
        $task->save();

        if ($paper->type->id == TEST_SPEAKING) {
            $paper->done = 0;
            $paper->status_id = ALLOCATED;
            $paper->save();
        }

        if ($sendReportMail) {
            ### 25. Reset notice
            ### Send reset notice to the client
            $this->emailService->sendEmail([
                'email' => $task->addedBy->email,
                'name' => $task->name,
                'language' => $task->language->name,
                'grade' => $oldGrade,
                'test_type' => $oldType,
            ], MAIL_RESET_REPORT);
        }

        ### send email to assessor
        if (!empty($task->assessor)) {
            $mailSent = $this->emailService->sendEmail([
                'email' => $task->assessor->email,
                'name' => $task->name,
                'link' => url('task/' . $task->id)
            ], MAIL_REPORT_AGAIN);

            if ($mailSent) {
                addLog([
                    'type' => TASK_HISTORY,
                    'description' => $paper->type->name . ' report has been successfully reset.',
                    'task_id' => $task->id,
                    'user_id' => auth()->user()->id
                ]);
            }
        }

        return ajaxResponse(SUCCESS, null, $paper);
    }

    /**
     * Reset test
     *
     * @param  Paper $paper
     * @return array
     */
    public function resetTest(Paper $paper)
    {
        ### delete reports
        $paper->report()->delete();

        $resetFields = [
            'done' => 0,
            'status_id' => ALLOCATED,
            'current_question_id' => null,
            'current_choices' => null,
            'current_audio_time' => null,
            'question_current_time' => null,
            'started_at' => null,
            'ended_at' => null,
        ];

        ### reset paper fields
        $this->paperRepository->update($paper->id, $resetFields);

        ### delete paper answers and reports
        $paper->paper_answers()->delete();

        ### get task
        $task = $this->taskRepository->getById($paper->task_id);
        if ($task->task_status_id == DONE) {
            ### check if has other done tests
            $doneTests = Paper::where('task_id', $task->id)->where('status_id', DONE)->count();
            if ($doneTests > 0) {
                $task->task_status_id = TaskStatus::STATUS_IN_PROGRESS;
            } else {
                $task->task_status_id = TaskStatus::STATUS_ALLOCATED;
            }
            $task->save();
        }

        addLog([
            'type' => TASK_HISTORY,
            'description' => $paper->type->name . ' test was reset',
            'task_id' => $task->id,
            'user_id' => auth()->user()->id
        ]);

        ### send mail to test taker
        $this->resendMail($task);

        return ajaxResponse(SUCCESS, null, $paper);
    }

    /**
     * Verify test-taker.
     *
     * @param  Project $project
     * @param  Request $request
     * @return array
     */
    public function verifyTestTaker(Project $project, Request $request)
    {
        $params = $request->toArray();

        $tasks = [];
        foreach ($params['languages'] as $language) {
            $result = $this->taskRepository->verifyTestTaker($language, $params);

            $tasks[] = [
                'other_project' => $result['other_project'],
                'task' => $result['task'],
                'language_id' => $language
            ];

        }

        return ajaxResponse(SUCCESS, null, compact('tasks'));
    }

    /**
     * Copy task data and reports to new client.
     *
     * @param  Project $project
     * @param  Request $request
     * @return array
     */
    public function duplicateTask(Project $project, Request $request)
    {
        $params = $request->toArray();
        $taskToDuplicate = $this->taskRepository->getById($params['task_to_duplicate']);
        $taskToDuplicateAttributes = $taskToDuplicate->attributesToArray();

        $attributes = $taskToDuplicateAttributes;
        $language = $attributes['language_id'];
        $native = $attributes['native'];
        $halfPrice = false;

        $attributes['project_id'] = $params['project_id'];
        $attributes['assessor_id'] = null;
        $attributes['name'] = $params['name'];
        $attributes['phone'] = $params['phone'];
        $attributes['skype'] = $params['skype'];
        $attributes['added_by_id'] = auth()->user()->id;
        if (isset($params['half_price']) && $params['half_price']) {
            $attributes['bill_client'] = Task::BILL_CLIENT_HALF;
            $halfPrice = true;
        }

        $projectId = $project->id;
        $clientId = $project->client_id;
        $prices = $this->pricesRepository->getClientPricesGrouped($clientId, $projectId);

        ### CREATE TASK
        $newTask = $this->taskRepository->create($attributes);

        $papers = array_keys($params['languagesExtra'][$newTask->language_id]['PaperTypes']);

        foreach ($taskToDuplicate->papers as $paper) {

            if (!$paper->report || !in_array($paper->paper_type_id, $papers)) {
                continue;
            }

            $attributes = $paper->toArray();

            unset($attributes['id']);
            unset($attributes['invoice_id']);

            $attributes['task_id'] = $newTask->id;

            ### SET PRICING
            $cost = "0.00";
            $key = $attributes['paper_type_id'];

            if ($key == TEST_SPEAKING && $native) {
                $pricingTypeId = PricingType::SPEAKING_NATIVE;
            } else if ($key == TEST_WRITING && $native) {
                $pricingTypeId = PricingType::WRITING_NATIVE;
            } else {
                $pricingTypeId = $this->pricesRepository->getTestTypeByPaperType($key);
            }

            if (!empty($prices[$language]) && !empty($prices[$language][$pricingTypeId])) {
                $cost = $prices[$language][$pricingTypeId]['price'];

                if ($halfPrice) {
                    $newCost = floatval($cost) / 2;
                    $cost = number_format($newCost, 2, '.', '');
                }
            }

            $attributes['cost'] = $cost;

            ### CREATE PAPERS
            $newPaper = $this->paperRepository->create($attributes);

            ### CREATE REPORT
            if (!empty($paper->report)) {
                $attributes = $paper->report->toArray();
                unset($attributes['id']);
                $attributes['paper_id'] = $newPaper->id;
                $this->paperReportRepository->create($attributes);
            }

            ### CREATE PAPER ANSWERS
            if (!empty($paper->paper_answers)) {
                foreach ($paper->paper_answers as $answer) {
                    $attributes = $answer->toArray();
                    unset($attributes['id']);
                    $attributes['paper_id'] = $newPaper->id;
                    $attributes['task_id'] = $newPaper->task_id;
                    $this->paperAnswerRepository->create($attributes);
                }
            }
        }

        ### CREATE FOLLOWERS
        if (isset($params['followers'])) {
            foreach ($params['followers'] as $follower) {

                $followerModel = $this->taskFollowerRepository->createOrSkip([
                    'user_id' => $follower,
                    'task_id' => $taskToDuplicate->id,
                ]);
            }
        }

        $this->taskRepository->update($newTask->id, ['task_status_id' => TaskStatus::STATUS_DONE]);

        return ajaxResponse(SUCCESS);
    }

    /**
     * @param $reportId
     * @return mixed
     */
    public function getTestTable($report_id)
    {
       // $paper = $this->paperRepository->getById($paperId);

        $report = PaperReport::withTrashed()->with([
            'paper',
            'paper.paper_answers',
            'paper.paper_answers.question',
            'paper.paper_answers.question.questionChoices',
            'paper.task',
            'paper.type',
            'paper.type.languagePaperType',
            'paper.type.languagePaperType.language'
        ])
            ->with(
                [
                    'paper.paper_answers.question.questionChoices' => function ($q) use ($report_id) {
                        $q->where('correct', 1);
                    },
                    'paper.paper_answers' => function($q) use ($report_id) {
                        $q->withTrashed();
                        $q->where(function ($q) use ($report_id) {
                            $q->whereNull('report_id');
                            $q->orWhere('report_id', $report_id);
                        });
                    }
                ]
            )
            ->where('id', $report_id)
            ->first();

        if (is_null($report->algorithm)) {
            return 'No data to display';
        }

        $paper = $report->paper;

        $questions = [];
        foreach ($report->paper->paper_answers as $paper_answer) {
            $questions[$paper_answer->question->id] = $paper_answer->question;

            if ($paper_answer->question->language_use_type == TEST_LU_ARRANGE){
                $questions[$paper_answer->question->id]['isCorrect'] = $paper_answer->user_answer == $paper_answer->question->body;
            } else {
                $questions[$paper_answer->question->id]['isCorrect'] = $paper_answer->question->questionChoices[0]->id == $paper_answer->user_answer;
            }

        }

        $abilities = [
            0 => 'Pre-A1',
            1 => 'A1',
            2 => 'A2',
            3 => 'B1',
            4 => 'B2',
            5 => 'C1',
            6 => 'C2'
        ];

        $algorithm = json_decode($report->algorithm, true);

        return view('task.table', compact('paper', 'report', 'questions', 'algorithm', 'abilities'));
    }


    /**
     * Download pdf document
     *
     * @param $task_id
     * @return mixed
     * @throws \Throwable
     */
    public function downloadAsPdf($task_id)
    {
        $task = $this->taskRepository->getById($task_id);
        return $this->generate($task)->download($task->name . '_' . $task->language->name . '_Eucom language assessment.pdf');
    }

    /**
     * Generate pdf file
     *
     * @param $task
     * @param bool $showAssessorInPDF
     * @return $this
     * @throws \Throwable
     */
    public function generate($task, $showAssessorInPDF = true)
    {

        $results = $this->getTask($task, false);

        $taskStatuses = $results['taskStatuses'];
        $taskStatusesColor = $results['taskStatusesColor'];
        $skilsAssessments = $results['skilsAssessments'];
        $updateActions = $results['updateActions'];
        $languages = $results['languages'];
        $assessors = $results['assessors'];
        $user_abilities = $results['user_abilities'];
        $defaultSkillAssessment = $this->skilsAssessments;

        $html = view('task.viewPDF',
            compact('task', 'taskStatuses', 'taskStatusesColor', 'skilsAssessments', 'updateActions', 'languages',
                'assessors', 'user_abilities', 'showAssessorInPDF', 'defaultSkillAssessment'))->render();

        $pdf = \App::make('snappy.pdf.wrapper');
//        $pdf->setBinary('C:\wkhtmltopdf\bin\wkhtmltopdf.exe'); //win only
        $pdf->setOption('enable-javascript', true)
            ->setOption('images', true)
            ->setOption('javascript-delay', 3000)
            ->setOption('enable-smart-shrinking', true)
            ->setOption('no-stop-slow-scripts', true);
        $pdf->loadHtml($html);

        return $pdf;
    }


    /**
     * Get task data
     *
     * @param Task $task
     * @param bool $is_user_authenticated
     * @return array
     */
    private function getTask(Task $task, $is_user_authenticated = true)
    {
        $taskStatuses = $this->taskStatusRepository->getAll()->pluck('name', 'id');
        $taskStatusesColor = $this->taskStatusRepository->getAll()->pluck('color', 'name');
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');

        if (in_array($task->project->project_type_id,
            [Project::PROJECT_TYPE_RECRUITING, Project::PROJECT_TYPE_COURSES])) {
            $assessors = $this->userRepository->getAllAssessors();
        } else {
            $assessors = $this->groupRepository->getAssessorsFromLanguageGroup($task->language_id, 0,
                $task->native)->pluck('full_name', 'id');
        }

        $skilsAssessments = $this->skilsAssessments;

        $taskUpdates = TaskUpdate::with('roles')->get();
        $updateActions = [];
        foreach ($taskUpdates as $taskUpdate) {
            foreach ($taskUpdate->roles as $role) {
                $updateActions[$role->slug][$taskUpdate->slug] = $taskUpdate->display_name;
            }
        }

        if (!$task->availability_from) {
            $updateActions['master']['reschedule'] = 'Schedule';
            $updateActions['assessor']['reschedule'] = 'Schedule';
            $updateActions['css']['reschedule'] = 'Schedule';
            $updateActions['client']['reschedule'] = 'Schedule';
        }


        if (auth()->check() && auth()->user()->hasRole(['client'])) {
            // check speaking is Done
            $speakingDone = Paper::where('task_id', $task->id)
                ->where('status_id', DONE)
                ->where('paper_type_id', TEST_SPEAKING)
                ->count();
            if ($speakingDone) {
                unset($updateActions['client']['reschedule']);
            }
        }

        if (auth()->check() && auth()->user()->hasRole(['master', 'administrator'])) {
            $updateActions['assessor']['test-was-reset'] = 'Candidate had issues during online test. Test was reset';
        }

        $user_abilities = [
            'Pre-A1' => 'Pre Basic',
            'A1' => 'Basic User',
            'A2' => 'Basic User',
            'A2+' => 'Basic User',
            'B1' => 'Independent User',
            'B1+' => 'Independent User',
            'B2' => 'Independent User',
            'B2+' => 'Independent User',
            'C1' => 'Proficient User',
            'C2' => 'Proficient User',
            'N' => 'Native User'
        ];

        return [
            'task' => $task,
            'taskStatuses' => $taskStatuses,
            'taskStatusesColor' => $taskStatusesColor,
            'skilsAssessments' => $skilsAssessments,
            'updateActions' => $updateActions,
            'languages' => $languages,
            'assessors' => $assessors,
            'user_abilities' => $user_abilities
        ];
    }
}
