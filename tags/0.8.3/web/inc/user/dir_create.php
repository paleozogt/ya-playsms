<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];

switch ($op) {
	case "create" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
		    <h2>Create group</h2>
		    <p>
		    <form action=menu.php?inc=dir_create&op=create_yes method=POST>
		    <p>Group Name: <input type=text name=dir_name size=50>
		    <p>Group Code: <input type=text name=dir_code size=10> (please use uppercase and make it short)
		    <p>Note: Group Code used by code BC (broadcast SMS from single SMS)
		    <p><input type=submit class=button value=Create> 
		    </form>
		";
		echo $content;
		break;
	case "create_yes" :
		$dir_name = $_POST[dir_name];
		$dir_code = strtoupper(trim($_POST[dir_code]));
		if ($dir_name && $dir_code) {
			$db_query = "SELECT gp_code FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' AND gp_code='$dir_code'";
			$db_result = dba_query($db_query);
			if ($db_row = dba_fetch_array($db_result)) {
				header("Location: menu.php?inc=dir_create&op=create&err=" . urlencode("Group code `$dir_code` already in use"));
				die();
			} else {
				$db_query = "INSERT INTO playsms_tblUserGroupPhonebook (uid,gp_name,gp_code) VALUES ('$uid','$dir_name','$dir_code')";
				$db_result = dba_query($db_query);
				header("Location:  menu.php?inc=dir_create&op=create&err=" . urlencode("Group `$dir_name` with code `$dir_code` has been added"));
				die();
			}
		}
		header("Location: menu.php?inc=dir_create&op=create&err=" . urlencode("Group name and description must be filled"));
		break;
}
?>
