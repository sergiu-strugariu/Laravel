<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenuePerDay extends Model
{

    protected $table = 'revenue_per_day';
    public $timestamps = true;

    protected $fillable = [
        'project_id',
        'day',
        'revenue',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }
}