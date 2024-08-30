<?php

namespace App\Console\Commands;

use App\Models\TaskStatus;
use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class ChangeToIssueOnTestNotDone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:status:issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $tasks = Task::where(function($q) {
            $q->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') <= '" . Carbon::now()->subDays(4)->format('Y-m-d H:i') . "'")
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') >= '" . Carbon::now()->subDays(4)->format('Y-m-d 00:00') . "'")
                ->whereNull("deadline")
                ->where('task_status_id', '=', TaskStatus::STATUS_IN_PROGRESS)
                ->where('reminder_sent', '=', 1)
                ->with('papers', 'project', 'project.owner', 'language', 'logs')
                ->whereHas('papers', function ($q) {
                    $q->where('done', 1);
                    $q->where('paper_type_id', TEST_SPEAKING);
                })
                ->whereHas('papers', function ($q) {
                    $q->where('done', 0);
                    $q->where('paper_type_id', '!=', TEST_SPEAKING);
                });
        })
            ->orWhere(function($q) {
                $q->whereRaw("DATE_FORMAT(deadline, '%Y-%m-%d %H:%i') <= '" . Carbon::now()->subDays(2)->format('Y-m-d H:i') . "'")
                    ->where('task_status_id', '=', TaskStatus::STATUS_IN_PROGRESS)
                    ->with('papers', 'project', 'project.owner', 'language', 'logs')
                    ->whereHas('papers', function ($q) {
                        $q->where('done', 1);
                        $q->where('paper_type_id', TEST_SPEAKING);
                    })
                    ->whereHas('papers', function ($q) {
                        $q->where('done', 0);
                        $q->where('paper_type_id', '!=', TEST_SPEAKING);
                    });
            })->get();


        $bar = $this->output->createProgressBar(count($tasks));

        foreach ($tasks as $task) {
            $task->task_status_id = TaskStatus::STATUS_ISSUE;
            $task->save();
            addLog([
                'type' => TASK_HISTORY,
                'description' => 'Task status was changed automatically to Issue',
                'task_id' => $task->id
            ]);
            $bar->advance();
        }
    }
}
