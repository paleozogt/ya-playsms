<?php
if (!defined("_SECURE_")) {

    die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$selfurl = $_SERVER['PHP_SELF'] . "?inc=user_inbox";

require_once 'DB/DataObject.php';
require_once 'DB/DataObject/FormBuilder.php';
require_once "$apps_path[libs]/inboxoutbox_importexport.php";

$op = $_GET[op];
$showall = $_GET[showall];

switch ($op) {
    case "del" :
        delete($_POST['id'], $uid, $selfurl);
        break;

    case "export":
        makeExportForm($selfurl, true);
        break;

    case "user_inbox" :
    default :

    	if ($showall) {
    	    $getuid= 0;
    	} else {
    	    $getuid= $uid;
    	}

        echo makeList($getuid, $selfurl, $_GET['offset']);
        break;

}

function delete($id, $uid, $selfurl) {
	if (!$id) { 
		return;   
	}

    $db = DB_DataObject :: factory('playsms_tblUserInbox');
	$db->in_id= $id;

	// non-admins can only delete their own logs
	if (!isadmin()) {
	    $db->uid= $uid;
	}

	$db->limit(1);
    if ($db->delete()) {
        $err = "Selected incoming SMS has been deleted";
    } else {
        $err = "Fail to delete incoming private SMS";
    }

    header("Location: $selfurl&err=" . urlencode($err));    
}

function makeList($uid, $selfurl, $offset = 0, $numShow = 75) {
    $db = DB_DataObject :: factory('playsms_tblUserInbox');
    if (!$offset)
        $offset = 0;
    $db->limit($offset, $numShow);
    $db->orderBy("in_id DESC");

    $pagetitle = "Inbox";
    if (isadmin() && !$uid) {
        $pagetitle .= " (All)";
    } else {        
    	$db->in_uid= $uid;
    }
    $db->in_hidden= '0';

	if ($offset) {
	    $newOffset=$offset-$numShow;
	    $prevUrl= "$selfurl&offset=$newOffset";
	} else {
	    $prevUrl= "#";
	}
	
	$newOffset= $offset+$numShow;
	$nextUrl= "$selfurl&offset=$newOffset";
    $exportUrl= "$selfurl&op=export";
	$linksPrevNext= "<a href='$prevUrl'>[ Prev] </a>
    		   		 <a href='$nextUrl'>[ Next ]</a>
                     <a href='$exportUrl'>[ Export ]</a>";

	// create hidden form with the 
	// id to delete, this way it will
	// get POSTed
	//
	$delForm   = generateActionSubmitter("del", "$selfurl&op=del", "id");

    $content = "$delForm
		    <h2>$pagetitle</h2>
		    <p/>
			$linksPrevNext
		    <p/>
		    <table width=100% cellpadding=1 cellspacing=1 border=1>
		    <tr>
		      <td align=center class=box_title width=4>*</td>
		      <td align=center class=box_title width=20%>Time</td>
		      <td align=center class=box_title width=20%>Sender</td>
		      <td align=center class=box_title width=60%>Message</td>
		      <td align=center class=box_title>Action</td>
		    </tr>
		";

	$db->find();
    while ($db->fetch()) {
        $in_id = $db->in_id;
        $in_sender = $db->in_sender;
        $p_desc = pnum2pdesc($in_sender);
        $current_sender = $in_sender;
        if ($p_desc) {
            $current_sender = "$in_sender<br>($p_desc)";
        }
        $in_msg = nl2br($db->in_msg);
        $in_datetime = $db->in_datetime;
        
  		$deleteCode= "javascript: del($db->in_id, " .
                     "'Are you sure you want to delete this SMS ?');";
		$actionCode= "<a href=\"$deleteCode\">[Delete]</a>";
        
        $content .= "
			<tr>
				<td valign=top class=box_text align=left width=4>$db->in_id</td>
				<td valign=top class=box_text align=center width=20%>$in_datetime</td>
				<td valign=top class=box_text align=center width=20%>$current_sender</td>
				<td valign=top class=box_text align=left width=60%>$in_msg</td>
				<td valign=top class=box_text align=left nowrap>$actionCode</td>
			</tr>
		    ";
    }
    $content .= "</table>";
    return $content;
}
?>
