<?php
// collection of functions
require_once("info.php");
require_once("doValence.php");

// Checks user's browser, returns true if it is Safari
function isSafari() {
    return (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') && !strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome'));
}

//returns true if user has auditors
function hasAuditor($userId){
    global $config;
    $response = doValenceRequest('GET', '/d2l/api/le/'.$config['LE_Version'].'/auditing/auditees/'.$userId);
    if (count($response['response']->Auditors)==0) return false; else return true; 
}

// Returns the list of auditors for the user in the following format : advisors['2222-example@email.com']=Full Name;
function getAdvisors($orgUnitId, $groupCategoryId){
    global $config;
    $classlist = array();
    $advisors = array();

    //paged award classlist api call
    $url = '/d2l/api/bas/1.1/orgunits/'.$orgUnitId.'/classlist/?awardType=1&limit=200';
    while ($url !=null){
        $response = doValenceRequest('GET', $url);
        $classlist = array_merge($classlist, $response['response']->Objects);
        $url = substr($response['response']->Next, strpos($response['response']->Next, "/d2l"));
    }

    foreach($classlist as $user){
        // Check if IssuedAwards exist and iterate through them
        if (isset($user['IssuedAwards']['Objects'])) {
            // foreach ($user['IssuedAwards']['Objects'] as $issued_award) {
            //     // Check if AwardId equals 146
            //     if ($issued_award['Award']['AwardId'] == 146) {
            //         // Add UserId and DisplayName to the array
            //         $advisors[] = array(
            //             'UserId' => $user['UserId'],
            //             'DisplayName' => $user['DisplayName']
            //         );
            //         // Break the loop if UserId with AwardId 146 is found
            //         break;
            //     }
            // }
        }
    }
    return $advisors;
}

//add auditor or deletes  an auditor from the user's auditor list
function addDeleteAuditor($verb, $auditorId, $auditeeId){
    global $config;
    return doValenceRequest($verb, '/d2l/api/le/'.$config['LE_Version'].'/auditing/auditors/'.$auditorId.'/auditees/', $auditeeId); 
}

//returns current academic turn
function getCurrentAcademicTerm() {
    $currentMonth = date('n');
    //$currentMonth = 10;
    $currentYear = date('Y');
    if ($currentMonth >= 1 && $currentMonth <= 4) {
        return "Winter-".$currentYear;
    } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
        return "Summer-" . $currentYear;
    } else {
        return "Fall-" . $currentYear;
    }
}

// returns user's auditors as a string with comma delimeter
function getMyAuditors($auditeeId){
    global $config;
    $result = "";
    $response = doValenceRequest('GET', '/d2l/api/le/'.$config['LE_Version'].'/auditing/auditees/'.$auditeeId); 
    if (!empty($response['response']->Auditors)){
        forEach ($response['response']->Auditors as $auditor){
            $result .= $auditor->AuditorId . ",";
        }
        $result = rtrim($result,",");
    }
    return $result;
}

//creates a group category in LMS
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
}

//creates a group in the LMS
function createGroup($orgUnitId, $groupCategoryId, $currentTerm){
    global $config;
    $data = array(
        "Name" => $currentTerm,
        "Code" => $currentTerm,
        "Description" => array("Content"=>"", "Type"=>"Html")
    );
    $response = doValenceRequest('POST', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/'.$groupCategoryId.'/groups/', $data);
    return $response->GroupId;
}

//Returns group categotyId with "Early Alert" string in its name, if not found calls create function
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
        createGroupCategory($orgUnitId);
        $groupCategoryId = getGroupCategoryId($orgUnitId);
    }
    return $groupCategoryId;
}

//returns group id based on the currrent term.
//checks if there is a group for a current term
//if there is none, then creates one
//if other groups found, then calls delete 
function getGroupId($orgUnitId, $categoryId, $currentTerm){
    global $config;
    $groupId = -1;
    $groups =  doValenceRequest('GET', '/d2l/api/lp/'.$config["LP_Version"].'/'.$orgUnitId.'/groupcategories/'.$categoryId.'/groups/');
    foreach ($groups['response'] as $group) {
        if ($group->Code == $currentTerm) {
            $groupId = $group->GroupId;
        }
        else {
            deletePastTerms($orgUnitId, $categoryId,  $group->GroupId, $group->Enrollments);
        }
    }
    if ($groupId==-1){
        $groupId = createGroup($orgUnitId, $categoryId, $currentTerm);
    }
    return  $groupId;
}


//enrolls user into the group
function enrollToGroup($orgUnitId, $groupCategoryId, $groupId, $userId){
    global $config;
    $data = array(
        "UserId" => $userId
    );
    $response = doValenceRequest('POST', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/'.$groupCategoryId.'/groups/'.$groupId.'/enrollments/', $data);
}

//unenrolls user from the group
function unEnrollFromGroup($orgUnitId, $groupCategoryId, $groupId, $userId){
    global $config;
    $response = doValenceRequest('DELETE', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/'.$groupCategoryId.'/groups/'.$groupId.'/enrollments/'.$userId);
}

//deletes a group, and removes auditors from the all users in the group
function deletePastTerms($orgUnitId, $categoryId,  $groupId, $enrollments){
    global $config;
    // unenroll users before  deleting the group.
    foreach ($enrollments as $userId){
        $auditors = getMyAuditors($userId);
        $myAuditors = explode(',', $auditors);
        foreach ($myAuditors as $auditorId) {
            $response = addDeleteAuditor("DELETE",$auditorId, $userId);
        }    
    }
    $deleteGroup = doValenceRequest('DELETE', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/'.$categoryId.'/groups/'.$groupId);
}

//sends an email notification to the advisor
function sendEmail($auditeeName, $sendTo){
    global $subject, $email_template, $support_email;
    
    $headers  = "From:".$support_email ."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $message = str_replace('username', $auditeeName, $email_template);

    $mail_success = mail($sendTo, $subject, $message, $headers);
}

?>