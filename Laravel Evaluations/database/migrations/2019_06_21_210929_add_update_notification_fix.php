<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateNotificationFix extends Migration
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
<div>{language}</div>
<div>The client has added a new test for task: {task_id} but the allocated assessor is inactive at the moment. Please check the task.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;


        DB::table('mail_templates')
            ->where("id", 34)
            ->update(array(
                'body_en' => $text[0],
                'body_ro' => $text[0],
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
        //
    }
}
