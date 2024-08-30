<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateNotificationInactive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $text = [];

        $text[] = <<<EOT
<div></div>Hello,
<div><br></div>
<div>A client has added an update to a task that has an inactive assessor, please check the task.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;


        DB::table('mail_templates')->insert(
            array(
                array(
                    'id' => 34,
                    'name' => 'Assessor is inactive',
                    'slug' => 'assessor-is-inactive',
                    'subject' => '[EUCOM] A client has updated a task with an inactive assessor',
                    'body_en' => $text[0],
                    'body_ro' => $text[0],
                ),
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
        DB::table('mail_templates')
            ->whereIn('id', [34])
            ->delete();
    }
}
