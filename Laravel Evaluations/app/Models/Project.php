<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{

    use SoftDeletes;

    const PROJECT_TYPE_AUDIT = 1;
    const PROJECT_TYPE_COURSES = 2;
    const PROJECT_TYPE_RECRUITING = 3;

    protected $table = 'projects';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'user_id',
        'client_id',
        'project_type_id',
        'default_bill_client',
        'default_pay_assessor',
        'billing_contract_annex_date',
        'billing_contract_annex',
        'billing_contract_date',
        'billing_contract_no',
        'billing_capital',
        'billing_bank',
        'billing_iban',
        'billing_address',
        'billing_cif',
        'billing_registry',
        'billing_company_name',
        'billing_distinct',
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\ProjectTypes', 'project_type_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function participants()
    {
        return $this->hasMany('App\Models\ProjectParticipant');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function assessors()
    {
        return Task::query()->with(['project', 'assessor'])->where(['project_id' => $this->id])->has('assessor')->get()->pluck('assessor');
    }

}