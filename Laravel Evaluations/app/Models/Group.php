<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model 
{

    protected $table = 'groups';
    public $timestamps = true;


    protected $fillable = [
        'language_id'
    ];

    public function userGroups()
    {
        return $this->hasMany('App\Models\UserGroup');
    }

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

}