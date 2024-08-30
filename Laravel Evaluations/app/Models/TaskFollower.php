<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFollower extends Model 
{

    protected $table = 'task_followers';
    public $timestamps = true;

    protected $fillable = [
        'task_id', 'user_id'
    ];

    public function task()
    {
        return $this->belongsTo('App\Models\Task');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}