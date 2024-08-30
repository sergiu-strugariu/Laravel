<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/24/2017
 * Time: 9:10 AM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssessorHistory extends Model
{

    protected $table = 'task_assessors_history';
    public $timestamps = true;

    protected $fillable = [
        'assessor_id',
        'task_id',
        'reason'
    ];

    public function task()
    {
        return $this->belongsTo('App\Models\Task');
    }

}