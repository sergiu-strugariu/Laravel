<?php
/**
 * Created by PhpStorm.
 * User: AlexBadea
 * Date: 20.07.2018
 * Time: 11:38
 */

namespace App\Services;


use App\Models\LanguagePaperTypes;
use App\Models\Paper;
use App\Models\Task;
use App\Repositories\GroupRepository;
use App\Repositories\PaperRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;

/**
 * Class EventService
 * @package App\Services
 */
class EventService
{

    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var EmailService
     */
    private $emailService;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PaperRepository
     */
    private $paperRepository;


    /**
     * EventService constructor.
     * @param GroupRepository $groupRepository
     * @param EmailService $emailService
     * @param UserRepository $userRepository
     * @param PaperRepository $paperRepository
     */
    public function __construct(
        GroupRepository $groupRepository,
        EmailService $emailService,
        UserRepository $userRepository,
        PaperRepository $paperRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->emailService = $emailService;
        $this->userRepository = $userRepository;
        $this->paperRepository = $paperRepository;

    }

    /**
     * @param $task
     * @param $oldTask
     */
    public function handleTaskLanguageChange($task, $oldTask)
    {

        ### get available paper types for the new lang
        $newLangAvailableTestTypes = LanguagePaperTypes::where('language_id', $task->language_id)->get()->pluck('paper_type_id')->toArray();
        ### delete all papers that are not in $newLangAvailableTestTypes
        if (!empty($newLangAvailableTestTypes)) {
            Paper::where('task_id', $task->id)->whereNotIn('paper_type_id', $newLangAvailableTestTypes)->delete();
        }

        $notNativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative($task->language_id, false);
        $nativeAssessors = $this->groupRepository->getAssessorsByLanguageAndNative($task->language_id, true);

        if ($task->native == 0 && $notNativeAssessors->count() == 0 && $nativeAssessors->count() > 0) {
            $task->native = 1;
        }

        if ($task->native == 1 && $nativeAssessors->count() == 0 && $notNativeAssessors->count() > 0) {
            $task->native = 0;
        }

        $task->assessor_id = null;
        $task->save();

        $assessor = $this->groupRepository->getRandomAssessor(intval($task->language_id), $task->assessor_id, $task->native);
        if (!empty($assessor)) {
            $task->assessor_id = $assessor->id;
            $task->save();
            $this->emailService->sendAssessorMail($assessor, $task);
            addAssessorHistory($task->id, $assessor->id, 'random');
            addLog([
                'type' => TASK_HISTORY,
                'description' => 'Task assessor was changed from ' . $oldTask->assessor->full_name . ' to ' . $assessor->full_name,
                'task_id' => $task->id,
                'user_id' => auth()->user()->id
            ]);
        } else {
            // language group doesn't have any assessors
            addLog([
                'type' => TASK_HISTORY,
                'description' => 'No assessor was found for this language!',
                'task_id' => $task->id,
                'user_id' => auth()->user()->id
            ]);
            $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService, $task);

        }

        $oldAssessorEmail = !is_null($oldTask->assessor) ? $oldTask->assessor->email : false;

        if ($oldAssessorEmail) {
            ### 11. Assessment cancelled
            ### Send email to old assessor
            $this->emailService->sendEmail([
                'email' => $oldAssessorEmail,
                'name' => $task->name
            ], MAIL_ASSESSMENT_CANCELED);
        }


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
            $link = $task->link;
        }

        ###  Send email to test taker
        $this->emailService->sendEmail([
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




    }

    /**
     * @param $task
     * @param $paper_type_id
     */
    public function handleTaskTestAdd($task, $paper_type_id)
    {

        $this->paperRepository->createOrSkip([
            'paper_type_id' => $paper_type_id,
            'task_id' => $task->id
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
                'type' => TASK_HISTORY,
                'task_id' => $task->id,
                'description' => 'Mail resent to test taker for task id ' . $task->id
            ]);
        }


        if ($paper_type_id == TEST_SPEAKING ) {
            $assessor = $this->groupRepository->getRandomAssessor($task->language_id, 0, $task->native);
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

                $this->userRepository->sendTaskGroupEmptyMailToAdmins($this->emailService, $task);
                addLog([
                    'type' => MAIL_SENT,
                    'task_id' => $task->id,
                    'user_id' => auth()->user()->id,
                    'description' => 'Mail sent to admin because group is empty'
                ]);

            }
        }

    }


}