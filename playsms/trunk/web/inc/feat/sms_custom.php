<?
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];

switch ($op) {
	case "sms_custom_list" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>List/Edit/Delete SMS customs</h2>
		    <p>
		    <a href=\"menu.php?inc=sms_custom&op=sms_custom_add\">[ Add SMS custom ]</a>
		    <p>
		";
		if (!isadmin()) {
			$query_user_only = "WHERE uid='$uid'";
		}
		$db_query = "SELECT * FROM playsms_featCustom $query_user_only ORDER BY custom_code";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$owner = uid2username($db_row[uid]);
			$content .= "[<a href=menu.php?inc=sms_custom&op=sms_custom_edit&custom_id=$db_row[custom_id]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete SMS custom code `$db_row[custom_code]` ?','menu.php?inc=sms_custom&op=sms_custom_del&custom_id=$db_row[custom_id]')\">x</a>] <b>Code:</b> $db_row[custom_code] &nbsp;&nbsp;<b>User:</b> $owner<br><b>URL:</b><br>" . stripslashes($db_row[custom_url]) . "<br><br>";
		}
		echo $content;
		echo "
		    <p>
		    <a href=\"menu.php?inc=sms_custom&op=sms_custom_add\">[ Add SMS custom ]</a>
		";
		break;
	case "sms_custom_edit" :
		$custom_id = $_GET[custom_id];
		$db_query = "SELECT * FROM playsms_featCustom WHERE custom_id='$custom_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$edit_custom_code = $db_row[custom_code];
		$edit_custom_url = stripslashes($db_row[custom_url]);
		$edit_custom_url = str_replace($feat_custom_path['bin'], '', $edit_custom_url);
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>Edit SMS custom</h2>
		    <p>
		    <form action=menu.php?inc=sms_custom&op=sms_custom_edit_yes method=post>
		    <input type=hidden name=edit_custom_id value=$custom_id>
		    <input type=hidden name=edit_custom_code value=$edit_custom_code>
		    <p>SMS custom code: <b>$edit_custom_code</b>
		    <p>Pass these parameter to custom URL field:
		    <p>##SMSDATETIME## replaced by SMS incoming date/time
		    <p>##SMSSENDER## replaced by sender number
		    <p>##CUSTOMCODE## replaced by custom code 
		    <p>##CUSTOMPARAM## replaced by custom parameter passed to server from SMS
		    <p>SMS custom URL: <input type=text size=60 name=edit_custom_url value=\"$edit_custom_url\">
		    <p><input type=submit class=button value=Save>
		    </form>
		";
		echo $content;
		break;
	case "sms_custom_edit_yes" :
		$edit_custom_id = $_POST[edit_custom_id];
		$edit_custom_code = $_POST[edit_custom_code];
		$edit_custom_url = $_POST[edit_custom_url];
		if ($edit_custom_id && $edit_custom_code && $edit_custom_url) {
			$db_query = "UPDATE playsms_featCustom SET custom_url='$edit_custom_url' WHERE custom_code='$edit_custom_code' AND uid='$uid'";
			if (@ dba_affected_rows($db_query)) {
				$error_string = "SMS custom code `$edit_custom_code` has been saved";
			} else {
				$error_string = "Fail to save SMS custom code `$edit_custom_code`";
			}
		} else {
			$error_string = "You must fill all fields!";
		}
		header("Location: menu.php?inc=sms_custom&op=sms_custom_edit&custom_id=$edit_custom_id&err=" . urlencode($error_string));
		break;
	case "sms_custom_del" :
		$custom_id = $_GET[custom_id];
		$db_query = "SELECT custom_code FROM playsms_featCustom WHERE custom_id='$custom_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$code_name = $db_row[custom_code];
		if ($code_name) {
			$db_query = "DELETE FROM playsms_featCustom WHERE custom_code='$code_name'";
			if (@ dba_affected_rows($db_query)) {
				$error_string = "SMS custom code `$code_name` has been deleted!";
			} else {
				$error_string = "Fail to delete SMS custom code `$code_name`";
			}
		}
		header("Location: menu.php?inc=sms_custom&op=sms_custom_list&err=" . urlencode($error_string));
		break;
	case "sms_custom_add" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>Add SMS custom</h2>
		    <p>
		    <form action=menu.php?inc=sms_custom&op=sms_custom_add_yes method=post>
		    <p>SMS custom code: <input type=text size=10 maxlength=10 name=add_custom_code value=\"$add_custom_code\">
		    <p>Pass these parameter to custom URL field:
		    <p>##SMSDATETIME## replaced by SMS incoming date/time
		    <p>##SMSSENDER## replaced by sender number
		    <p>##CUSTOMCODE## replaced by custom code 
		    <p>##CUSTOMPARAM## replaced by custom parameter passed to server from SMS
		    <p>SMS custom URL: <input type=text size=60 maxlength=200 name=add_custom_url value=\"$add_custom_url\">
		    <p><input type=submit class=button value=Add>
		    </form>
		";
		echo $content;
		break;
	case "sms_custom_add_yes" :
		$add_custom_code = strtoupper($_POST[add_custom_code]);
		$add_custom_url = $_POST[add_custom_url];
		if ($add_custom_code && $add_custom_url) {
			if (checkavailablecode($add_custom_code)) {
				$db_query = "INSERT INTO playsms_featCustom (uid,custom_code,custom_url) VALUES ('$uid','$add_custom_code','$add_custom_url')";
				if ($new_uid = @ dba_insert_id($db_query)) {
					$error_string = "SMS custom code `$add_custom_code` has been added";
				} else {
					$error_string = "Fail to add SMS custom code `$add_custom_code`";
				}
			} else {
				$error_string = "SMS code `$add_custom_code` already exists, reserved or use by other feature!";
			}
		} else {
			$error_string = "You must fill all fields!";
		}
		header("Location: menu.php?inc=sms_custom&op=sms_custom_add&err=" . urlencode($error_string));
		break;
}
?>
