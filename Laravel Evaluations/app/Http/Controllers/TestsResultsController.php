<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/9/2018
 * Time: 12:05 PM
 */

namespace App\Http\Controllers;


use App\Models\PaperAnswers;
use App\Models\PaperReport;
use App\Repositories\LanguageRepository;
use App\Repositories\PaperAnswerRepository;
use App\Repositories\PaperReportRepository;
use App\Repositories\PaperRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Knp\Snappy\Pdf;


class TestsResultsController extends Controller
{

    private $pdf;
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
     * @var $languageRepository
     */
    private $languageRepository;

    /**
     * TestController constructor.
     * @param TaskRepository $taskRepository
     * @param PaperRepository $paperRepository
     * @param PaperReportRepository $paperReportRepository
     * @param PaperAnswerRepository $paperAnswerRepository
     * @param QuestionRepository $questionRepository
     * @param QuestionChoiceRepository $questionChoiceRepository
     * @param LanguageRepository $languageRepository
     */
    public function __construct(
        TaskRepository $taskRepository,
        PaperRepository $paperRepository,
        PaperReportRepository $paperReportRepository,
        PaperAnswerRepository $paperAnswerRepository,
        QuestionRepository $questionRepository,
        QuestionChoiceRepository $questionChoiceRepository,
        LanguageRepository $languageRepository,
        Pdf $pdf
    ) {
        $this->taskRepository = $taskRepository;
        $this->paperRepository = $paperRepository;
        $this->paperReportRepository = $paperReportRepository;
        $this->paperAnswerRepository = $paperAnswerRepository;
        $this->questionRepository = $questionRepository;
        $this->questionChoiceRepository = $questionChoiceRepository;
        $this->languageRepository = $languageRepository;
        $this->pdf = $pdf;
    }


    public function viewAll()
    {
        $languages = $this->languageRepository->getAll()->pluck('name', 'id');
        return view('tests.results.index', compact('languages'));
    }

    public function getDatatable(Request $request)
    {
        $filters = $request->input('filters');
        $results = $this->paperReportRepository->filterResults($filters)->with([
            'paper',
            'paper.paper_answers',
            'paper.task',
            'paper.task.language',
            'paper.type'
        ])
            ->whereHas('paper', function ($q) {
                $q->whereIn('paper_type_id', [
                    TEST_WRITING,
                    TEST_LANGUAGE_USE_NEW,
                    TEST_LANGUAGE_USE,
                    TEST_READING,
                    TEST_LISTENING
                ]);
            })->withTrashed();
        return DataTables::of($results)->make(true);
    }

    public function viewTestResult($test_id)
    {

        $data = $this->_generateTestResultsData($test_id);

        $report = $data['report'];
        $questions = $data['questions'];
        $total_time = $data['total_time'];
        $total_questions = $data['total_questions'];
        $total_correct_questions = $data['total_correct_questions'];
        $total_incorrect_questions = $data['total_incorrect_questions'];
        $total_unanswered_questions = $data['total_unanswered_questions'];
        $skillsAssessments = $data['skillsAssessments'];
        $isClient = Auth::user()->hasRole('client');

        $grades = $skillsAssessments['grades'];
        $grades[] = 'Native';
        $global_grade = $report->grade == 'N' ? 'N' : $report->assessments['general_descriptors'];

        $nextGrade = $global_grade;

        foreach ($grades as $key => $grade) {
            if ($grade == $global_grade && isset($grades[$key + 1])) {
                $nextGrade = $grades[$key + 1];
                break;
            }
        }

        return view('tests.results.result',
            compact('report', 'questions', 'total_time', 'total_questions', 'total_correct_questions', 'total_incorrect_questions', 'total_unanswered_questions', 'skillsAssessments', 'isClient', 'nextGrade'));
    }

    public function downloadPDF($test_id)
    {
        $data = $this->_generateTestResultsData($test_id);
        $report = $data['report'];
        $questions = $data['questions'];
        $total_time = $data['total_time'];
        $skillsAssessments = $data['skillsAssessments'];

        $html = view('tests.results.pdf-result',
            compact('report', 'questions', 'total_time', 'skillsAssessments'))->render();
        $pdf = \App::make('snappy.pdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->download($report->paper->type->name . $report->paper->task->name . $report->created_at . '.pdf');
    }

    /**
     * Generate data for views
     *
     * @param $report_id
     * @return array
     */
    private function _generateTestResultsData($report_id)
    {
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
                'paper.paper_answers' => function($q1) use ($report_id) {
                    $q1->withTrashed();
                    $q1->where(function ($q) use ($report_id, $q1) {
                        $q->where(function($q) use ($report_id, $q1){
                            $q->whereNull('report_id');
                            $q->orWhere('report_id', $report_id);
                        })->orWhere(function($q) use ($q1) {
                            $q->where('paper_id', $q1->getBindings()[0]);
                        });

                    });
                }
            ]
        )
        ->where('id', $report_id)
        ->first();


        $questions = [];
        foreach ($report->paper->paper_answers as $paper_answer) {
            if ($paper_answer->deleted_at == null) {
                if ($paper_answer->question->audio_file_path != null) {
                    $questions[$paper_answer->question->id]['file_extension'] = pathinfo(url('audio/' . $paper_answer->question->id . '/' . $paper_answer->question->audio_file_path))['extension'];
                }
                $questions[$paper_answer->question->id] = $paper_answer->question;
                $questions[$paper_answer->question->id]['opts'] = json_decode($paper_answer->choices, true);
                $questions[$paper_answer->question->id]['user_answer'] = $paper_answer->user_answer;
                $questions[$paper_answer->question->id]['time_spent'] = $paper_answer->time;
            }
        }
        $total_time = 0;
        $total_questions = 0;
        $total_correct_questions = 0;
        $total_incorrect_questions = 0;
        $total_unanswered_questions = 0;
        foreach ($questions as $question) {
            $total_questions++;
            $total_time = $total_time + $question['time_spent'];
            if ($question->language_use_type == TEST_LU_FILLGAPS) {
                $class = '';
                if (!empty($question['user_answer'])) {
                    if ($question['user_answer'] == $question->questionChoices[0]->answer) {
                        $class = 'correct';
                        $total_correct_questions++;
                    } else {
                        $class = 'incorrect';
                        $total_incorrect_questions++;
                    }
                } else {
                    $class = 'empty';
                    $total_unanswered_questions++;
                }

                $question->userBody = preg_replace('/_+/', $question['user_answer'], $question->body);
                $question->isCorrect = $question['user_answer'] == $question->questionChoices[0]->answer;
                $question->class = $class;
            } elseif ($question->language_use_type == TEST_LU_ARRANGE) {
                $class = '';
                if (!empty($question['user_answer'])) {
                    if (json_decode($question['user_answer'], true) == json_decode($question->body, true)) {
                        $class = 'correct';
                        $total_correct_questions++;
                    } else {
                        $class = 'incorrect';
                        $total_incorrect_questions++;
                    }
                } else {
                    $class = 'empty';
                    $total_unanswered_questions++;
                }
                $question->class = $class;
            } elseif (!empty($question['user_answer'])) {
                if ($question["opts"] != null) {
                    foreach ($question["opts"] as $choice) {
                        if ($choice['correct'] == 1) {
                            if ($question['user_answer'] == $choice['id']) {
                                $total_correct_questions++;
                            } else {
                                $total_incorrect_questions++;
                            } 
                        }
                    }
                } 
            } else {
                $total_unanswered_questions++;
            }
            
        }
        $total_time = gmdate('i:s', $total_time);

        $report->assessments = json_decode($report->assessments, true);
        $skillsAssessments = [
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

        return [
            'report' => $report,
            'questions' => $questions,
            'total_time' => $total_time,
            'total_questions' => $total_questions,
            'total_correct_questions' => $total_correct_questions,
            'total_incorrect_questions' => $total_incorrect_questions,
            'total_unanswered_questions' => $total_unanswered_questions,
            'skillsAssessments' => $skillsAssessments
        ];
    }

}