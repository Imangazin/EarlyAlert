<?php
require_once("src/functions.php");
require_once("src/info.php");

//Detects if session_id is empty, then looks to see if one has been stashed in a hidden form item to allow cross domain requests
$session_id = session_id();
if(empty($session_id) && !empty($_POST["session_id"])) session_id($_POST["session_id"]); 

session_start();

if($_SESSION['_basic_lti_context']['oauth_consumer_key'] == $lti_auth['key']){
    $user_id = $_SESSION['_basic_lti_context']['user_id'];
    preg_match('/_(\d+)/', $user_id, $matches);
    $auditeeId = (bool) $matches ? $matches[1] : -1;
    if (isset($_POST["advisor"])){
        $auditorId = $_POST["advisor"];
        $response = addDeleteAuditor("POST",$auditorId, $auditeeId);
        if ($response['Code']==200) {
            echo 'Your advisor have access to all course progresses.';
        }
    } else{
        $myAuditors = explode(',', $_POST["myAuditors"]);
        foreach ($myAuditors as $auditorId) {
            $response = addDeleteAuditor("DELETE",$auditorId, $auditeeId);
        }    
        if ($response['Code']==200) {
            echo 'Your advisor(s) no longer have access to all course progresses.';
        }
    }
} else {
    echo "Expired user session, please contact ".$supportEmail.' for support.';
}

?>