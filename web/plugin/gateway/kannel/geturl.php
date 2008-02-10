<?php

chdir("../../../");
include "init.php";
include "$apps_path[libs]/function.php";
chdir("plugin/gateway/kannel");

$remote_addr = $_SERVER["REMOTE_ADDR"];
if ($remote_addr != $kannel_param['bearerbox_host']) {
	die();
}

$t = $_GET['t'];
$q = $_GET['q'];
$a = $_GET['a'];

// if there's no datetime param,
// then use the current date
if (empty ($t))
	$t = date("Y-m-d H:i");

if ($t && $q && $a) {
	$sms_datetime = trim($t);
	$sms_sender = trim($q);
	$message = trim($a);
	setsmsincomingaction($sms_datetime, $sms_sender, $message);
}
?>
