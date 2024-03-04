<?php
require_once("src/functions.php");
require_once("src/info.php");


//Detects if session_id is empty, then looks to see if one has been stashed in a hidden form item to allow cross domain requests
// $session_id = session_id();
// echo $session_id;
// if(empty($session_id) && !empty($_POST["session_id"])) {session_id($_POST["session_id"]); echo "Using form session";} else echo "Using real session";

if (!isChromeOrEdge()) session_id($_POST["session_id"]);
session_start();

if($_SESSION['_basic_lti_context']['oauth_consumer_key'] == $lti_auth['key']){
    
    $orgUnitId = $_SESSION['_basic_lti_context']['context_id'];

    $userId = $_SESSION['_basic_lti_context']['user_id'];
    preg_match('/_(\d+)/', $userId, $matches);
    $auditeeId = (bool) $matches ? $matches[1] : -1;
    
    if (isset($_POST["advisor"])){
        $auditorId = $_POST["advisor"];
        $response = addDeleteAuditor("POST",$auditorId, $auditeeId);
        if ($response['Code']==200) {
            $groupCategoryId = $_POST["groupCategoryId"];
            $groupId = $_POST["groupId"];
            enrollToGroup($orgUnitId, $groupCategoryId, $groupId, $auditeeId);
            echo 'Your advisor have access to all course progresses.';
        }
        else {
            echo 'Sorry, a technical error occurred, please contact '.$supportEmail.' for support.';
        }
    } else{
        $myAuditors = explode(',', $_POST["myAuditors"]);
        foreach ($myAuditors as $auditorId) {
            $response = addDeleteAuditor("DELETE",$auditorId, $auditeeId);
        }    
        if ($response['Code']==200) {
            $groupCategoryId = $_POST["groupCategoryId"];
            $groupId = $_POST["groupId"];
            unEnrollFromGroup($orgUnitId, $groupCategoryId, $groupId, $auditeeId);
            echo 'Your advisor(s) no longer have access to all course progresses.';
        }
        else {
            echo 'Sorry, a technical error occurred, please contact '.$supportEmail.' for support.';
        }
    }
} else {
    echo 'Expired user session, please contact '.$supportEmail.' for support.';
}

?>