<?php
//Quick Add LTI info.php

//require_once 'ims-blti/blti.php';

//The LTI Credentials for index.php

//You need to set the LTI Key and LTI Secret
$lti_auth = array('key' => '', 'secret' => '');
$cookie_loation = '';
//Support Email shown to users in the tool
$supportEmail = "";

$subject = 'Early Alert: You have been added as a class progress auditor in Brightspace';

$emailTemplate = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xml:lang="en-CA" lang="en-CA">
<head>
<title>Early Alert Notification</title>
</head>
<body style="font-family:sans-serif;">

<p>Greetings!</p>
<p>The student, {insert username here}, has added you as an advisor/auditor on their profile in Brightspace. Access the class progress for this student through the <a href="https://brocktest.brightspace.com/d2l/le/auditors/index.d2l?ou=6606" target="_blank">Auditors</a> widget on Brightspace\'s homepage.</p> 
<p>You can learn more about <a href="" target="_blank">Auditing students using Brightspace</a> on the Brightspace documentations.</p>
</body>
</html>';

$widget_consent_message = '<p>Your <a href="https://cpibrock.atlassian.net/wiki/spaces/BLEstudents/pages/1040810044/Using+Brightspace#Can-I-view-my-class-progress?" target="_blank">Brightspace Progress page</a> contains your grades, system access, and course activity across all your enrolled courses (current and past).</p>
<p>By selecting your advisor from the dropdown menu and then clicking the button below, you consent to giving your academic advisor access to your Progress page for all current and past courses. The advisor retains access to your Progress page until the end of the current semester or until you choose to revoke access yourself.</p>';
$widget_cancel_message = '<p>You have already consented to sharing your Progress page with your advisor. The advisor retains access to your Progress page until the end of the current semester. If at any point you would like to revoke this access, please click the Cancel button below.</p>';
$consent_feedback = 'Your advisor have access to all course progresses.';
$cancel_feedback = 'Your advisor(s) no longer have access to all course progresses.';

$config = array(
    'libpath'    => 'lib',

    'host'       => '',
    'port'       => 443,
    'scheme'     => 'https',

    'appId'      => '',
    'appKey'     => '',
    'userId'     => '',
    'userKey'    => '',

    'LP_Version' => '1.45',
    'LE_Version' => '1.74');
?>