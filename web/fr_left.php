<?php
include "init.php";
include "$apps_path[libs]/function.php";

if (!valid()) {
	forcelogout();
};

$username = $_COOKIE[vc2];
$uid = username2uid($username);
$status = username2status($username);

if (isadmin()) {
	$admin_menu = "
	<h2>Administration:</h2>
    <li><a href=\"menu.php?inc=user_inbox&op=user_inbox&showall=1\" target=fr_right>Inbox (All Unrouted)</a></li>
    <li><a href=\"menu.php?inc=get_status&op=get_status&showall=1\" target=fr_right>Outbox (All Sent)</a></li>
    <li><a href=menu_admin.php?inc=user_mgmnt&op=user_list target=fr_right>Manage Users</a></li>
	<li><a href=menu_admin.php?inc=main_config&op=main_config target=fr_right>Main configuration</a></li>
	<li><a href=menu_admin.php?inc=daemon&op=daemon target=fr_right>Daemon manual refresh</a></li>
	<p>
    ";
	$admin_feat = "
	<li><a href=\"menu_admin.php?inc=sms_command&op=sms_command_list\" target=fr_right>Manage SMS commands</a></li>
	<li><a href=\"menu_admin.php?inc=sms_autosend&op=list\" target=fr_right>Manage SMS autosend</a></li>
    ";

	$admin_gwmod = "";
	/*"
	<h2>Gateway Module:</h2>
	<li><a href=menu_admin.php?inc=gwmod_clickatell&op=manage target=fr_right>Manage clickatell</a></li>
	<li><a href=menu_admin.php?inc=gwmod_gnokii&op=manage target=fr_right>Manage gnokii</a></li>
	<li><a href=menu_admin.php?inc=gwmod_kannel&op=manage target=fr_right>Manage kannel</a></li>
	<li><a href=menu_admin.php?inc=gwmod_uplink&op=manage target=fr_right>Manage uplink</a></li>
	<p>
	";*/
}

$content = "
    <p><b>Hello $username..</b>
    <p>
    <h2>Personal:</h2>
    <li><a href=user.php target=_top>Phonebook</a></li>
    <li><a href=menu.php?inc=phonebook_public target=fr_right>Public phonebook</a></li>
    <li><a href=menu.php?inc=sms_template&op=list target=fr_right>Message template</a></li>
    <li><a href=menu.php?inc=send_sms&op=sendsmstopv target=fr_right>Send text SMS</a></li>
    <li><a href=menu.php?inc=send_sms&op=sendsmstogr target=fr_right>Send broadcast SMS</a></li>
    <li><a href=menu.php?inc=user_inbox&op=user_inbox target=fr_right>Inbox</a></li>
    <li><a href=menu.php?inc=get_status&op=get_status target=fr_right>Outbox</a></li>
 
    <li><a href=menu.php?inc=user_pref&op=user_pref target=fr_right>Preferences</a></li>
    <p>
    <h2>Features:</h2>
    <li><a href=menu.php?inc=sms_autoreply&op=sms_autoreply_list target=fr_right>Manage SMS autoreplies</a></li>
    <li><a href=menu.php?inc=sms_board&op=sms_board_list target=fr_right>Manage SMS boards</a></li>
    $admin_feat
    <li><a href=menu.php?inc=sms_custom&op=sms_custom_list target=fr_right>Manage SMS customs</a></li>
    <li><a href=menu.php?inc=sms_poll&op=sms_poll_list target=fr_right>Manage SMS polls</a></li>
    <p>
    $admin_gwmod
    $admin_menu
    <p>
    <h2>Help:</h2>
    <li><a href=docs/FAQ target=_blank>FAQ</a></li>
    <li><a href=docs/README target=_blank>README</a></li>
    <li><a href=docs/INSTALL target=_blank>INSTALL</a></li>
    <li><a href=docs/CHANGELOG target=_blank>CHANGELOG</a></li>
    <p>
    <li><a href=logout.php target=_top>Logout</a></li>
";

include "html_header.php";

echo $content;

include "html_footer.php";
?>
