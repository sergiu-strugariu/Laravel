<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model 
{

    protected $table = 'role_user';
    public $timestamps = true;

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

}