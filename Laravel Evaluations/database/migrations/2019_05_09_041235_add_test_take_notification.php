<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestTakeNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $text_en = <<<EOT
<div></div><h1>Dear {name},</h1>You are invited to carry out the <b>{language}</b> language assessment:<br>#speaking<br>
<b>Speaking</b>: You will soon be contacted by one of our colleagues for a 10-15 minute conversation.
<br>In order to make sure the results are as fair as possible, it is
<b>essential </b>to make sure that during the conversation you are in a quiet place, without any distractions, your phone has enough battery and the reception is very good, so that the call does not get interrupted.
<br>speaking#<br><br>#online<br><b>Online
    test</b>: We also invite you to take the following online tests before the deadline: {deadline}<br>online#<br>
<br>#writing<br>For the writing test please access this link:<br>{writing_link}<br>writing#<br><br>#language_use
<br>For the language use test please access this link:<br>{language_use_link}<br>language_use#<br><br>#reading
<br>For the reading test please access this link:<br>{reading_link}<br>reading#<br><br>#listening
<br>For the listening test please access this link:<br>{listening_link}<br>listening#<br><br>Thank you,
<br>Eucom, on behalf of {company}
<div><br></div>
EOT;

        $text_ro = <<<EOT
<div></div><h1>Dragă {name},</h1>Te invităm să parcurgi evaluarea de limba <b>{language}</b>:<br>#speaking<br><b>Speaking</b>: Te va contacta în curând unul din colegii nostri pentru o discuție de 10-15 minute.
<br>Pentru rezultate cât mai corecte, este
<b>esențial </b>să te asiguri că pe durata discuției te afli într-un loc liniștit, fără distracții exterioare, că telefonul are suficientă baterie și semnalul este foarte bun, astfel încât apelul să nu se întrerupă.
<br>speaking#<br><br>#online
<br>Test online: Te invităm să parcurgi și următoarele teste online până la data limită: {deadline}<br>online#<br>
<br>#writing<br>Pentru testul de abilități scrise te rugăm să apeși pe link-ul de mai jos:<br>{writing_link}<br>writing#
<br><br>#language_use<br>Pentru testul de utilizare a limbii te rugăm să apeși pe link-ul de mai jos:
<br>{language_use_link}<br>language_use#<br><br>#reading
<br>Pentru testul de înțelegere a textelor scrise te rugăm să apeși pe link-ul de mai jos:<br>{reading_link}<br>reading#
<br><br>#listening<br>Pentru testul de înțelegere după auz te rugăm să apeși pe link-ul de mai jos:<br>{listening_link}
<br>listening#<br><br>Mulțumim,<br>Eucom, în numele companiei {company}
<div><br></div>
EOT;


        DB::table('mail_templates')->insert(
            array(
                array(
                    'id' => 33,
                    'name' => 'Invitation to take test [Multiple]',
                    'slug' => 'take-test-multiple',
                    'subject' => '[{company}] {language} language assessment',
                    'body_en' => $text_en,
                    'body_ro' => $text_ro,
                )
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
            ->whereIn('id', [33])
            ->delete();
    }
}
