<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskUpdateNotifications extends Migration
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
<div>Please contact candidate via Whatsapp.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;

        $text[] = <<<EOT
<div></div>Hello,
<div><br></div>
<div>Please contact candidate via Skype.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;


        DB::table('mail_templates')->insert(
            array(
                array(
                    'id' => 31,
                    'name' => 'Please contact candidate via Whatsapp',
                    'slug' => 'contact-via-whatsapp',
                    'subject' => '[EUCOM] Please contact candidate via Whatsapp',
                    'body_en' => $text[0],
                    'body_ro' => $text[0],
                ),
                array(
                    'id' => 32,
                    'name' => 'Please contact candidate via Skype',
                    'slug' => 'contact-via-skype',
                    'subject' => '[EUCOM] Please contact candidate via Skype',
                    'body_en' => $text[1],
                    'body_ro' => $text[1],
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
            ->whereIn('id', [31, 33])
            ->delete();
    }
}
