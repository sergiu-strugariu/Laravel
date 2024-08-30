<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportIdPaperAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paper_answers', function (Blueprint $table) {
            $table->integer('report_id')->unsigned()->nullable()->after('paper_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paper_answers', function (Blueprint $table) {
            $table->dropColumn('report_id');
        });
    }
}
