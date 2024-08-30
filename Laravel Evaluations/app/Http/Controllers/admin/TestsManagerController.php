<?php

namespace App\Http\Controllers\admin;

use App\Models\Group;
use App\Models\Language;
use App\Models\LanguagePaperTypes;
use App\Models\PaperType;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\QuestionLevel;
use App\Repositories\LanguagePaperTypeRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\QuestionRepository;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Mockery\Exception;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

/**
 * Class TestsManagerController
 * @package App\Http\Controllers\admin
 */
class TestsManagerController extends Controller
{
    /**
     * @var $userRepository
     */
    private $questionRepository;
    /**
     * @var $questionChoiceRepository
     */
    private $questionChoiceRepository;
    /**
     * @var $languagePaperTypeRepository
     */
    private $languagePaperTypeRepository;

    /**
     * TestsManagerController constructor.
     *
     * @param QuestionRepository $questionRepository
     */
    public function __construct(
        QuestionRepository $questionRepository,
        QuestionChoiceRepository $questionChoiceRepository,
        LanguagePaperTypeRepository $languagePaperTypeRepository
    )
    {
        $this->questionRepository = $questionRepository;
        $this->questionChoiceRepository = $questionChoiceRepository;
        $this->languagePaperTypeRepository = $languagePaperTypeRepository;

    }

    public function reminderUpdateTask()
    {


    }

    /**
     * Add paper type to lang
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTestType(Request $request)
    {

        $rules = [
            'paper_type_id' => 'required',
            'language_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->passes()) {
            return ($validator->messages());
        }

        try {

            $languagePaperType = LanguagePaperTypes::withTrashed()->where([
                'paper_type_id' => $request->paper_type_id,
                'language_id' => $request->language_id,
            ])->first();

            if ($languagePaperType != null) {
                return ajaxResponse(ERROR, 'The test type already exists!');
            }

            $languageTestType = new LanguagePaperTypes();
            $languageTestType->paper_type_id = $request->paper_type_id;
            $languageTestType->language_id = $request->language_id;
            $languageTestType->save();
            $languageTestType->delete();

            return ajaxResponse(SUCCESS, 'The Test type was added!', $languageTestType);

        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return ajaxResponse(ERROR, 'Something went wrong');
    }

    /**
     *  Tests type page by language
     *
     * @return mixed
     */
    public function getTestsPage()
    {

        $languages = Language::with([
            'language_paper_type' => function ($q) {
                $q->withTrashed();
            },
            'language_paper_type.paperTypes'
        ])->get();
        $paper_types = PaperType::pluck('name', 'id');

        return view('manageTests.index', compact('languages', 'paper_types'));
    }

    /**
     *  Activate and deactivate
     *
     * @param $action
     * @param $id
     * @return mixed
     */
    public function updateTestTypeStatus($action, $id)
    {
        if ($action == 'activate') {

            $test = LanguagePaperTypes::withTrashed()->find($id);
            $testType = $test->paper_type_id;
            if (in_array($testType, [TEST_LANGUAGE_USE_NEW, TEST_LANGUAGE_USE])) {
                $searchTest = $testType == TEST_LANGUAGE_USE ? TEST_LANGUAGE_USE_NEW : TEST_LANGUAGE_USE;
                if (LanguagePaperTypes::where(['paper_type_id' => $searchTest, 'language_id' => $test->language_id])->count()) {
                    return ajaxResponse(ERROR, 'You can\'t activate both Language Use tests');
                }
            }

            $this->languagePaperTypeRepository->untrash($id);
        } elseif ($action == 'deactivate') {
            $this->languagePaperTypeRepository->delete($id);
        }

        return ajaxResponse(SUCCESS);
    }

    /**
     *  Question list page by Language and Test Type
     *
     * @param $languagePaperType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLanguageTestTypePage($languagePaperType)
    {

        $testType = LanguagePaperTypes::withTrashed()->find($languagePaperType);

        if (!$testType) {
            return redirect()->back();
        }

        $language = Language::find($testType->language_id);
        $questions = Question::where('language_paper_type_id', $languagePaperType)->get();
        $levels = QuestionLevel::all()->pluck('name', 'id');
        $levels[''] = 'All';

        $testTypes = [
            1 => 'language-use-new',
            3 => 'writing',
            4 => 'listening',
            5 => 'reading',
            6 => 'language-use',
        ];

        $hasChoices = in_array($testType->paper_type_id, [
            TEST_READING,
            TEST_LISTENING,
            TEST_LANGUAGE_USE_NEW,
            TEST_LANGUAGE_USE,
        ]);

        $qTypes = [
            '' => 'All'
        ];
        for ($i = 1; $i <= 20; $i++) {
            $qTypes[$i] = 'Q' . $i;
        }

        return view('manageTests.language-test-' . $testTypes[$testType->paper_type_id],
            compact('language', 'testType', 'questions', 'levels', 'qTypes', 'hasChoices'));
    }

    /**
     * Datatables
     *
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function getTestQuestions($id, Request $request)
    {
        $testType = LanguagePaperTypes::withTrashed()->find($id);

        $filters = $request->input('filters');

        $query = $this->questionRepository->search($filters)->where('language_paper_type_id', $id);

        if ($testType->paper_type_id == TEST_READING || $testType->paper_type_id == TEST_LISTENING
            || ($testType->paper_type_id == TEST_LANGUAGE_USE_NEW)
        ) {
            $query->with('level');
        }

        // Removed default ordering because it affected the sorting sent from the frontend
        // $results = $query->orderBy('code', 'asc');

        $results = $query;

        return DataTables::of($results)->make(true);

    }

    /**
     * Datatables
     *
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function getTestQuestionsChoices($id, Request $request)
    {
        $filters = $request->input('filters');
        $results = $this->questionChoiceRepository->search($filters)->where('question_id', $id);

        return DataTables::of($results)->make(true);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createWritingQuestion(Request $request)
    {

        $rules = [
            'body' => 'required',
            'description' => 'nullable',
            'max_words' => 'integer|required',
            'minutes' => 'integer|required',
            'seconds' => 'integer|required',
            'language_paper_type_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        unset($rules['minutes']);
        unset($rules['seconds']);

        //calculate seconds
        $time = $request->get('minutes') * 60 + $request->get('seconds');
        $request->request->add(['time' => $time]);

        $rules['time'] = $time;

        if ($time == 0) {
            return ajaxResponse(ERROR, 'The time limit must be greater than 0');
        }

        $this->questionRepository->create($request->only(array_keys($rules)));

        return ajaxResponse(SUCCESS);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createReadingQuestion(Request $request)
    {

        $rules = [
            'body' => 'required',
            'q_type' => 'required',
            'description' => 'nullable',
            'question_level_id' => 'integer|required',
            'minutes' => 'integer|required',
            'seconds' => 'integer|required',
            'language_paper_type_id' => 'required',
            'answer.*' => 'required',
            'answer' => 'array|required'
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        unset($rules['minutes']);
        unset($rules['seconds']);

        //calculate seconds
        $time = $request->get('minutes') * 60 + $request->get('seconds');
        $request->request->add(['time' => $time]);

        $rules['time'] = $time;

        if ($time == 0) {
            return ajaxResponse(ERROR, 'The time limit must be greater than 0');
        }


        $correctValues = array_values($request->get('correct'));
        $totalCorrect = array_filter($correctValues, function($item){
            return $item == 1;
        });
        if (count($correctValues) - count($totalCorrect) < 3) {
            return ajaxResponse(ERROR, 'You must add at least 3 incorrect answers');
        }
        if (count($totalCorrect) != 1) {
            return ajaxResponse(ERROR, 'There should be 1 correct answer');
        }

        $statusValues = array_values($request->get('status'));
        if (count($statusValues) <= 3) {
            return ajaxResponse(ERROR, 'Wrong question choices. Question must have at least 3 incorrect and active choices and 1 correct and active choice.');
        }


        $newQuestion = $this->questionRepository->create($request->only(array_keys($rules)));

        ### UPDATE CHOICES

        if ($request->has('answer')) {
            $this->_updateChoices($newQuestion->id, $request);
        }

//        $redirectTo = url('/admin/questions/' . $newQuestion->id . '/choices');

        return ajaxResponse(SUCCESS); //, null, ['redirectTo' => $redirectTo]);

    }

    /**language use 60
     * @param Request $request
     * @return mixed
     */
    public function createLanguageUseQuestion(Request $request)
    {
        $rules = [
            'body' => 'required',
            'description' => 'nullable',
            'language_paper_type_id' => 'required',
            'answer.*' => 'required',
            'answer' => 'array|required'
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $correctValues = array_values($request->get('correct'));
        $totalCorrect = array_filter($correctValues, function($item){
            return $item == 1;
        });
        if (count($correctValues) - count($totalCorrect) < 3) {
            return ajaxResponse(ERROR, 'You must add at least 3 incorrect answers');
        }
        if (count($totalCorrect) != 1) {
            return ajaxResponse(ERROR, 'There should be 1 correct answer');
        }

        $statusValues = array_values($request->get('status'));
        if (count($statusValues) <= 3) {
            return ajaxResponse(ERROR, 'Wrong question choices. Question must have at least 3 incorrect and active choices and 1 correct and active choice.');
        }

        $newQuestion = $this->questionRepository->create($request->only(array_keys($rules)));

        ### UPDATE CHOICES

        if ($request->has('answer')) {
            $this->_updateChoices($newQuestion->id, $request);
        }

        //$redirectTo = url('/admin/questions/' . $newQuestion->id . '/choices');

        return ajaxResponse(SUCCESS);//, null, ['redirectTo' => $redirectTo]);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createListeningQuestion(Request $request)
    {

        $rules = [
            'body' => 'required',
            'q_type' => 'required',
            'description' => 'nullable',
            'question_level_id' => 'integer|required',
            'minutes' => 'integer|required',
            'seconds' => 'integer|required',
            'language_paper_type_id' => 'required',
            'file' => 'required',
            'answer.*' => 'required',
            'answer' => 'array|required'
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $file = $request->file('file');

        ### check file for uploading
        $audioFileOk = $this->_checkAudioFile($file);
        if ($audioFileOk !== true) {
            return $audioFileOk;
        }

        unset($rules['minutes']);
        unset($rules['seconds']);

        ### calculate seconds
        $time = $request->get('minutes') * 60 + $request->get('seconds');
        $request->request->add(['time' => $time]);

        $rules['time'] = $time;

        $fileName = $file->getClientOriginalName();
        $rules['audio_file_path'] = $fileName;

        $request->request->add(['audio_file_path' => $fileName]);

        if ($time == 0) {
            return ajaxResponse(ERROR, 'The time limit must be greater than 0');
        }

        $correctValues = array_values($request->get('correct'));
        $totalCorrect = array_filter($correctValues, function($item){
            return $item == 1;
        });
        if (count($correctValues) - count($totalCorrect) < 3) {
            return ajaxResponse(ERROR, 'You must add at least 3 incorrect answers');
        }
        if (count($totalCorrect) != 1) {
            return ajaxResponse(ERROR, 'There should be 1 correct answer');
        }

        $statusValues = array_values($request->get('status'));
        if (count($statusValues) <= 3) {
            return ajaxResponse(ERROR, 'Wrong question choices. Question must have at least 3 incorrect and active choices and 1 correct and active choice.');
        }

        $question = $this->questionRepository->create($request->only(array_keys($rules)));

        ### Upload file
        $destinationPath = public_path('audio');
        $file->move($destinationPath, $question->id . '-' . $fileName);

        ### UPDATE CHOICES

        if ($request->has('answer')) {
            $this->_updateChoices($question->id, $request);
        }

        //$redirectTo = url('/admin/questions/' . $question->id . '/choices');

        return ajaxResponse(SUCCESS);//, null, ['redirectTo' => $redirectTo]);


    }

    /**
     * language use new
     * @param Request $request
     * @return mixed
     *
     */
    public function createLanguageQuestion(Request $request)
    {

        $rules = [
            'description' => 'nullable',
            'language_use_type' => 'integer|required',
            'question_level_id' => 'integer|required',
            'q_type' => 'required',
            'minutes' => 'integer|required',
            'seconds' => 'integer|required',
            'language_paper_type_id' => 'required',
        ];

        $redirectTo = false;

        switch ($request->language_use_type) {
            case TEST_LU_READING:
                $rules['body_reading'] = 'required';
                $rules['answer.*'] = 'required';
                $rules['answer'] = 'array|required';
                $request->request->add(['body' => $request->body_reading]);

                $redirectTo = true;
                break;
            case TEST_LU_ARRANGE:
                $rules['body_arrange'] = 'required';
                $rules['body_incorrect'] = 'nullable';
                $request->request->add(['body' => $request->body_arrange_json]);

                if (strlen($request->body_arrange_json) == 0) {
                    return ajaxResponse(ERROR, 'The question body is required ');
                }

                break;
            case TEST_LU_FILLGAPS:
                $rules['body_gaps'] = 'required';
                $request->request->add(['body' => $request->body_gaps]);

                if (strpos($request->body_gaps, '_') === false) {
                    return ajaxResponse(ERROR, 'The question body must have underscore "__" ');
                }

                break;
            default;
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        unset($rules['minutes']);
        unset($rules['seconds']);
        unset($rules['body_reading']);
        unset($rules['body_arrange']);
        unset($rules['body_gaps']);

        $rules['body'] = null;

        //calculate seconds
        $time = $request->get('minutes') * 60 + $request->get('seconds');
        $request->request->add(['time' => $time]);

        $rules['time'] = $time;

        if ($time == 0) {
            return ajaxResponse(ERROR, 'The time limit must be greater than 0');
        }

        if ($request->language_use_type == TEST_LU_READING) {
            $correctValues = array_values($request->get('correct'));
            $totalCorrect = array_filter($correctValues, function($item){
                return $item == 1;
            });
            if (count($correctValues) - count($totalCorrect) < 3) {
                return ajaxResponse(ERROR, 'You must add at least 3 incorrect answers');
            }
            if (count($totalCorrect) != 1) {
                return ajaxResponse(ERROR, 'There should be 1 correct answer');
            }

            $statusValues = array_values($request->get('status'));
            if (count($statusValues) <= 3) {
                return ajaxResponse(ERROR, 'Wrong question choices. Question must have at least 3 incorrect and active choices and 1 correct and active choice.');
            }

        }


        $newQuestion = $this->questionRepository->create($request->only(array_keys($rules)));

        if ($request->language_use_type == TEST_LU_FILLGAPS) {
            $this->questionChoiceRepository->create([
                'question_id' => $newQuestion->id,
                'answer' => $request->lu_gap_answer,
                'correct' => 1
            ]);
        }

        if ($request->language_use_type == TEST_LU_READING) {
            ### UPDATE CHOICES

            if ($request->has('answer')) {
                $this->_updateChoices($newQuestion->id, $request);
            }
        }

//        if ($redirectTo) {
//            $redirectTo = url('/admin/questions/' . $newQuestion->id . '/choices');
//        }

        return ajaxResponse(SUCCESS);//, null, ['redirectTo' => $redirectTo]);

    }

    /**
     *  Check file size and mime
     *
     * @param $file
     * @return mixed
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

        return true;

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createQuestionChoice(Request $request)
    {

        $rules = [
            'answer' => 'required',
            'correct' => 'required|integer',
            'question_id' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($request->get('correct') == 1) {
            $total = QuestionChoice::where(['correct' => 1, 'question_id' => $request->get('question_id')])->count();
            if ($total > 0) {
                return ajaxResponse(ERROR, 'There is already a correct answer');
            }
        }

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        QuestionChoice::create($request->only(array_keys($rules)));

        return ajaxResponse(SUCCESS);
    }

    /**
     *  Activate and deactivate
     *
     * @param $action
     * @param $id
     * @return mixed
     */
    public function updateQuestionStatus($action, $id)
    {
        if ($action == 'activate') {

            $question = Question::withTrashed()->where('id', $id)->with('languagePaperTypes')->first();
            $testType = $question->languagePaperTypes->paper_type_id;

            ### check question supports choices
            if ($testType == TEST_LANGUAGE_USE || $testType == TEST_READING || $testType == TEST_LISTENING || ($testType == TEST_LANGUAGE_USE_NEW && $question->language_use_type == TEST_LU_READING)) {

                $correctchoices = QuestionChoice::where('correct', 1)->where('question_id', $id)->count();
                $incorrectchoices = QuestionChoice::where('correct', 0)->where('question_id', $id)->count();

                if ($correctchoices != 1 || $incorrectchoices < 3) {
                    return ajaxResponse(ERROR,
                        'You cannot activate this question because the question doesn\'t have at least 1 correct choice and 3 incorrect choices');
                }
            }

            $this->questionRepository->untrash($id);
        } elseif ($action == 'deactivate') {
            $this->questionRepository->delete($id);
        }

        return ajaxResponse(SUCCESS);
    }

    public function forceDeleteQuestion($id){
        Question::withTrashed()->find($id)->forceDelete();
        return ajaxResponse(SUCCESS);
    }

    /**
     *  Activate and deactivate
     *
     * @param $action
     * @param $id
     * @return mixed
     */
    public function updateChoiceStatus($action, $id)
    {
        if ($action == 'activate') {
            $this->questionChoiceRepository->untrash($id);
        } elseif ($action == 'deactivate') {
            $this->questionChoiceRepository->delete($id);
        }

        return ajaxResponse(SUCCESS);
    }

    /**
     *  Ajax - get details
     * @param $id
     * @return mixed
     */
    public function getQuestion($id)
    {

        $question = Question::withTrashed()->with(['questionChoices' => function($q){
            $q->withTrashed();
        }])->find($id);
        $testType = LanguagePaperTypes::withTrashed()->find($question->language_paper_type_id);
        $language = Language::find($testType->language_id);
        $levels = QuestionLevel::all()->pluck('name', 'id');
        $levels[''] = 'All';

        $qTypes = ['' => 'All'];
        for ($i = 1; $i <= 20; $i++) {
            $qTypes[$i] = 'Q' . $i;
        }

        $fileExtension = '';

        ### audio file for listening
        if ($testType->paper_type_id == TEST_LISTENING) {
            if ($question->audio_file_path != null) {
                $fileExtension = pathinfo(url('audio/' . $question->id . '/' . $question->audio_file_path))['extension'];
            }
        }

        ### correct answer for fill in gap
        if ($testType->paper_type_id == TEST_LANGUAGE_USE_NEW && $question->language_use_type == TEST_LU_FILLGAPS) {
            $question->lu_gap_answer = QuestionChoice::where('question_id', $id)->first()->answer;
        }

        $testTypes = [
            1 => 'language-use-new',
            3 => 'writing',
            4 => 'listening',
            5 => 'reading',
            6 => 'language-use',
        ];

        $hasChoices = in_array($testType->paper_type_id, [
            TEST_READING,
            TEST_LISTENING,
            TEST_LANGUAGE_USE_NEW,
            TEST_LANGUAGE_USE,
        ]);

        return view('manageTests.partials.modal-' . $testTypes[$testType->paper_type_id],
            compact('question', 'testType', 'language', 'levels', 'fileExtension', 'qTypes', 'hasChoices'));

    }

    /**
     *  Ajax - get details
     * @param $id
     * @return mixed
     */
    public function getQuestionChoice($id)
    {

        $choice = QuestionChoice::withTrashed()->find($id);

        return view('manageTests.partials.modal-choice', compact('choice'));

    }

    /**
     *  Question choices list
     * @param $id
     * @return mixed
     */
    public function getQuestionChoices($id)
    {

        $question = $this->questionRepository->getById($id);

        return view('manageTests.language-test-choices', compact('question'));

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateQuestionChoice($id, Request $request)
    {

        $rules = [
            'answer' => 'required',
            'correct' => 'required|integer',
            'question_id' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($request->get('correct') == 1) {
            $total = QuestionChoice::where(['correct' => 1, 'question_id' => $request->get('question_id')])->where('id',
                '!=', $id)->count();
            if ($total > 0) {
                return ajaxResponse(ERROR, 'There is already a correct answer');
            }
        }

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $this->questionChoiceRepository->update($id, $request->only(array_keys($rules)));

        return ajaxResponse(SUCCESS);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateQuestion($id, Request $request)
    {
        $question = Question::withTrashed()->where('id', $id)->with('languagePaperTypes')->first();
        $testType = $question->languagePaperTypes->paper_type_id;

        ### set rules
        switch ($testType) {
            case TEST_WRITING:

                $rules = [
                    'body' => 'required',
                    'description' => 'nullable',
                    'max_words' => 'integer|required',
                    'minutes' => 'integer|required',
                    'seconds' => 'integer|required',
                    'language_paper_type_id' => 'required'
                ];

                break;
            case TEST_READING:

                $rules = [
                    'body' => 'required',
                    'description' => 'nullable',
                    'question_level_id' => 'integer|required',
                    'q_type' => 'required',
                    'minutes' => 'integer|required',
                    'seconds' => 'integer|required',
                    'language_paper_type_id' => 'required',
                    'answer.*' => 'required',
                    'answer' => 'array|required',
                ];

                break;

            case TEST_LANGUAGE_USE:

                $rules = [
                    'body' => 'required',
                    'description' => 'nullable',
                    'language_paper_type_id' => 'required',
                    'answer.*' => 'required',
                    'answer' => 'array|required',
                ];

                break;
            case TEST_LISTENING:

                $rules = [
                    'body' => 'required',
                    'description' => 'nullable',
                    'question_level_id' => 'integer|required',
                    'q_type' => 'required',
                    'minutes' => 'integer|required',
                    'seconds' => 'integer|required',
                    'language_paper_type_id' => 'required',
                    'file' => 'nullable',
                    'answer.*' => 'required',
                    'answer' => 'array|required',
                ];
                break;
            case TEST_LANGUAGE_USE_NEW:

                $rules = [
                    'description' => 'nullable',
                    'language_use_type' => 'integer|required',
                    'question_level_id' => 'integer|required',
                    'q_type' => 'required',
                    'minutes' => 'integer|required',
                    'seconds' => 'integer|required',
                    'language_paper_type_id' => 'required',
                ];

                switch ($request->language_use_type) {
                    case TEST_LU_READING:
                        $rules['body_reading'] = 'required';
                        $rules['answer.*'] = 'required';
                        $rules['answer'] = 'required';

                        $request->request->add(['body' => $request->body_reading]);
                        break;
                    case TEST_LU_ARRANGE:
                        $rules['body_arrange'] = 'required';
                        $rules['body_incorrect'] = 'nullable';
                        $request->request->add(['body' => $request->body_arrange_json]);
                        break;
                    case TEST_LU_FILLGAPS:
                        $rules['body_gaps'] = 'required';
                        $request->request->add(['body' => $request->body_gaps]);
                        break;
                    default;
                        break;
                }

                break;

                break;

            default:
                $rules = [];
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }


        ### check correct options
        switch ($testType) {
            case TEST_READING:
            case TEST_LISTENING:
            case TEST_LANGUAGE_USE:
            case TEST_LANGUAGE_USE_NEW:

                if($testType == TEST_LANGUAGE_USE_NEW &&
                    ($request->language_use_type == TEST_LU_ARRANGE || $request->language_use_type == TEST_LU_FILLGAPS)) {
                    break;
                }

                $correctValues = array_values($request->get('correct'));
                $totalCorrect = array_filter($correctValues, function($item){
                    return $item == 1;
                });
                if (count($correctValues) - count($totalCorrect) < 3) {
                    return ajaxResponse(ERROR, 'You must add at least 3 incorrect answers');
                }
                if (count($totalCorrect) != 1) {
                    return ajaxResponse(ERROR, 'There should be 1 correct answer');
                }

                $statusValues = array_values($request->get('status'));
                if (count($statusValues) <= 3) {
                    return ajaxResponse(ERROR, 'Wrong question choices. Question must have at least 3 incorrect and active choices and 1 correct and active choice.');
                }

                break;
        }

        if ($testType == TEST_LISTENING && $request->has('file')) {

            $file = $request->file('file');

            ### check file for uploading
            $audioFileOk = $this->_checkAudioFile($file);
            if ($audioFileOk !== true) {
                return $audioFileOk;
            }

            $fileName = $file->getClientOriginalName();
            $rules['audio_file_path'] = $fileName;

            $request->request->add(['audio_file_path' => $fileName]);

            ### Upload file
            $destinationPath = public_path('audio');
            $file->move($destinationPath, $id . '-' . $fileName);

        }

        unset($rules['minutes']);
        unset($rules['seconds']);

        if ($testType == TEST_LANGUAGE_USE_NEW) {

            unset($rules['body_reading']);
            unset($rules['body_arrange']);
            unset($rules['body_gaps']);

            $rules['body'] = null;

        }

        if ($testType != TEST_LANGUAGE_USE) {

            //calculate seconds
            $time = $request->get('minutes') * 60 + $request->get('seconds');
            $request->request->add(['time' => $time]);
            $rules['time'] = $time;

            if ($time == 0) {
                return ajaxResponse(ERROR, 'The time limit must be greater than 0');
            }

        }

        $this->questionRepository->update($id, $request->only(array_keys($rules)));

        if ($request->has('language_use_type') && $request->language_use_type == TEST_LU_FILLGAPS) {
            QuestionChoice::where('question_id', $id)->update([
                'answer' => $request->lu_gap_answer,
            ]);
        }
        
        
        ### UPDATE CHOICES

        if ($request->has('answer')) {
            $this->_updateChoices($id, $request);
        }

        return ajaxResponse(SUCCESS);
    }


    /**
     * @param $questionId
     * @param $request
     */
    private function _updateChoices($questionId, $request)
    {
        $finalChoicesIds = [];
        foreach ($request->get('answer') as $choiceId => $v) {
            $choice = [
                'question_id' => $questionId,
                'correct' => $request->correct[$choiceId],
                'answer' => $request->answer[$choiceId],
                'deleted_at' => isset($request->status[$choiceId]) && $request->status[$choiceId] == 1 ? null : Carbon::now()
            ];
            if (substr($choiceId, 0, 3) === 'new') {
                $newChoice = $this->questionChoiceRepository->create($choice);
                $finalChoicesIds[] = $newChoice->id;
            } else {
                $finalChoicesIds[] = $choiceId;
                $this->questionChoiceRepository->update($choiceId, $choice);
            }
        }


        $allChoicesIds = QuestionChoice::withTrashed()->where('question_id', $questionId)->pluck('id')->toArray();

        $toDeleteChoices = array_diff($allChoicesIds, $finalChoicesIds);

        if (!empty($toDeleteChoices)) {
            QuestionChoice::whereIn('id', $toDeleteChoices)->forceDelete();
        }
    }


}