<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCostColumnToPaper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paper_types', function($table) {
            $table->float('cost')->after("name");
        });
        DB::table('paper_types')->update(["cost" => 10.00]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paper_types', function($table) {
            $table->dropColumn('cost');
        });
    }
}
