<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToMailSeeder extends Migration
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
<div>We apologise, the {grade} result you have previously received for {name} - {language} {test_type} was incorrect.
    The assessor is currently filling in the correct report and you will receive a new email with the correct result
    shortly.
</div>
<div><br></div>
<div>Many thanks,<br></div>
EOT;

        $text_ro = <<<EOT
<div>Bună ziua,</div>
<div><br></div>
<div>Ne cerem scuze, dar rezultatul de nivel {grade} pe care l-ați primit anterior pentru {name} - {language}
    {test_type} a fost incorect. Evaluatorul va completa un nou raport cu rezultatul corect și veți primi un nou email
    cu raportul corectat în scurt timp.
</div>
<div><br></div>
<div>Va mulțumim!<br></div>
<br>
EOT;


        DB::table('mail_templates')->insert(
            array(
                'id' => 25,
                'name' => 'Reset Report',
                'slug' => 'reset-report',
                'subject' => 'Reset Report',
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
        DB::table('mail_templates')->delete('25');
    }
}
