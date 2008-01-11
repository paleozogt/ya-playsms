<?
include "init.php";
include "$apps_path[libs]/function.php";

$inc = $_GET[inc];
$err = $_GET[err];
$username = $_COOKIE[vc2];
$uid = username2uid($username);
$sender = username2sender($username);
$mobile = username2mobile($username);
$email = username2email($username);
$name = username2name($username);
$status = username2status($username);

if (!valid()) {
	forcelogout();
};

include "html_header.php";

switch ($inc) {
	case "dir_create" :
		include $apps_path[incs] . "/user/dir_create.php";
		break;
	case "dir_edit" :
		include $apps_path[incs] . "/user/dir_edit.php";
		break;
	case "phone_add" :
		include $apps_path[incs] . "/user/phone_add.php";
		break;
	case "phone_del" :
		include $apps_path[incs] . "/user/phone_del.php";
		break;
	case "send_sms" :
		include $apps_path[incs] . "/user/send_sms.php";
		break;
	case "get_status" :
		include $apps_path[incs] . "/user/get_status.php";
		break;
	case "user_inbox" :
		include $apps_path[incs] . "/user/user_inbox.php";
		break;
	case "user_pref" :
		include $apps_path[incs] . "/user/user_pref.php";
		break;
	case "sms_autoreply" :
		include $apps_path[incs] . "/feat/sms_autoreply.php";
		break;
	case "sms_autoreply_scenario" :
		include $apps_path[incs] . "/feat/sms_autoreply_scenario.php";
		break;
	case "sms_board" :
		include $apps_path[incs] . "/feat/sms_board.php";
		break;
	case "sms_custom" :
		include $apps_path[incs] . "/feat/sms_custom.php";
		break;
	case "sms_poll" :
		include $apps_path[incs] . "/feat/sms_poll.php";
		break;
	case "phonebook" :
		include $apps_path[incs] . "/user/phonebook.php";
		break;
	case "phonebook_public" :
		include $apps_path[incs] . "/user/phonebook_public.php";
		break;
	case "phonebook_exim" :
		include $apps_path[incs] . "/user/phonebook_exim.php";
		break;
	case "sms_template" :
		include $apps_path[incs] . "/user/sms_template.php";
		break;

}

include "html_footer.php";
?>
