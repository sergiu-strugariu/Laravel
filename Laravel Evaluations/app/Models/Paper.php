<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{

    protected $table = 'papers';
    public $timestamps = true;

    protected $fillable = [
        'paper_type_id',
        'task_id',
        'status_id',
        'done',
        'started_at',
        'ended_at',
        'current_question_id',
        'current_choices',
        'current_audio_time',
        'question_current_time',
        'cost',
        'invoice_id'
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question');
    }

    public function task()
    {
        return $this->belongsTo('App\Models\Task')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function type()
    {
        return $this->belongsTo('App\Models\PaperType', 'paper_type_id');
    }

    public function report()
    {
        return $this->hasOne('App\Models\PaperReport');
    }


    public function paper_answers(){
        return $this->hasMany('App\Models\PaperAnswers', 'paper_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\TaskStatus', 'status_id');
    }
    
    public function getElapsedTime()
    {
        $started_at = Carbon::parse($this->started_at);
        $ended_at = Carbon::parse($this->ended_at);

        return $ended_at->diff($started_at)->format('%i minute(s) and %s second(s)');
    }

}