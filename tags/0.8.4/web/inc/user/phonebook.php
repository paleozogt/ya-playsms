<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP [" . $_SERVER['REMOTE_ADDR'] . "] logged");
};

$op = $_POST['op'];
if (!$op) {
	$op = $_GET['op'];
}
$item_count = $_POST[item_count];
for ($i = 1; $i <= $item_count; $i++) {
	${ "chkid" . $i } = $_POST["chkid" . $i];
	${ "pid" . $i } = $_POST["pid" . $i];
	${ "pdesc" . $i } = $_POST["pdesc" . $i];
	${ "pnum" . $i } = $_POST["pnum" . $i];
	${ "pemail" . $i } = $_POST["pemail" . $i];
}

switch ($op) {
	case "edit" :
		$op_content = "
		    <table cellpadding=0 cellspacing=0 border=1 width=300>
		    <form action=\"menu.php?inc=phonebook\" method=post>
		    <input type=hidden name=op value=edit_yes>
		    <tr>
			<td class=box_title width=4>&nbsp;*&nbsp;</td>
			<td class=box_title>Edited Owner</td>
			<td class=box_title>Edited Number</td>
			<td class=box_title>Edited Email</td>
		    </tr>
		";
		$j = 0;
		for ($i = 1; $i <= $item_count; $i++) {
			$c_chkbox = ${"chkid" . $i};
			if (($c_pid = ${"pid" . $i}) && ($c_chkbox == "on")) {
				$j++;
				$c_pnum = pid2pnum($c_pid);
				$c_pdesc = pnum2pdesc($c_pnum);
				$c_pemail = pnum2pemail($c_pnum);
				$op_content .= "
					    <input type=hidden name=pid" . $j . " value=\"$c_pid\">
					    <tr>
						<td class=box_text width=4>&nbsp;$j.&nbsp;</td>
						<td class=box_text><input type=text size=40 maxlength=100 name=\"pdesc" . $j . "\" value=\"$c_pdesc\"></td>
						<td class=box_text><input type=text size=40 maxlength=100 name=\"pnum" . $j . "\" value=\"$c_pnum\"></td>
						<td class=box_text><input type=text size=40 maxlength=100 name=\"pemail" . $j . "\" value=\"$c_pemail\"></td>
					    </tr>
					";
			}
		}
		$item_count = $j;
		$op_content .= "
		    </table>
		    <p><input type=submit class=button value=\"Save Changes\">
		    <input type=hidden name=item_count value=\"$item_count\">
		    </form>
		";
		break;
	case "edit_yes" :
		for ($i = 1; $i <= $item_count; $i++) {
			$c_pid = ${"pid" . $i};
			$c_pdesc = ${"pdesc" . $i};
			$c_pnum = ${"pnum" . $i};
			$c_pemail = ${"pemail" . $i};
			if ($c_pid && $c_pdesc && $c_pnum) {
				$db_query = "UPDATE playsms_tblUserPhonebook SET p_desc='$c_pdesc',p_num='$c_pnum',p_email='$c_pemail' WHERE pid='$c_pid'";
				$db_result = @ dba_affected_rows($db_query);
			}
		}
		header("Location: fr_right.php");
		die();
		break;
	case "copy" :
		$op_content = "
		    <table cellpadding=0 cellspacing=0 border=1 width=400>
		    <form action=\"menu.php?inc=phonebook\" method=post>
		    <input type=hidden name=op value=copy_yes>
		    <tr>
			<td class=box_title width=4>&nbsp;*&nbsp;</td>
			<td class=box_title>Copied Owner</td>
			<td class=box_title>Copied Number</td>
			<td class=box_title>Copied Email</td>
			
		    </tr>
		";
		$j = 0;
		for ($i = 1; $i <= $item_count; $i++) {
			$c_chkbox = ${"chkid" . $i};
			if (($c_pid = ${"pid" . $i}) && ($c_chkbox == "on")) {
				$j++;
				$c_pnum = pid2pnum($c_pid);
				$c_pdesc = pnum2pdesc($c_pnum);
				$c_pemail = pnum2pemail($c_pnum);
				$op_content .= "
					    <input type=hidden name=pid" . $j . " value=\"$c_pid\">
					    <tr>
						<td class=box_text width=4>&nbsp;$j.&nbsp;</td>
						<td class=box_text>&nbsp;$c_pdesc</td>
						<td class=box_text>&nbsp;$c_pnum</td>
						<td class=box_text>&nbsp;$c_pemail</td>
					    </tr>
					";
			}
		}
		$db_query = "SELECT * FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' ORDER BY gp_name";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$option_group .= "<option value=\"" . $db_row['gpid'] . "\">" . $db_row['gp_name'] . " - Code: " . $db_row['gp_code'] . "</option>";
		}
		$item_count = $j;
		$op_content .= "
		    </table>
		    <p>Select destination group: <select name=gpid>$option_group</select> <input type=submit class=button value=\"Go\">
		    <input type=hidden name=item_count value=\"$item_count\">
		    </form>
		";
		break;
	case "copy_yes" :
		$gpid = $_POST['gpid'];
		for ($i = 1; $i <= $item_count; $i++) {
			$c_pid = ${"pid" . $i};
			if ($c_pid) {
				$c_pnum = pid2pnum($c_pid);
				$c_pdesc = pnum2pdesc($c_pnum);
				$c_pemail = pnum2pemail($c_pnum);
				$db_query = "INSERT INTO playsms_tblUserPhonebook (gpid,uid,p_num,p_desc,p_email) VALUES ('$gpid','$uid','$c_pnum','$c_pdesc','$c_pemail')";
				$db_result = @ dba_insert_id($db_query);
			}
		}
		header("Location: fr_right.php");
		die();
		break;
	case "delete" :
		$op_content = "
		    <table cellpadding=0 cellspacing=0 border=1 width=400>
		    <form action=\"menu.php?inc=phonebook\" method=post>
		    <input type=hidden name=op value=delete_yes>
		    <tr>
			<td class=box_title width=4>&nbsp;*&nbsp;</td>
			<td class=box_title>Deleted Owner</td>
			<td class=box_title>Deleted Number</td>
			<td class=box_title>Deleted Email</td>
			
		    </tr>
		";
		$j = 0;
		for ($i = 1; $i <= $item_count; $i++) {
			$c_chkbox = ${"chkid" . $i};
			if (($c_pid = ${"pid" . $i}) && ($c_chkbox == "on")) {
				$j++;
				$c_pnum = pid2pnum($c_pid);
				$c_pdesc = pnum2pdesc($c_pnum);
				$c_pemail = pnum2pemail($c_pnum);
				$op_content .= "
					    <input type=hidden name=pid" . $j . " value=\"$c_pid\">
					    <tr>
						<td class=box_text width=4>&nbsp;$j.&nbsp;</td>
						<td class=box_text>&nbsp;$c_pdesc</td>
						<td class=box_text>&nbsp;$c_pnum</td>
						<td class=box_text>&nbsp;$c_pemail</td>
					    </tr>
					";
			}
		}
		$item_count = $j;
		$op_content .= "
		    </table>
		    <p><input type=submit class=button value=\"Delete Item(s)\">
		    <input type=hidden name=item_count value=\"$item_count\">
		    </form>
		";
		break;
	case "move" :
		$op_content = "
		    <table cellpadding=0 cellspacing=0 border=1 width=400>
		    <form action=\"menu.php?inc=phonebook\" method=post>
		    <input type=hidden name=op value=move_yes>
		    <tr>
			<td class=box_title width=4>&nbsp;*&nbsp;</td>
			<td class=box_title>Moved Owner</td>
			<td class=box_title>Moved Number</td>
			<td class=box_title>Moved Email</td>
			
		    </tr>
		";
		$j = 0;
		for ($i = 1; $i <= $item_count; $i++) {
			$c_chkbox = ${"chkid" . $i};
			if (($c_pid = ${"pid" . $i}) && ($c_chkbox == "on")) {
				$j++;
				$c_pnum = pid2pnum($c_pid);
				$c_pdesc = pnum2pdesc($c_pnum);
				$c_pemail = pnum2pemail($c_pnum);
				$op_content .= "
					    <input type=hidden name=pid" . $j . " value=\"$c_pid\">
					    <tr>
						<td class=box_text width=4>&nbsp;$j.&nbsp;</td>
						<td class=box_text>&nbsp;$c_pdesc</td>
						<td class=box_text>&nbsp;$c_pnum</td>
						<td class=box_text>&nbsp;$c_pemail</td>
					    </tr>
					";
			}
		}
		$db_query = "SELECT * FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' ORDER BY gp_name";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$option_group .= "<option value=\"" . $db_row['gpid'] . "\">" . $db_row['gp_name'] . " - Code: " . $db_row['gp_code'] . "</option>";
		}
		$item_count = $j;
		$op_content .= "
		    </table>
		    <p>Select destination group: <select name=gpid>$option_group</select> <input type=submit class=button value=\"Go\">
		    <input type=hidden name=item_count value=\"$item_count\">
		    </form>
		";
		break;
	case "move_yes" :
		$gpid = $_POST['gpid'];
		for ($i = 1; $i <= $item_count; $i++) {
			$c_pid = ${"pid" . $i};
			if ($c_pid) {
				$db_query = "UPDATE playsms_tblUserPhonebook SET gpid='$gpid' WHERE pid='$c_pid'";
				$db_result = @ dba_affected_rows($db_query);
			}
		}
		header("Location: fr_right.php");
		die();
		break;
	case "delete" :
		$op_content = "
		    <table cellpadding=0 cellspacing=0 border=1 width=400>
		    <form action=\"menu.php?inc=phonebook\" method=post>
		    <input type=hidden name=op value=delete_yes>
		    <tr>
			<td class=box_title width=4>&nbsp;*&nbsp;</td>
			<td class=box_title>Deleted Owner</td>
			<td class=box_title>Deleted Number</td>
			<td class=box_title>Deleted Email</td>
			
		    </tr>
		";
		$j = 0;
		for ($i = 1; $i <= $item_count; $i++) {
			$c_chkbox = ${"chkid" . $i};
			if (($c_pid = ${"pid" . $i}) && ($c_chkbox == "on")) {
				$j++;
				$c_pnum = pid2pnum($c_pid);
				$c_pdesc = pnum2pdesc($c_pnum);
				$c_pemail = pnum2pemail($c_pnum);
				$op_content .= "
					    <input type=hidden name=pid" . $j . " value=\"$c_pid\">
					    <tr>
						<td class=box_text width=4>&nbsp;$j.&nbsp;</td>
						<td class=box_text>&nbsp;$c_pdesc</td>
						<td class=box_text>&nbsp;$c_pnum</td>
						<td class=box_text>&nbsp;$c_pemail</td>
					    </tr>
					";
			}
		}
		$item_count = $j;
		$op_content .= "
		    </table>
		    <p><input type=submit class=button value=\"Delete Item(s)\">
		    <input type=hidden name=item_count value=\"$item_count\">
		    </form>
		";
		break;
	case "delete_yes" :
		$gpid = $_POST['gpid'];
		for ($i = 1; $i <= $item_count; $i++) {
			$c_pid = ${"pid" . $i};
			if ($c_pid) {
				$db_query = "DELETE FROM playsms_tblUserPhonebook WHERE pid='$c_pid'";
				$db_result = @ dba_affected_rows($db_query);
			}
		}
		header("Location: fr_right.php");
		die();
		break;
	case "share_this_group" :
		$gpid = $_GET['gpid'];
		if ($gpid) {
			$db_query = "DELETE FROM playsms_tblUserGroupPhonebook_public WHERE gpid='$gpid' AND uid='$uid'";
			$db_result = @ dba_query($db_query);
			$gpname = gpid2gpname($gpid);
			$error_string = "Fail to make public group `$gpname` on your phonebook";
			$db_query = "INSERT INTO playsms_tblUserGroupPhonebook_public (gpid,uid) VALUES ('$gpid','$uid')";
			$db_result = @ dba_insert_id($db_query);
			if ($db_result > 0) {
				$error_string = "Group `$gpname` has been published for public view";
			}
		}
		header("Location: fr_right.php?err=" . urlencode($error_string));
		die();
		break;
	case "hide_from_public" :
		$pp = $_GET['pp'];
		$gpid = $_GET['gpid'];
		if ($gpid) {
			$gpname = gpid2gpname($gpid);
			$error_string = "Fail to hide public group `$gpname`";
			$db_query = "DELETE FROM playsms_tblUserGroupPhonebook_public WHERE gpid='$gpid' AND uid='$uid'";
			$db_result = @ dba_affected_rows($db_query);
			if ($db_result > 0) {
				$error_string = "Group `$gpname` has been removed from public view";
			}
		}
		if ($pp == 1) {
			header("Location: menu.php?inc=phonebook_public");
		} else {
			header("Location: fr_right.php?err=" . urlencode($error_string));
		}
		die();
		break;
}

$content = "
    <h2>Phonebook - " . ucfirst($op) . "</h2>
    <p>
";
if ($err) {
	$content .= "<p><font color=red>$err</font><p>";
}
$content .= "
    $op_content
";

include "html_header.php";

echo $content;

include "html_footer.php";
?>
