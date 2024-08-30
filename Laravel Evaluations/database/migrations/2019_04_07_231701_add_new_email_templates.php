<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewEmailTemplates extends Migration
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
<div>Candidate was called and said he/she would call back later.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;

        $text[] = <<<EOT
<div></div>Hello,
<div><br></div>
<div>Candidate did not pass the Identity validation step.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;

        $text[] = <<<EOT
<div></div>Hello,
<div><br></div>
<div>Candidate said he had issues during online test, asked to take it again.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;

        $text[] = <<<EOT
<div></div>Hello,
<div><br></div>
<div>Candidate was called several times but line was engaged each time. SMS text sent.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;


        DB::table('mail_templates')->insert(
            array(
                array(
                    'id' => 27,
                    'name' => 'Candidate was called',
                    'slug' => 'candidate-cancelled-call-back',
                    'subject' => '[EUCOM] Candidate was called',
                    'body_en' => $text[0],
                    'body_ro' => $text[0],
                ),
                array(
                    'id' => 28,
                    'name' => 'Candidate did not pass the Identity validation step',
                    'slug' => 'candidate-client-not-validated',
                    'subject' => '[EUCOM] Candidate did not pass the Identity validation step',
                    'body_en' => $text[1],
                    'body_ro' => $text[1],
                ),
                array(
                    'id' => 29,
                    'name' => 'Candidate said he had issues during online test, asked to take it again',
                    'slug' => 'candidate-issues-during-test',
                    'subject' => '[EUCOM] Candidate said he had issues during online test, asked to take it again',
                    'body_en' => $text[2],
                    'body_ro' => $text[2],
                ),
                array(
                    'id' => 30,
                    'name' => 'Candidate was called several times but line was engaged each time. SMS text sent.',
                    'slug' => 'candidate-busy-sms-sent',
                    'subject' => '[EUCOM] Candidate was called several times',
                    'body_en' => $text[3],
                    'body_ro' => $text[3],
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
            ->whereIn('id', [27, 28, 29, 30])
            ->delete();
    }
}
