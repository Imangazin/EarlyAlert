<?php
require_once("functions.php");
require_once("info.php");

session_start();

//Detects if session_id is empty, then looks to see if one has been stashed in a hidden form item to allow cross domain requests
$sa = session_id();
if(empty($a) && !empty($_POST["session_id"])) session_id($_POST["session_id"]); 

session_start();

if($_SESSION['_basic_lti_context']->oauth_consumer_key == $lti_auth['key']){
    $auditeeId = preg_match('/_(\d+)/', $_SESSION['_basic_lti_context']->user_id, $matches) ? $matches[1] : '-1';
    $auditorId = $_POST["advisor"];
    if (isset($_POST["advisor"])){
        $response = addDeleteAuditor("POST",$auditorId, $auditeeId);
        if ($response['Code']==200) {
            echo 'Your advisor have access to all course progresses.';
        }
    } else{
        $response = addDeleteAuditor("DELETE",$auditorId, $auditeeId);
        if ($response['Code']==200) {
            echo 'Your advisor no longer have access to all course progresses.';
        }
    }
} else {
    echo "Expired user session, please contact ".$supportEmail.' for support.';
}

?>