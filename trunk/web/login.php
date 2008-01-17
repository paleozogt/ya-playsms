<?php
include "init.php";
include "$apps_path[libs]/function.php";

$username = trim($_POST[username]);
$password = trim($_POST[password]);

if ($_POST[username] && $_POST[password]) {
	if ($ticket = validatelogin($username, $password)) {
		$db_query = "UPDATE playsms_tblUser SET ticket='$ticket' WHERE username='$username'";
		if (@ dba_affected_rows($db_query)) {
			setcookie("vc1", "$ticket");
			setcookie("vc2", "$username");

			if ($apps_config['multilogin']) {
				$multilogin_id = md5($username . $password);
				setcookie("vc3", "$multilogin_id");
			}

			header("Location: user.php");
			die();
		}
	}
}

header("Location: index.php?err=" . urlencode("Your username or password is not valid!"));
?>
