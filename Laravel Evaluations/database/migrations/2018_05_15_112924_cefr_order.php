<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CefrOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("update `references` set `order` = 1 where level = 'A1'");
        \DB::statement("update `references` set `order` = 2 where level = 'A2'");
        \DB::statement("update `references` set `order` = 3 where level = 'A2+'");
        \DB::statement("update `references` set `order` = 4 where level = 'B1'");
        \DB::statement("update `references` set `order` = 5 where level = 'B1+'");
        \DB::statement("update `references` set `order` = 6 where level = 'B2'");
        \DB::statement("update `references` set `order` = 7 where level = 'B2+'");
        \DB::statement("update `references` set `order` = 8 where level = 'C1'");
        \DB::statement("update `references` set `order` = 9 where level = 'C2'");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
