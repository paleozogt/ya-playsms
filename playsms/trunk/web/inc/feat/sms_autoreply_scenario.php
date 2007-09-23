<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$op = $_GET[op];

switch ($op)
{
    case "sms_autoreply_scenario_del":
	$autoreply_scenario_id = $_GET[autoreply_scenario_id];
	$autoreply_id = $_GET[autoreply_id];
	$db_query = "SELECT autoreply_scenario_code FROM playsms_featAutoreply_scenario WHERE autoreply_scenario_id='$autoreply_scenario_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$code_name = $db_row[autoreply_scenario_code];
	if ($code_name)
	{
	    $db_query = "DELETE FROM playsms_featAutoreply_scenario WHERE autoreply_id='$autoreply_id' AND autoreply_scenario_id='$autoreply_scenario_id'";
	    if (@dba_affected_rows($db_query))
	    {
		$error_string = "SMS autoreply scenario code `$code_name` has been deleted!";
	    }
	    else
	    {
		$error_string = "Fail to delete SMS autoreply scenario code `$code_name`";
	    }
	}
	header ("Location: menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_list&err=".urlencode($error_string));
	break;
    case "sms_autoreply_scenario_add":
	$autoreply_id = $_GET[autoreply_id];
	$db_query = "SELECT * FROM playsms_featAutoreply WHERE autoreply_id='$autoreply_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$autoreply_code = $db_row[autoreply_code];
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$formName= "autoReplyScenario";
	$content .= "
	    <h2>Add SMS autoreply scenario</h2>
	    <p>
	    <p>SMS autoreply code: <b>$autoreply_code</b>
	    <p>
	    <form id= \"$formName\"
                  action=menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_add_yes 
                  method=post>
	    <input type=hidden name=autoreply_id value=\"$autoreply_id\">
	";
	for ($i=1;$i<=7;$i++)
	{
	    $content .= "<p>SMS autoreply scenario param $i: <input type=text size=20 maxlength=20 name=add_autoreply_scenario_param$i value=\"".${"add_autoreply_scenario_param".$i}."\">\n";
	}
	$content.= generateSmsInput($formName, "SMS autoreply scenario result:", 
                                    $add_autoreply_scenario_result, "add_autoreply_scenario_result");
	$content.= "<p><input type=submit class=button value=Add>
	    <p><li><a href=\"menu.php?inc=sms_autoreply&op=sms_autoreply_manage&autoreply_id=$autoreply_id\">Back</a>
	    </form>
	";
	echo $content;
	break;
    case "sms_autoreply_scenario_add_yes":
	$autoreply_id = $_POST[autoreply_id];
	$add_autoreply_scenario_result = $_POST["add_autoreply_scenario_result"];
	for ($i=1;$i<=7;$i++)
	{
	    ${"add_autoreply_scenario_param".$i} = strtoupper($_POST["add_autoreply_scenario_param$i"]);
	}
	if ($add_autoreply_scenario_result)
	{
	    for ($i=1;$i<=7;$i++)
	    {
		$autoreply_scenario_param_list .= "autoreply_scenario_param$i,";
	    }
	    for ($i=1;$i<=7;$i++)
	    {
		$autoreply_scenario_code_param_entry .= "'".${"add_autoreply_scenario_param".$i}."',";
	    }
	    $db_query = "
		INSERT INTO playsms_featAutoreply_scenario 
		(autoreply_id,".$autoreply_scenario_param_list."autoreply_scenario_result) VALUES ('$autoreply_id',$autoreply_scenario_code_param_entry'$add_autoreply_scenario_result')";
	    if ($new_uid = dba_insert_id($db_query))
	    {
		$error_string = "SMS autoreply scenario has been added";
	    }
	    else
	    {
	        $error_string = "Fail to add SMS autoreply scenario";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_add&autoreply_id=$autoreply_id&err=".urlencode($error_string));
	break;
    case "sms_autoreply_scenario_edit":
	$autoreply_scenario_id = $_GET[autoreply_scenario_id];
	$autoreply_id = $_GET[autoreply_id];
	$db_query = "SELECT * FROM playsms_featAutoreply WHERE autoreply_id='$autoreply_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$autoreply_code = $db_row[autoreply_code];
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	

	$formName= "autoReplyScenario";
	$content .= "
	    <h2>Edit SMS autoreply scenario</h2>
	    <p>
	    <p>SMS autoreply code: <b>$autoreply_code</b>
	    <p>
	    <form id=\"$formName\" action=menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_edit_yes method=post>
	    <input type=hidden name=autoreply_id value=\"$autoreply_id\">
	    <input type=hidden name=autoreply_scenario_id value=\"$autoreply_scenario_id\">
	";
	$db_query = "SELECT * FROM playsms_featAutoreply_scenario WHERE autoreply_id='$autoreply_id' AND autoreply_scenario_id='$autoreply_scenario_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	for ($i=1;$i<=7;$i++)
	{
	    ${"edit_autoreply_scenario_param".$i} = $db_row["autoreply_scenario_param$i"];
	}
	for ($i=1;$i<=7;$i++)
	{
	    $content .= "<p>SMS autoreply scenario param $i:<input type=text size=20 maxlength=20 name=edit_autoreply_scenario_param$i value=\"".${"edit_autoreply_scenario_param".$i}."\">\n";
	}
	$edit_autoreply_scenario_result = $db_row[autoreply_scenario_result];
	$content.= generateSmsInput($formName, "SMS autoreply scenario result:", 
                                    $edit_autoreply_scenario_result, "edit_autoreply_scenario_result");
	$content.= "<p/><input type=submit class=button value=\"Save\">
	    <p/><li><a href=\"menu.php?inc=sms_autoreply&op=sms_autoreply_manage&autoreply_id=$autoreply_id\">Back</a>
	    </form>
	";
	echo $content;
	break;
    case "sms_autoreply_scenario_edit_yes":
	$autoreply_scenario_id = $_POST[autoreply_scenario_id];
	$autoreply_id = $_POST[autoreply_id];
	$edit_autoreply_scenario_result = $_POST["edit_autoreply_scenario_result"];
	for ($i=1;$i<=7;$i++)
	{
	    ${"edit_autoreply_scenario_param".$i} = strtoupper($_POST["edit_autoreply_scenario_param$i"]);
	}
	if ($edit_autoreply_scenario_result)
	{
	    for ($i=1;$i<=7;$i++)
	    {
		$autoreply_scenario_param_list .= "autoreply_scenario_param$i='".${"edit_autoreply_scenario_param".$i}."',";
	    }
	    $db_query = "
		UPDATE playsms_featAutoreply_scenario 
		SET ".$autoreply_scenario_param_list."autoreply_scenario_result='$edit_autoreply_scenario_result' 
		WHERE autoreply_id='$autoreply_id' AND autoreply_scenario_id='$autoreply_scenario_id'
	    ";
	    if ($db_result = @dba_affected_rows($db_query))
	    {
		$error_string = "SMS autoreply scenario has been edited";
	    }
	    else
	    {
	        $error_string = "Fail to edit SMS autoreply scenario";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=sms_autoreply_scenario&op=sms_autoreply_scenario_edit&autoreply_id=$autoreply_id&autoreply_scenario_id=$autoreply_scenario_id&err=".urlencode($error_string));
	break;
}

?>