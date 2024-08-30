<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{

    protected $table = 'prices';
    public $timestamps = true;

    protected $fillable = [
        'language_id',
        'pricing_type_id',
        'client_id',
        'project_id',
        'level',
        'price',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pricingType()
    {
        return $this->belongsTo('App\Models\PricingType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

}