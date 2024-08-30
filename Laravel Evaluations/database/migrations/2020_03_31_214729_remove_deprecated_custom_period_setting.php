<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDeprecatedCustomPeriodSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')
            ->whereIn('id', [20])
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->insert(
            array(
                array(
                    'id' => 20,
                    'key' => 'custom_period_cost',
                    'value' => "15",
                    'description' => 'The cost of the "Custom Period" option',
                ),
            )
        );
    }
}
