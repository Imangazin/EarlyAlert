<?php
// Lti credentials
$lti_auth = array('key'=>'', 'secret'=>'');
//Cookie location
$cookie_location = '';
//Support email adress
$support_email = ''; 

//Award set-up
//Advisor award must be already available across the organization
//Then add it to the course offering where the widget will be installed
//Copy the awardId from the award edit page url
$awardId=0;

//Email settings
$subject = '';
$email_template = '';
//Widget language
$widget_consent_message = '';
$widget_cancel_message = '';
//Widget feedback language
$consent_feedback = '';
$cancel_feedback = '';

//BLE api credentials
$config = array(
    'host' => '',
    'port' => 443,
    'scheme' => 'https',
    'appId' => '',
    'appKey' => '',
    'userId' => '',
    'userKey' => '',
    'LP_Version' =>'1.45',
    'LE_Version' => '1.74' 
);
?>