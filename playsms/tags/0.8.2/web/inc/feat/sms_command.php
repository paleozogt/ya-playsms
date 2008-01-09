<?
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];

switch ($op) {
	case "sms_command_list" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>List/Edit/Delete SMS commands</h2>
		    <p>
		    <a href=\"menu_admin.php?inc=sms_command&op=sms_command_add\">[ Add SMS command ]</a>
		    <p>
		";
		if (!isadmin()) {
			$query_user_only = "WHERE uid='$uid'";
		}
		$db_query = "SELECT * FROM playsms_featCommand $query_user_only ORDER BY command_code";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$owner = uid2username($db_row[uid]);
			$content .= "[<a href=menu_admin.php?inc=sms_command&op=sms_command_edit&command_id=$db_row[command_id]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete SMS command code `$db_row[command_code]` ?','menu_admin.php?inc=sms_command&op=sms_command_del&command_id=$db_row[command_id]')\">x</a>] <b>Code:</b> $db_row[command_code] &nbsp;&nbsp;<b>User:</b> $owner<br><b>Exec:</b><br>" . stripslashes($db_row[command_exec]) . "<br><br>";
		}
		echo $content;
		echo "
		    <p>
		    <a href=\"menu_admin.php?inc=sms_command&op=sms_command_add\">[ Add SMS command ]</a>
		";
		break;
	case "sms_command_edit" :
		$command_id = $_GET[command_id];
		$db_query = "SELECT * FROM playsms_featCommand WHERE command_id='$command_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$edit_command_code = $db_row[command_code];
		$edit_command_exec = stripslashes($db_row[command_exec]);
		$edit_command_exec = str_replace($feat_command_path['bin'], '', $edit_command_exec);
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>Edit SMS command</h2>
		    <p>
		    <form action=menu_admin.php?inc=sms_command&op=sms_command_edit_yes method=post>
		    <input type=hidden name=edit_command_id value=$command_id>
		    <input type=hidden name=edit_command_code value=$edit_command_code>
		    <p>SMS command code: <b>$edit_command_code</b>
		    <p>Pass these parameter to command exec field:
		    <p>##SMSDATETIME## replaced by SMS incoming date/time
		    <p>##SMSSENDER## replaced by sender number
		    <p>##COMMANDCODE## replaced by command code 
		    <p>##COMMANDPARAM## replaced by command parameter passed to server from SMS
		    <p>SMS command binary path : <b>" . $feat_command_path['bin'] . "</b>
		    <p>SMS command exec: <input type=text size=60 name=edit_command_exec value=\"$edit_command_exec\">
		    <p><input type=submit class=button value=Save>
		    </form>
		";
		echo $content;
		break;
	case "sms_command_edit_yes" :
		$edit_command_id = $_POST[edit_command_id];
		$edit_command_code = $_POST[edit_command_code];
		$edit_command_exec = $_POST[edit_command_exec];
		if ($edit_command_id && $edit_command_code && $edit_command_exec) {
			$edit_command_exec = $feat_command_path['bin'] . "/" . $edit_command_exec;
			$edit_command_exec = str_replace("//", "/", $edit_command_exec);
			$edit_command_exec = str_replace("..", ".", $edit_command_exec);
			$db_query = "UPDATE playsms_featCommand SET command_exec='$edit_command_exec' WHERE command_code='$edit_command_code' AND uid='$uid'";
			if (@ dba_affected_rows($db_query)) {
				$error_string = "SMS command code `$edit_command_code` has been saved";
			} else {
				$error_string = "Fail to save SMS command code `$edit_command_code`";
			}
		} else {
			$error_string = "You must fill all fields!";
		}
		header("Location: menu_admin.php?inc=sms_command&op=sms_command_edit&command_id=$edit_command_id&err=" . urlencode($error_string));
		break;
	case "sms_command_del" :
		$command_id = $_GET[command_id];
		$db_query = "SELECT command_code FROM playsms_featCommand WHERE command_id='$command_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$code_name = $db_row[command_code];
		if ($code_name) {
			$db_query = "DELETE FROM playsms_featCommand WHERE command_code='$code_name'";
			if (@ dba_affected_rows($db_query)) {
				$error_string = "SMS command code `$code_name` has been deleted!";
			} else {
				$error_string = "Fail to delete SMS command code `$code_name`";
			}
		}
		header("Location: menu_admin.php?inc=sms_command&op=sms_command_list&err=" . urlencode($error_string));
		break;
	case "sms_command_add" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>Add SMS command</h2>
		    <p>
		    <form action=menu_admin.php?inc=sms_command&op=sms_command_add_yes method=post>
		    <p>SMS command code: <input type=text size=10 maxlength=10 name=add_command_code value=\"$add_command_code\">
		    <p>Pass these parameter to command exec field:
		    <p>##SMSDATETIME## replaced by SMS incoming date/time
		    <p>##SMSSENDER## replaced by sender number
		    <p>##COMMANDCODE## replaced by command code 
		    <p>##COMMANDPARAM## replaced by command parameter passed to server from SMS
		    <p>SMS command binary path : <b>" . $feat_command_path['bin'] . "</b>
		    <p>SMS command exec: <input type=text size=60 maxlength=200 name=add_command_exec value=\"$add_command_exec\">
		    <p><input type=submit class=button value=Add>
		    </form>
		";
		echo $content;
		break;
	case "sms_command_add_yes" :
		$add_command_code = strtoupper($_POST[add_command_code]);
		$add_command_exec = $_POST[add_command_exec];
		if ($add_command_code && $add_command_exec) {
			$add_command_exec = $feat_command_path['bin'] . "/" . $add_command_exec;
			$add_command_exec = str_replace("//", "/", $add_command_exec);
			$add_command_exec = str_replace("..", ".", $add_command_exec);
			if (checkavailablecode($add_command_code)) {
				$db_query = "INSERT INTO playsms_featCommand (uid,command_code,command_exec) VALUES ('$uid','$add_command_code','$add_command_exec')";
				if ($new_uid = @ dba_insert_id($db_query)) {
					$error_string = "SMS command code `$add_command_code` has been added";
				} else {
					$error_string = "Fail to add SMS command code `$add_command_code`";
				}
			} else {
				$error_string = "SMS code `$add_command_code` already exists, reserved or use by other feature!";
			}
		} else {
			$error_string = "You must fill all fields!";
		}
		header("Location: menu_admin.php?inc=sms_command&op=sms_command_add&err=" . urlencode($error_string));
		break;
}
?>
