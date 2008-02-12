<?php
chdir("../../../");
include "init.php";
include "$apps_path[libs]/function.php";
chdir("plugin/gateway/kannel");

$remote_addr = $_SERVER["REMOTE_ADDR"];
if ($remote_addr != $kannel_param['bearerbox_host']) {
    die();
}

$smslog_id = $_GET[smslog_id];
$uid       = $_GET[uid];
$kannel_dlr= $_GET[dlr];

if (isset($kannel_dlr, $smslog_id, $uid)) {
	kannel_gw_set_delivery_status($smslog_id, $uid, $kannel_dlr);
}
?>
