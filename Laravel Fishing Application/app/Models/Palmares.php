<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Palmares extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'assigned_to',
        'luna',
        'an',
        'organizator',
        'pescar',
        'data_concurs',
        'lac',
        'nume_concurs',
        'mansa',
        'stand',
        'cantitate',
        'puncte',
        'loc_sector',
        'loc_general',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
