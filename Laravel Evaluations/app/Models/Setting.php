<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/12/2018
 * Time: 9:53 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    protected $table = 'settings';
    public $timestamps = true;

    protected $fillable = [
        'key',
        'value',
        'description'
    ];
}
