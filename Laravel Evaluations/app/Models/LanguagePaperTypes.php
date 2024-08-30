<?php
/**
 * Created by PhpStorm.
 * User: AlexBadea
 * Date: 21.12.2017
 * Time: 14:36
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LanguagePaperTypes extends Model
{
    protected $table = 'language_paper_type';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [
        'language_id',
        'paper_type_id',
    ];

    public function language()
    {
        return $this->belongsTo('App\Models\Language', 'language_id');
    }

    public function paperTypes()
    {
        return $this->belongsTo('App\Models\PaperType', 'paper_type_id');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question', 'language_paper_type_id');
    }
}