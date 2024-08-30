<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Permission extends \Yajra\Acl\Models\Permission
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = ['name', 'slug', 'module_id', 'resource', 'system'];

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }
}
