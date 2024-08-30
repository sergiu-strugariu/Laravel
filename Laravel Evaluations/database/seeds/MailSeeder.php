<?php

use Illuminate\Database\Seeder;
use App\Models\MailTemplate;

use Faker\Factory;


class MailSeeder extends Seeder
{
    public function run()
    {

        MailTemplate::create(['name' => 'Welcome', 'slug' => 'welcome', 'subject' => 'Welcome', 'body_en' => '<h1>Hello</h1><p></p><p>Your account has been created!</p><p></p><p>You can log in by using your email address and with the following password: {password}</p><p></p><p></p>', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Forgot password', 'slug' => 'forgot', 'subject' => 'Forgot password', 'body_en' => 'Hi {first_name} {last_name},<br><br>You requested password reset.<br><br>Click here to change your password: <br><br><br>', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Invitation to take online test', 'slug' => 'take-test', 'subject' => 'Invitation to take online test', 'body_en' => '<h1>Hi {name},</h1>Company {company}<br><br>You have a new test to take for {language} until {deadline}<br><br>#speaking<br>You have speaking test on {schedule}<br>speaking#<br><br>Click the button below<br>', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Reminder to take online test', 'slug' => 'remind-test', 'subject' => 'Reminder to take online test', 'body_en' => 'Hi {name},<br><br>We remind you that you have to take a test.<br>Here is the link:<br>', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Feedback request', 'slug' => 'feedback-request', 'subject' => 'Feedback request', 'body_en' => 'Hi {name},<br><br>You have been assessed for your speaking test. You can leave us a feedback request at this link: <br>', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'New task assigned to assessor', 'slug' => 'assessor-assigned', 'subject' => 'New task assigned to assessor', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Reminder to update task', 'slug' => 'remind-task-update', 'subject' => 'Reminder to update task', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Status update request from client', 'slug' => 'client-update-request', 'subject' => 'Status update request from client', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Phone number/skype ID updated', 'slug' => 'contacts-updated', 'subject' => 'Phone number/skype ID updated', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Candidate (re)scheduled by client', 'slug' => 'candidate-rescheduled', 'subject' => 'Candidate (re)scheduled by client', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Assessment cancelled', 'slug' => 'assessment-cancelled', 'subject' => 'Assessment cancelled', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Please fill in the report again', 'slug' => 'report-again', 'subject' => 'Please fill in the report again', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Task done', 'slug' => 'task-done', 'subject' => 'Task done', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Candidate no longer interested', 'slug' => 'candidate-uninterested', 'subject' => 'Candidate no longer interested', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Candidate refused to be assessed', 'slug' => 'candidate-refused', 'subject' => 'Candidate refused to be assessed', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Candidate indicated that he/she should be assessed for a different language', 'slug' => 'candidate-different-lang', 'subject' => 'Candidate indicated that he/she should be assessed for a different language', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Check candidate phone number', 'slug' => 'candidate-check-phone', 'subject' => 'Check candidate phone number', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Wrong number. Someone else answered', 'slug' => 'wrong-number', 'subject' => 'Wrong number. Someone else answered', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'Add candidate skype ID', 'slug' => 'candidate-skype-add', 'subject' => 'Add candidate skype ID', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);
        MailTemplate::create(['name' => 'One test in a task finished', 'slug' => 'one-test-finish', 'subject' => 'One test in a task finished', 'body_en' => 'body_en', 'body_ro' => 'body_ro']);

    }
}