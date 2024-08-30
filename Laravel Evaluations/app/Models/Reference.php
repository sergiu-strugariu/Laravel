<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{

    protected $table = 'references';
    public $timestamps = true;

    protected $fillable = [
        'category',
        'level',
        'description',
    ];

}