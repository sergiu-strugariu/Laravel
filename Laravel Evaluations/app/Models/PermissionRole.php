<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model 
{

    protected $table = 'permission_role';
    public $timestamps = true;

    public function Role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function Permission()
    {
        return $this->belongsTo('App\Models\Permission');
    }

}