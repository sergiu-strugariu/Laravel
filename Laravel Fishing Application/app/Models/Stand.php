<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stand extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'lac_id',
        'nume',
        'created_by',
    ];

    
    public function lac()
    {
        return $this->belongsTo(Lac::class, 'lac_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
