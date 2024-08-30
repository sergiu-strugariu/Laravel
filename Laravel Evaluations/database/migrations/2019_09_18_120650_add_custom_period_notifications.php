<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomPeriodNotifications extends Migration
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
<div>Language:&nbsp;{language}

</div><div>Test:&nbsp;{test_type}

</div><div><br></div><div>Name:

{name}<br></div><div>Phone number: {phone}<br></div><div>Email address: {user_email}</div><div>

Client Company: {company}

<br></div>
<div>Availability (Custom period):&nbsp;{availability}</div>
<div><br></div>
<div>Please contact the candidate at exactly this time and day.</div>
<div><br></div>
<div>Thank you!</div><div><br></div>
EOT;

        $text[] = <<<EOT
<div>Limba: {language}

</div><div>Test:&nbsp;{test_type}

</div><div><br></div><div>Nume:&nbsp;{name}<br></div><div>Număr telefon: {phone}<br></div><div>Adresă email: {user_email}</div><div>Compania Client: {company}<br></div><div>

Disponibilitate (Custom Period): {availability}

<br></div>
<div><br></div>
<div>Te rugăm să contactezi candidatul la exact această zi și exact această oră. </div>
<div><br></div>
<div>Mulțumim!</div><div><br></div>
EOT;

        $text[] = <<<EOT
<div>Hello,</div>
<div><br></div>
<div>{language}<br></div>
<div>The client has added a new test for task: {task_id} with Custom Period: {custom_period}. Please check the task.<br></div>
<div><br></div>
<div>Thank you!</div>
<div><br></div>
EOT;

        DB::table('mail_templates')->insert(
            array(
                array(
                    'id' => 35,
                    'name' => 'New task with custom period assigned to assessor',
                    'slug' => 'assessor-assigned-custom-period',
                    'subject' => '[EUCOM] New {language} language {test_type} assessment - Custom Period: {name}',
                    'body_en' => $text[0],
                    'body_ro' => $text[1],
                ),
                array(
                    'id' => 36,
                    'name' => 'Reminder to update task',
                    'slug' => 'remind-task-update-custom',
                    'subject' => '[EUCOM] Custom Period - Reminder for {name}',
                    'body_en' => $text[2],
                    'body_ro' => $text[2],
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
        DB::table('settings')
            ->whereIn('id', [35, 36])
            ->delete();
    }
}
