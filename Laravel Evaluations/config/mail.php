<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    |
    | Laravel supports both SMTP and PHP's "mail" function as drivers for the
    | sending of e-mail. You may specify which one you're using throughout
    | your application here. By default, Laravel is setup for SMTP mail.
    |
    | Supported: "smtp", "sendmail", "mailgun", "mandrill", "ses",
    |            "sparkpost", "log", "array"
    |
    */

    'driver' => env('MAIL_DRIVER', 'sendgrid'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Address
    |--------------------------------------------------------------------------
    |
    | Here you may provide the host address of the SMTP server used by your
    | applications. A default option is provided that is compatible with
    | the Mailgun mail service which will provide reliable deliveries.
    |
    */

    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Port
    |--------------------------------------------------------------------------
    |
    | This is the SMTP port used by your application to deliver e-mails to
    | users of the application. Like the host we have set this value to
    | stay compatible with the Mailgun e-mail application by default.
    |
    */

    'port' => env('MAIL_PORT', 587),

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | E-Mail Encryption Protocol
    |--------------------------------------------------------------------------
    |
    | Here you may specify the encryption protocol that should be used when
    | the application send e-mail messages. A sensible default using the
    | transport layer security protocol should provide great security.
    |
    */

    'encryption' => env('MAIL_ENCRYPTION', 'tls'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Username
    |--------------------------------------------------------------------------
    |
    | If your SMTP server requires a username for authentication, you should
    | set it here. This will get used to authenticate with your server on
    | connection. You may also set the "password" value below this one.
    |
    */

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sendmail System Path
    |--------------------------------------------------------------------------
    |
    | When using the "sendmail" driver to send e-mails, we will need to know
    | the path to where Sendmail lives on this server. A default path has
    | been provided here, which will work well on most of your systems.
    |
    */

    'sendmail' => '/usr/sbin/sendmail -bs',

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    'stream' => [
        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ],

    'new_account' => 'new_account',
    'new_task' => 'new_task',
    'new_task_follower' => 'new_task_follower',
    'removed_task_follower' => 'removed_task_follower',
    'new_comment' => 'new_comment',
    'task_canceled' => 'task_canceled',
    'task_reprogrammed' => 'task_reprogrammed',
    'task_overdue' => 'task_overdue',
    'task_done' => 'task_done',
    'task_empty_group' => 'task_empty_group',
    'assessor' => 'assessor',
    'administrator' => 'administrator',
    'recover_password' => 'recover_password',
    'manager_language_audit' => 'manager-language-audit',

    'vars' => [
        'first_name' => 'User\'s first name',
        'last_name' => 'User\'s last name',
        'email' => 'User\'s email address',
        'user_email' => 'User\'s email address',
        'password' => 'Generated password',
        'name' => 'Test taker name',
        'company' => 'Company name',
        'language' => 'Test language',
        'test_type' => 'Test type',
        'availability' => 'Test availability',
        'assessor_first_name' => 'assessor\'s first name',
        'phone' => 'Test taker phone',
        'deadline' => 'Task deadline',
        'tests' => 'Test types list',
        'grade' => 'Test grade',
        'ability' => 'Test ability',
        'test_results' => 'Test names and grades',
        'speaking_link' =>  "The URL for the Speaking test",
        'writing_link' =>  "The URL for the Writing test",
        'listening_link' =>  "The URL for the Listening test",
        'language_use_link' => "The URL for the Language Use test",
        'language_use_new_link' => "The URL for the Language Use test",
        'reading_link' =>  "The URL for the Language Use test",
        'task_id' => "The ID of the Task",
        'custom_period' => "The custom availability period for the task"

    ],

    'vars_by_type' => [
        MAIL_WELCOME => [
            'first_name' => 'User\'s first name',
            'last_name' => 'User\'s last name',
            'email' => 'User\'s email address',
            'password' => 'Generated password',
        ],
        MAIL_FORGOT => [
            'first_name' => 'User\'s first name',
            'last_name' => 'User\'s last name',
            'email' => 'User\'s email address',
        ],
        MAIL_TEST_TAKE => [
            'email' => 'User\'s email address',
            'name' => 'Test taker name',
            'company' => 'Company name',
            'language' => 'Test language',
            'deadline' => 'Task deadline',
            'schedule' => 'Speaking availability date time',
            'tests' => 'Online Test types list'
        ],
        MAIL_TEST_REMIND => [
            'email' => 'User\'s email address',
            'name' => 'Test taker name',
            'company' => 'Company name',
            'language' => 'Test language',
        ],
        MAIL_FEEDBACK_REQ => [
            'name' => 'Test taker name',
            'assessor_first_name' => 'assessor\'s first name',
            'language' => 'Test language',
        ],
        MAIL_ASSESSOR_ASSIGNED => [
            'name' => 'Test taker name',
            'phone' => 'Test taker phone',
            'user_email' => 'Test taker email',
            'company' => 'Company name',
            'language' => 'Test language',
            'test_type' => 'Test type',
            'availability' => 'Test availability'
        ],
        MAIL_TASK_REMIND_UPDATE => [
            'name' => 'Test taker name',
        ],
        MAIL_CLIENT_UPDATE_REQ => [
            'name' => 'Test taker name',
        ],
        MAIL_CONTACTS_UPDATED => [
            'name' => 'Test taker name',
        ],
        MAIL_CANDIDATE_RESCHEDULED => [
            'name' => 'Test taker name',
            'availability' => 'Test availability',
        ],
        MAIL_ASSESSMENT_CANCELED => [
            'name' => 'Test taker name',
        ],
        MAIL_REPORT_AGAIN => [
            'name' => 'Test taker name',
        ],
        MAIL_TASK_DONE => [
            'name' => 'Test taker name',
            'test_results' => 'Test names and grades'
        ],
        MAIL_CANDIDATE_UNINTERESTED => [
            'name' => 'Test taker name',
        ],
        MAIL_CANDIDATE_REFUSED => [
            'name' => 'Test taker name',
        ],
        MAIL_BAD_RECEPTION => [
            'name' => 'Test taker name',
        ],
        MAIL_TEST_WAS_RESET => [
            'name' => 'Test taker name',
        ],
        MAIL_CANDIDATE_DID_NOT_ANSWER => [
            'name' => 'Test taker name',
        ],
        MAIL_CANDIDATE_DIFFERENT_LANG => [
            'name' => 'Test taker name',
        ],
        MAIL_CANDIDATE_CHECK_PHONE => [
            'name' => 'Test taker name',
        ],
        MAIL_WRONG_NUMBER => [
            'name' => 'Test taker name',
        ],
        MAIL_CANDIDATE_SKYPE_ADD => [
            'name' => 'Test taker name',
        ],
        MAIL_ONE_TEST_FINISH => [
            'name' => 'Test taker name',
            'language' => 'Test language',
            'grade' => 'Test grade',
            'ability' => 'Test ability',
            'test_type' => 'Finished test type',
        ],
        SMS_TEST_REMIND => [
            'language' => 'Test language',
            'company' => 'Company name',
        ],
        MAIL_RESET_REPORT => [
            'name' => 'Test taker name',
            'language' => 'Test language',
            'grade' => 'Test grade',
            'test_type' => 'Test type',
        ],
        MAIL_CANCELLED_TEST => [
            'name' => 'Task Name',
            'test_type' => 'Test type',
        ],
        MAIL_CONTACT_VIA_WHATSAPP => [],
        MAIL_CONTACT_VIA_SKYPE => [],
        MAIL_CANDIDATE_CANCELLED_CALL_BACK => [],
        MAIL_CANDIDATE_NOT_VALIDATED => [],
        MAIL_CANDIDATE_ISSUES_DURING_TEST => [],
        MAIL_CANDIDATE_BUSY_SMS_SENT => [],
        MAIL_TEST_TAKE_MULTIPLE => [],
        MAIL_ASSESSOR_IS_INACTIVE => [
            'task_id' => "The ID of the Task"
        ],
        MAIL_ASSESSOR_ASSIGNED_CUSTOM => [],
        MAIL_REMIND_UPDATE_CUSTOM => [],
    ]

];
