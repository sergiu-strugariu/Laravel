<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    const BILL_CLIENT_NO = 0;
    const BILL_CLIENT_YES = 1;
    const BILL_CLIENT_HALF = 2;

    protected $table = 'tasks';
    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'assessor_id',
        'language_id',
        'added_by_id',
        'name',
        'skype',
        'email',
        'phone',
        'deadline',
        'availability_from',
        'availability_to',
        'mark',
        'department',
        'task_status_id',
        'bill_client',
        'additional_cost',
        'pay_assessor',
        'half_price',
        'native',
        'link',
        'link_access',
        'link_expires_at',
        'reminder_sent',
        'extra_info',
        'custom_period_timezone',
        'custom_period',
        'custom_period_cost',
    ];


    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->email = trim($model->email);
        });

        self::created(function ($model) {
            $model->link = md5($model->id . time());
            $model->link_expires_at = Carbon::now()->addMonths(1);
            $model->save();
        });

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function papers()
    {
        return $this->hasMany('App\Models\Paper')->orderByRaw('FIELD(paper_type_id, 1,6,2,3,4,5)');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assessor()
    {
        return $this->belongsTo('App\Models\User', 'assessor_id')->withTrashed();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followers()
    {
        return $this->hasMany('App\Models\TaskFollower');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\TaskStatus', 'task_status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paperAnswers()
    {
        return $this->hasMany('App\Models\PaperAnswers');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taskAssessorHistories()
    {
        return $this->hasMany('App\Models\TaskAssessorHistory');
    }

    /**
     * @return mixed
     */
    public function addedBy()
    {
        return $this->belongsTo('App\Models\User', 'added_by_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function attachments()
    {
        return Attachment::query()->where(['model' => 'Task', 'model_id' => $this->id])->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log', 'task_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function taskUpdates()
    {
        return $this->logs()->where(['type' => TASK_UPDATE])->orderBy('id', 'desc')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function taskHistory()
    {
        return $this->logs()->where(['type' => TASK_HISTORY])->orderBy('id', 'desc')->get();
    }

    /**
     * @param bool $done
     * @return bool
     */
    public function speakingTest($done = true)
    {
        foreach ($this->papers as $paper) {
            if ($paper->type->id == TEST_SPEAKING && $paper->done == $done && $paper->status_id != CANCELED) {
                return $paper;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasSpeaking()
    {
        foreach ($this->papers as $paper) {
            if ($paper->type->id == TEST_SPEAKING && $paper->status_id != CANCELED) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasWriting()
    {
        foreach ($this->papers as $paper) {
            if ($paper->type->id == TEST_WRITING && $paper->status_id != CANCELED) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param bool $done
     * @return bool
     */
    public function writingTest($done = true)
    {
        foreach ($this->papers as $paper) {
            if ($paper->type->id == TEST_WRITING && $paper->done == $done && $paper->status_id != CANCELED) {
                return $paper;
            }
        }
        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function completedTests()
    {
        return Paper::query()->with(['type', 'report', 'task'])
            ->where(['done' => true, 'papers.task_id' => $this->id])
            ->orderByRaw('FIELD(paper_type_id, 1,6,2,3,4,5)')
            ->has('report')
            ->get();
    }

    /**
     * @param $task_id
     * @return bool
     */
    public static function allTestsAreDoneAndHaveReports($task_id){

        $total = self::where('id', $task_id)->with(['papers' => function($query){
            $query->where('status_id', '!=', CANCELED);
            $query->where(function($query){
                $query->where('done', 0);
                $query->orWhere(function($query){
                    $query->where('done', 1);
                    $query->whereDoesntHave('report');
                });
            });
        }])->first();

        return count($total->papers) === 0;
    }

    /**
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function remainingOnlineTests($onlyActive = false)
    {
        $completedTests = $this->completedTests();
        $query = Paper::query()->with(['type', 'report', 'task'])->where(['done' => false, 'papers.task_id' => $this->id]);
        foreach ($completedTests as $completedTest) {
            $query = $query->where('papers.id', '<>', $completedTest->id);
        }

        if (!empty($this->speakingTest(false))) {
            $query = $query->where('papers.id', '<>', $this->speakingTest(false)->id);
        }
//        if (!empty($this->writingTest(false))) {
//            $query = $query->where('papers.id', '<>', $this->writingTest(false)->id);
//        }

        $results = $query->get();

        if ($onlyActive){
            foreach ($results as $res) {
                if ($res->status_id != CANCELED && $res->status_id != DONE) {
                    return true;
                }
            }
            return false;
        }
        return $results;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getOnlineTests(){

        $query = Paper::with('type', 'report', 'task')
            ->where('papers.task_id', $this->id)
            ->where('paper_type_id', '!=', TEST_SPEAKING);

        return $query->get();
        
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function onlineTests()
    {
        $query = Paper::query()->with(['type', 'report', 'task'])->where(['papers.task_id' => $this->id]);

        if (!empty($this->speakingTest(false))) {
            $query = $query->where('papers.id', '<>', $this->speakingTest(false)->id);
        } elseif (!empty($this->speakingTest(true))) {
            $query = $query->where('papers.id', '<>', $this->speakingTest(true)->id);
        }

        return $query->get();
    }

    /**
     * @return mixed
     */
    public function getGlobalLevelAttribute()
    {
        return $this->completedTests()->pluck('report.ability')->avg();
    }

    /**
     * @return mixed|null
     */
    public function getTrainingNeedsAttribute()
    {
        return empty($this->global_level) ? null : $this->global_level + 1;
    }


    /**
     * @return bool
     */
    public function canRefuse()
    {
        if ($this->assessor_id == \Auth::user()->id) {

            $totalNative = count(array_filter($this->language->groups[0]->userGroups->toArray(), function($elem){
                return $elem['native'] == 1;
            }));

            $totalNonNative = $this->language->groups[0]->userGroups->count() - $totalNative;

            if ($this->language->groups[0]->userGroups->count() < 2) {
                return false;
            }

            if ($this->native == 1 && $totalNative <= 1) {
                return false;
            }

            if ($this->native == 0 && $totalNonNative <= 1) {
                return false;
            }

            foreach ($this->papers as $paper) {
                if ($paper->type->id == TEST_WRITING || $paper->type->id == TEST_SPEAKING) {
                    if (empty($paper->report)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

}