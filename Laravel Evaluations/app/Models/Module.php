<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model 
{

    protected $table = 'modules';
    public $timestamps = true;

    public function permissions()
    {
        return $this->hasMany('App\Models\Permission');
    }

}