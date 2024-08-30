<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionChoice extends Model 
{

    use SoftDeletes;

    protected $table = 'question_choices';
    public $timestamps = true;

    protected $fillable = [
        'answer', 'correct', 'question_id', 'deleted_at'
    ];

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function paperAnswers()
    {
        return $this->hasMany('App\Models\PaperAnswers', 'answer_id');
    }

}