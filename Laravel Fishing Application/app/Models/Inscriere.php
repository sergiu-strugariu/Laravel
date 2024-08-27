<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscriere extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'created_by',
        'pescar_id',
        'concurs_id',
        'mansa_id',
        'stand_id',
        'lac_id',
        'sector_id',
        'puncte_penalizare',
        'nume_trofeu',
    ];

    public function pescari(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pescar_id');
    }

    public function concursuri(): BelongsTo
    {
        return $this->belongsTo(Concurs::class, 'concurs_id');
    }

    public function manse(): BelongsTo
    {
        return $this->belongsTo(Mansa::class, 'mansa_id');
    }

    public function standuri(): BelongsTo
    {
        return $this->belongsTo(Stand::class, 'stand_id');
    }

    public function lacuri(): BelongsTo
    {
        return $this->belongsTo(Lac::class, 'lac_id');
    }

    public function sectoare(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pescarNume()
    {
        return $this->belongsTo(User::class, 'pescar_id');
    }

    public function concursNume()
    {
        return $this->belongsTo(Concurs::class, 'concurs_id');
    }

    public function mansaNume()
    {
        return $this->belongsTo(Mansa::class, 'mansa_id');
    }

    public function standNume()
    {
        return $this->belongsTo(Stand::class, 'stand_id');
    }

    public function lacNume()
    {
        return $this->belongsTo(Lac::class, 'lac_id');
    }

    public function sectorNume()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }
}
