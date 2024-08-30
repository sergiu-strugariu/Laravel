<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestTakeReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:test_take';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an email to test taker to remind them they have a test to take';

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

        $tasks = Task::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') <= '" . Carbon::now()->subDays(2)->format('Y-m-d H:i') . "'")
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') >= '" . Carbon::now()->subDays(2)->format('Y-m-d 00:00') . "'")
            //whereDate('created_at', Carbon::now()->subDays(2)->toDateString())
            ->with('papers', 'project', 'project.owner', 'language', 'logs')
            ->whereDoesntHave('logs', function($q) {
                $q->where("description", 'LIKE', "%Candidate no longer interested%");
            })
            ->whereHas('papers', function ($q) {
                $q->where('done', 0);
                $q->where('reminder_sent', 0);
                $q->where('status_id', ALLOCATED);
                $q->where('paper_type_id', '!=', TEST_SPEAKING);
            })->get();

        $bar = $this->output->createProgressBar(count($tasks));

        $liveEnv = env('APP_ENV') == 'live';

        foreach ($tasks as $task) {

            $attributes = [
                'email' => $task->email,
                'name' => $task->name,
                'link' => $task->link,
                'company' => $task->project->owner->name,
                'language' => $task->language->name,
            ];

            //Email invitation for online test resent
            addLog([
                'type' => TASK_UPDATE,
                'description' => 'Email invitation for online test resent to ' . $task->email . ' (task id: ' . $task->id . ')',
                'task_id' => $task->id
            ]);

            $this->emailService->sendEmail($attributes, MAIL_TEST_REMIND);

            if ($liveEnv) {

                //SMS invitation for online test resent
                $smsSent = $this->emailService->sendSms([
                    'phone' => $task->phone,
                    'company' => $attributes['company'],
                    'language' => $attributes['language'],
                ], SMS_TEST_REMIND);

                if ($smsSent === true) {
                    addLog([
                        'type' => TASK_UPDATE,
                        'description' => 'Automatic SMS reminder sent to test-taker',
                        'task_id' => $task->id
                    ]);
                } else {
                    addLog([
                        'type' => TASK_UPDATE,
                        'description' => 'SMS reminder not sent. ' . $smsSent,
                        'task_id' => $task->id
                    ]);
                }

            }

            $task->reminder_sent = true;
            $task->save();

            $bar->advance();

        }

        $bar->finish();

        if (count($tasks) === 0) {
            $this->line("\n");
            $this->info('No tasks found');
        }
    }

}
