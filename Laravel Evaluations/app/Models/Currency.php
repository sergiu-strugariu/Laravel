<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/28/2017
 * Time: 4:40 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Currency extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'currencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cur_from', 'cur_to', 'rate'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];


}