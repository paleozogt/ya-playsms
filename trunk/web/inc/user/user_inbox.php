<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];

switch ($op) {
	case "user_inbox" :
		$content = "
		    <h2>Inbox</h2>
		    <p>
		    <table width=100% cellpadding=1 cellspacing=1 border=1>
		    <tr>
		      <td align=center class=box_title width=4>*</td>
		      <td align=center class=box_title width=20%>Time</td>
		      <td align=center class=box_title width=20%>Sender</td>
		      <td align=center class=box_title width=60%>Message</td>
		      <td align=center class=box_title>Action</td>
		    </tr>
		";
		$db_query = "SELECT * FROM playsms_tblUserInbox WHERE in_uid='$uid' AND in_hidden='0' ORDER BY in_id DESC LIMIT 0,50";
		$db_result = dba_query($db_query);
		$i = dba_num_rows($db_query) + 1;
		while ($db_row = dba_fetch_array($db_result)) {
			$in_id = $db_row[in_id];
			$in_sender = $db_row[in_sender];
			$p_desc = pnum2pdesc($in_sender);
			$current_sender = $in_sender;
			if ($p_desc) {
				$current_sender = "$in_sender<br>($p_desc)";
			}
			$in_msg = $db_row[in_msg];
			$in_datetime = $db_row[in_datetime];
			$i--;
			$content .= "
				<tr>
			          <td valign=top class=box_text align=left width=4>$i.</td>
			          <td valign=top class=box_text align=center width=20%>$in_datetime</td>
			          <td valign=top class=box_text align=center width=20%>$current_sender</td>
			          <td valign=top class=box_text align=left width=60%>$in_msg</td>
			          <td valign=top class=box_text align=left nowrap><a href=\"javascript: ConfirmURL('Are you sure you want to delete this SMS ?','menu.php?inc=user_inbox&op=user_inbox_del&inid=$in_id')\">[ Delete ]</a></td>
				</tr>
			    ";
		}
		$content .= "</table>";
		echo $content;
		break;
	case "user_inbox_del" :
		$error_string = "Fail to delete incoming private SMS";
		if ($in_id = $_GET[inid]) {
			$db_query = "UPDATE playsms_tblUserInbox SET in_hidden='1' WHERE in_id='$in_id'";
			$db_result = dba_affected_rows($db_query);
			if ($db_result > 0) {
				$error_string = "Selected incoming private SMS has been deleted";
			}
		}
		header("Location: menu.php?inc=user_inbox&op=user_inbox&err=" . urlencode($error_string));
		break;
}
?>
