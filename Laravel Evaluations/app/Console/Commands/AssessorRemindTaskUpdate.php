<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Paper;
use App\Models\TaskStatus;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AssessorRemindTaskUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:assessor_task_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends and email to assessors to remind them they have to update a task';

    protected $emailService;

    /**
     * Create a new command instance.
     *
     * @param EmailService $emailService
     */
    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.

     *
     * @return mixed
     */
    public function handle()
    {
        $papers = Paper::where('paper_type_id', TEST_SPEAKING)
            ->where('reminder_update_sent', 0)
            //->where('status_id', '!=', CANCELED)
            ->whereNotIn('status_id', array(CANCELED, ARCHIVED))
            ->whereDoesntHave('report')
            ->whereHas('task', function($q) {
                $q->where('task_status_id', '!=', TaskStatus::STATUS_CANCELED);
                $q->where(function($q) {
                    $q->whereRaw("DATE_FORMAT(availability_to, '%Y-%m-%d %H:%i') <= '". Carbon::now()->subHour()->format('Y-m-d H:i')."'");
                    $q->orWhereRaw("availability_to IS NULL AND DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') <= '". Carbon::now()->subHour(5)->format('Y-m-d H:i')."'");
                });
                
            })
            ->with('task', 'task.assessor')
            ->get();

        // remove all papers that have no assessor
        $papers = $papers->filter(function($item) {
            return $item->task->assessor !== null;
        });

        foreach ( $papers as $paper ){

            $log = Log::where('task_id', $paper->task_id)
                ->where('type', TASK_UPDATE)
                ->where('user_id', $paper->task->assessor_id)
//                ->where('created_at', '>', is_null($paper->task->availability_from) ? $paper->task->created_at : Carbon::parse($paper->task->availability_from))
                ->first();

            if (!empty($log)) {
                $paper->reminder_update_sent = 1;
                $paper->save();
                continue;
            }

            $attributes = [
                'email' => $paper->task->assessor->email,
                'name' => $paper->task->name,
                'link' => url('task/'.$paper->task->id)
            ];

            $this->emailService->sendEmail($attributes, MAIL_TASK_REMIND_UPDATE);

            //Email reminder
            addLog([
                'type' => TASK_UPDATE,
                'description' => 'Email reminder sent to assessor to update task id: '.$paper->task->id,
                'task_id' => $paper->task->id
            ]);

            $paper->reminder_update_sent = 1;
            $paper->save();
        }
    }
}
