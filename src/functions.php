<?php
// collection of functions
require_once("info.php");
require_once("doValence.php");

// Will use partitioned cookie for chrome and edge
function isChromeOrEdge($userAgent = '') {
    // Get the current user agent if none provided
    if (empty($userAgent)) {
        $userAgent =SERVER['HTTP_USER_AG'];
    }
    // Check if user agent contains "Chrome" or "Edg"
    return (strpos($userAgent, 'Chrome') !== false || strpos($userAgent, 'Edg') !== false);
}

function hasAuditor($userId){
    $response = doValenceRequest('GET', '/d2l/api/le/'.$config['LP_Version'].'/auditing/auditees/'.$userId);
    return count(($response['response']->Auditors)===0) ? false : true;
}

function getAdvisors($orgUnitId){
    $qparams = array("roleId"=>109);
    $response = doValenceRequest('GET', '/d2l/api/lp/'.$config['LP_Version'].'/enrollments/orgUnits/'.$orgUnitId.'/users/?roleId=109');
    $advisors = array();
    foreach($response['response']->Items as $advisor){
        $advisors[$advisor->User->Identifier] = $advisor->User->DisplayName;
    }
    return $advisors;
}

function addDeleteAuditor($verb, $auditorId, $auditeeId){
    return doValenceRequest($verb, '/d2l/api/le/1.74/auditing/auditors/'.$auditorId.'/auditees/', $auditeeId); 
}

function getCurrentAcademicTerm() {
    $currentMonth = date('n');
    $currentYear = date('Y');
    if ($currentMonth >= 1 && $currentMonth <= 4) {
        return "Winter " . $currentYear;
    } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
        return "Spring and Summer " . $currentYear;
    } else {
        return "Fall " . $currentYear;
    }
}

?>
