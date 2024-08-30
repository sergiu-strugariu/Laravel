<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillingFieldsToProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('billing_contract_annex_date')->after('default_pay_assessor');
            $table->string('billing_contract_annex')->after('default_pay_assessor');
            $table->string('billing_contract_date')->after('default_pay_assessor')->nullable();
            $table->string('billing_contract_no')->after('default_pay_assessor')->nullable();
            $table->string('billing_capital')->after('default_pay_assessor')->nullable();
            $table->string('billing_bank')->after('default_pay_assessor')->nullable();
            $table->string('billing_iban')->after('default_pay_assessor')->nullable();
            $table->string('billing_address')->after('default_pay_assessor')->nullable();
            $table->string('billing_cif')->after('default_pay_assessor')->nullable();
            $table->string('billing_registry')->after('default_pay_assessor')->nullable();
            $table->string('billing_company_name')->after('default_pay_assessor')->nullable();
            $table->boolean('billing_distinct')->after('default_pay_assessor')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('billing_contract_annex_date');
            $table->dropColumn('billing_contract_annex');
            $table->dropColumn('billing_contract_date');
            $table->dropColumn('billing_contract_no');
            $table->dropColumn('billing_capital');
            $table->dropColumn('billing_bank');
            $table->dropColumn('billing_iban');
            $table->dropColumn('billing_address');
            $table->dropColumn('billing_cif');
            $table->dropColumn('billing_registry');
            $table->dropColumn('billing_company_name');
            $table->dropColumn('billing_distinct');
        });
    }
}
