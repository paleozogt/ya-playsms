<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$op = $_GET[op];

switch ($op)
{
    case "sms_autoreply_list":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>List/Manage/Delete SMS autoreplies</h2>
	    <p>
	    <a href=\"menu.php?inc=sms_autoreply&op=sms_autoreply_add\">[ Add SMS autoreply ]</a>
	    <p>
	";
	if (!isadmin())
	{
	    $query_user_only = "WHERE uid='$uid'";
	}
	$db_query = "SELECT * FROM playsms_featAutoreply $query_user_only ORDER BY autoreply_code";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result))
	{
	    $owner = uid2username($db_row[uid]);
	    $content .= "[<a href=menu.php?inc=sms_autoreply&op=sms_autoreply_manage&autoreply_id=$db_row[autoreply_id]>m</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete SMS autoreply code `$db_row[autoreply_code]` ?','menu.php?inc=sms_autoreply&op=sms_autoreply_del&autoreply_id=$db_row[autoreply_id]')\">x</a>] <b>Code:</b> $db_row[autoreply_code] &nbsp;&nbsp;<b>User:</b> $owner<br><br>";
	}
	echo $content;
	echo "
	    <p>
	    <a href=\"menu.php?inc=sms_autoreply&op=sms_autoreply_add\">[ Add SMS autoreply ]</a>
	";
	break;
    case "sms_autoreply_manage":
	$autoreply_id = $_GET[autoreply_id];
	if (!isadmin())
	{
	    $query_user_only = "AND uid='$uid'";
	}
	$db_query = "SELECT * FROM playsms_featAutoreply WHERE autoreply_id='$autoreply_id' $query_user_only";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$manage_autoreply_code = $db_row[autoreply_code];
	$o_uid = $db_row[uid];
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Manage SMS autoreply</h2>
	    <p>
	    <p>SMS autoreply code: <b>$manage_autoreply_code</b>
	    <p>
	    <a href=\"menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_add&autoreply_id=$autoreply_id\">[ Add SMS autoreply scenario ]</a>
	    <p>
	";
	$db_query = "SELECT * FROM playsms_featAutoreply_scenario WHERE autoreply_id='$autoreply_id' ORDER BY autoreply_scenario_param1";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result))
	{
	    $owner = uid2username($o_uid);
	    $list_of_param = "";
	    for ($i=1;$i<=7;$i++)
	    { 
		$list_of_param .= $db_row["autoreply_scenario_param$i"]."&nbsp";
	    }
	    $content .= "[<a href=menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_edit&autoreply_id=$autoreply_id&autoreply_scenario_id=$db_row[autoreply_scenario_id]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete this SMS autoreply scenario ?','menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_del&autoreply_scenario_id=$db_row[autoreply_scenario_id]')\">x</a>] <b>Param:</b> ".$list_of_param."&nbsp;<br><b>Return:</b> ".$db_row[autoreply_scenario_result]."&nbsp;&nbsp;<b>User:</b> $owner<br><br>";
	}
	$content .= "
	    <p>
	    <a href=\"menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_add&autoreply_id=$autoreply_id\">[ Add SMS autoreply scenario ]</a>
	    </form>
	";
	echo $content;
	break;
    case "sms_autoreply_del":
	$autoreply_id = $_GET[autoreply_id];
	$db_query = "SELECT autoreply_code FROM playsms_featAutoreply WHERE autoreply_id='$autoreply_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$code_name = $db_row[autoreply_code];
	if ($code_name)
	{
	    $db_query = "DELETE FROM playsms_featAutoreply WHERE autoreply_code='$code_name'";
	    if (@dba_affected_rows($db_query))
	    {
		$error_string = "SMS autoreply code `$code_name` has been deleted!";
	    }
	    else
	    {
		$error_string = "Fail to delete SMS autoreply code `$code_name`";
	    }
	}
	header ("Location: menu.php?inc=sms_autoreply&op=sms_autoreply_list&err=".urlencode($error_string));
	break;
    case "sms_autoreply_add":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Add SMS autoreply</h2>
	    <p>
	    <form action=menu.php?inc=sms_autoreply&op=sms_autoreply_add_yes method=post>
	    <p>SMS autoreply code: <input type=text size=10 maxlength=10 name=add_autoreply_code value=\"$add_autoreply_code\">
	    <p><input type=submit class=button value=Add>
	    </form>
	";
	echo $content;
	break;
    case "sms_autoreply_add_yes":
	$add_autoreply_code = strtoupper($_POST[add_autoreply_code]);
	if ($add_autoreply_code)
	{
	    if (checkavailablecode($add_autoreply_code))
	    {
		$db_query = "INSERT INTO playsms_featAutoreply (uid,autoreply_code) VALUES ('$uid','$add_autoreply_code')";
		if ($new_uid = @dba_insert_id($db_query))
		{
	    	    $error_string = "SMS autoreply code `$add_autoreply_code` has been added";
		}
		else
		{
	    	    $error_string = "Fail to add SMS autoreply code `$add_autoreply_code`";
		}
	    }
	    else
	    {
		$error_string = "SMS code `$add_autoreply_code` already exists, reserved or use by other feature!";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=sms_autoreply&op=sms_autoreply_add&err=".urlencode($error_string));
	break;
}

?>