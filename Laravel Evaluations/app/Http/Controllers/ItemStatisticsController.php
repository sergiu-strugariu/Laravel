<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Repositories\LanguageRepository;
use App\Repositories\PaperAnswerRepository;
use App\Repositories\PaperReportRepository;
use App\Repositories\PaperRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\TaskRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ItemStatisticsController extends Controller
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
        LanguageRepository $languageRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->paperRepository = $paperRepository;
        $this->paperReportRepository = $paperReportRepository;
        $this->paperAnswerRepository = $paperAnswerRepository;
        $this->questionRepository = $questionRepository;
        $this->questionChoiceRepository = $questionChoiceRepository;
        $this->languageRepository = $languageRepository;
    }


    public function viewAll()
    {

        $languageUseTypes = [TEST_LU_READING => 'Multiple choice', TEST_LU_ARRANGE => 'Arrange words', TEST_LU_FILLGAPS => 'Fill gaps'];

        return view('itemStatistics.index', array(
            "languageUseTypes" => $languageUseTypes
        ));
    }

    public function getStatistics(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('start', 0);

        $total =  Question::whereHas("paperAnswers")
            ->count();
        $data = Question::whereHas("paperAnswers")
            ->with('level')
            ->with(["languagePaperTypes" => function($q) {
                $q->with("paperTypes");
            }])
            ->select(['id', 'code', 'q_type', 'language_use_type', 'language_paper_type_id', 'question_level_id'])
            ->with("questionChoices")
            ->with(["paperAnswers" => function($q) {
                $q->select(['id', 'time', 'answer_id', 'question_id', 'paper_id'])
                ->with(['report' => function($q) {
                    $q->select(['id', 'grade', 'paper_id']);
                }]);
            }])
            ->offset($request->get('start', 10))
            ->limit($request->get('length', 10))
            ->get()
            ->toArray();
//        dd($data[2]);

        $result = [];
        $minTime = 10;
        $maxTime = 50;
        foreach ($data as $curRow) {
            $row = $curRow;

            $meanTotal = 0;
            $correctAnswers = 0;
            $totalAnswers = 0;
            $cGradePapers = [];
            $aGradePapers = [];
            $row['mean'] = 0;
            $row['facility'] = 0;
            $row['discrimination'] = 0;
            $correctAnswerId = 0;

            if ($row['question_choices']) {
                foreach($row['question_choices'] as $choice) {
                    if ($choice['correct']) {
                        $correctAnswerId = $choice['id'];
                    }
                }
            }

            if ($row['paper_answers']) {
                $meanCount = 0;
                foreach ($row['paper_answers'] as $paperRow) {

                    // Ignore edges
                    if ($paperRow['time'] > $minTime && $paperRow['time'] < $maxTime) {

                        // Handle Multiple Choice
                        if ($correctAnswerId) {
                            if ($correctAnswerId == $paperRow['answer_id']) {
                                $correctAnswers++;
                            }
                            $totalAnswers++;
                        }

                        // Calculate the mean time of answer
                        $meanTotal += $paperRow['time'];
                        $meanCount++;

                        // Add up grades
                        if ($paperRow['report']) {
                            if ($paperRow['report']['grade'] == "A1" || $paperRow['report']['grade'] == "A2") {
                                if (!array_key_exists($paperRow['report']['id'], $aGradePapers)) {
                                    $aGradePapers[$paperRow['report']['id']] = 1;
                                }
                            } else if ($paperRow['report']['grade'] == "C1" || $paperRow['report']['grade'] == "C2") {
                                if (!array_key_exists($paperRow['report']['id'], $cGradePapers)) {
                                    $cGradePapers[$paperRow['report']['id']] = 1;
                                }
                            }
                        }
                    }
                }

                if (($totalGrades = sizeof($aGradePapers) + sizeof($cGradePapers)) > 0) {
                    $row['discrimination'] = round((sizeof($cGradePapers) / $totalGrades) * 100) / 100;
                }
                if ($correctAnswerId && $totalAnswers) {
                    $row['facility'] = round(($correctAnswers / $totalAnswers) * 100) / 100;
                }
                if ($meanCount) {
                    $row['mean'] = round(($meanTotal / $meanCount) * 100) / 100;
                }
            }

            $result[] = $row;
        }
//        $dt = DataTables::of($result)->with([
//            'recordsTotal' => $total,
//            'recordsFiltered' => $total
//        ])->make();
//        return $dt;

        return new JsonResponse([
            "data" => $result,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'draw' => $request->get('draw', 0)
        ]);
    }
}
