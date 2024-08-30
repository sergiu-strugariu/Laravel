<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model 
{
    const STATUS_ALLOCATED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_DONE = 3;
    const STATUS_ISSUE = 4;
    const STATUS_CANCELED = 5;
    const STATUS_ARCHIVED = 6;

    const STATUSES = [
        self::STATUS_ALLOCATED => 'Allocated',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_DONE => 'Done',
        self::STATUS_ISSUE => 'Issue',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_ARCHIVED => 'Archived'
    ];

    protected $table = 'task_statuses';
    public $timestamps = true;

    protected $fillable = [
        'name', 'color'
    ];

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function papers()
    {
        return $this->hasMany('App\Models\Papers');
    }

}