<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AssessorRemindCustomPeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:assessor:custom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminds assessors of custom periods and sets the task to Issue if not updated';


    /**
     * @var
     */
    protected $emailService;

    /**
     * Create a new command instance.
     *
     * @return void
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
        $tasks = Task::where("custom_period_cost", ">", 0)
            ->where("custom_period_reminder_sent", 0)
            ->whereRaw("DATE_FORMAT(availability_to, '%Y-%m-%d %H:%i') <= '" . Carbon::now()->subHour(2)->format('Y-m-d H:i') . "'")
            ->whereHas('papers', function($q) {
                $q->whereDoesntHave('report');
            })->get();
        $bar = $this->output->createProgressBar(count($tasks));

        foreach ($tasks as $task) {
            try {
                $task->custom_period_reminder_sent = 1;
                $task->task_status_id = TaskStatus::STATUS_ISSUE;
                $task->custom_period_cost = 0;
                $availability = Carbon::parse($task->availability_from)->format('d M Y') .
                    ', from ' .
                    Carbon::parse($task->availability_from)->format('H:i') .
                    ' to ' .
                    Carbon::parse($task->availability_to)->format('H:i');
                $task->save();

                $attributes = [
                    'email' => $task->assessor->email,
                    'name' => $task->name,
                    'link' => url('task/' . $task->id),
                    'custom_period' => $availability
                ];

                $this->emailService->sendEmail($attributes, MAIL_REMIND_UPDATE_CUSTOM);

                addLog([
                    'type' => TASK_HISTORY,
                    'description' => 'Task status was changed automatically to Issue and a reminder was sent',
                    'task_id' => $task->id
                ]);
            } catch (\Exception $e) {

            }

            $bar->advance();
        }
    }
}
