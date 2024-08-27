<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lac extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'nume',
        'created_by'
    ];

    public function stands(): hasMany {
        return $this->hasMany(Stand::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
