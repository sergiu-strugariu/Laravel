<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model 
{

    protected $table = 'user_statuses';
    public $timestamps = true;

    public function users()
    {
        return $this->hasMany('App\Models\User', 'status_id');
    }

}