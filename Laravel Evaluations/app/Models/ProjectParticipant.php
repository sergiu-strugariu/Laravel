<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectParticipant extends Model
{

    protected $table = 'project_participants';
    public $timestamps = true;

    protected $fillable = ['user_id', 'project_id'];

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
