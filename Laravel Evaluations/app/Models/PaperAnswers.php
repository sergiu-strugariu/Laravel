<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperAnswers extends Model 
{

    use SoftDeletes;

    protected $table = 'paper_answers';

    protected $fillable =['question_id', 'answer_id', 'task_id', 'paper_id', 'user_answer', 'time', 'choices', 'observations' ];
    public $timestamps = true;

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function answer()
    {
        return $this->belongsTo('App\Models\QuestionChoice', 'answer_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function task()
    {
        return $this->belongsTo('App\Models\Task');
    }

    public function report()
    {
        return $this->belongsTo('App\Models\PaperReport', 'paper_id', 'paper_id');
    }
}