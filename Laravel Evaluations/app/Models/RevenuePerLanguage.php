<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenuePerLanguage extends Model
{

    protected $table = 'revenue_per_languages';
    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'language_id',
        'revenue',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

}