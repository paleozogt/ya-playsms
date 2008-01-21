<?php
if (!defined("_SECURE_")) {

    die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$selfurl = $_SERVER['PHP_SELF'] . "?inc=get_status";

require_once 'DB/DataObject.php';
require_once 'DB/DataObject/FormBuilder.php';

$op = $_GET[op];
$smslog_id = $_GET[smslog_id];
$err = $_GET[err];
$showall = $_GET[showall];

if ($err) {
    echo "<font color=red>$err</font><br><br>";
}

switch ($op) {
    case "del" :
    	delete($smslog_id, $uid, $selfurl);
    	break;

    case "get_status" :
    default:
    	if ($showall) {
    	    $getuid= 0;
    	} else {
    	    $getuid= $uid;
    	}
		print makeList($getuid, $selfurl, $_GET[offset]);
		break;    	
}

return;

function makeList($uid, $selfurl, $offset= 0, $numShow= 75) {
    $db = DB_DataObject :: factory(playsms_tblSMSOutgoing);
    $db->limit($offset, $numShow);
    $db->orderBy("smslog_id DESC");

    $pagetitle = "Delivery report";
    if (isadmin() && !$uid) {
        $pagetitle .= " (All)";
    } else {
    	$db->uid= $uid;
    }
    $db->flag_deleted= '0';

	if ($offset) {
	    $newOffset=$offset-$numShow;
	    $prevUrl= "$selfurl&offset=$newOffset";
	} else {
	    $prevUrl= "#";
	}
	
	$newOffset= $offset+$numShow;
	$nextUrl= "$selfurl&offset=$newOffset";

    $content = "
    		    <h2>$pagetitle</h2>
    		    <p/>
    		    <a href='$prevUrl'>[ Prev] </a>
    		   	<a href='$nextUrl'>[ Next ]</>
    		    <p/>
    		    <table width=100% cellpadding=1 cellspacing=1 border=1>
    		    <tr>
    		      <td align=center class=box_title width=2%>id</td>
    		      <td align=center class=box_title width=12%>Time</td>
    		      <td align=center class=box_title width=12%>Receiver</td>
    		      <td align=center class=box_title width=50%>Message</td>
    		      <td align=center class=box_title width=8%>Status</td>
    		      <td align=center class=box_title width=8%>Group</td>
    		      <td align=center class=box_title width=8%>Action</td>
    		    </tr>
    		";    

    $db->find();
    while ($db->fetch()) {
        $p_desc = pnum2pdesc($db->p_dst);
        $current_p_dst = $db->p_dst;
        if ($p_desc) {
            $current_p_dst = "$db->p_dst<br>($p_desc)";
        }
        $hide_p_dst = $db->p_dst;
        if ($p_desc) {
            $hide_p_dst = "$p_dst ($p_desc)";
        }
        $p_sms_type = $db->p_sms_type;
        $hide_p_dst = str_replace("\'", "", $hide_p_dst);
        $hide_p_dst = str_replace("\"", "", $hide_p_dst);
        $p_msg = $db->p_msg;
        if (($p_footer = $db->p_footer) && (($p_sms_type == "text") || ($p_sms_type == "flash"))) {
            $p_msg = $p_msg . " $p_footer";
        }
        $p_datetime = $db->p_datetime;
        $p_update = $db->p_update;
        $p_status = $db->p_status;
        $p_gpid = $db->p_gpid;

        $status_color= getStatusColor($p_status);
        $status_name = getStatusName($p_status);
        $p_status="<font color=$status_color>$status_name</font> <br/>($db->send_tries tries)";

        $p_status .= " $db_row[p_status]";
        if ($p_gpid) {
            $db_query1 = "SELECT gp_code FROM playsms_tblUserGroupPhonebook WHERE gpid='$p_gpid'";
            $db_result1 = dba_query($db_query1);
            $db_row1 = dba_fetch_array($db_result1);
            $p_gpcode = strtoupper($db_row1[gp_code]);
        } else {
            $p_gpcode = "&nbsp;";
        }
 
 		$deleteCode= "javascript: " .
					 "ConfirmURL('Are you sure you want to delete outgoing SMS to `$hide_p_dst`, number $db->smslog_id ?'," .
					 "'menu.php?inc=get_status&op=del&smslog_id=$db->smslog_id'" .
					 ")";
 
		$content .= "
				<tr>
					<td valign=top class=box_text align=left width=2%>$db->smslog_id</td>
					<td valign=top class=box_text align=center width=12%>$db->p_datetime</td>
					<td valign=top class=box_text align=center width=12%>$current_p_dst</td>
					<td valign=top class=box_text align=left width=50%>$p_msg</td>
					<td valign=top class=box_text align=center width=8%>$p_status</td>
					<td valign=top class=box_text align=center width=8%>$p_gpcode</td>
					<td valign=top class=box_text align=center width=8%>
						<a href=\"$deleteCode\">[ Delete ]</a>
					</td>
				</tr>
			    ";
        
    }
    
    $content .= "</form></table>";
    return $content;
}

function getStatusColor($status) {
    switch ($status) {
        case DLR_PENDING :
        	$status_color= "orange";
            break;
        case DLR_SENT :
        	$status_color= "green";
            break;
        case DLR_FAILED :
        	$status_color= "red";
            break;
        case DLR_DELIVERED :
        	$status_color= "green";
            break;
        default:
        	$status_color= "black";
        	break;
    }
    return $status_color;
}

function getStatusName($status) {
    switch ($status) {
        case DLR_PENDING :
        	$status_name = "Pending";
            break;
        case DLR_SENT :
        	$status_name = "Sent";
            break;
        case DLR_FAILED :
        	$status_name = "Failed";
            break;
        case DLR_DELIVERED :
        	$status_name = "Delivered";
            break;
        default:
        	$status_name = "Unknown";
        	break;
    }    
    return $status_name;
}

function delete($smslog_id, $uid, $selfurl) {
	if (!smslog_id) { 
		return;   
	}

    $db = DB_DataObject :: factory(playsms_tblSMSOutgoing);
	$db->smslog_id= $smslog_id;

	// non-admins can only delete their own logs
	if (!isadmin()) {
	    $db->uid= $uid;
	}

	$db->limit(1);
    if ($db->delete()) {
        $err = "SMS Log ID: $smslog_id has been deleted";
    } else {
        $err = "Fail to delete SMS";
    }

    header("Location: $selfurl&err=" . urlencode($err));    
}

?>
