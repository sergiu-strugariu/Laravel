<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mansa extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'created_by',
        'concurs_id',
        'lac_id',
        'nume',
        'start_mansa',
        'stop_mansa',
        'status_mansa',
        'participanti',
        'participanti_max',
    ];

    public function concurs(): BelongsTo
    {
        return $this->belongsTo(Concurs::class, 'concurs_id');
    }

    public function lac(): BelongsTo
    {
        return $this->belongsTo(Lac::class, 'lac_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
