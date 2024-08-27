<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlocareStand extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'created_by',
        'stand_id',
        'pescar_id',
        'sector_id',
        'concurs_id',
        'lac_id',
    ];

    public function pescar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pescar_id');
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class, 'stand_id');
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function concurs(): BelongsTo
    {
        return $this->belongsTo(Concurs::class, 'concurs_id');
    }

    public function lac(): BelongsTo
    {
        return $this->belongsTo(Lac::class, 'lac_id');
    }
}
