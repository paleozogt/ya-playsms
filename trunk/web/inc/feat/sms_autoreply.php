<?
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];
error_log("op=$op");

switch ($op) {
	case "sms_autoreply_list" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		        <h2>List/Manage/Delete SMS autoreplies</h2>
		        <p>
		        <a href=\"menu.php?inc=sms_autoreply&op=sms_autoreply_add\">[ Add SMS autoreply ]</a>
		        <p>
		    ";
		if (!isadmin()) {
			$query_user_only = "WHERE uid='$uid'";
		}
		$db_query = "SELECT * FROM playsms_featAutoreply $query_user_only ORDER BY autoreply_code";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$owner = uid2username($db_row[uid]);
			$content .= "[<a href=menu.php?inc=sms_autoreply&op=sms_autoreply_manage&autoreply_id=$db_row[autoreply_id]>m</a>] " .
			"[<a href=\"javascript: ConfirmURL('Are you sure you want to delete SMS autoreply code `$db_row[autoreply_code]` ?'," .
			"'menu.php?inc=sms_autoreply&op=sms_autoreply_del&autoreply_id=$db_row[autoreply_id]')\">x</a>]" .
			"<b>Code:</b> $db_row[autoreply_code] &nbsp;&nbsp;<b>User:</b> $owner<br><br>";
		}
		echo $content;
		echo "
		        <p>
		        <a href=\"menu.php?inc=sms_autoreply&op=sms_autoreply_add\">[ Add SMS autoreply ]</a>
		    ";
		break;
	case "sms_autoreply_manage" :
		$autoreply_id = $_GET[autoreply_id];
		if (!isadmin()) {
			$query_user_only = "AND uid='$uid'";
		}
		$db_query = "SELECT * FROM playsms_featAutoreply WHERE autoreply_id='$autoreply_id' $query_user_only";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$manage_autoreply_code = $db_row[autoreply_code];
		$o_uid = $db_row[uid];
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		        <h2>Manage SMS autoreply</h2>
		        <p>
		        <p>SMS autoreply code: <b>$manage_autoreply_code</b>
	            <p/>
		        <p>
		        <a href=\"menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_add&autoreply_id=$autoreply_id\">[ Add SMS autoreply scenario ]</a>
	            &nbsp
	            <a href=\"menu.php?inc=sms_autoreply&op=sms_autoreply_edit&autoreply_code=$manage_autoreply_code&autoreply_id=$autoreply_id\">[ Rename ]</a>
		        <p>
		    ";
		$db_query = "SELECT * FROM playsms_featAutoreply_scenario WHERE autoreply_id='$autoreply_id' ORDER BY autoreply_scenario_param1";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$owner = uid2username($o_uid);
			$list_of_param = "";
			for ($i = 1; $i <= 7; $i++) {
				$list_of_param .= $db_row["autoreply_scenario_param$i"] . "&nbsp";
			}
			$content .= "[<a href=menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_edit&autoreply_id=$autoreply_id&autoreply_scenario_id=$db_row[autoreply_scenario_id]>e</a>] " .
			"[<a href=\"javascript: ConfirmURL('Are you sure you want to delete this SMS autoreply scenario ?'," .
			"'menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_del&autoreply_scenario_id=$db_row[autoreply_scenario_id]&autoreply_id=$autoreply_id')\">x</a>] " .
			"<b>Param:</b> $list_of_param&nbsp;<br>" .
			"<b>Return:</b> $db_row[autoreply_scenario_result]&nbsp;&nbsp;<b>User:</b> $owner<br><br>";
		}
		$content .= "
		        <p>
		        </form>
		    ";
		echo $content;
		break;
	case "sms_autoreply_del" :
		$autoreply_id = $_GET[autoreply_id];

		// delete all of the scenarios under this autoreply,
		// then delete the autoreply itself
		$db_query = "DELETE FROM playsms_featAutoreply_scenario WHERE autoreply_id='$autoreply_id'";
		@ dba_affected_rows($db_query);
		$db_query = "DELETE FROM playsms_featAutoreply WHERE autoreply_id='$autoreply_id'";
		if (@ dba_affected_rows($db_query)) {
			$error_string = "SMS autoreply code `$code_name` has been deleted!";
		} else {
			$error_string = "Fail to delete SMS autoreply code `$code_name`";
		}

		header("Location: menu.php?inc=sms_autoreply&op=sms_autoreply_list&err=" . urlencode($error_string));
		break;

	case "sms_autoreply_add" :
		echo makeAddEditForm(true, $err);
		break;
	case "sms_autoreply_add_yes" :
		doAddEdit(true, $_POST[autoreply_code]);
		break;

	case "sms_autoreply_edit" :
		echo makeAddEditForm(false, $err, $_GET[autoreply_code], $_GET[autoreply_id]);
		break;
	case "sms_autoreply_edit_yes" :
		doAddEdit(false, $_POST[autoreply_code], $_GET[autoreply_id]);

		break;
}

function makeAddEditForm($add, $err, $autoreply_code = "", $autoreply_id = "") {
	if ($add) {
		$actionparam = "add";
		$usertext = "Add";
	} else {
		$actionparam = "edit";
		$usertext = "Edit";
	}

	if ($err) {
		$content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	        <h2>$usertext SMS autoreply</h2>
	        <p>
	        <form action=\"menu.php?inc=sms_autoreply&op=sms_autoreply_{$actionparam}_yes&autoreply_id=$autoreply_id\" method=post>
	        <p>SMS autoreply code: <input type=text size=10 maxlength=10 name=autoreply_code value=\"$autoreply_code\">
	        <p><input type=submit class=button value=\"$usertext\">
	        </form>
	    ";

	return $content;
}

function doAddEdit($add, $autoreply_code, $autoreply_id = "") {
	if ($add) {
		$actionparam = "add";
		$usertext = "add";
	} else {
		$actionparam = "edit";
		$usertext = "edit";
	}

	$gotourl = "menu.php?inc=sms_autoreply&op=sms_autoreply_$actionparam";

	if ($autoreply_code) {
		if (checkavailablecode($autoreply_code)) {
			if ($add) {
				$db_query = "INSERT INTO playsms_featAutoreply (uid,autoreply_code) VALUES ('$uid','$autoreply_code')";
				$ok = ($autoreply_id = @ dba_insert_id($db_query));
			} else {
				$db_query = "UPDATE playsms_featAutoreply SET autoreply_code='$autoreply_code' " .
				"WHERE autoreply_id='$autoreply_id'";
				$ok = ($db_result = @ dba_affected_rows($db_query));
			}

			if ($ok) {
				$error_string = "SMS autoreply code `$autoreply_code` has been {$usertext}ed";
				$gotourl = "menu.php?inc=sms_autoreply&op=sms_autoreply_manage&autoreply_id=$autoreply_id";
			} else {
				$error_string = "Fail to $usertext SMS autoreply code `$autoreply_code`";
			}
		} else {
			$error_string = "SMS code `$autoreply_code` already exists, reserved or use by other feature!";
		}
	} else {
		$error_string = "You must fill all fields!";
	}
	header("Location: $gotourl&err=" . urlencode($error_string));
}
?>
