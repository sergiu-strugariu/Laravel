<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLanguageAuditManagerSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')
            ->where(array(
                "key" => "language_audit_manager_email"
            ))
            ->update(
                array(
                    "value" => "florina.potirniche@eucom.ro"
                )
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->where(array(
                "key" => "language_audit_manager_email"
            ))
            ->update(
                array(
                    "value" => "mariana.nistor@eucom.ro"
                )
            );
    }
}
