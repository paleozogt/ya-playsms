<?php

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$op = $_GET[op];
$gpid = $_GET[gpid];

switch ($op)
{
    case "edit":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Edit group</h2>
	    <p>
	    <form action=menu.php?inc=dir_edit&op=edit_yes&gpid=$gpid method=POST>
	    <p>Group Name: <input type=text name=dir_name value=\"".gpid2gpname($gpid)."\" size=50>
	    <p>Group Code: <input type=text name=dir_code value=\"".gpid2gpcode($gpid)."\" size=10> (please use uppercase and make it short)
	    <p>Note: Group Code used by code BC (broadcast SMS from single SMS)
	    <p><input type=submit class=button value=\"Save\"> 
	    </form>
	";
	echo $content;
	break;
    case "edit_yes":
	$dir_name = $_POST[dir_name];
	$dir_code = strtoupper(trim($_POST[dir_code]));
	if ($dir_name && $dir_code)
	{
	    $db_query = "SELECT gp_code FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' AND gp_code='$dir_code' AND NOT gpid='$gpid'";
	    $db_result = dba_query($db_query);
	    if ($db_row = dba_fetch_array($db_result))
	    {
		header("Location: fr_right.php?err=".urlencode("No changes has been made on group `$dir_name` code `$dir_code`"));
		die();
	    }
	    else
	    {
		$db_query = "UPDATE playsms_tblUserGroupPhonebook SET gp_name='$dir_name',gp_code='$dir_code' WHERE uid='$uid' AND gpid='$gpid'";
		$db_result = dba_query($db_query);
		header("Location:  fr_right.php?err=".urlencode("Group `$dir_name` with code `$dir_code` has been edited"));
		die();
	    }
	}
	header ("Location: menu.php?inc=dir_edit&op=edit&gpid=$gpid&err=".urlencode("Group name and description must be filled"));
	break;
}

?>