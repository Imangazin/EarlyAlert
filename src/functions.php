<?php
// collection of functions
require_once("info.php");
require_once("doValence.php");

function isChromeOrEdge() {
    $userAgent = $_SERVER['HTTP__AGENT'];
    return (strpos($userAgent,'Chrome') !== false || strpos($user, 'Edg') !== false);
}

function hasAuditor($userId){
    global $config;
    $response = doValenceRequest('GET', '/d2l/api/le/'.$config['LE_Version'].'/auditing/auditees/'.$userId);
    if (count($response['response']->Auditors)==0) return false; else return true; 
}

function getAdvisors($orgUnitId){
    global $config;
    $qparams = array("roleId"=>109);
    $response = doValenceRequest('GET', '/d2l/api/lp/'.$config['LP_Version'].'/enrollments/orgUnits/'.$orgUnitId.'/users/?roleId=109');
    $advisors = array();
    foreach($response['response']->Items as $advisor){
        $advisors[$advisor->User->Identifier] = $advisor->User->DisplayName;
    }
    return $advisors;
}

function addDeleteAuditor($verb, $auditorId, $auditeeId){
    global $config;
    return doValenceRequest($verb, '/d2l/api/le/'.$config['LE_Version'].'/auditing/auditors/'.$auditorId.'/auditees/', $auditeeId); 
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

function getMyAuditors($auditeeId){
    global $config;
    $result ='';
    $response = doValenceRequest('GET', '/d2l/api/le/'.$config['LE_Version'].'auditing/auditees/'.$auditeeId); 
    if (!empty($response['response']->Auditors)){
        $myAuditors = array_column($response['response']->Auditors, 'AuditorId');
        echo var_dump($response);
        $result = implode(',', $myAuditors);
    }
    return $result;
}

?>
