<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/20/2017
 * Time: 8:59 AM
 */

namespace App\Http\Controllers;


use App\Models\LanguagePaperTypes;
use App\Models\Paper;
use App\Models\PaperAnswers;
use App\Models\PaperReport;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\QuestionLevel;
use App\Models\Task;
use App\Repositories\GroupRepository;
use App\Repositories\PaperAnswerRepository;
use App\Repositories\PaperReportRepository;
use App\Repositories\PaperRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\SettingRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{

    /**
     * @var $taskRepository
     */
    private $taskRepository;

    /**
     * @var $paperAnswer
     */
    private $paperAnswerRepository;

    /**
     * @var $paperRepository
     */
    private $paperRepository;

    /**
     * @var $paperReportRepository
     */
    private $paperReportRepository;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var $questionChoiceRepository
     */
    private $questionChoiceRepository;

    /**
     * @var $settingRepository
     */
    private $settingRepository;

    /**
     * @var $emailService
     */
    private $emailService;

    private $taskController;

    const VIEW_TEST_READING = 'reading';
    const VIEW_TEST_LISTENING = 'listening';
    const VIEW_TEST_LANGUAGE_USE_NEW = 'language_use_new';
    const VIEW_TEST_LANGUAGE_USE = 'language_use';

    const VIEW_TEST_COMPLETED = 'completed';

    const MAX_LANG_USE_QUESTIONS = 60;
    const MAX_LANG_USE_TIME = 30 * 60; // 1800 sec

    /**
     * TestController constructor.
     * @param TaskRepository $taskRepository
     * @param PaperRepository $paperRepository
     * @param PaperReportRepository $paperReportRepository
     * @param PaperAnswerRepository $paperAnswerRepository
     * @param QuestionRepository $questionRepository
     * @param QuestionChoiceRepository $questionChoiceRepository
     * @param SettingRepository $settingRepository
     * @param EmailService $emailService
     * @param TaskController $taskController
     */
    public function __construct(
        TaskRepository $taskRepository,
        PaperRepository $paperRepository,
        PaperReportRepository $paperReportRepository,
        PaperAnswerRepository $paperAnswerRepository,
        QuestionRepository $questionRepository,
        QuestionChoiceRepository $questionChoiceRepository,
        SettingRepository $settingRepository,
        EmailService $emailService,
        TaskController $taskController
    ) {
        $this->taskRepository = $taskRepository;
        $this->paperRepository = $paperRepository;
        $this->paperReportRepository = $paperReportRepository;
        $this->paperAnswerRepository = $paperAnswerRepository;
        $this->questionRepository = $questionRepository;
        $this->questionChoiceRepository = $questionChoiceRepository;
        $this->settingRepository = $settingRepository;
        $this->emailService = $emailService;
        $this->taskController = $taskController;

        $this->middleware('testHash')->only([
            'insertCurrentAudio',
        ]);

    }


    /**
     * Load instruction page
     *
     * @param $hash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTestInstructionsPage($hash)
    {
        $task = Task::where('link', $hash)->with('papers')->where('task_status_id', '<>', CANCELED)->first();

        if ($task == null) {
            $canceledText = $this->settingRepository->getByKey('test_task_canceled_text')->value;
            $canceledTitle = $this->settingRepository->getByKey('test_task_canceled_title')->value;
            return view('tests.canceled', compact('canceledText', 'canceledTitle'));
        }

        if ($task->link_expires_at < Carbon::now()) {
            $expiredText = $this->settingRepository->getByKey('test_task_expired_text')->value;
            $expiredTitle = $this->settingRepository->getByKey('test_task_expired_title')->value;
            return view('tests.link_expired', compact('expiredText', 'expiredTitle'));
        }

        $tests = Paper::where('task_id', $task->id)
            ->where('done', '0')
            ->where('status_id', '!=', CANCELED)
            ->with('type')
            ->orderByRaw("FIELD(papers.paper_type_id, '3', '5', '4', '1', '6') ASC")
            ->get()
            ->pluck('type.name', 'id');

        $tests = implode(', ', $tests->toArray());

        $containsListening = false;
        foreach ($task->papers as $paper) {
            if ($paper->paper_type_id == TEST_LISTENING && $paper->status_id != CANCELED) {
                $containsListening = true;
            }
        }
        $settingsArray = [];
        $settings = $this->settingRepository->getAll();
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }
        $fileExtension = pathinfo(url('audio/' . $settingsArray['audio_file_path']))['extension'];
        $settingsArray['instructions'] = str_replace('{tests}', $tests, $settingsArray['instructions']);
        $settingsArray['instructions'] = str_replace('{name}', $task->name, $settingsArray['instructions']);
        $settingsArray['audio_instruction'] = str_replace('{tests}', $tests, $settingsArray['audio_instruction']);
        $settingsArray['audio_instruction'] = str_replace('{name}', $task->name, $settingsArray['audio_instruction']);
        $settingsArray['welcome'] = str_replace('{name}', $task->name, $settingsArray['welcome']);
        $settingsArray['welcome_audio'] = str_replace('{name}', $task->name, $settingsArray['welcome_audio']);

        return view('tests.instructions', compact('hash', 'containsListening', 'settingsArray', 'fileExtension'));
    }

    /**
     * Get the instructions page for the selected test type
     *
     * @param $hash
     * @param $testType
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTestInstructionsForPaperPage($hash, $testType)
    {
        // Get the task associated with the test
        $task = Task::where('link', $hash)->with('papers')->where('task_status_id', '<>', CANCELED)->first();

        // Check if the task is valid
        if ($task == null) {
            $canceledText = $this->settingRepository->getByKey('test_task_canceled_text')->value;
            $canceledTitle = $this->settingRepository->getByKey('test_task_canceled_title')->value;
            return view('tests.canceled', compact('canceledText', 'canceledTitle'));
        }

        // Check if the link has expired
        if ($task->link_expires_at < Carbon::now()) {
            $expiredText = $this->settingRepository->getByKey('test_task_expired_text')->value;
            $expiredTitle = $this->settingRepository->getByKey('test_task_expired_title')->value;
            return view('tests.link_expired', compact('expiredText', 'expiredTitle'));
        }

        $tests = Paper::where('task_id', $task->id)
            ->where('done', '0')
            ->where('status_id', '!=', CANCELED)
            ->with('type')
            ->whereHas('type', function($query) use ($testType) {
                $query->where('id', $testType);
            })
            ->get()
            ->pluck('type.name', 'id');
        $tests = implode(', ', $tests->toArray());

        $containsListening = $testType == TEST_LISTENING;

        $settingsArray = [];

        $settings = $this->settingRepository->getAll();
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }
        $fileExtension = pathinfo(url('audio/' . $settingsArray['audio_file_path']))['extension'];
        $settingsArray['instructions'] = str_replace('{name}', $task->name, $settingsArray[TEST_INSTRUCTIONS[$testType]]);
        $settingsArray['audio_instruction'] = str_replace('{tests}', $tests, $settingsArray['audio_instruction']);
        $settingsArray['audio_instruction'] = str_replace('{name}', $task->name, $settingsArray['audio_instruction']);
        $settingsArray['welcome'] = str_replace('{name}', $task->name, $settingsArray['welcome']);
        $settingsArray['welcome_audio'] = str_replace('{name}', $task->name, $settingsArray['welcome_audio']);

        return view('tests.instructions-for-type', compact('hash', 'containsListening', 'settingsArray', 'fileExtension', 'testType'));
    }


    /**
     * Get the instructions page for the selected test type
     *
     * @param $hash
     * @param $testType
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTestDemoForPaperPage($hash, $testType, $step)
    {
        // Get the task associated with the test
        $task = Task::where('link', $hash)->with('papers')->where('task_status_id', '<>', CANCELED)->first();

        // Check if the task is valid
        if ($task == null) {
            $canceledText = $this->settingRepository->getByKey('test_task_canceled_text')->value;
            $canceledTitle = $this->settingRepository->getByKey('test_task_canceled_title')->value;
            return view('tests.canceled', compact('canceledText', 'canceledTitle'));
        }

        // Check if the link has expired
        if ($task->link_expires_at < Carbon::now()) {
            $expiredText = $this->settingRepository->getByKey('test_task_expired_text')->value;
            $expiredTitle = $this->settingRepository->getByKey('test_task_expired_title')->value;
            return view('tests.link_expired', compact('expiredText', 'expiredTitle'));
        }

        $settingsArray = [];

        $settings = $this->settingRepository->getAll();
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }

        $next = null;
        if ($step == 1) {
            $question = $this->questionRepository->getById(99991);
            $choices = $this->questionChoiceRepository->search(array(
                "question_id" => 99991
            ))->get();
            $question->body = json_decode($question->body);
            $next = 2;
        } else if ($step == 2) {
            $question = $this->questionRepository->getById(99992);
            $choices = $this->questionChoiceRepository->search(array(
                "question_id" => 99992
            ))->get();
            $next = 3;
        } else if ($step == 3) {
            $question = $this->questionRepository->getById(99993);
            $choices = $this->questionChoiceRepository->search(array(
                "question_id" => 99993
            ))->get();
        }

        return view('tests.demo-for-type', compact('hash', 'testType', 'settingsArray', 'question', 'choices', 'next'));
    }

    /**
     * Get test page
     *
     * @param $hash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function getTestPage($hash)
    {
        $task = Task::where('link', $hash)->with('papers')->where('task_status_id', '<>', CANCELED)->first();

        if ($task == null) {
            $canceledText = $this->settingRepository->getByKey('test_task_canceled_text')->value;
            $canceledTitle = $this->settingRepository->getByKey('test_task_canceled_title')->value;
            return view('tests.canceled', compact('canceledText', 'canceledTitle'));
        }

        if ($task->link_expires_at < Carbon::now()) {
            $expiredText = $this->settingRepository->getByKey('test_task_expired_text')->value;
            $expiredTitle = $this->settingRepository->getByKey('test_task_expired_title')->value;
            return view('tests.link_expired', compact('expiredText', 'expiredTitle'));
        }

        if ($task->link_access == 0) {
            addLog([
                'type' => TASK_HISTORY,
                'description' => 'The task link has been accessed.',
                'task_id' => $task->id
            ]);
            $task->link_access = 1;
            $task->save();
        }

        $tests = Paper::where('task_id', $task->id)
            ->where('done', '0')
            ->where('status_id', '!=', CANCELED)
            ->with('task')
            ->orderByRaw("FIELD(papers.paper_type_id, '3', '5', '4', '1', '6') ASC")
            ->get();

        if (count($tests) == 1 && $tests[0]->paper_type_id == TEST_SPEAKING) {
            return view('tests.completed');
        }

        if (empty($tests->toArray())) {
            return view('tests.completed');
        }

        if (Task::allTestsAreDoneAndHaveReports($task->id)) {
            $this->taskRepository->update($task->id, ['task_status_id' => DONE]);
            $this->taskController->sendMailTaskDone($task);
        }

        if (!session()->has('current_test')) {
            session()->put('current_test', [
                'time_start' => Carbon::now()
            ]);
        }

        foreach ($tests as $test) {

            switch ($test->paper_type_id) {
                case TEST_WRITING:
                    return $this->_loadWritingTest($test);
                    break;
                case TEST_READING:
                    return $this->_generateTestTemplate($test, self::VIEW_TEST_READING);
                    break;
                case TEST_LANGUAGE_USE_NEW:
                    return $this->_generateTestTemplate($test, self::VIEW_TEST_LANGUAGE_USE_NEW);
                    break;
                case TEST_LISTENING:
                    return $this->_generateTestTemplate($test, self::VIEW_TEST_LISTENING);
                    break;
                case TEST_LANGUAGE_USE:
                    return $this->_loadLanguageUseTest($test);
                    break;
            }

        }

    }

    /**
     * Get the test page for the test type
     *
     * @param $hash
     * @param $testType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTestForPaperPage($hash, $testType)
    {
        $task = Task::where('link', $hash)->with('papers')->where('task_status_id', '<>', CANCELED)->first();

        if ($task == null) {
            $canceledText = $this->settingRepository->getByKey('test_task_canceled_text')->value;
            $canceledTitle = $this->settingRepository->getByKey('test_task_canceled_title')->value;
            return view('tests.canceled', compact('canceledText', 'canceledTitle'));
        }

        if ($task->link_expires_at < Carbon::now()) {
            $expiredText = $this->settingRepository->getByKey('test_task_expired_text')->value;
            $expiredTitle = $this->settingRepository->getByKey('test_task_expired_title')->value;
            return view('tests.link_expired', compact('expiredText', 'expiredTitle'));
        }

        if ($task->link_access == 0) {
            addLog([
                'type' => TASK_HISTORY,
                'description' => 'The task link has been accessed.',
                'task_id' => $task->id
            ]);
            $task->link_access = 1;
            $task->save();
        }

        $tests = Paper::where('task_id', $task->id)
            ->where('done', '0')
            ->where('status_id', '!=', CANCELED)
            ->with('task')
            ->with('type')
            ->whereHas('type', function($query) use ($testType) {
                $query->where('id', $testType);
            })
            ->get();

        if (empty($tests->toArray())) {
            return view('tests.completed');
        }

        if (Task::allTestsAreDoneAndHaveReports($task->id)) {
            $this->taskRepository->update($task->id, ['task_status_id' => DONE]);
            $this->taskController->sendMailTaskDone($task);
        }

        if (!session()->has('current_test')) {
            session()->put('current_test', [
                'time_start' => Carbon::now()
            ]);
        }

        foreach ($tests as $test) {
            switch ($testType) {
                case TEST_WRITING:
                    return $this->_loadWritingTest($test);
                    break;
                case TEST_READING:
                    return $this->_generateTestTemplate($test, self::VIEW_TEST_READING);
                    break;
                case TEST_LANGUAGE_USE_NEW:
                    return $this->_generateTestTemplate($test, self::VIEW_TEST_LANGUAGE_USE_NEW);
                    break;
                case TEST_LISTENING:
                    return $this->_generateTestTemplate($test, self::VIEW_TEST_LISTENING);
                    break;
                case TEST_LANGUAGE_USE:
                    return $this->_loadLanguageUseTest($test);
                    break;
            }
        }

    }

    /**
     * Submit writing test answer
     *
     * @param Request $request
     * @param GroupRepository $groupRepository
     * @param UserRepository $userRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitWriting(Request $request, GroupRepository $groupRepository, UserRepository $userRepository)
    {
        $question = Question::find(session()->get('current_test')['question_id']);
        $answer = $request->input('user_answer');
        $answer = preg_replace('/[^\w]/', ' ', $answer);
        $answer = explode(" ", $answer);

        if (count($answer) > $question->max_words) {
            $request->request->add(['observations' => 'Test taker has submitted more words than the limit!']);
        }

        $dateStarted = session()->get('current_test')['time'];
        $dateStarted = new Carbon($dateStarted);
        $time = Carbon::now()->diffInSeconds($dateStarted);

        $task_id = session()->get('current_test')['task_id'];
        $request->request->add([
            'time' => $time,
            'paper_id' => session()->get('current_test')['paper_id'],
            'paper_type_id' => session()->get('current_test')['paper_type_id'],
            'task_id' => $task_id,
            'question_id' => session()->get('current_test')['question_id']
        ]);

        try {
            $this->paperAnswerRepository->create($request->all());
            $resType = SUCCESS;

            $task = Task::where('id', $task_id)->with(['papers', 'language', 'assessor', 'project', 'project.owner'])->first();

            if (!$task->assessor) {
                $randAssessor = $groupRepository->getRandomAssessor($task->language_id, 0, $task->native);
                if ($randAssessor) {
                    $task->assessor_id = $randAssessor->id;
                    $task->save();
                    $this->emailService->sendAssessorMail($randAssessor, $task, TEST_WRITING);
                } else {
                    // language group doesn't have any assessors
                    $userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService, $task);
                }
            } else {
                $this->emailService->sendAssessorMail($task->assessor, $task, TEST_WRITING);
            }

        } catch (Exception $e) {
            Log::info($e->getMessage());
            $resType = ERROR;
        }

        try {
            $attributes = [
                "done" => 1,
                "ended_at" => Carbon::now(),
            ];
            $this->paperRepository->update($request->input('paper_id'), $attributes);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        try {
            if (!$task) {
                $task = Task::where('id', $task_id)->with(['papers', 'language', 'assessor', 'project', 'project.owner'])->first();
            }

            if ($task->task_status_id != ISSUE) {
                $this->taskRepository->update($task_id, ['task_status_id' => IN_PROGRESS]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        if (!$request->ajax()) {
            return redirect()->back();
        } else {
            return response()->json(['resType' => $resType]);
        }
    }

    /**
     * Submit reading test answers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitReading(Request $request)
    {
        $resType = $this->_submitAnswer($request);
        return ajaxResponse($resType);
    }

    /**
     * Submit listening test answer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitListening(Request $request)
    {
        $resType = $this->_submitAnswer($request);
        return ajaxResponse($resType);
    }

    /**
     * Submit language use new test answer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitLanguageUseNew(Request $request)
    {
        $question = $this->questionRepository->getById(session()->get('current_test')['question_id']);
        if ($question->language_use_type == TEST_LU_FILLGAPS || $question->language_use_type == TEST_LU_ARRANGE) {
            $resType = $this->_submitLanguageCompletion($request);
        } else {
            $resType = $this->_submitAnswer($request);
        }
        return ajaxResponse($resType);
    }

    /**
     * Submit language use test answer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitLanguageUse(Request $request)
    {
        $resType = $this->_submitAnswer($request);
        return ajaxResponse($resType);
    }

    /**
     * Generate test template
     *
     * @param $test
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function _loadLanguageUseTest($test)
    {
        $paperAnswers = PaperAnswers::where('paper_id', $test->id)->with([
            'question',
            'question.questionChoices' => function ($query) {
                $query->where('correct', '=', 1);
            }
        ])->get();

        ### time has elapsed
        if ($test->started_at === null) {
            $timeOut = false;
        } else {
            $started_at = Carbon::parse($test->started_at);
            $secondsElapsed = Carbon::now()->diffInSeconds($started_at);
            $timeRemaining = self::MAX_LANG_USE_TIME - $secondsElapsed;
            $timeOut = $timeRemaining <= 0;
        }

        ### check stop condition and return thank you page or next test
        if (count($paperAnswers) >= self::MAX_LANG_USE_QUESTIONS || $timeOut) {

            $totalCorrect = 0;

            foreach ($paperAnswers as $paperAnswer) {
                if ($paperAnswer->question->questionChoices[0]->id == $paperAnswer->answer_id) {
                    $totalCorrect++;
                }
            }

            $level = PRE_A1;

            if ($totalCorrect <= 6) {
                $level = PRE_A1;
            } elseif ($totalCorrect >= 7 && $totalCorrect <= 16) {
                $level = A1;
            } elseif ($totalCorrect >= 17 && $totalCorrect <= 26) {
                $level = A2;
            } elseif ($totalCorrect >= 27 && $totalCorrect <= 36) {
                $level = B1;
            } elseif ($totalCorrect >= 37 && $totalCorrect <= 47) {
                $level = B2;
            } elseif ($totalCorrect >= 48 && $totalCorrect <= 57) {
                $level = C1;
            } elseif ($totalCorrect > 57) {
                $level = C2;
            }

            $attributes = [
                'paper_id' => $test->id,
                'grade' => $level,
                'ability' => $totalCorrect / 10
            ];

            $this->_endTest($test, $attributes);

            $tests = Paper::where('task_id', $test->task->id)->where('done', '0')->with('task')->get();

            ### check all tests are done
            if (empty($tests->toArray())) {
                $this->taskRepository->update($test->task->id, ['task_status_id' => DONE]);
                return view('tests.' . self::VIEW_TEST_COMPLETED);
            }

            ### check if the last remaining test is TEST_SPEAKING
            if ((count($tests) == 1 && $tests[0]->paper_type_id == TEST_SPEAKING)) {
                return view('tests.' . self::VIEW_TEST_COMPLETED);
            }
        }

        $question = $this->_generateQuestion($test, false);
        $choices = json_decode($test->current_choices);

        if ($test->started_at == null) {
            $test->started_at = Carbon::now();
            $test->save();
        }

        $started_at = Carbon::parse($test->started_at);
        $secondsElapsed = Carbon::now()->diffInSeconds($started_at);

        session()->put('current_test', [
            'paper_id' => $test->id,
            'paper_type_id' => $test->paper_type_id,
            'task_id' => $test->task_id,
            'question_id' => $question->id,
            'choices' => $choices,
            'time' => Carbon::now()
        ]);

        $time = self::MAX_LANG_USE_TIME - $secondsElapsed;
        $timeLimit = strtotime(Carbon::now()) + $time;
        $timeLimit = date('Y/m/d H:i:s', $timeLimit);

        $totalQuestions = self::MAX_LANG_USE_QUESTIONS;
        $currentQuestionIndex = count($paperAnswers) + 1;

        return view('tests.' . self::VIEW_TEST_LANGUAGE_USE,
            compact('test', 'question', 'choices', 'timeLimit', 'currentQuestionIndex', 'totalQuestions'));
    }

    /**
     * Load writing test page
     *
     * @param $test
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function _loadWritingTest($test)
    {
        if ($test->current_question_id == null) {
            $languagePaperType = LanguagePaperTypes::where('paper_type_id', $test->paper_type_id)->where('language_id',
                $test->task->language_id)->with('questions')->first();
            $randomQuestion = array_rand($languagePaperType->questions->toArray());
            $question = $languagePaperType->questions[$randomQuestion];
            $test->current_question_id = $question->id;
            $test->save();
        } else {
            $question = $this->questionRepository->getById($test->current_question_id);
        }

        if ($test->started_at == null) {
            $test->started_at = Carbon::now();
            $test->save();
        } 
        
        $timeLimit = strtotime($test->started_at) + $question->time;
        if (Carbon::createFromTimestamp($timeLimit)->lt(Carbon::now())) {
            $expiredText = $this->settingRepository->getByKey('test_task_expired_text')->value;
            $expiredTitle = $this->settingRepository->getByKey('test_task_expired_title')->value;
            return view('tests.link_expired', compact('expiredText', 'expiredTitle'));
        }
        
        session()->put('current_test', [
            'paper_id' => $test->id,
            'paper_type_id' => $test->paper_type_id,
            'task_id' => $test->task_id,
            'question_id' => $question->id,
            'time' => Carbon::now()
        ]);

        $timeLimit = date('Y/m/d H:i:s', $timeLimit);
        return view('tests.writing', compact('test', 'question', 'timeLimit'));
    }

    /**
     * Generate test template
     *
     * @param $test
     * @param $testType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function _generateTestTemplate($test, $testType)
    {
        $answersArrayAlgorithm = [];

        $paperAnswers = PaperAnswers::where('paper_id', $test->id)->with([
            'question',
            'question.questionChoices' => function ($query) {
                $query->where('correct', '=', 1);
            }
        ])->get();

        foreach ($paperAnswers as $paperAnswerKey => $paperAnswerValue) {
            $answersArrayAlgorithm[$paperAnswerKey] = $paperAnswerValue;
        }

        $results = $this->_calculateDifficulty($answersArrayAlgorithm);

        $view = $this->_checkStopConditions($paperAnswers, $test, $results);

        if ($view == 'completed') {
            $tests = Paper::where('task_id', $test->task->id)->where('done', '0')->with('task')->get();
            if (empty($tests->toArray())) {
                $this->taskRepository->update($test->task->id, ['task_status_id' => DONE]);
                return view('tests.' . $view);
            }

            if ((count($tests) == 1 && $tests[0]->paper_type_id == TEST_SPEAKING)) {
                return view('tests.' . $view);
            }
        }

        if (empty($paperAnswers->toArray())) {
            $question = $this->_generateQuestion($test);
            $fileExtension = '';
            if ($question->audio_file_path != null) {
                $fileExtension = pathinfo(url('audio/' . $question->id . '/' . $question->audio_file_path))['extension'];
            }
            $choices = json_decode($test->current_choices);
            $timeLimit = $this->_setTimeLimit($test, $question);
            return view('tests.' . $testType, compact('test', 'question', 'choices', 'timeLimit', 'fileExtension'));
        } else {
            // Get the difficulty level
            $difficulty = $results['difficulty'];
            $languagePaperType = LanguagePaperTypes::where('paper_type_id', $test->paper_type_id)->where('language_id',
                $test->task->language_id)->first();
            $question = $this->_generateQuestionByDifficulty($difficulty, $languagePaperType->id, $paperAnswers, $test,
                $results['q_type']);
            $fileExtension = '';
            if ($question->audio_file_path != null) {
                $fileExtension = pathinfo(url('audio/' . $question->id . '/' . $question->audio_file_path))['extension'];
            }
            $choices = json_decode($test->current_choices);
            $timeLimit = $this->_setTimeLimit($test, $question, $paperAnswers);
            return view('tests.' . $testType, compact('test', 'question', 'choices', 'timeLimit', 'fileExtension'));
        }
    }

    /**
     * Check and generate question on page refresh
     *
     * @param $test
     * @param bool $isAlgorithmNeeded
     * @return Question
     */
    private function _generateQuestion($test, $isAlgorithmNeeded = true)
    {
        $choices = '';
        ### no previous state (is the first question)
        if ($test->current_question_id == null) {
            ### get questions of test type
            $languagePaperType = LanguagePaperTypes::where('paper_type_id', $test->paper_type_id)->where('language_id',
                $test->task->language_id)->with([
                'questions' => function ($query) use ($isAlgorithmNeeded) {
                    $query->when($isAlgorithmNeeded, function ($q) { //if we use algorithm, apply conditions
                        $q->where('question_level_id', '=', 2)->where('q_type', 1);
                    });
                }
            ])->first();

            $randomQuestion = array_rand($languagePaperType->questions->toArray());
            $question = $languagePaperType->questions[$randomQuestion];

            ### generate choices
            if ($question->language_use_type != 2 && $question->language_use_type != 3) {
                $choices = $this->_generateChoices($question);
            } else {
                if ($question->language_use_type == 3) {
                    $question->body = preg_replace('/_+/', '_', $question->body);
                } else {
                    $question->body = json_decode($question->body);
                }

            }

            ### set current state to task
            $test->current_question_id = $question->id;
            $test->current_choices = json_encode($choices);
            $test->save();

            ### load previous state (on page refresh)
        } else {

            ### check user already answered to the current question
            $answer = PaperAnswers::where('question_id', $test->current_question_id)->where('paper_id',
                $test->id)->first();

            ### he didn't answer
            if ($answer == null) {

                ### get the same question
                $question = $this->questionRepository->getById($test->current_question_id);

                ### if question has choices
                if ($question->language_use_type != 2 && $question->language_use_type != 3) {
                    $choices = json_decode($test->current_choices);
                } else {
                    if ($question->language_use_type == 3) {
                        $question->body = preg_replace('/_+/', '_', $question->body);
                    } else {
                        $question->body = json_decode($question->body);
                    }

                }

                ### set current state to task
                $test->current_question_id = $question->id;
                $test->current_choices = json_encode($choices);
                $test->save();

                ### he already answered, get next question
            } else {

                if (!$isAlgorithmNeeded) {
                    ### get previous questions
                    $previousQuestionsIds = $test->paper_answers->pluck('question_id');
                }

                $languagePaperType = LanguagePaperTypes::where('paper_type_id', $test->paper_type_id)
                    ->where('language_id', $test->task->language_id)
                    ->with([
                        'questions' => function ($query) use ($isAlgorithmNeeded, $previousQuestionsIds) {
                            $query->when($isAlgorithmNeeded, function ($q) { //if we use algorithm, apply conditions
                                $q->where('question_level_id', '=', 2)->where('q_type', 1);
                            });

                            if (!$isAlgorithmNeeded) {
                                $query->whereNotIn('id', $previousQuestionsIds);
                            }
                        }
                    ])->first();

                $randomQuestion = array_rand($languagePaperType->questions->toArray());
                $question = $languagePaperType->questions[$randomQuestion];
                if ($question->language_use_type != 2 && $question->language_use_type != 3) {
                    $choices = $this->_generateChoices($question);
                } else {
                    if ($question->language_use_type == 3) {
                        $question->body = preg_replace('/_+/', '_', $question->body);
                    } else {
                        $question->body = json_decode($question->body);
                    }

                }

                ### set current state to task
                $test->current_question_id = $question->id;
                $test->current_choices = json_encode($choices);
                $test->save();
            }
        }
        return $question;
    }

    /**
     * Set the time limit for a question
     *
     * @param $test
     * @param $question
     * @param null $paperAnswers
     * @return false|int|string
     */
    private function _setTimeLimit($test, $question, $paperAnswers = null)
    {
        $choices = '';
        if ($question->language_use_type != 2 && $question->language_use_type != 3) {
            $choices = $this->_generateChoices($question);
        }
        if ($test->started_at == null) {
            $test->started_at = Carbon::now();
            $test->save();
        }

        if ($paperAnswers == null) {
            $test->started_at = Carbon::parse($test->started_at);
            $test->question_current_time = Carbon::now()->diffInSeconds($test->started_at);
        } else {
            $test->question_current_time = Carbon::now()->diffInSeconds(Carbon::parse(array_reverse($paperAnswers->toArray())[0]['created_at']));
        }
        $test->save();

        session()->put('current_test', [
            'paper_id' => $test->id,
            'paper_type_id' => $test->paper_type_id,
            'task_id' => $test->task_id,
            'question_id' => $question->id,
            'choices' => $choices,
            'time' => Carbon::now()
        ]);
        $time = $question->time - $test->question_current_time;
        $timeLimit = strtotime(Carbon::now()) + $time;
        $timeLimit = date('Y/m/d H:i:s', $timeLimit);

        return $timeLimit;
    }

    /**
     * Generate answer choices for a question
     *
     * @param $question
     * @return mixed
     */
    private function _generateChoices($question)
    {
        $correctChoice = QuestionChoice::where('question_id', $question->id)->where('correct', 1)->first();
        //$randomChoices = QuestionChoice::where('question_id', $question->id)->where('correct', 0)->get()->random(3);
        $incorrectChoices = QuestionChoice::where('question_id', $question->id)->where('correct', 0)->get();
        //$choices = $randomChoices->push($correctChoice)->shuffle();
        $choices = QuestionChoice::where('question_id', $question->id)->get();

        return $choices;
    }

    /**
     * Check the stop conditions for a test
     *
     * @param $paperAnswers
     * @param $test
     * @param $results
     * @return string
     */
    private function _checkStopConditions($paperAnswers, $test, $results)
    {
        $allCorrect = false;
        $allFalse = false;

        $view = '';

        $correctAnswers = $results['correctAnswers'] ? $results['correctAnswers'] : [];
        $incorrectAnswers = $results['incorrectAnswers'] ? $results['incorrectAnswers'] : [];


        switch ($results['level']) {
            case 1:
                $level = A1;
                break;
            case 2:
                $level = A2;
                break;
            case 3:
                $level = B1;
                break;
            case 4:
                $level = B2;
                break;
            case 5:
                $level = C1;
                break;
            case 6:
                $level = C2;
                break;
            default:
                $level = $results['level'];
                break;
        }

        if ($results['level'] < 1) {
            $level = PRE_A1;
        }

        $attributes = [
            'paper_id' => $test->id,
            'grade' => $level,
            'ability' => $results['ability'],
            'algorithm' => json_encode($results)
        ];

        ### check last 4 levels are identical
        $latest4LevelsIdentical = false;

        if (count($paperAnswers) >= 4) {

            $last4answers = array_slice($results['all_levels'], -5, 4, true);
            $latest4LevelsIdentical = count(array_unique($last4answers)) === 1;
        }

        if (count($paperAnswers) == 20) {
            ### Finish test and create report
            return $this->_endTest($test, $attributes);
        }

        foreach ($correctAnswers as $diff => $totalCorrectAnswers) {
            if ($totalCorrectAnswers >= 7) {
                $allCorrect = true;
                break;
            }
        }

        foreach ($incorrectAnswers as $diff => $totalIncorrectAnswers) {
            if ($totalIncorrectAnswers >= 7) {
                $allFalse = true;
                break;
            }
        }
        if ($allFalse == true || $allCorrect == true) {
            ### Finish test and create report
            return $this->_endTest($test, $attributes);
        }

        ### count total questions by difficulty
        $totalQuestionsDifficulty = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
        ];

        foreach ($paperAnswers as $answer) {
            $totalQuestionsDifficulty[$answer->question->question_level_id]++;
        }

        if (count($paperAnswers) && $totalQuestionsDifficulty[$results['all_difficulties'][count($paperAnswers)]] >= 7 && $latest4LevelsIdentical == true) {
            ### Finish test and create report
            return $this->_endTest($test, $attributes);
        }

        return $view;
    }


    /**
     * Finish test and create report
     *
     * @param $test
     * @param $attributes
     * @return string
     */
    private function _endTest($test, $attributes)
    {

        $test_type = $test->type->name;

        $this->_setTestEndDate($test);
        $this->paperReportRepository->create($attributes);

        try {
            if ($test->task->task_status_id != ISSUE) {
                $this->taskRepository->update($test->task->id, ['task_status_id' => IN_PROGRESS]);
            }
        } catch (Exception $e) {
            $this->taskRepository->update($test->task->id, ['task_status_id' => IN_PROGRESS]);
        }

        $task = $test->task;

        if (Task::allTestsAreDoneAndHaveReports($task->id)) {
            $this->taskController->sendMailTaskDone($task);
        } else {
            ### 20. One test in a task finished
            ### Send email to task owner and to followers
            $this->emailService->sendEmailOneTestFinished($test->task, $attributes['ability'], $attributes['grade'],
                $test_type);
        }
        ### stop test
        return 'completed';
    }

    /**
     * Check number of correct/incorrect answers
     *
     * @param $paperAnswer
     * @param $correctAnswers
     * @param $incorrectAnswers
     */
    private function _checkCorrectAnswer($paperAnswer, &$correctAnswers, &$incorrectAnswers)
    {
        if ($paperAnswer->answer_id == null) {
            if ($paperAnswer->question->language_use_type == TEST_LU_ARRANGE) {
                $correct = $paperAnswer->question->body == $paperAnswer->user_answer;
            } else {
                $correctAnswer = QuestionChoice::where('answer', $paperAnswer->user_answer)->where('question_id',
                    $paperAnswer->question->id)->first();
                $correct = false;
                if ($correctAnswer != null) {
                    $correct = true;
                }
            }
            if ($correct) {
                $correctAnswers[$paperAnswer->question->question_level_id]++;
            } else {
                $incorrectAnswers[$paperAnswer->question->question_level_id]++;
            }
        } else {
            if ($paperAnswer->question->questionChoices[0]->id == $paperAnswer->answer_id) {
                $correctAnswers[$paperAnswer->question->question_level_id]++;
            } else {
                $incorrectAnswers[$paperAnswer->question->question_level_id]++;
            }
        }
    }

    /**
     * Generate a question based on the difficulty
     *
     * @param $difficulty
     * @param $paper_type_id
     * @param $paperAnswers
     * @param $test
     * @param $q_type
     * @return mixed
     */
    private function _generateQuestionByDifficulty($difficulty, $paper_type_id, $paperAnswers, $test, $q_type)
    {
        $question_ids = [];
        foreach ($paperAnswers as $paperAnswer) {
            $question_ids[$paperAnswer->id] = $paperAnswer->question_id;
        }

        $choices = [];

        ### get new question
        if (in_array($test->current_question_id, array_values($question_ids))) {

            $question = Question::where('question_level_id', $difficulty)->where('q_type', $q_type)
                ->where('language_paper_type_id', $paper_type_id)->whereNotIn('id',
                $question_ids)->get()->random(1)->first();

            if ($question->language_use_type != TEST_LU_ARRANGE && $question->language_use_type != TEST_LU_FILLGAPS) {
                $choices = $this->_generateChoices($question);
            }

            if ($question->language_use_type == TEST_LU_ARRANGE) {

                $question->body = json_decode($question->body, true);
                $body = $question->body;

                do {
                    shuffle($body);
                } while ($body === $question->body);

                $question->body = $body;

            }

            $test->current_question_id = $question->id;
            $test->current_choices = json_encode($choices);
            $test->save();
            return $question;

            ### get same question
        } else {
            $question = $this->questionRepository->getById($test->current_question_id);
            if ($question->language_use_type == TEST_LU_ARRANGE) {

                $question->body = json_decode($question->body, true);
                $body = $question->body;

                do {
                    shuffle($body);
                } while ($body === $question->body);

                $question->body = $body;
            }
            $choices = json_decode($test->current_choices);
            $test->current_question_id = $question->id;
            $test->current_choices = json_encode($choices);
            $test->save();
            return $question;
        }

    }

    /**
     * Set the end date for a test
     *
     * @param $test
     */
    private function _setTestEndDate($test)
    {
        try {
            $attributes = [
                "done" => 1,
                "ended_at" => Carbon::now(),
            ];
            $this->paperRepository->update($test->id, $attributes);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    /**
     * Calculate the next question's difficulty based on previous answer
     *
     * @param $answersArray
     * @return int|mixed
     */
    private function _calculateDifficulty($answersArray)
    {

        $totalAnswers = [];
        for ($j = 1; $j <= 6; $j++) {
            $correctAnswers[$j] = 0;
            $incorrectAnswers[$j] = 0;
            $totalAnswers[$j] = 0;
        }

        $D = $M = $N = $T = $P = [];

        // Nivelul calculat in functie de abilitate
        // $M = abilitate
        function calculateN($M) {

            $currentLevel = 0;
            if ($M < .7) {
                $currentLevel = 0;
            } elseif (.7 <= $M && $M < 1.7) {
                $currentLevel = 1;
            } elseif (1.7 <= $M && $M < 2.7) {
                $currentLevel = 2;
            } elseif (2.7 <= $M && $M < 3.7) {
                $currentLevel = 3;
            } elseif (3.7 <= $M && $M < 4.7) {
                $currentLevel = 4;
            } elseif (4.7 <= $M && $M < 5.7) {
                $currentLevel = 5;
            } elseif (5.7 <= $M && $M < 6) {
                $currentLevel = 6;
            } elseif ($M == 6) {
                $currentLevel = 6;
            }

            return $currentLevel;
        }

        // Calcularea dificultatii
        // $T = Total Answers
        // $P = Ratia dintre raspunsuri corecte si raspunsuri totale pentru dificultatea curenta
        // $D = Dificultatea
        // $M = Abilitatea
        // $i = indexul curent al raspunsului
        function calculateD($P, $i, $T, $M, $D){

            $difficulty = 2;

            if (isset($D[$i-1]) && $T[$D[$i-1]] >= 3 && $P[$D[$i-1]] == 0 ) {
                $difficulty = $D[$i-1] - 1;
            } elseif ($D[$i] - $M[$i] <= 0.25 && $D[$i] < 6) {
                $difficulty = $D[$i] + 1;
            } elseif ($D[$i] - $M[$i] <= 0.25 && $D[$i] == 6) {
                $difficulty = 6;
            } elseif (0.25 < $D[$i] - $M[$i] && $D[$i] - $M[$i] <= 0.65) {
                $difficulty = $D[$i];
            } elseif ($D[$i] - $M[$i] > 0.65) {
                $difficulty = $D[$i] - 1;
            } elseif ($D[$i] - $M[$i] > 0.65 && $D[$i] == 1) {
                $difficulty = 1;
            }

            if ($difficulty < 1) {
                $difficulty = 1;
            }

            return $difficulty;
        }

        foreach ($answersArray as $i => $answer) {

            $i++;

            $this->_checkCorrectAnswer($answer, $correctAnswers, $incorrectAnswers);
            $answer_difficulty = $answer->question->question_level_id;
            $totalAnswers[$answer_difficulty]++;

            $D[$i] = $answer_difficulty;
            $T[$answer_difficulty] = $totalAnswers[$answer_difficulty];
            $P[$answer_difficulty] = $correctAnswers[$answer_difficulty] / $T[$answer_difficulty];
            $P[$answer_difficulty] = number_format($P[$answer_difficulty], 2);
            $M[$i] = $D[$i] - 1 + $P[$D[$i]];
            $N[$i] = calculateN($M[$i]);

            $nextDifficulty = calculateD($P, $i, $T, $M, $D);

            if ($i == count($answersArray)) {

                $q_types = [];

                foreach ($D as $ind => $diff) {
                    $q_types[$diff] = isset($q_types[$diff]) ? $q_types[$diff] + 1 : 1;
                }

                $arr = [
                    'difficulty' => $nextDifficulty,
                    'all_difficulties' => $D,
                    'all_levels' => $N,
                    'all_abilities' => $M,
                    'ability' => $M[$i],
                    'level' => $N[$i],
                    'q_type' => isset($q_types[$nextDifficulty]) ?  $q_types[$nextDifficulty] + 1 : 1,
                    'correctAnswers' => $correctAnswers,
                    'incorrectAnswers' => $incorrectAnswers,
                ];
//                dump(get_defined_vars());
                return $arr;
            }

        }

    }

    /**
     * Submit test answer
     *
     * @param $request
     * @return string
     */
    private function _submitAnswer($request)
    {
        ###question choice id
        $answer_id = $request->input('user_answer');

        $dateStarted = session()->get('current_test')['time'];
        $dateStarted = new Carbon($dateStarted);
        $time = Carbon::now()->diffInSeconds($dateStarted);

        $paper = $this->paperRepository->getById(session()->get('current_test')['paper_id']);

        $request->request->add([
            'time' => $time,
            'paper_id' => session()->get('current_test')['paper_id'],
            'paper_type_id' => session()->get('current_test')['paper_type_id'],
            'task_id' => session()->get('current_test')['task_id'],
            'question_id' => session()->get('current_test')['question_id'],
            'choices' => $paper->current_choices,
            'answer_id' => $answer_id
        ]);


        try {
            $this->paperAnswerRepository->create($request->all());
            $resType = SUCCESS;
        } catch (Exception $e) {
            $resType = ERROR;
        }

        return $resType;
    }

    /**
     * Submit complete sentence request
     *
     * @param $request
     * @return string
     */
    private function _submitLanguageCompletion($request)
    {
        $answer_id = $request->input('user_answer');

        $dateStarted = session()->get('current_test')['time'];
        $dateStarted = new Carbon($dateStarted);
        $time = Carbon::now()->diffInSeconds($dateStarted);

        $request->request->add([
            'time' => $time,
            'paper_id' => session()->get('current_test')['paper_id'],
            'paper_type_id' => session()->get('current_test')['paper_type_id'],
            'task_id' => session()->get('current_test')['task_id'],
            'question_id' => session()->get('current_test')['question_id'],
            'user_answer' => $answer_id
        ]);


        try {
            $this->paperAnswerRepository->create($request->all());
            $resType = SUCCESS;
        } catch (Exception $e) {
            $resType = ERROR;
        }

        return $resType;
    }

    /**
     *  Ajax - Set current audio time
     *
     * @param Request $request
     * @return Paper
     */
    public function insertCurrentAudio(Request $request)
    {
        if($request->time == 0){
            $request->time = null;
        }
        $test = Paper::find($request->test_id);
        $test->current_audio_time = $request->time;
        $test->save();
        return $test;
    }

}