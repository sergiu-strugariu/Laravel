<?php


define("MAIL_SENT", "Mail");
define("MAIL_ERROR", "Mail Error");
define("TASK_UPDATE", "Task Update");
define("TASK_HISTORY", "Task History");
define("LINK_ACCESSED", "Accessed link");
define("SMS_ERROR", "SMS Error");

define("ERROR", "error");
define("SUCCESS", "success");

define("TEST_LANGUAGE_USE_NEW", 1);
define("TEST_SPEAKING", 2);
define("TEST_WRITING", 3);
define("TEST_LISTENING", 4);
define("TEST_READING", 5);
define("TEST_LANGUAGE_USE", 6);

// Instructions
define("TEST_INSTRUCTIONS", [
    1 => "instructions_language_use_new",
    2 => "instructions_speaking",
    3 => "instructions_writing",
    4 => "instructions_listening",
    5 => "instructions_reading",
    6 => "instructions_language_use",
]);

define("TOTAL_TEST_TYPES", 6);

define("TEST_LU_READING", 1);
define("TEST_LU_ARRANGE", 2);
define("TEST_LU_FILLGAPS", 3);

define("ALLOCATED", 1);
define("IN_PROGRESS", 2);
define("DONE", 3);
define("ISSUE", 4);
define("CANCELED", 5);
define("ARCHIVED", 6);

define("PRE_A1", 'Pre-A1');
define("A1", 'A1');
define("A2", 'A2');
define("B1", 'B1');
define("B2", 'B2');
define("C1", 'C1');
define("C2", 'C2');

### Mail Templates

define("MAIL_WELCOME", 'welcome');
define("MAIL_FORGOT", 'forgot');
define("MAIL_TEST_TAKE", 'take-test');
define("MAIL_TEST_REMIND", 'remind-test');
define("MAIL_FEEDBACK_REQ", 'feedback-request');
define("MAIL_ASSESSOR_ASSIGNED", 'assessor-assigned');
define("MAIL_ASSESSOR_ASSIGNED_CUSTOM", 'assessor-assigned-custom-period');
define("MAIL_REMIND_UPDATE_CUSTOM", 'remind-task-update-custom');
define("MAIL_TASK_REMIND_UPDATE", 'remind-task-update');
define("MAIL_CLIENT_UPDATE_REQ", 'client-update-request');
define("MAIL_CONTACTS_UPDATED", 'contacts-updated');
define("MAIL_CANDIDATE_RESCHEDULED", 'candidate-rescheduled');
define("MAIL_ASSESSMENT_CANCELED", 'assessment-cancelled');
define("MAIL_REPORT_AGAIN", 'report-again');
define("MAIL_TASK_DONE", 'task-done');
define("MAIL_CANDIDATE_UNINTERESTED", 'candidate-uninterested');
define("MAIL_CANDIDATE_REFUSED", 'candidate-refused');
define("MAIL_BAD_RECEPTION", 'bad-reception');
define("MAIL_TEST_WAS_RESET", 'test-was-reset');
define("MAIL_CANDIDATE_DID_NOT_ANSWER", 'candidate-did-not-answer');
define("MAIL_CANDIDATE_DIFFERENT_LANG", 'candidate-different-lang');
define("MAIL_CANDIDATE_CHECK_PHONE", 'candidate-check-phone');
define("MAIL_WRONG_NUMBER", 'wrong-number');
define("MAIL_CANDIDATE_SKYPE_ADD", 'candidate-skype-add');
define("MAIL_ONE_TEST_FINISH", 'one-test-finish');
define("MAIL_MANAGER_LANGUAGE_AUDIT", 'manager-language-audit');
define("SMS_TEST_REMIND", 'sms-remind-test');
define("MAIL_RESET_REPORT", 'reset-report');
define("MAIL_CANCELLED_TEST", 'cancelled-test');

// 27-updates-pentru-clienti
// Updates pentru clienti
define("MAIL_CONTACT_VIA_WHATSAPP", 'contact-via-whatsapp');
define("MAIL_CONTACT_VIA_SKYPE", 'contact-via-skype');

// 26-diversificare-updates-pentru-evaluatori
// New Notifications and updates
define("MAIL_CANDIDATE_CANCELLED_CALL_BACK", 'candidate-cancelled-call-back');
define("MAIL_CANDIDATE_NOT_VALIDATED", 'candidate-client-not-validated');
define("MAIL_CANDIDATE_ISSUES_DURING_TEST", 'candidate-issues-during-test');
define("MAIL_CANDIDATE_BUSY_SMS_SENT", 'candidate-busy-sms-sent');

define("MAIL_TEST_TAKE_MULTIPLE", 'take-test-multiple');

define("MAIL_ASSESSOR_IS_INACTIVE", 'assessor-is-inactive');


function sendSms($to, $text)
{

    if (substr($to, 0 , 2) === '07') {
        $to = '+407' . substr($to, 2);
    }

    try {
        Nexmo::message()->send([
            'to' => $to,
            'from' => 'Eucom',
            'text' => $text
        ]);

        return true;

    } catch (\Exception $e) {
        return $e->getMessage();
    }

}

function getSql($model)
{
    $replace = function ($sql, $bindings) {
        $needle = '?';
        foreach ($bindings as $replace) {
            $pos = strpos($sql, $needle);
            if ($pos !== false) {
                if (gettype($replace) === "string") {
                    $replace = ' "' . addslashes($replace) . '" ';
                }
                $sql = substr_replace($sql, $replace, $pos, strlen($needle));
            }
        }
        return $sql;
    };
    $sql = $replace($model->toSql(), $model->getBindings());

    return $sql;
}

/**
 * Log function
 *
 * @param $attributes
 */
function addLog($attributes)
{
    try {
        App\Models\Log::create($attributes);
    } catch (Exception $e) {
        \Illuminate\Support\Facades\Log::info($e->getMessage());
    }
}


/**
 * @param $taskId
 * @param $assessorId
 * @param string $reason
 */
function addAssessorHistory($taskId, $assessorId, $reason = '')
{
    try {
        App\Models\TaskAssessorHistory::create([
            'assessor_id' => $assessorId,
            'task_id' => $taskId,
            'reason' => $reason
        ]);
    } catch (Exception $e) {
        \Illuminate\Support\Facades\Log::info($e->getMessage());
    }
}


/**
 *  Returns json response
 *
 * @param string $resType
 * @param string $errMsg
 * @param null $data
 * @return \Illuminate\Http\JsonResponse
 */
function ajaxResponse($resType = SUCCESS, $errMsg = '', $data = null)
{
    return response()->json([
        'resType' => $resType,
        'errMsg' => $errMsg,
        'data' => $data
    ]);
}
