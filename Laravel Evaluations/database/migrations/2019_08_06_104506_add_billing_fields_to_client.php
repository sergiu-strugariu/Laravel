<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillingFieldsToClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('billing_contract_date')->after('name');
            $table->string('billing_contract_no')->after('name');
            $table->string('billing_capital')->after('name');
            $table->string('billing_bank')->after('name');
            $table->string('billing_iban')->after('name');
            $table->string('billing_address')->after('name');
            $table->string('billing_cif')->after('name');
            $table->string('billing_registry')->after('name');
            $table->string('billing_company_name')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('billing_company_name');
            $table->dropColumn('billing_registry');
            $table->dropColumn('billing_cif');
            $table->dropColumn('billing_address');
            $table->dropColumn('billing_iban');
            $table->dropColumn('billing_bank');
            $table->dropColumn('billing_capital');
            $table->dropColumn('billing_contract_no');
            $table->dropColumn('billing_contract_date');
        });
    }
}
