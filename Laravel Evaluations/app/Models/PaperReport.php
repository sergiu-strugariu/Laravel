<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/5/2018
 * Time: 4:12 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperReport extends Model
{
    use SoftDeletes;

    protected $table = 'paper_report';
    public $timestamps = true;

    protected $fillable = [
        'paper_id', 'assessor_id', 'grade', 'ability', 'assessments', 'algorithm'
    ];

    public function paper()
    {
        return $this->belongsTo('App\Models\Paper');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}