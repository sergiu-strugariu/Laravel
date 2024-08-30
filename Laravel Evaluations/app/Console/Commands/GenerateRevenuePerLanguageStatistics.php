<?php

namespace App\Console\Commands;

use App\Models\Paper;
use App\Models\RevenuePerLanguage;
use App\Models\TaskStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRevenuePerLanguageStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:revenue:language';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the statistics data for the revenue per language.';

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
            ->with('type')
            ->get();

        $projects = [];

        // id, project_id, language_id, cost
        foreach ($papers as $paper) {
            $languageId = $paper['task']['language_id'];
            $projectId = $paper['task']['project_id'];
            if (!isset($projects[$projectId])) {
                $projects[$projectId] = array($languageId => 0);
            }

            if (!isset($projects[$projectId][$languageId])) {
                $projects[$projectId][$languageId] = 0;
            }

            if ($paper['paper_type_id'] === TEST_SPEAKING && $paper['task']['custom_period_cost'] > 0) {
                $projects[$projectId][$languageId] += $paper['task']['custom_period_cost'];
            }

            $projects[$projectId][$languageId] += $paper['type']['cost'];
        }
        $bar->advance();

        $updateData = [];
        $date = Carbon::now();
        foreach ($projects as $projectId => $project) {
            foreach ($project as $languageId => $revenue) {
                $updateData[] = [
                    'language_id' => $languageId,
                    'project_id' => $projectId,
                    'revenue' => $revenue,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }
        $bar->advance();

        RevenuePerLanguage::truncate();
        RevenuePerLanguage::insert($updateData);

        $bar->finish();
    }
}
