<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model 
{

    protected $fillable = [
        'name',
    ];

    protected $table = 'languages';
    public $timestamps = true;

    public function papers()
    {
        return $this->hasMany('App\Models\Paper');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Group');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }
    
    public function language_paper_type()
    {
        return $this->hasMany('App\Models\LanguagePaperTypes');
    }

}