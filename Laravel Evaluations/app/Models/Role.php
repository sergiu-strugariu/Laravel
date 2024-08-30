<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Role extends \Yajra\Acl\Models\Role
{
    CONST ROLE_MASTER = 1;
    CONST ROLE_ADMINISTRATOR = 2;
    CONST ROLE_RECRUITER = 3;
    CONST ROLE_CSS = 4;
    CONST ROLE_CLIENT = 5;
    CONST ROLE_TDS = 6;
    CONST ROLE_ASSESSOR = 7;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';
    public $timestamps = true;

    /**
     * Fillable fields.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'system'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taskUpdates()
    {
        return $this->belongsToMany('App\Models\TaskUpdate');
    }
}
