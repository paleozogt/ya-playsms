<?php
include "init.php";
include "$apps_path[libs]/function.php";

// -----------------------------------------------------------------------------
// query string: 
// u	: username
// p	: password
// ta	: type of action 
// 	pv = send private
// 	bc = send broadcast
// 	ds = delivery status
// last : last SMS log ID (this number not included on result)
// c	: number of delivery status retrived
// slid	: SMS Log ID (for ta=ds, when slid defined 'last' and 'c' has no effect)
// to	: destination number (for ta=pv) or destination group code (for ta=bc)
// msg	: message
// from	: sender's mobile number
// type : message type (1=flash, 2=text)
// example: 
// http://x.com/ws.php?u=anton&p=g0rg0n&ta=bc&to=TI&msg=meeting+at+15.00+today!
// -----------------------------------------------------------------------------
// if succeded returns: OK SMS_LOG_ID (eg: OK 754)
// if error occured returns:
// 	ERR 100	= authentication failed
//	ERR 101	= type of action not valid
//	ERR 102	= one or more field empty
//	ERR 200	= send private failed
//	ERR 201 = destination number or message is empty
//	ERR 300	= send broadcast failed
//	ERR 301 = destination group or message is empty
//	ERR 400 = no delivery status retrieved
// ----------------------------------------------------------------------------
// output delivery status (for ta=ds) in CSV form:
// SMS log ID; Source number; Destination Number; Message; Delivery Time; Update Pending Status Time; SMS Status
// SMS Status:
// 0 = pending
// 1 = sent
// 2 = failed
// 3 = delivered
// ----------------------------------------------------------------------------

$u = trim($_GET[u]);
$p = trim($_GET[p]);
$ta = strtoupper($_GET[ta]);
$last = trim($_GET[last]);
$c = trim($_GET[c]);
$slid = trim($_GET[slid]);
$to = strtoupper($_GET[to]);
$msg = trim($_GET[msg]);
$from = trim($_GET[from]);
$type = trim($_GET[type]);

if ($u && $p) {
	if (!validatelogin($u, $p)) {
		echo "ERR 100";
		die();
	}
}

if ($ta) {
	switch ($ta) {
		case "PV" :
			if ($to && $msg) {
				$transparent = false;
				if ($trn) {
					$transparent = true;
				}
				$smslog_id = websend2pv($u, $to, $msg);
				if ($smslog_id) {
					echo "OK $smslog_id";
				} else {
					echo "ERR 200";
				}
			} else {
				echo "ERR 201";
			}
			die();
			break;
		case "BC" :
			if ($to && $msg) {
				$transparent = false;
				if ($trn) {
					$transparent = true;
				}
				if (websend2group($u, $to, $msg)) {
					echo "OK";
				} else {
					echo "ERR 300";
				}
			} else {
				echo "ERR 301";
			}
			die();
			break;
		case "DS" :
			// output in CSV form:
			// SMS log ID; Source number; Destination Number; Message; Delivery Time; Update Pending Status Time; SMS Status
			// SMS Status:
			// 0 = pending
			// 1 = sent
			// 2 = failed
			// 3 = delivered
			$uid = username2uid($u);
			$content = "";
			if ($slid) {
				$db_query = "SELECT p_status FROM playsms_tblSMSOutgoing WHERE uid='$uid' AND smslog_id='$slid'";
				$db_result = dba_query($db_query);
				if ($db_row = dba_fetch_array($db_result)) {
					$p_status = $db_row[p_status];
					echo $p_status;
				} else {
					echo "ERR 400";
				}
				die();
			} else {
				if ($c) {
					$query_limit = " LIMIT 0,$c";
				}
				if ($last) {
					$query_last = "AND smslog_id>$last";
				}
				$db_query = "SELECT * FROM playsms_tblSMSOutgoing WHERE uid='$uid' $query_last ORDER BY p_datetime DESC $query_limit";
				$db_result = dba_query($db_query);
				while ($db_row = dba_fetch_array($db_result)) {
					$smslog_id = $db_row[smslog_id];
					$p_src = $db_row[p_src];
					$p_dst = $db_row[p_dst];
					$p_msg = $db_row[p_msg];
					$p_datetime = $db_row[p_datetime];
					$p_update = $db_row[p_update];
					$p_status = $db_row[p_status];
					$content .= "\"$smslog_id\";\"$p_src\";\"$p_dst\";\"$p_msg\";\"$p_datetime\";\"$p_update\";\"$p_status\";\n";
				}
				if ($content) {
					echo $content;
				} else {
					echo "ERR 400";
				}
				die();
			}
			break;
	}
	echo "ERR 101";
	die();
}
echo "ERR 102";
?>
