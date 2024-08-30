<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Model 
{
    use SoftDeletes;

    protected $table = 'user_groups';
    public $timestamps = true;

    protected $fillable = [
        'group_id', 'user_id', 'native'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

}