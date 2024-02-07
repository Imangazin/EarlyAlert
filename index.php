<?php
require_once("src/info.php");
require_once("src/functions.php");
// Load up the LTI Support code
require_once 'ims-blti/blti.php';

//All of the LTI Launch data gets passed through in $_REQUEST
if(isset($_REQUEST['lti_message_type'])) {    //Is this an LTI Request?
    //setting user sessions
    $currentCookieParams = session_get_cookie_params();
    if (isChromeOrEdge()) $cookie_loation = $cookie_loation . ' partitioned;';
    session_set_cookie_params(
        $currentCookieParams["lifetime"],
        $cookie_loation,
        $_SERVER['HTTP_HOST'],
        "1",
        "1"
    );

    session_start();
    $context = new BLTI($lti_auth['secret'], true, false);

    if($context->complete) exit(); //True if redirect was done by BLTI class
    if($context->valid) { //True if LTI request was verified
        $userId = preg_match('/_(\d+)/', $context->info['user_id'], $matches) ? $matches[1] : '-1';
        $orgUnitId = $context->info['context_id'];
        $hasAuditor = hasAuditor($userId);
        $advisors = getAdvisors($orgUnitId);
        $myAuditors = getMyAuditors($userId);

        echo 'My Auditors: ' . $myAuditors . 'haha';
        
        include 'home.php';
    }
}
else { 
    echo 'LTI credentials not valid. Please refresh the page and try again. If you continue to receive this message please contact <a href="mailto:'.$supportEmail.'?Subject=Quick Add Widget Issue" target="_top">'.$supportEmail.'</a>';
}
?>