<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{

    protected $table = 'attachments';
    public $timestamps = true;


    protected $fillable = [
        'filepath',
        'filename',
        'filetype',
        'url',
        'model_id',
        'model',
        'model'
    ];

}