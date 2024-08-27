<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Database\Factories\ConcursFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concurs extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'nume',
        'created_by',
        'organizator_id',
        'organizator_nume',
        'descriere',
        'regulament',
        'poza',
        'start',
        'stop',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizator_id');
    }

    public function manse(): HasMany
    {
        return $this->hasMany(Mansa::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ConcursFactory::new();
    }
}
