<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessorInactivity extends Model
{

    protected $table = 'assessors_inactivity';
    public $timestamps = true;

    protected $fillable = ['user_id', 'date_from', 'date_to'];


    public function assessor()
    {
        return $this->belongsTo('App\Models\User');
    }

}