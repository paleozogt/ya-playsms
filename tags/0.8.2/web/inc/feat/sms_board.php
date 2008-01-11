<?
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];

switch ($op) {
	case "sms_board_list" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>List/Edit/Delete SMS boards</h2>
		    <p>
		    <a href=\"menu.php?inc=sms_board&op=sms_board_add\">[ Add SMS board ]</a>
		    <p>
		";
		if (!isadmin()) {
			$query_user_only = "WHERE uid='$uid'";
		}
		$db_query = "SELECT * FROM playsms_featBoard $query_user_only ORDER BY board_code";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$owner = uid2username($db_row[uid]);
			$content .= "[<a href=menu.php?inc=sms_board&op=sms_board_view&board_id=$db_row[board_id] target=_blank>v</a>] [<a href=menu.php?inc=sms_board&op=sms_board_edit&board_id=$db_row[board_id]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete SMS board `$db_row[board_code]` with all its messages ?','menu.php?inc=sms_board&op=sms_board_del&board_id=$db_row[board_id]')\">x</a>] <b>Code:</b> $db_row[board_code] &nbsp;&nbsp;<b>Forward:</b> $db_row[board_forward_email] &nbsp;&nbsp;<b>User:</b> $owner<br>";
		}
		echo $content;
		echo "
		    <p>
		    <a href=\"menu.php?inc=sms_board&op=sms_board_add\">[ Add SMS board ]</a>
		";
		break;
	case "sms_board_view" :
		$board_id = $_GET[board_id];
		$db_query = "SELECT board_code FROM playsms_featBoard WHERE board_id='$board_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$board_code = $db_row[board_code];
		header("Location: output.php?code=$board_code");
		break;
	case "sms_board_edit" :
		$board_id = $_GET[board_id];
		$db_query = "SELECT * FROM playsms_featBoard WHERE board_id='$board_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$edit_board_code = $db_row[board_code];
		$edit_email = $db_row[board_forward_email];
		$edit_template = $db_row[board_pref_template];
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>Edit SMS board</h2>
		    <p>
		    <form action=menu.php?inc=sms_board&op=sms_board_edit_yes method=post>
		    <input type=hidden name=edit_board_id value=$board_id>
		    <input type=hidden name=edit_board_code value=$edit_board_code>
		    <p>SMS board: <b>$edit_board_code</b>
		    <p>Forward to email: <input type=text size=30 name=edit_email value=\"$edit_email\">
		    <p>Template:
		    <br><textarea name=edit_template rows=5 cols=60>$edit_template</textarea>
		    <p><input type=submit class=button value=Save>
		    </form>
		";
		echo $content;
		break;
	case "sms_board_edit_yes" :
		$edit_board_id = $_POST[edit_board_id];
		$edit_board_code = $_POST[edit_board_code];
		$edit_email = $_POST[edit_email];
		$edit_template = $_POST[edit_template];
		if ($edit_board_id) {
			if (!$edit_template) {
				$edit_template = "<font color=black size=-1><b>##SENDER##</b></font><br>";
				$edit_template .= "<font color=black size=-2><i>##DATETIME##</i></font><br>";
				$edit_template .= "<font color=black size=-1>##MESSAGE##</font>";
			}
			$db_query = "
			        UPDATE playsms_featBoard
			        SET board_forward_email='$edit_email',board_pref_template='$edit_template'
				WHERE board_id='$edit_board_id' AND uid='$uid'
			    ";
			if (@ dba_affected_rows($db_query)) {
				$error_string = "SMS board `$edit_board_code` has been saved";
			}
		} else {
			$error_string = "You must fill all fields!";
		}
		header("Location: menu.php?inc=sms_board&op=sms_board_edit&board_id=$edit_board_id&err=" . urlencode($error_string));
		break;
	case "sms_board_del" :
		$board_id = $_GET[board_id];
		$db_query = "SELECT board_code FROM playsms_featBoard WHERE board_id='$board_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$board_code = $db_row[board_code];
		if ($board_code) {
			$db_query = "DELETE FROM playsms_featBoard WHERE board_code='$board_code'";
			if (@ dba_affected_rows($db_query)) {
				$db_query = "DELETE FROM playsms_tblSMSIncoming WHERE in_code='$board_code'";
				if (@ dba_affected_rows($db_query)) {
					$error_string = "SMS board `$board_code` with all its messages has been deleted!";
				}
			}
		}
		header("Location: menu.php?inc=sms_board&op=sms_board_list&err=" . urlencode($error_string));
		break;
	case "sms_board_add" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>Add SMS board</h2>
		    <p>
		    <form action=menu.php?inc=sms_board&op=sms_board_add_yes method=post>
		    <p>SMS board code: <input type=text size=30 maxlength=30 name=add_board_code value=\"$add_board_code\">
		    <p><b>Leave them empty if you dont know what to fill in these boxes below</b>
		    <p>Forward to email: <input type=text size=30 name=add_email value=\"$add_email\">
		    <p>Template:
		    <br><textarea name=add_template rows=5 cols=60>$add_template</textarea>
		    <p><input type=submit class=button value=Add>
		    </form>
		";
		echo $content;
		break;
	case "sms_board_add_yes" :
		$add_board_code = strtoupper($_POST[add_board_code]);
		$add_email = $_POST[add_email];
		$add_template = $_POST[add_template];
		if ($add_board_code) {
			if (checkavailablecode($add_board_code)) {
				if (!$add_template) {
					$add_template = "<font color=black size=-1><b>##SENDER##</b></font><br>";
					$add_template .= "<font color=black size=-2><i>##DATETIME##</i></font><br>";
					$add_template .= "<font color=black size=-1>##MESSAGE##</font>";
				}
				$db_query = "
					    INSERT INTO playsms_featBoard (uid,board_code,board_forward_email,board_pref_template)
					    VALUES ('$uid','$add_board_code','$add_email','$add_template')
					";
				if ($new_uid = @ dba_insert_id($db_query)) {
					$error_string = "SMS board `$add_board_code` has been added";
				} else {
					$error_string = "Fail to add SMS board `$add_board_code`";
				}
			} else {
				$error_string = "SMS code `$add_board_code` already exists, reserved or use by other feature!";
			}
		} else {
			$error_string = "You must fill all fields!";
		}
		header("Location: menu.php?inc=sms_board&op=sms_board_add&err=" . urlencode($error_string));
		break;
}
?>
