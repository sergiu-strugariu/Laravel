<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/13/2017
 * Time: 12:06 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $table = 'clients';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'billing_contract_date',
        'billing_contract_no',
        'billing_capital',
        'billing_bank',
        'billing_iban',
        'billing_address',
        'billing_cif',
        'billing_registry',
        'billing_company_name',
        'billing_hidden',
    ];

    public function projects()
    {
        return $this->hasMany('App\Models\Project', 'client_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'client_id');
    }

}