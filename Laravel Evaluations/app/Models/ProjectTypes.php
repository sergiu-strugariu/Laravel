<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTypes extends Model
{

    protected $table = 'project_types';
    public $timestamps = true;

    protected $fillable = ['name'];

    public function projects()
    {
        return $this->hasMany('App\Models\Project');
    }

}