<?php
include "init.php";
include "$apps_path[libs]/function.php";
error_log("resend.php " . print_r($_GET, true));

// this url should only be called from
// playsms itself, so it should always
// be from the server
//
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
	die();
}

$smslog_id= $_GET[smslog_id];
error_log("got resend request $smslog_id");

resend($smslog_id);

?>
