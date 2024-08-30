<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionLevel extends Model 
{

    protected $table = 'question_levels';
    public $timestamps = true;

    public function questions()
    {
        return $this->hasMany('App\Models\Question');
    }

}