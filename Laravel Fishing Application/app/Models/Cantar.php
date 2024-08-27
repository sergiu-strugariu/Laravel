<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cantar extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'stand_id',
        'concurs_id',
        "created_by",
        'lac_id',
        'cantitate',
    ];


    public function lac()
    {
        return $this->belongsTo(Lac::class, 'lac_id');
    }

    public function concurs()
    {
        return $this->belongsTo(Concurs::class, 'concurs_id');
    }

    public function stand()
    {
        return $this->belongsTo(Stand::class, 'stand_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
