<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Question extends Model
{

    use SoftDeletes;

    protected $table = 'questions';
    public $timestamps = true;


    protected $fillable = [
        'body',
        'body_incorrect',
        'description',
        'max_words',
        'language_paper_type_id',
        'time',
        'question_level_id',
        'q_type',
        'v_number',
        'code',
        'audio_file_path',
        'language_use_type'
    ];

    /**
     *  Extra attributes for question model
     *
     * @var array
     */
    protected $appends = [
        'minutes',
        'seconds'
    ];

    protected static $levels = [
        1 => 'A1',
        2 => 'A2',
        3 => 'B1',
        4 => 'B2',
        5 => 'C1',
        6 => 'C2',
    ];


    public static function boot()
    {
        parent::boot();

        ### set code
        self::created(function ($model) {
            if (!is_null($model->question_level_id)) {
                $row = Question::select(DB::raw('MAX(v_number) as max_v_number'))
                    ->withTrashed()
                    ->where('language_paper_type_id', $model->language_paper_type_id)
                    ->where('question_level_id', $model->question_level_id)
                    ->where('q_type', $model->q_type)
                    ->groupBy('question_level_id')
                    ->groupBy('q_type')
                    ->first();
                $model->v_number = $row->max_v_number ? $row->max_v_number + 1 : 1;
                $model->code = Question::$levels[$model->question_level_id] . ' Q' . $model->q_type . ' V' . $model->v_number;
                $model::withTrashed()->find($model->id)->update(['v_number' => $model->v_number, 'code' => $model->code]);
            } else {

                ### language use index generate 
                $paperType = $model->languagePaperTypes->paperTypes->id;
                if ($paperType == TEST_LANGUAGE_USE) {

                    $row = Question::select(DB::raw('MAX(v_number) as max_v_number'))
                        ->withTrashed()
                        ->where('language_paper_type_id', $model->language_paper_type_id)
                        ->groupBy('question_level_id')
                        ->groupBy('q_type')
                        ->first();

                    $model->v_number = $row->max_v_number ? $row->max_v_number + 1 : 1;
                    $model::withTrashed()->find($model->id)->update(['v_number' => $model->v_number]);

                }
            }
        });

        ### soft delete to set status inactive
        self::creating(function ($question) {
            $question->deleted_at = Carbon::now();
        });

        ### update code
        self::updating(function ($model) {
            if ($model->question_level_id != "") {
                $model->code = Question::$levels[$model->question_level_id] . ' Q' . $model->q_type . ' V' . $model->v_number;
            }
        });

    }

    public function languagePaperTypes()
    {
        return $this->belongsTo('App\Models\LanguagePaperTypes', 'language_paper_type_id')->withTrashed();
    }

    public function level()
    {
        return $this->belongsTo('App\Models\QuestionLevel', 'question_level_id');
    }

    public function questionChoices()
    {
        return $this->hasMany('App\Models\QuestionChoice');
    }

    public function paperAnswers()
    {
        return $this->hasMany('App\Models\PaperAnswers');
    }

    public function getMinutesAttribute()
    {
        return (int)floor($this->time / 60);
    }

    public function getSecondsAttribute()
    {
        return (int)($this->time - floor($this->time / 60) * 60);
    }

}