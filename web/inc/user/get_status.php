<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];
$slid = $_GET[slid];
$err = $_GET[err];
$showall= $_GET[showall];

switch ($op) {
	case "get_status" :
		$pagetitle= "Delivery report";
		if (isadmin() && $showall) {
			$pagetitle.= " (All)";
		} else {
			$where= "uid='$uid' AND ";
		}
		$where.= "flag_deleted='0'";
	
		$content = "
		    <h2>$pagetitle</h2>
		    <p>
		    <table width=100% cellpadding=1 cellspacing=1 border=1>
		    <tr>
		      <td align=center class=box_title width=4>*</td>
		      <td align=center class=box_title width=20%>Time</td>
		      <td align=center class=box_title width=20%>Receiver</td>
		      <td align=center class=box_title width=50%>Message</td>
		      <td align=center class=box_title width=10%>Status</td>
		      <td align=center class=box_title width=4>Group</td>
		      <td align=center class=box_title width=4>Action</td>
		    </tr>
		";
		

	
		$db_query = "SELECT * FROM playsms_tblSMSOutgoing WHERE $where ORDER BY smslog_id DESC LIMIT 0,50";
		$db_result = dba_query($db_query);
		$i = dba_num_rows($db_query) + 1;
		while ($db_row = dba_fetch_array($db_result)) {
			$current_slid = $db_row[smslog_id];
			$p_dst = $db_row[p_dst];
			$p_desc = pnum2pdesc($p_dst);
			$current_p_dst = $p_dst;
			if ($p_desc) {
				$current_p_dst = "$p_dst<br>($p_desc)";
			}
			$hide_p_dst = $p_dst;
			if ($p_desc) {
				$hide_p_dst = "$p_dst ($p_desc)";
			}
			$p_sms_type = $db_row[p_sms_type];
			$hide_p_dst = str_replace("\'", "", $hide_p_dst);
			$hide_p_dst = str_replace("\"", "", $hide_p_dst);
			$p_msg = $db_row[p_msg];
			if (($p_footer = $db_row[p_footer]) && (($p_sms_type == "text") || ($p_sms_type == "flash"))) {
				$p_msg = $p_msg . " $p_footer";
			}
			$p_datetime = $db_row[p_datetime];
			$p_update = $db_row[p_update];
			$p_status = $db_row[p_status];
			$p_gpid = $db_row[p_gpid];
			// 0 = pending
			// 1 = sent
			// 2 = failed
			// 3 = delivered
			if ($p_status == "1") {
				$p_status = "<font color=green>Sent</font>";
			} else
				if ($p_status == "2") {
					$p_status = "<font color=red>Failed</font>";
				} else
					if ($p_status == "3") {
						$p_status = "<font color=green>Delivered</font>";
					} else {
						$p_status = "<font color=orange>Pending</font>";
					}
			if ($p_gpid) {
				$db_query1 = "SELECT gp_code FROM playsms_tblUserGroupPhonebook WHERE gpid='$p_gpid'";
				$db_result1 = dba_query($db_query1);
				$db_row1 = dba_fetch_array($db_result1);
				$p_gpcode = strtoupper($db_row1[gp_code]);
			} else {
				$p_gpcode = "&nbsp;";
			}
			$i--;
			$content .= "
					<tr>
				          <td valign=top class=box_text align=left width=4>$i.</td>
				          <td valign=top class=box_text align=center width=10%>$p_datetime</td>
				          <td valign=top class=box_text align=center width=20%>$current_p_dst</td>
				          <td valign=top class=box_text align=left width=60%>$p_msg</td>
				          <td valign=top class=box_text align=center width=10%>$p_status</td>
				          <td valign=top class=box_text align=center width=4>$p_gpcode</td>
				          <td valign=top class=box_text align=center width=4>
					    <a href=\"javascript: ConfirmURL('Are you sure you want to delete outgoing SMS to `$hide_p_dst`, number $i ?','menu.php?inc=get_status&op=del&slid=$current_slid')\">[ Delete ]</a>
					  </td>
					</tr>
				    ";
		}
		$content .= "</form></table>";
		if ($err) {
			echo "<font color=red>$err</font><br><br>";
		}
		echo $content;
		break;
	case "del" :
		if ($slid) {
			$db_query = "UPDATE playsms_tblSMSOutgoing SET flag_deleted='1' WHERE smslog_id='$slid' AND uid='$uid'";
			$db_result = dba_affected_rows($db_query);
			if ($db_result > 0) {
				$err = "SMS Log ID: $slid has been deleted";
			} else {
				$err = "Fail to delete SMS";
			}
		}
		header("Location: menu.php?inc=get_status&op=get_status&err=" . urlencode($err));
		break;
}
?>
