<?php
include "init.php";
include "$apps_path[libs]/function.php";

$inc = $_GET[inc];
$err = $_GET[err];
$username = $_COOKIE[vc2];
$uid = username2uid($username);
$mobile = username2mobile($username);
$email = username2email($username);

if (!isadmin()) {
	forcelogout();
};

include "html_header.php";

switch ($inc) {
	case "user_mgmnt" :
		include $apps_path[incs] . "/admin/user_mgmnt.php";
		break;
	case "main_config" :
		include $apps_path[incs] . "/admin/main_config.php";
		break;
	case "sms_command" :
		include $apps_path[incs] . "/feat/sms_command.php";
		break;
	case "sms_autosend" :
		include $apps_path[incs] . "/feat/sms_autosend.php";
		break;
	case "daemon" :
		$manual = 1;
		include $apps_path[base] . "/daemon.php";
		break;
	case "gwmod_template" :
		// include $apps_path[plug]."/gateway/template/manage.php";
		break;
	case "gwmod_clickatell" :
		include $apps_path[plug] . "/gateway/clickatell/manage.php";
		break;
	case "gwmod_gnokii" :
		include $apps_path[plug] . "/gateway/gnokii/manage.php";
		break;
	case "gwmod_kannel" :
		include $apps_path[plug] . "/gateway/kannel/manage.php";
		break;
	case "gwmod_uplink" :
		include $apps_path[plug] . "/gateway/uplink/manage.php";
		break;
}

include "html_footer.php";
?>
