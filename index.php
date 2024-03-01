<?php
require_once("src/info.php");
require_once("src/functions.php");
// Load up the LTI Support code
require_once 'ims-blti/blti.php';

function isChromeOrEdge() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    //echo  "User agent: ". $userAgent . "<br>";
    return (strpos($userAgent,'Chrome') !== false || strpos($user, 'Edg') !== false);
}
//setting user sessions
$currentCookieParams = session_get_cookie_params();

if (isChromeOrEdge()) echo "it is chrome <br>"; else echo 'it is not chrome <br>';

if (isChromeOrEdge()) $cookie_loation = $cookie_loation . ' partitioned;';
echo  "Location: ".$cookie_loation."<br>" ;
session_set_cookie_params(
    $currentCookieParams["lifetime"],
    $cookie_loation,
    $_SERVER['HTTP_HOST'],
    "1",
    "1"
);

session_start();

//All of the LTI Launch data gets passed through in $_REQUEST
if(isset($_REQUEST['lti_message_type'])) {    //Is this an LTI Request?
    $context = new BLTI($lti_auth['secret'], true, false);

    if($context->complete) exit(); //True if redirect was done by BLTI class
    if($context->valid) { //True if LTI request was verified
        $userId = preg_match('/_(\d+)/', $context->info['user_id'], $matches) ? $matches[1] : '-1';
        $orgUnitId = $context->info['context_id'];

        $advisors = getAdvisors($orgUnitId);
        $myAuditors = getMyAuditors($userId);

        $groupCategoryId = getGroupCategoryId($orgUnitId);
        $currentTerm = getCurrentAcademicTerm();
        $groupId = getGroupId($orgUnitId, $groupCategoryId, $currentTerm, $myAuditors);

        $hasAuditor = hasAuditor($userId);

        include 'home.php';
    }
}
else { 
    echo 'LTI credentials not valid. Please refresh the page and try again. If you continue to receive this message please contact <a href="mailto:'.$supportEmail.'?Subject=Quick Add Widget Issue" target="_top">'.$supportEmail.'</a>';
}
?>