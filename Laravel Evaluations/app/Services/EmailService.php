<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use App\Repositories\MailsRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Spatie\CalendarLinks\Link;

class EmailService implements EmailServiceInterface
{
    /**
     * @var
     */
    private $from;
    /**
     * @var
     */
    private $name;
    /**
     * @var MailsRepository
     */
    private $mailsRepository;
    /**
     * @var
     */
    private $mailTemplate;
    /**
     * @var
     */
    private $templateSlug;

    /**
     * EmailService constructor.
     * @param MailsRepository $mailsRepository
     */
    public function __construct(MailsRepository $mailsRepository)
    {
        $this->from = config('app.email_address');
        $this->name = config('app.email_name');
        $this->mailsRepository = $mailsRepository;

    }

    /**
     * @param $template
     */
    private function _setMailTemplate($template)
    {
        $this->mailTemplate = $this->mailsRepository->getBySlug($template);
        $this->templateSlug = $template;
    }

    /**
     * @param $data
     * @return array
     */
    private function _injectVariables($data, $extra = array())
    {

        if (!is_array($data)) {
            $data = json_decode(json_encode($data), true);
        }

        $vars = config('mail.vars');

        foreach ($vars as $var => $desc) {
            if (isset($data[$var])) {
                $this->mailTemplate->body_ro = str_replace('{' . $var . '}', $data[$var], $this->mailTemplate->body_ro);
                $this->mailTemplate->body_en = str_replace('{' . $var . '}', $data[$var], $this->mailTemplate->body_en);
                $this->mailTemplate->subject = str_replace('{' . $var . '}', $data[$var], $this->mailTemplate->subject);
            }
            else {
                $this->mailTemplate->body_ro = str_replace('{' . $var . '}', '', $this->mailTemplate->body_ro);
                $this->mailTemplate->body_en = str_replace('{' . $var . '}', '', $this->mailTemplate->body_en);
                $this->mailTemplate->subject = str_replace('{' . $var . '}', '', $this->mailTemplate->subject);
            }
        }

        if ($this->templateSlug == MAIL_TEST_TAKE) {
            $this->_replaceTagSpeaking($data);
            $this->_replaceTagOnline($data);
        }

        if ($this->templateSlug == MAIL_TEST_TAKE_MULTIPLE) {
            $this->mailTemplate->body_en = $this->_customTags($data['testList'], $this->mailTemplate->body_en);
            $this->mailTemplate->body_ro = $this->_customTags($data['testList'], $this->mailTemplate->body_ro);
            if (!empty($extra) && isset($extra['resent_notice'])) {
                $this->mailTemplate->body_ro = str_replace('{resent_notice}', 'Aceasta este o reinvitație oficială: completați numai testul (sau testele) pentru care ați făcut solicitarea.', $this->mailTemplate->body_ro);
                $this->mailTemplate->body_en = str_replace('{resent_notice}', 'This is an officially resent invitation: please only complete the test (or tests) for which you have requested.', $this->mailTemplate->body_en);
            } else {
                $this->mailTemplate->body_ro = str_replace('{resent_notice}', '', $this->mailTemplate->body_ro);
                $this->mailTemplate->body_en = str_replace('{resent_notice}', '', $this->mailTemplate->body_en);
            }
        }
        
        $mailBodies = [
            'body_en' => $this->mailTemplate->body_en,
            'body_ro' => $this->mailTemplate->body_ro,
        ];

        return array_merge($data, $mailBodies);
    }

    /**
     * @param $data
     */
    private function _replaceTagSpeaking($data)
    {
        if (strpos($this->mailTemplate->body_en, '#speaking') !== false) {

            ### EN
            $speakingTextStart = strpos($this->mailTemplate->body_en, '#speaking');
            $speakingTextEnd = strpos($this->mailTemplate->body_en, 'speaking#');
            $speakingTextStart += 9;

            $textSpeakingEN = substr($this->mailTemplate->body_en, $speakingTextStart,
                $speakingTextEnd - $speakingTextStart);
            $this->mailTemplate->body_en = str_replace($textSpeakingEN, '', $this->mailTemplate->body_en);

        }

        if (strpos($this->mailTemplate->body_ro, '#speaking') !== false) {

            ### RO
            $speakingTextStart = strpos($this->mailTemplate->body_ro, '#speaking');
            $speakingTextEnd = strpos($this->mailTemplate->body_ro, 'speaking#');
            $speakingTextStart += 9;

            $textSpeakingRO = substr($this->mailTemplate->body_ro, $speakingTextStart,
                $speakingTextEnd - $speakingTextStart);

            $this->mailTemplate->body_ro = str_replace($textSpeakingRO, '', $this->mailTemplate->body_ro);

        }

        $textSpeakingRO = isset($textSpeakingRO) ? $textSpeakingRO : '';
        $textSpeakingEN = isset($textSpeakingEN) ? $textSpeakingEN : '';

        $tests = explode(',', $data['tests']);
        $tests = array_map(function($elem){
            return trim(strtolower($elem));
        }, $tests);

        if (in_array('speaking', $tests)) {

            ### set availability
            if ($data['availability_from'] == $data['availability_to']) {
                $availability_ro = Carbon::parse($data['availability_from'])->format('d M Y, H:i');
                $availability_en = Carbon::parse($data['availability_from'])->format('d M Y, H:i');
            } else {
                $availability_ro = Carbon::parse($data['availability_from'])->format('d M Y') . ', de la ' .
                    Carbon::parse($data['availability_from'])->format('H:i') . ' la ' . Carbon::parse($data['availability_to'])->format('H:i');
                $availability_en = Carbon::parse($data['availability_from'])->format('d M Y') . ', from ' .
                    Carbon::parse($data['availability_from'])->format('H:i') . ' to ' . Carbon::parse($data['availability_to'])->format('H:i');
            }

            $textSpeakingRO = str_replace('{schedule}', $availability_ro, $textSpeakingRO);
            $textSpeakingEN = str_replace('{schedule}', $availability_en, $textSpeakingEN);
            $this->mailTemplate->body_ro = str_replace('#speakingspeaking#', $textSpeakingRO, $this->mailTemplate->body_ro);
            $this->mailTemplate->body_en = str_replace('#speakingspeaking#', $textSpeakingEN, $this->mailTemplate->body_en);


        } else {
            $this->mailTemplate->body_ro = str_replace('#speakingspeaking#', '', $this->mailTemplate->body_ro);
            $this->mailTemplate->body_en = str_replace('#speakingspeaking#', '', $this->mailTemplate->body_en);
        }
    }

    /**
     * @param $data
     */
    private function _replaceTagOnline($data)
    {
        if (strpos($this->mailTemplate->body_en, '#online') !== false) {

            ### EN
            $onlineTextStart = strpos($this->mailTemplate->body_en, '#online');
            $onlineTextEnd = strpos($this->mailTemplate->body_en, 'online#');
            $onlineTextStart += 7;

            $textOnlineEN = substr($this->mailTemplate->body_en, $onlineTextStart,
                $onlineTextEnd - $onlineTextStart);
            $this->mailTemplate->body_en = str_replace($textOnlineEN, '', $this->mailTemplate->body_en);

        }

        if (strpos($this->mailTemplate->body_ro, '#online') !== false) {

            ### RO
            $onlineTextStart = strpos($this->mailTemplate->body_ro, '#online');
            $onlineTextEnd = strpos($this->mailTemplate->body_ro, 'online#');
            $onlineTextStart += 7;

            $textOnlineRO = substr($this->mailTemplate->body_ro, $onlineTextStart,
                $onlineTextEnd - $onlineTextStart);

            $this->mailTemplate->body_ro = str_replace($textOnlineRO, '', $this->mailTemplate->body_ro);

        }

        $textOnlineRO = isset($textOnlineRO) ? $textOnlineRO : '';
        $textOnlineEN = isset($textOnlineEN) ? $textOnlineEN : '';

        $tests = explode(',', $data['tests']);
        $tests = array_map(function($elem){
            return trim(strtolower($elem));
        }, $tests);

        ### check it has online tests
        if (count($tests) > 1 || (count($tests) == 1 && reset($tests) !== 'speaking' && reset($tests) !== '' )) {

            $vars = config('mail.vars');

            foreach ($vars as $var => $desc) {
                if (isset($data[$var])) {
                    $textOnlineRO = str_replace('{' . $var . '}', $data[$var], $textOnlineRO);
                    $textOnlineEN = str_replace('{' . $var . '}', $data[$var], $textOnlineEN);
                }  else {
                    $textOnlineRO = str_replace('{' . $var . '}', '', $textOnlineRO);
                    $textOnlineEN = str_replace('{' . $var . '}', '', $textOnlineEN);
                }
            }

            $this->mailTemplate->body_ro = str_replace('#onlineonline#', $textOnlineRO, $this->mailTemplate->body_ro);
            $this->mailTemplate->body_en = str_replace('#onlineonline#', $textOnlineEN, $this->mailTemplate->body_en);

        } else {
            $this->mailTemplate->body_ro = str_replace('#onlineonline#', '', $this->mailTemplate->body_ro);
            $this->mailTemplate->body_en = str_replace('#onlineonline#', '', $this->mailTemplate->body_en);
        }
    }


    /**
     * Parse custom # tags in the Controller
     *
     * @param $data
     * @param $text
     * @return mixed
     */
    private function _customTags($data, $text)
    {
        foreach ($data as $tag => $shown) {
            $startTag = "#" . $tag;
            $endTag = $tag . '#';

            if ($shown) {
                $text = str_replace(array($startTag, $endTag), '', $text);
            } else if (strpos($text, $startTag)) {
                $startIndex = strpos($text, $startTag);
                $endIndex = strpos($text, $endTag) + (strlen($endTag));
                $text = substr_replace($text, '', $startIndex, $endIndex - $startIndex);
            }
        }

        return $text;
    }

    /**
     * @param array $attributes
     * @param $template
     * @return bool|JsonResponse
     */
    public function sendEmail(array $attributes, $template, $extra = array())
    {
        $this->_setMailTemplate($template);

        if (!$this->mailTemplate) {

            return $this->sendEmail_old($attributes);

        } else {
            $data = $this->_injectVariables($attributes, $extra);

            try {

                $user = User::where('email', $attributes['email'])->first();
                if ($user && in_array($template, $user->notifications)) {
                    return response()->json(['message' => 'Request completed']);
                }

                Mail::send('emails.' . $template, $data, function ($message) use ($attributes) {
                    $message->from($this->from, $this->name);
                    $message->to($attributes['email'])->subject($this->mailTemplate->subject);
                    if (isset($attributes['attachment'])) {
                        $message->attach($attributes['attachment']);
                    }
                });
            } catch (Exception $e) {

                addLog([
                    'type' => MAIL_ERROR,
                    'description' => $template . ' mail not sent'
                ]);

                return false;
            }


            return response()->json(['message' => 'Request completed']);
        }

    }

    /**
     * @param array $attributes
     * @param $template
     * @return bool|string
     */
    public function sendSms(array $attributes, $template)
    {
        $this->_setMailTemplate($template);

        $data = $this->_injectVariables($attributes);
        
        return sendSms($attributes['phone'], $data['body_en']);

    }



    /**
     * @param $task
     * @param $ability
     * @param $grade
     * @param $test_type
     */
    public function sendEmailOneTestFinished($task, $ability, $grade, $test_type)
    {

        $this->sendEmail([
            'email' => $task->addedBy->email,
            'name' => $task->name,
            'language' => $task->language->name,
            'ability' => $ability,
            'grade' => $grade,
            'test_type' => $test_type,
            'link' => url('task/' . $task->id)
        ], MAIL_ONE_TEST_FINISH);

        ### Send to followers
        $followers = $task->followers;
        if (!empty($followers)) {
            foreach ($followers as $follower) {
                $this->sendEmail([
                    'email' => $follower->user->email,
                    'name' => $task->name,
                    'language' => $task->language->name,
                    'ability' => $ability,
                    'grade' => $grade,
                    'test_type' => $test_type,
                    'link' => url('task/' . $task->id)
                ], MAIL_ONE_TEST_FINISH);
            }
        }
    }

    /**
     * @param $assessor
     * @param $task
     * @param $testType
     */
    public function sendAssessorMail($assessor, $task, $testType = TEST_SPEAKING)
    {

        ### get test types
        $testTypes = $task->papers->pluck('type')->pluck('name', 'id')->toArray();
        
        ### if task does not have Speaking, and this function's $testType == Speaking
        if (!isset($testTypes[TEST_SPEAKING]) && $testType == TEST_SPEAKING) {
            return;
        }

        $testName = $testTypes[$testType];
        $calendarLinks = [];
        ### if availability is set in the task
        if ($task->availability_from != null) {
            // Format so that the the date will be on one line
            if ($task->availability_from == $task->availability_to) {
                $from = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_from);
                $to = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_from);
                $availability = Carbon::parse($task->availability_from)->format('d M Y, H:i');
            }
            // Else if there are both set, concat them using "from - to"
            else {
                $from = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_from);
                $to = \DateTime::createFromFormat('Y-m-d H:i:s', $task->availability_to);

                $availability = Carbon::parse($task->availability_from)->format('d M Y') .
                    ', from ' .
                    Carbon::parse($task->availability_from)->format('H:i') .
                    ' to ' .
                    Carbon::parse($task->availability_to)->format('H:i')
                ;
            }

            $link = Link::create('[EUCOM] ' . $testName . " - " . $task->name, $from, $to);

            // Removed ics because sendgrid screws up the data/calendar link
            foreach(['google', 'yahoo', 'webOutlook'] as $service) {
                $calendarLinks[] = [
                    "serviceName" => $service,
                    "serviceLink" => call_user_func([$link, $service])
                ];
            }
        } else {
            $availability = '-';
        }

        $emailType = MAIL_ASSESSOR_ASSIGNED;
        if ($task->custom_period_cost > 0) {
            $emailType = MAIL_ASSESSOR_ASSIGNED_CUSTOM;
        }

        $this->sendEmail([
            'email' => $assessor->email,
            'user_email' => $task->email,
            'name' => $task->name,
            'phone' => $task->phone,
            'company' => $task->project->owner->name,
            'calendar_links' => $calendarLinks,
            'language' => $task->language->name,
            'test_type' => $testName,
            'availability' => $availability,
            'link' => url('task/' . $task->id),
            'linkRefuse' => url('task/' . $task->id . '/refuse'),
        ], $emailType);


    }

    /**
     * @param $task_id
     * @param $attributes
     */
    public function sendEmailToLanguageAuditManager($task_id, $attributes)
    {

        $this->sendEmail([
            'link' => url('task/' . $task_id),
            'subject' => $attributes['subject'],
            'body' => $attributes['body'],
            'task_id' => $attributes['task_id'],
            'template' => MAIL_MANAGER_LANGUAGE_AUDIT,
            'email' => $attributes['to']
        ], MAIL_MANAGER_LANGUAGE_AUDIT);

    }

    /**
     * Send email notifications
     *
     * @param array $attributes
     * @return \Illuminate\Http\JsonResponse
     */
    function sendEmail_old(array $attributes)
    {
        $user = User::where('email', $attributes['email'])->first();
        if ($user && in_array($attributes['template'], $user->notifications)) {
            return response()->json(['message' => 'Request completed']);
        }

        $from = config('app.email_address');
        $name = config('app.email_name');

        ### send mail for new account registration
        if ($attributes['template'] == config('mail.new_account')) {
            $password = $attributes['password'];
            $to = $attributes['email'];
            Mail::send('emails.' . $attributes['template'], ['password' => $password],
                function ($message) use ($from, $to, $name) {

                    $message->from($from, $name);

                    $message->to($to)->subject(config('email_translate.' . config('mail.new_account') . '.subject'));

                });

            return response()->json(['message' => 'Request completed']);
        }

        ### send mail for new task added to project
        if ($attributes['template'] == config('mail.new_task')) {
            $to = $attributes['email'];
            Mail::send('emails.' . $attributes['template'],
                [
                    'name' => $attributes['name'],
                    'link' => $attributes['link'],
                ],
                function ($message) use ($from, $to, $name) {

                    $message->from($from, $name);

                    $message->to($to)->subject(config('email_translate.' . config('mail.new_task') . '.subject'));

                });

            return response()->json(['message' => 'Request completed']);
        }

        ### send mail to assessor
        if ($attributes['template'] == config('mail.assessor')) {
            $to = $attributes['email'];
            Mail::send('emails.' . $attributes['template'],
                [
                    'name' => $attributes['name'],
                    'link' => $attributes['link'],
                    'linkRefuse' => $attributes['linkRefuse'],
                    'testTaker' => $attributes['testTaker'],
                    'testTakerEmail' => $attributes['testTakerEmail'],
                    'testTakerPhone' => $attributes['testTakerPhone'],
                    'language' => $attributes['language'],
                    'mark' => $attributes['mark'],
                    'department' => $attributes['department'],
                    'task' => $attributes['task'],
                    'user' => isset($attributes['user']) ? $attributes['user'] : array(),
                ],
                function ($message) use ($from, $to, $name, $attributes) {

                    $message->from($from, $name);

                    $message->to($to)->subject(config('email_translate.' . config('mail.assessor') . '.subject') . ' #' . $attributes['task']->id);

                });

            return response()->json(['message' => 'Request completed']);
        }


        ### send mail to administrator
        if ($attributes['template'] == config('mail.administrator')) {
            $to = $attributes['email'];
            Mail::send('emails.' . $attributes['template'],
                [
                    'name' => $attributes['name'],
                    'task' => $attributes['task'],
                    'verifyTask' => $attributes['verifyTask'],
                ],
                function ($message) use ($from, $to, $name) {

                    $message->from($from, $name);
                    $message->to($to)->subject(config('email_translate.administrator.subject'));

                });

            return response()->json(['message' => 'Request completed']);
        }

        ### send group empty mail to administrator
        if ($attributes['template'] == config('mail.task_empty_group')) {
            $to = $attributes['email'];
            Mail::send('emails.' . $attributes['template'],
                [
                    'name' => $attributes['name'],
                    'testTaker' => $attributes['testTaker'],
                    'task' => $attributes['task'],
                    'user' => $attributes['user']
                ],
                function ($message) use ($from, $to, $name) {

                    $message->from($from, $name);

                    $message->to($to)->subject(config('email_translate.' . config('mail.task_empty_group') . '.subject'));

                });

            return response()->json(['message' => 'Request completed']);
        }


        ### send mail if new comment added
        if ($attributes['template'] == config('mail.new_comment')) {
            $to = $attributes['email'];
            Mail::send('emails.' . $attributes['template'],
                [
                    'link' => $attributes['link'],
                ],
                function ($message) use ($from, $to, $name) {

                    $message->from($from, $name);

                    $message->to($to)->subject(config('email_translate.' . config('mail.new_comment') . '.subject'));

                });

            return response()->json(['message' => 'Request completed']);
        }

        ### send mail with task information
        if ($attributes['template'] == config('mail.task_info')) {
            $to = $attributes['email'];
            $taskInfo = $attributes['taskInfo'];

            Mail::send('emails.task_info',
                [
                    'link' => $attributes['link'],
                    'taskInfo' => $taskInfo,
                    'task' => $attributes['task'],
                    'user' => isset($attributes['user']) ? $attributes['user'] : array(),
                ],
                function ($message) use ($from, $to, $name, $taskInfo, $attributes) {

                    $message->from($from, $name);

                    $message->to($to)->subject(config('email_translate.' . config('mail.' . $taskInfo) . '.subject') . ' #' . $attributes['task']->id);

                });

            return response()->json(['message' => 'Request completed']);
        }

        ###
        if ($attributes['template'] == config('mail.manager_language_audit')) {
            $to = $attributes['email'];
            Mail::send('emails.manager-language-audit',
                [
                    'link' => $attributes['link'],
                    'body' => $attributes['body'],
                ],
                function ($message) use ($from, $to, $name, $attributes) {

                    $message->from($from, $name);

                    $message->to($to)->subject('Task#' . $attributes['task_id']);

                });

            return response()->json(['message' => 'Request completed']);

        }

    }

    /**
     * Send mail notification to assessor
     *
     * @param Task $task
     * @param User $assessor
     * @param array $params
     * @return JsonResponse
     */
    public function sendTaskMailToAssessor(Task $task, User $assessor, $params)
    {
        return $this->sendEmail_old([
            'name' => $task->name,
            'link' => '[Here will be the link to the test page]',
            'linkRefuse' => '[Here will be the linkRefuse]',
            'testTaker' => $task->name,
            'testTakerEmail' => $task->email,
            'testTakerPhone' => $task->phone,
            'language' => $task->language->name,
            'mark' => $task->mark,
            'department' => $task->department,
            'email' => $assessor->email,
            'user' => $assessor,
            'task' => $task,
            'template' => config('mail.assessor')
        ]);
    }

    /**
     * Send mail notification to follower
     *
     * @param Task $task
     * @param User $follower
     * @param string $info
     * @param array $params
     * @return JsonResponse
     */
    public function sendTaskMailToFollower(Task $task, User $follower, $info, $params)
    {
        return $this->sendEmail_old([
            'template' => config('mail.task_info'),
            'name' => $params['name'],
            'deadline' => $params['deadline'],
            'link' => '[Here will be the link to the test page]',
            'taskInfo' => $info,
            'email' => $follower->email,
            'user' => $follower,
            'task' => $task,
        ]);
    }
}
