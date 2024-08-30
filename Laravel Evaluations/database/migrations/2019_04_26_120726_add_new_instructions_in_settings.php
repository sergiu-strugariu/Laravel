<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewInstructionsInSettings extends Migration
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
<h1>Welcome, {name}!</h1>
<br>
<p>We love languages, and we're pretty sure you love them too! We hope you'll have fun taking this short language use test!</p>
<p>Before you start, please read the instructions below.</p>
<u>Language use test</u>
<ol>
    <li>You will see the timer in the top right corner.</li>
    <li>The time limit is 30 minutes. </li>
    <li>You will not be able to pause once you start.</li>
    <li>You will not be able to save and finish later.</li>
    <li>You will not be able to go back and change your answers.</li>
    <li>You will see each item only once.</li>
    <li>You can leave an item unanswered, but you will not be able to see it again.</li>
</ol>
<p></p>
<p><br></p>
<p></p>
EOT;

        $text[] = <<<EOT
<h1>Welcome, {name}!</h1>
<br>
<p>We love languages, and we're pretty sure you love them too! This is why we invite you to take this short writing test!</p>
<p>Before you start, please read the instructions below.</p>
<u>Writing test</u>
<ol>
    <li>Each task has a specific time and word limit.</li>
    <li>You will see the timer in the top right corner.</li>
    <li>You will see the number of words still available as you type.</li>
    <li>You will not be able to pause once you start.</li>
    <li>You will not be able to save and finish later.</li>
    <li>Once you finish, click on “Submit”. </li>
</ol>
<p></p>
<p><br></p>
<p></p>
EOT;

        $text[] = <<<EOT
<h1>Welcome, {name}!</h1>
<br>
<p>We love languages, and we're pretty sure you love them too! We hope you'll have fun taking this reading test!</p>
<p>Before you start, please read the instructions below.</p>
<u>Reading test</u>
<ol>
    <li>You will see the timer in the top right corner.</li>
    <li>The time limit is XX minutes.</li>
    <li>You will not be able to pause once you start.</li>
    <li>You will not be able to save and finish later.</li>
    <li>You will not be able to go back and change your answers.</li>
    <li>You will see each item only once.</li>
    <li>You can leave an item unanswered, but you will not be able to see it again.</li>
</ol>
<p></p>
<p><br></p>
<p></p>
EOT;

        $text[] = <<<EOT
<h1>Welcome, {name}!</h1>
<br>
<p>We love languages, and we're pretty sure you love them too! We hope you'll have fun taking this language use test!</p>
<p>Before you start, please read the instructions below.</p>
<u>Language use (new) test</u>
<ol>
    <li>Before you start the test you will see three sample questions.</li> 
    <li>The answers to the sample questions are not added to your final score.</li>
    <li>Once you start the test, you will see the timer in the top right corner.</li>
    <li>The time limit is XX minutes. </li>
    <li>You will not be able to pause once you start.</li>
    <li>You will not be able to save and finish later.</li>
    <li>You will not be able to go back and change your answers.</li>
    <li>You will see each item only once.</li>
    <li>You can leave an item unanswered, but you will not be able to see it again.</li>
</ol>
<p></p>
<p><br></p>
<p></p>
EOT;

        $text[] = <<<EOT
<h1>Welcome, {name}!</h1>
<br>
<p>We love languages, and we're pretty sure you love them too! We hope you'll have fun taking this reading test!</p>
<p>Before you start, please read the instructions below.</p>
<u>Listening test</u>
<ol>
    <li>Before you start the test you will have to test your speakers.</li> 
    <li>Make sure the volume is right and that you are in a silent environment.</li>
    <li>You can listen to the recording twice.</li>
    <li>You will see the timer in the top right corner.</li>
    <li>The time limit is XX minutes.</li>
    <li>You will not be able to pause once you start.</li>
    <li>You will not be able to save and finish later.</li>
    <li>You will not be able to go back and change your answers.</li>
    <li>You will see each item only once.</li>
    <li>You can leave an item unanswered, but you will not be able to see it again.</li>
    <li>Once you finish, click on “Submit”.</li>
</ol>
<p></p>
<p><br></p>
<p></p>
EOT;


        DB::table('settings')->insert(
            array(
                array(
                    'id' => 15,
                    'key' => 'instructions_language_use',
                    'value' => $text[0],
                    'description' => '',
                ),
                array(
                    'id' => 16,
                    'key' => 'instructions_writing',
                    'value' => $text[1],
                    'description' => '',
                ),
                array(
                    'id' => 17,
                    'key' => 'instructions_reading',
                    'value' => $text[2],
                    'description' => '',
                ),
                array(
                    'id' => 18,
                    'key' => 'instructions_language_use_new',
                    'value' => $text[3],
                    'description' => '',
                ),
                array(
                    'id' => 19,
                    'key' => 'instructions_listening',
                    'value' => $text[4],
                    'description' => '',
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
            ->whereIn('id', [15,16,17,18,19])
            ->delete();
    }
}
