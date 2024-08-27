<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'created_by',
        'nume',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
