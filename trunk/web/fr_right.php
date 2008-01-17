<?php
include "init.php";
include "$apps_path[libs]/function.php";

if (!valid()) {
	forcelogout();
};

$username = $_COOKIE[vc2];
$uid = username2uid($username);
$err = $_GET[err];

/*
$db_query = "SELECT * FROM playsms_tblUserGroupPhonebook WHERE uid='$uid'";
$db_result = dba_query($db_query);
while ($db_row = dba_fetch_array($db_result))
{
    $gpid = $db_row[gpid];
    $list_of_phonenumber .= "<font size=+1>[<a href=\"javascript:ConfirmURL('Are you sure you want to delete group `$db_row[gp_name]` with all its members ?','menu.php?inc=phone_del&op=group&gpid=$gpid')\">x</a>] Group: <font color=darkgreen>$db_row[gp_name]</font> - code: <font color=darkgreen>$db_row[gp_code]</font> [<a href=\"javascript: PopupSendSms('BC','$db_row[gp_code]')\">send</a>]</font><br>\n";
    $db_query1 = "SELECT * FROM playsms_tblUserPhonebook WHERE gpid='$gpid' AND uid='$uid'";
    $db_result1 = dba_query($db_query1);
    while ($db_row1 = dba_fetch_array($db_result1))
    {
	$list_of_phonenumber .= "[<a href=\"javascript:ConfirmURL('Are you sure you want to delete mobiles number `$db_row1[p_num]` owned by `$db_row1[p_desc]` ?','menu.php?inc=phone_del&op=user&pid=$db_row1[pid]')\">x</a>] <font size=-1>Number: <font color=darkgreen>$db_row1[p_num]</font> - Owner: <font color=darkgreen>$db_row1[p_desc]</font> [<a href=\"javascript: PopupSendSms('PV','$db_row1[p_num]')\">send</a>]<br>\n";
    }
    $list_of_phonenumber .= "<br>";
}
*/

$db_query = "SELECT * FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' ORDER BY gp_name";
$db_result = dba_query($db_query);
while ($db_row = dba_fetch_array($db_result)) {
	$gpid = $db_row[gpid];
	$fm_name = "fm_phonebook_" . $db_row[gp_code];
	$db_query1 = "SELECT gpidpublic FROM playsms_tblUserGroupPhonebook_public WHERE uid='$uid' AND gpid='$gpid'";
	$db_result1 = dba_num_rows($db_query1);
	if ($db_result1 > 0) {
		$option_public = "<a href=\"menu.php?inc=phonebook&op=hide_from_public&gpid=$gpid\">[ hide from public ]</a>";
	} else {
		$option_public = "<a href=\"menu.php?inc=phonebook&op=share_this_group&gpid=$gpid\">[ share this group ]</a>";
	}
	$option_group_edit = "<a href=\"menu.php?inc=dir_edit&op=edit&gpid=$gpid\">[ edit ]</a>";

	// WWW 041208
	$option_group_export = "<a href=\"menu.php?inc=phonebook_exim&op=export&gpid=$gpid\">[ export ]</a>";
	$option_group_import = "<a href=\"menu.php?inc=phonebook_exim&op=import&gpid=$gpid\">[ import ]</a>";

	$list_of_phonenumber .= "
		<form name=\"$fm_name\" action=\"menu.php?inc=phonebook\" method=post>
		<p>[<a href=\"javascript:ConfirmURL('Are you sure you want to delete group `$db_row[gp_name]` with all its members ?','menu.php?inc=phone_del&op=group&gpid=$gpid')\">x</a>] Group: $db_row[gp_name] - code: $db_row[gp_code] <a href=\"javascript: PopupSendSms('BC','$db_row[gp_code]')\">[ send ]</a> $option_public $option_group_edit $option_group_export $option_group_import
		<table width=100% cellpadding=0 cellspacing=0 border=1>
		<tr>
		    <td class=box_title width=4>&nbsp;*&nbsp;</td>
		    <td class=box_title width=35%>Owner</td>
		    <td class=box_title width=25%>Number</td>
		    <td class=box_title width=40%>Email</td>
		    <td class=box_title width=4><input type=checkbox onclick=CheckUncheckAll(document." . $fm_name . ")></td>
		</tr>
	    ";
	$db_query1 = "SELECT * FROM playsms_tblUserPhonebook WHERE gpid='$gpid' AND uid='$uid' ORDER BY p_desc";
	$db_result1 = dba_query($db_query1);
	$i = 0;
	while ($db_row1 = dba_fetch_array($db_result1)) {
		// $list_of_phonenumber .= "[<a href=\"javascript:ConfirmURL('Are you sure you want to delete mobiles number `$db_row1[p_num]` owned by `$db_row1[p_desc]` ?','menu.php?inc=phone_del&op=user&pid=$db_row1[pid]')\">x</a>] <font size=-1>Number: <font color=darkgreen>$db_row1[p_num]</font> - Owner: <font color=darkgreen>$db_row1[p_desc]</font> [<a href=\"javascript: PopupSendSms('PV','$db_row1[p_num]')\">send</a>]<br>\n";
		$i++;
		$list_of_phonenumber .= "
			    <tr>
				<td class=box_text width=4>&nbsp;$i.&nbsp;</td>
				<td class=box_text width=35%>&nbsp;$db_row1[p_desc]</td>
				<td class=box_text width=25%>&nbsp;<a href=\"javascript: PopupSendSms('PV','$db_row1[p_num]')\">$db_row1[p_num]</a></td>
				<td class=box_text width=40%>&nbsp;$db_row1[p_email]</td>
				<td class=box_text width=4>
				    <input type=hidden name=pid" . $i . " value=\"" . $db_row1['pid'] . "\">
				    <input type=checkbox name=chkid" . $i . ">
				</td>
			    </tr>
			";
	}
	$option_action = "
		<option value=edit>Edit selections</option>
		<option value=copy>Copy selections</option>
		<option value=move>Move selections</option>
		<option value=delete>Delete selections</option>
	    ";
	$item_count = $i;
	$list_of_phonenumber .= "
		</table>
		<table width=100% cellpadding=0 cellspacing=0 border=0>
		<tr>
		    <td class=box_text width=100% colspan=2 align=right>
		        Select action: <select name=op>$option_action</select> <input type=submit class=button value=\"Go\">
		    </td>
		</tr>
		</table>
		<input type=hidden name=item_count value=\"$item_count\">	
		</form>
		<p>
	    ";
}

$content = "
    <h2>Phonebook</h2>
    <p>
";
if ($err) {
	$content .= "<p><font color=red>$err</font><p>";
}
$content .= "
    <p><a href=\"menu.php?inc=dir_create&op=create\">[ Create Group ]</a>&nbsp;<a href=\"menu.php?inc=phone_add&op=add\">[ Add Number to Group ]</a>&nbsp;<a href=\"menu.php?inc=phonebook_exim&op=export\">[ Export All ]</a>
    <p>$list_of_phonenumber
    <p><a href=\"menu.php?inc=dir_create&op=create\">[ Create Group ]</a>&nbsp;<a href=\"menu.php?inc=phone_add&op=add\">[ Add Number to Group ]</a>&nbsp;<a href=\"menu.php?inc=phonebook_exim&op=export\">[ Export All ]</a>
";

include "html_header.php";

echo $content;

include "html_footer.php";
?>
