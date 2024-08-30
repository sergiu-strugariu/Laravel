<?php

namespace App\Console\Commands;

use App\Models\Paper;
use App\Models\RevenuePerDay;
use App\Models\RevenuePerLanguage;
use App\Models\TaskStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRevenuePerDayStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:revenue:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the statistics data for the revenue per day.';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $bar = $this->output->createProgressBar(3);

        $papers = Paper::with('task')
            ->where('status_id', DONE)
            ->where('cost', '>', 0)
            ->get();

        $projects = [];
        // id, project_id, day, cost
        foreach ($papers as $paper) {
            $createdAt = $paper['created_at'];
            $dateParts = explode(" ", $createdAt);
            $projectId = $paper['task']['project_id'];
            if (!isset($projects[$projectId])) {
                $projects[$projectId] = array($dateParts[0] => 0);
            }

            if (!isset($projects[$projectId][$dateParts[0]])) {
                $projects[$projectId][$dateParts[0]] = 0;
            }

            if ($paper['paper_type_id'] === TEST_SPEAKING && $paper['task']['custom_period_cost'] > 0) {
                $projects[$projectId][$dateParts[0]] += $paper['task']['custom_period_cost'];
            }

            $projects[$projectId][$dateParts[0]] += $paper['cost'];
        }
        $bar->advance();

        $updateData = [];
        $date = Carbon::now();
        foreach ($projects as $projectId => $project) {
            foreach ($project as $day => $revenue) {
                $updateData[] = [
                    'day' => $day . " 00:00:00",
                    'project_id' => $projectId,
                    'revenue' => $revenue,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }
        $bar->advance();

        RevenuePerDay::truncate();
        RevenuePerDay::insert($updateData);
        $bar->advance();

        $bar->finish();
    }
}
