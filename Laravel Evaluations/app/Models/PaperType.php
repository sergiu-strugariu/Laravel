<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperType extends Model 
{

    protected $table = 'paper_types';
    public $timestamps = true;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'cost'
    ];

    public function papers()
    {
        return $this->hasMany('App\Models\Paper');
    }

    public function languagePaperType()
    {
        return $this->hasOne('App\Models\LanguagePaperTypes', 'paper_type_id');
    }

}