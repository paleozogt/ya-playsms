<?php
include "init.php";
include "$apps_path[libs]/function.php";

// this url should only be called from
// playsms itself, so it should always
// be from localhost
//
$remote_addr = $_SERVER["REMOTE_ADDR"];
if ($remote_addr != "127.0.0.1") {
	die();
}

$smslog_id= $_GET[smslog_id];
error_log("got resend request $smslog_id");

resend($smslog_id);

?>
