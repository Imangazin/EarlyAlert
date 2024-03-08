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
function getAdvisors($orgUnitId){
    global $config;
    $qparams = array("roleId"=>109);
    $response = doValenceRequest('GET', '/d2l/api/lp/'.$config['LP_Version'].'/enrollments/orgUnits/'.$orgUnitId.'/users/?roleId=109');
    $advisors = array();
    foreach($response['response']->Items as $advisor){
        $id = $advisor->User->Identifier;
        $fullName = $advisor->User->DisplayName;
        $email = $advisor->User->EmailAddress;
        $advisors[$id.'-'.$email] = $fullName;
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

//Earlier version of the tool that creates groups based on the available semesters in the LMS
//left here in case we would like to use it

// function getGroups($orgUnitId, $categoryId){
//     global $config;
    
//     // Collecting terms info, paged response
//     $hasMore = true;
//     $bookmark = '';
//     $data = array();
//     while ($hasMore){
//         $terms_response = doValenceRequest('GET', '/d2l/api/lp/'.$config['LP_Version'].'/orgstructure/?orgUnitType=5&orgUnitCode=2023&bookmark='.$bookmark);
//         foreach($terms_response['response']->Items as $item){
//             array_push($data, array('Name'=>$item->Name, 'Code' => $item->Code));
//         }
//         $hasMore = $terms_response['response']->PagingInfo->HasMoreItems;
//         $bookmark = $terms_response['response']->PagingInfo->Bookmark;
//     }

//     // Get the current year and month
//     $currentYear = date('Y');
//     $currentMonth = date('n');
//     $months = array('SP'=>6, 'SU'=>8, 'FW'=>12);

//     $groups_response =  doValenceRequest('GET', '/d2l/api/lp/'.$config["LP_Version"].'/'.$orgUnitId.'/groupcategories/'.$categoryId.'/groups/');

//     // Create an array to store the results
//     $terms = array();

//     foreach ($data as $entry) {
//         $code = $entry['Code'];
//         // Extract year and term from the "Code" field
//         $year = substr($code, 0, 4);
//         $term = substr($code, -2);
//         $termMonths = $months[$term];
//         if ($year > $currentYear) {
//             $groupId = getGroupId($orgUnitId, $categoryId, $groups_response['response'], $entry['Name'], $code);
//             array_push($terms, array('Code' => $code, 'Name' => $entry['Name'], 'groupId' => $groupId));
//         }elseif($year == $currentYear && $termMonths>=$currentMonth){
//             $groupId = getGroupId($orgUnitId, $categoryId, $groups_response['response'], $entry['Name'], $code);
//             array_push($terms, array('Code' => $code, 'Name' => $entry['Name'], 'groupId' => $groupId));
//         }elseif($year+1==$currentYear && $termMonths==12 && $currentMonth<5){
//             $groupId = getGroupId($orgUnitId, $categoryId, $groups_response['response'], $entry['Name'], $code);
//             array_push($terms, array('Code' => $code, 'Name' =>$entry['Name'], 'groupId' => $groupId));
//         }
//     }

//     return $terms;
// }

// function getGroupId($orgUnitId, $categoryId, $groups, $name, $code){
//     $groupId = -1;
//     foreach ($groups as $group) {
//         if ($group->Code == $code) {
//             $groupId = $group->GroupId;
//             return  $groupId;
//         }
//     }
//     if ($groupId==-1){
//         $groupId = createGroup($orgUnitId, $categoryId, $name, $code);
//     }
//     return  $groupId;
// }

//returns group id based on the currrent term.
//checks if there is a group for a current term
//if there is none, then creates one
//if other groups found, then calls delete 
function getGroupId($orgUnitId, $categoryId, $currentTerm, $myAuditors){
    global $config;
    $groupId = -1;
    $groups =  doValenceRequest('GET', '/d2l/api/lp/'.$config["LP_Version"].'/'.$orgUnitId.'/groupcategories/'.$categoryId.'/groups/');
    foreach ($groups['response'] as $group) {
        if ($group->Code == $currentTerm) {
            $groupId = $group->GroupId;
        }
        else {
            deletePastTerms($orgUnitId, $categoryId,  $group->GroupId, $group->Enrollments, $myAuditors);
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
function deletePastTerms($orgUnitId, $categoryId,  $groupId, $enrollments, $auditors){
    global $config;
    // unenroll users before  deleting the group.
    $myAuditors = explode(',', $auditors);
    
    foreach ($enrollments as $userId){
        foreach ($myAuditors as $auditorId) {
            $response = addDeleteAuditor("DELETE",$auditorId, $userId);
        }    
    }
    echo 'auditor: '.$auditorId.'.  auditeeId: '.$auditeeId.'<br>';
    $deleteGroup = doValenceRequest('DELETE', '/d2l/api/lp/'.$config['LP_Version'].'/'.$orgUnitId.'/groupcategories/'.$categoryId.'/groups/'.$groupId);
}

//sends an email notification to the advisor
function sendEmail($auditeeName, $sendTo){
    global $subject, $emailTemplate;
    $message = str_replace('username', $auditeeName, $emailTemplate);
    $headers = "From:". $supportEmail . "\r\n";
    $headers .= "Content-Type: text/html;charset=utf-8\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";


    $mail_success = mail($sendTo, $subject, $message, $headers);
}

?>
w