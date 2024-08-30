<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/19/2017
 * Time: 2:33 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    protected $table = 'logs';
    public $timestamps = true;


    protected $fillable = [
        'user_id',
        'task_id',
        'type',
        'description',
        'task_log_type',
        'task_schedule_from',
        'task_schedule_to',
    ];

    public function task()
    {
        return $this->belongsTo('App\Models\Task', 'task_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

}