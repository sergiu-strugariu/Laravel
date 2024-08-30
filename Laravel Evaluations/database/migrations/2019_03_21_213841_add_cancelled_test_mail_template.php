<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelledTestMailTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $text = <<<EOT
<div></div>Hello,
<div><br></div>
<div>The {test_type} assessment for {name} is no longer required.</div>
<div>Please do not carry out the {test_type} assessment.</div>
<div>Other tests may still be active for {name}. Please check the task page.</div>
<div><br></div>
<div>Thank you!<br></div>
EOT;

        $text_ro = <<<EOT
<div>Hello,</div>
<div><br></div>
<div>Evaluarea de {test_type} pentru candidatul {name} nu mai este necesară.</div>
<div>Te rugăm să nu mai faci evaluarea de {test_type}.</div>
<div>Este posibil ca alte tipuri de teste sa fie încă active. Te rugăm să verifici pagina de task.</div> 
<div><br></div>
<div>Va mulțumim!<br></div>
<br>
EOT;


        DB::table('mail_templates')->insert(
            array(
                'id' => 26,
                'name' => 'Cancelled Test',
                'slug' => 'cancelled-test',
                'subject' => 'Cancelled Test',
                'body_en' => $text,
                'body_ro' => $text_ro,
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
