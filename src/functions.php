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
    $response = doValenceRequest('GET', '/d2l/api/le/'.$config['LE_Version'].'/auditing/auditees/'.$auditeeId); 
    if (!empty($response['response']->Auditors)){
        forEach ($response['response']->Auditors as $auditor){
            $result .= $auditor->AuditorId;
        }
    }
    return $result;
}

function createGroupCategory($orgUnitId){
    global $config;
    $data = array(
        "Name" => "Early Alert Widget",
        "Description" => array("Content"=>"Early-Alert Widget DB. Do not delete. Do not change its name.", "Type"=>"Html"),
        "EnrollmentStyle" => 0,
        "EnrollmentQuantity" => null,
        "AutoEnroll" => false,
        "RandomizeEnrollments" => false,
        "NumberOfGroups" => 1,
        "MaxUsersPerGroup" => null,
        "AllocateAfterExpiry" => false,
        "SelfEnrollmentExpiryDate" => null,
        "GroupPrefix" => null,
        "RestrictedByOrgUnitId" => null,
        "DescriptionsVisibleToEnrolees" => false
    );
    $response = doValenceRequest('POST', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/', $data);
    echo var_dump($response);
    return $response->CategoryId;
}

function createGroup($orgUnitId, $groupCategotyId, $name, $code){
    global $config;
    $data = array(
        "Name" => $name,
        "Code" => $code,
        "Description" => array("Content"=>"", "Type"=>"Html")
    );
    $response = doValenceRequest('POST', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/'.$groupCategotyId.'/groups/', $data);
    return $response->GroupId;
}

function getGroupCategoryId($orgUnitId){
    global $config;
    $groupCategoryId = -1;
    $response = doValenceRequest('GET', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/');
    foreach($response['response'] as $groupCategories){
        if (strpos($groupCategories->Name, "Early Alert") !== false){
            $groupCategoryId = $groupCategories->GroupCategoryId;
        }   
    }

    if ($groupCategoryId == -1){
        $groupCategoryId = createGroupCategory($orgUnitId);
    }
    return $groupCategoryId;
}
?>
