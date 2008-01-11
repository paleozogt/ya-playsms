<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$op = $_GET[op];

switch ($op)
{
    case "user_list":
	$db_query = "SELECT * FROM playsms_tblUser WHERE status='2' ORDER BY username";
	$db_result = dba_query($db_query);
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>List/Edit/Delete user</h2>
	    <p>
	    <a href=\"menu_admin.php?inc=user_mgmnt&op=user_add\">[ Add user ]</a>
	    <p>
	    <font size=+1>Status: <font color=darkgreen>Administrator</font></font><br>
	";
	while ($db_row = dba_fetch_array($db_result))
	{
	    $content .= "[<a href=menu_admin.php?inc=user_mgmnt&op=user_edit&uname=$db_row[username]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete user `$db_row[username]` ?','menu_admin.php?inc=user_mgmnt&op=user_del&uname=$db_row[username]')\">x</a>] $db_row[username] ($db_row[name]) &nbsp;&nbsp;<b>Sender ID:</b> $db_row[mobile]<br>";
	}
	/*
	$db_query = "SELECT * FROM playsms_tblUser WHERE status='1' ORDER BY username";
	$db_result = dba_query($db_query);
	$content .= "<p><font size=+1>Status: <font color=darkgreen>Advertiser User</font></font><br>";
	while ($db_row = dba_fetch_array($db_result))
	{
	    $content .= "[<a href=menu_admin.php?inc=user_mgmnt&op=user_edit&uname=$db_row[username]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete user `$db_row[username]` ?','menu_admin.php?inc=user_mgmnt&op=user_del&uname=$db_row[username]')\">x</a>] $db_row[username] ($db_row[name]) &nbsp;&nbsp;<b>Sender ID:</b> $db_row[mobile]<br>";
	}
	*/
	$db_query = "SELECT * FROM playsms_tblUser WHERE status='3' ORDER BY username";
	$db_result = dba_query($db_query);
	$content .= "<p><font size=+1>Status: <font color=darkgreen>Normal User</font></font><br>";
	while ($db_row = dba_fetch_array($db_result))
	{
	    $content .= "[<a href=menu_admin.php?inc=user_mgmnt&op=user_edit&uname=$db_row[username]>e</a>] [<a href=\"javascript: ConfirmURL('Are you sure you want to delete user `$db_row[username]` ?','menu_admin.php?inc=user_mgmnt&op=user_del&uname=$db_row[username]')\">x</a>] $db_row[username] ($db_row[name]) &nbsp;&nbsp;<b>Sender ID:</b> $db_row[mobile]<br>";
	}
	echo $content;
	echo "
	    <p>
	    <a href=\"menu_admin.php?inc=user_mgmnt&op=user_add\">[ Add user ]</a>
	";
	break;
    case "user_del":
	$uname = $_GET[uname];
	$del_uid = username2uid($uname);
	$error_string = "Fail to delete user `$uname`!";
	if (($del_uid > 1) && ($del_uid != $uid))
	{
	    $db_query = "DELETE FROM playsms_tblUser WHERE uid='$del_uid'";
	    if (@dba_affected_rows($db_query))
	    {
		$error_string = "User `$uname` has been deleted!";
	    }
	}
	if (($del_uid == 1) || ($uname == "admin"))
	{
	    $error_string = "User `$uname` is immune to deletion!";
	}
	else if ($del_uid == $uid)
	{
	    $error_string = "Current logged in user is immune to deletion!";
	}
	header ("Location: menu_admin.php?inc=user_mgmnt&op=user_list&err=".urlencode($error_string));
	break;
    case "user_edit":
	$uname = $_GET[uname];
	$uid = username2uid($uname);
	$mobile = username2mobile($uname);
	$email = username2email($uname);
	$name = username2name($uname);
	$status = username2status($uname);
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	// if ($status == 1) { $selected_1 = "selected"; }
	if ($status == 2) { $selected_2 = "selected"; }
	if ($status == 3) { $selected_3 = "selected"; }
	$option_status = "
	    <option value=2 $selected_2>Administrator</option>
	    <!--
	    <option value=1 $selected_1>Advertiser</option>
	    -->
	    <option value=3 $selected_3>Normal User</option>
	";
	$content .= "
	    <h2>Preferences: $uname</h2>
	    <p>
	    <form action=menu_admin.php?inc=user_mgmnt&op=user_edit_save method=post>
	    <input type=hidden name=uname value=\"$uname\">
	    <p>Username: <b>$uname</b>
	    <p>Email: <input type=text size=30 maxlength=30 name=up_email value=\"$email\">
	    <p>Full name: <input type=text size=30 maxlength=30 name=up_name value=\"$name\">
	    <p>Mobile number: <input type=text size=16 maxlength=16 name=up_mobile value=\"$mobile\"> (Max. 16 numeric or 11 alphanumeric char.)
	    <p>SMS footer (SMS sender ID): <input type=text size=35 maxlength=30 name=up_sender value=\"$sender\"> (Max. 30 Alphanumeric char.)
	    <p>Password: <input type=text size=30 maxlength=30 name=up_password> (Fill to change password for username `$uname`)
	    <p>User level: <select name=up_status>$option_status</select>
	    <p><input type=submit class=button value=save>
	    </form>
	";
	echo $content;
	break;
    case "user_edit_save":
	$uname = $_POST[uname];
	$up_name = $_POST[up_name];
	$up_email = $_POST[up_email];
	$up_mobile = $_POST[up_mobile];
	$up_sender = $_POST[up_sender];
	$up_password = $_POST[up_password];
	$up_status = $_POST[up_status];
//	$status = username2status($uname);
	$error_string = "No changes made!";
	if ($up_name && $up_mobile && $up_email)
	{
	    $db_query = "SELECT email FROM playsms_tblUser WHERE email=$email' AND NOT username='$uname'";
	    $db_result = dba_num_rows($db_query);
	    if ($db_result > 0)
	    {
		$error_string = "Email `$email` already in use by other username";
	    }
	    else
	    {
		if ($up_password)
		{
		    $chg_pwd = ",password='$up_password'";
		}
		$db_query = "UPDATE playsms_tblUser SET name='$up_name',email='$up_email',mobile='$up_mobile',sender='$up_sender',status='$up_status'".$chg_pwd." WHERE username='$uname'";
		if (@dba_affected_rows($db_query))
		{
		    $error_string = "Preferences for user `$uname` has been saved";
		}
		else
		{
		    $error_string = "Fail to save preferences for `$uname`";
		}
	    }
	}
	else
	{
	    $error_string = "Empty field is not allowed";
	}
	header ("Location: menu_admin.php?inc=user_mgmnt&op=user_edit&uname=$uname&err=".urlencode($error_string));
	break;
    case "user_add":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$option_status = "
	    <option value=2>Administrator</option>
	    <!--
	    <option value=1>Advertiser</option>
	    -->
	    <option value=3 selected>Normal User</option>
	";
	$content .= "
	    <h2>Add user</h2>
	    <p>
	    <form action=menu_admin.php?inc=user_mgmnt&op=user_add_yes method=post>
	    <p>Username: <input type=text size=30 maxlength=30 name=add_username value=\"$add_username\">
	    <p>Email: <input type=text size=30 maxlength=30 name=add_email value=\"$add_email\"> (Format: username@somedomain.com eg: anton@ngoprek.org)
	    <p>Full name: <input type=text size=30 maxlength=30 name=add_name value=\"$add_name\">
	    <p>Mobile number: <input type=text size=16 maxlength=16 name=add_mobile value=\"$add_mobile\"> (Max. 16 numeric or 11 alphanumeric char.)
	    <p>SMS footer (SMS sender ID): <input type=text size=35 maxlength=30 name=add_sender value=\"$add_sender\"> (Max. 30 Alphanumeric char.)
	    <p>Password: <input type=text size=30 maxlength=30 name=add_password value=\"$add_password\">
	    <p>User level: <select name=add_status>$option_status</select>
	    <p><input type=submit class=button value=Add>
	    </form>
	";
	echo $content;
	break;
    case "user_add_yes":
	$add_email = $_POST[add_email];
	$add_username = $_POST[add_username];
	$add_name = $_POST[add_name];
	$add_mobile = $_POST[add_mobile];
	$add_sender = $_POST[add_sender];
	$add_password = $_POST[add_password];
	$add_status = $_POST[add_status];
	if (ereg("^(.+)(.+)\\.(.+)$",$add_email,$arr) && $add_email && $add_username && $add_name && $add_password)
	{
	    $db_query = "SELECT * FROM playsms_tblUser WHERE username='$add_username'";
	    $db_result = dba_query($db_query);
	    if ($db_row = dba_fetch_array($db_result))
	    {
		$error_string = "User with username `$db_row[username]` already exists!";
	    }
	    else
	    {
		$db_query = "
		    INSERT INTO playsms_tblUser (status,username,password,name,mobile,email,sender)
		    VALUES ('$add_status','$add_username','$add_password','$add_name','$add_mobile','$add_email','$add_sender')
		";
		if ($new_uid = @dba_insert_id($db_query))
		{
		    $error_string = "User with username `$add_username` has been added";
		}
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu_admin.php?inc=user_mgmnt&op=user_add&err=".urlencode($error_string));
	break;
}

?>