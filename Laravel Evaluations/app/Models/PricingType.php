<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PricingType extends Model
{
    protected $table = 'pricing_type';

    CONST WRITING_NATIVE = 1;
    CONST WRITING = 2;
    CONST SPEAKING_NATIVE = 3;
    CONST SPEAKING = 4;
    CONST READING = 5;
    CONST LANGUAGE_USE = 6;
    CONST LANGUAGE_USE_NEW = 7;
    CONST LISTENING = 8;
    CONST CUSTOM_PERIOD_SPEAKING = 9;

    public $timestamps = true;

    protected $fillable = [
        'name',
    ];
}