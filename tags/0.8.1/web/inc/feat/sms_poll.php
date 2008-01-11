<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$op = $_GET[op];

switch ($op)
{
    case "sms_poll_list":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>List/Edit/Delete SMS polls</h2>
	    <p>
	    <a href=\"menu.php?inc=sms_poll&op=sms_poll_add\">[ Add SMS poll ]</a>
	    <p>
	";
	if (!isadmin())
	{
	    $query_user_only = "WHERE uid='$uid'";
	}
	$db_query = "SELECT * FROM playsms_featPoll $query_user_only ORDER BY poll_id";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result))
	{
	    $owner = uid2username($db_row[uid]);
	    $poll_status = "<font color=red>Disable</font>";
	    if ($db_row[poll_enable])
	    {
		$poll_status = "<font color=green>Enable</font>";
	    }
	    $content .= "[<a href=menu.php?inc=sms_poll&op=sms_poll_view&poll_id=$db_row[poll_id] target=_blank>v</a>] [<a href=menu.php?inc=sms_poll&op=sms_poll_edit&poll_id=$db_row[poll_id]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete SMS poll code `$db_row[poll_code]` with all its choices and votes ?','menu.php?inc=sms_poll&op=sms_poll_del&poll_id=$db_row[poll_id]')\">x</a>] <b>Status:</b> $poll_status &nbsp;&nbsp;<b>Code:</b> $db_row[poll_code] &nbsp;&nbsp;<b>Title:</b> $db_row[poll_title] &nbsp;&nbsp;<b>User:</b> $owner<br>";
	}
	echo $content;
	echo "
	    <p>
	    <a href=\"menu.php?inc=sms_poll&op=sms_poll_add\">[ Add SMS poll ]</a>
	";
	break;
    case "sms_poll_view":
	$poll_id = $_GET[poll_id];
	$db_query = "SELECT poll_code FROM playsms_featPoll WHERE poll_id='$poll_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$poll_code = $db_row[poll_code];
	header ("Location: output.php?show=poll&code=$poll_code");
	break;
    case "sms_poll_edit":
	$poll_id = $_GET[poll_id];
	$db_query = "SELECT * FROM playsms_featPoll WHERE poll_id='$poll_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$edit_poll_title = $db_row[poll_title];
	$edit_poll_code = $db_row[poll_code];
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Edit SMS poll</h2>
	    <p>
	    <form action=menu.php?inc=sms_poll&op=sms_poll_edit_yes method=post>
	    <input type=hidden name=edit_poll_id value=\"$poll_id\">
	    <input type=hidden name=edit_poll_code value=\"$edit_poll_code\">
	    <p>SMS poll code: <b>$edit_poll_code</b>
	    <p>SMS poll title: <input type=text size=60 maxlength=200 name=edit_poll_title value=\"$edit_poll_title\">
	    <p><input type=submit class=button value=\"Save Poll\">
	    </form>
	    <br>
	";
	echo $content;
	$content = "
	    <h2>Edit SMS poll choices</h2>
	    <p>
	";
	$db_query = "SELECT choice_id,choice_title,choice_code FROM playsms_featPoll_choice WHERE poll_id='$poll_id' ORDER BY choice_code";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result))
	{
	    $choice_id = $db_row[choice_id];
	    $choice_code = $db_row[choice_code];
	    $choice_title = $db_row[choice_title];
	    $content .= "[<a href=\"javascript:ConfirmURL('Are you sure you want to delete choice titled `".$choice_title."` with code `".$choice_code."` ?','menu.php?inc=sms_poll&op=sms_poll_choice_del&poll_id=$poll_id&choice_id=$choice_id');\">x</a>] <b>Code:</b> $choice_code &nbsp;&nbsp;<b>Title:</b> $choice_title<br>";
	}
	$content .= "
	    <p><b>Add choice to this poll</b>
	    <form action=\"menu.php?inc=sms_poll&op=sms_poll_choice_add\" method=post>
	    <input type=hidden name=poll_id value=\"$poll_id\">
	    <p>Choice Code: <input type=text size=3 maxlength=10 name=add_choice_code>
	    <p>Choice Title: <input type=text size=60 maxlength=250 name=add_choice_title>
	    <p><input type=submit class=button value=\"Add Choice\">
	    </form>
	    <br>";
	echo $content;
	$db_query = "SELECT poll_enable FROM playsms_featPoll WHERE poll_id='$poll_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$poll_status = "<font color=red><b>Disable</b></font>";
	if ($db_row[poll_enable])
	{
	    $poll_status = "<font color=green><b>Enable</b></font>";
	}
	$content = "
	    <h2>Enable or disable this poll</h2>
	    <p>
	    <p>Current status: $poll_status
	    <p>What do you want to do ?
	    <p>- <a href=\"menu.php?inc=sms_poll&op=sms_poll_status&poll_id=$poll_id&ps=1\">I want to <b>enable</b> this poll</a>
	    <p>- <a href=\"menu.php?inc=sms_poll&op=sms_poll_status&poll_id=$poll_id&ps=0\">I want to <b>disable</b> this poll</a>
	    <br>
	";
	echo $content;
	break;
    case "sms_poll_edit_yes":
	$edit_poll_id = $_POST[edit_poll_id];
	$edit_poll_code = $_POST[edit_poll_code];
	$edit_poll_title = $_POST[edit_poll_title];
	if ($edit_poll_id && $edit_poll_title && $edit_poll_code)
	{
	    $db_query = "
	        UPDATE playsms_featPoll
	        SET poll_title='$edit_poll_title',poll_code='$edit_poll_code'
		WHERE poll_id='$edit_poll_id' AND uid='$uid'
	    ";
	    if (@dba_affected_rows($db_query))
	    {
	        $error_string = "SMS poll with code `$edit_poll_code` has been saved";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=sms_poll&op=sms_poll_edit&poll_id=$edit_poll_id&err=".urlencode($error_string));
	break;
    case "sms_poll_status":
	$poll_id = $_GET[poll_id];
	$ps = $_GET[ps];
	$db_query = "UPDATE playsms_featPoll SET poll_enable='$ps' WHERE poll_id='$poll_id'";
	$db_result = @dba_affected_rows($db_query);
	if ($db_result > 0)
	{
	    $error_string = "This poll status has been changed!";
	}
	header ("Location: menu.php?inc=sms_poll&op=sms_poll_edit&poll_id=$poll_id&err=".urlencode($error_string));
	break;
    case "sms_poll_del":
	$poll_id = $_GET[poll_id];
	$db_query = "SELECT poll_title FROM playsms_featPoll WHERE poll_id='$poll_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$poll_title = $db_row[poll_title];
	if ($poll_title)
	{
	    $db_query = "DELETE FROM playsms_featPoll WHERE poll_title='$poll_title'";
	    if (@dba_affected_rows($db_query))
	    {
		$db_query = "DELETE FROM playsms_tblSMSIncoming WHERE in_poll='$poll_title'";
		if (@dba_affected_rows($db_query))
		{
		    $error_string = "SMS poll `$poll_title` with all its messages has been deleted!";
		}
	    }
	}
	header ("Location: menu.php?inc=sms_poll&op=sms_poll_list&err=".urlencode($error_string));
	break;
    case "sms_poll_choice_add":
	$poll_id = $_POST[poll_id];
	$add_choice_title = $_POST[add_choice_title];
	$add_choice_code = strtoupper($_POST[add_choice_code]);
	if ($poll_id && $add_choice_title && $add_choice_code)
	{
	    $db_query = "SELECT choice_id FROM playsms_featPoll_choice WHERE poll_id='$poll_id' AND choice_code='$add_choice_code'";
	    $db_result = @dba_num_rows($db_query);
	    if (!$db_result)
	    {
		$db_query = "
		    INSERT INTO playsms_featPoll_choice 
		    (poll_id,choice_title,choice_code)
		    VALUES ('$poll_id','$add_choice_title','$add_choice_code')
		";
		if ($db_result = @dba_insert_id($db_query))
		{
		    $error_string = "Choice with code `$add_choice_code` has been added";
		}
	    }
	    else
	    {
		$error_string = "Choice with code `$add_choice_code` already exists";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";	    
	}
	header ("Location: menu.php?inc=sms_poll&op=sms_poll_edit&poll_id=$poll_id&err=".urlencode($error_string));
	break;
    case "sms_poll_choice_del":
	$poll_id = $_GET[poll_id];
	$choice_id = $_GET[choice_id];
	$db_query = "SELECT choice_code FROM playsms_featPoll_choice WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$choice_code = $db_row[choice_code];
	$error_string = "Fail to delete SMS poll choice with code `$choice_code`!";
	if ($poll_id && $choice_id && $choice_code)
	{
	    $db_query = "DELETE FROM playsms_featPoll_choice WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
	    if (@dba_affected_rows($db_query))
	    {
		$db_query = "DELETE FROM playsms_featPoll_result WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
		dba_query($db_query);
		$error_string = "SMS poll choice with code `$choice_code` and all its voters has been deleted!";
	    }
	}
	header ("Location: menu.php?inc=sms_poll&op=sms_poll_edit&poll_id=$poll_id&err=".urlencode($error_string));
	break;
    case "sms_poll_add":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Add SMS poll</h2>
	    <p>
	    <form action=menu.php?inc=sms_poll&op=sms_poll_add_yes method=post>
	    <p>SMS poll code: <input type=text size=3 maxlength=10 name=add_poll_code value=\"$add_poll_code\">
	    <p>SMS poll title: <input type=text size=60 maxlength=200 name=add_poll_title value=\"$add_poll_title\">
	    <p><input type=submit class=button value=Add>
	    </form>
	";
	echo $content;
	break;
    case "sms_poll_add_yes":
	$add_poll_code = strtoupper($_POST[add_poll_code]);
	$add_poll_title = $_POST[add_poll_title];
	if ($add_poll_title && $add_poll_code)
	{
	    if (checkavailablecode($add_poll_code))
	    {
		$db_query = "
		    INSERT INTO playsms_featPoll (uid,poll_code,poll_title)
		    VALUES ('$uid','$add_poll_code','$add_poll_title')
		";
		if ($new_uid = @dba_insert_id($db_query))
		{
		    $error_string = "SMS poll with code `$add_poll_code` has been added";
		}
	    }
	    else
	    {
		$error_string = "SMS code `$add_poll_code` already exists, reserved or use by other feature!";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=sms_poll&op=sms_poll_add&err=".urlencode($error_string));
	break;
}

?>