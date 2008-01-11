<?
if (!defined("_SECURE_")) {

	die("Intruder: IP [" . $_SERVER['REMOTE_ADDR'] . "] logged");
};

$op = $_GET[op];
$gpid = $_GET[gpid];
$pid = $_GET[pid];

switch ($op) {
	case "group" :
		if ($gpid) {
			$db_query = "DELETE FROM playsms_tblUserGroupPhonebook WHERE gpid='$gpid' AND uid='$uid'";
			if (@ dba_affected_rows($db_query)) {
				$db_query = "DELETE FROM playsms_tblUserPhonebook WHERE gpid='$gpid' AND uid='$uid'";
				$db_result = dba_query($db_query);
			}
		}
		header("Location: fr_right.php");
		break;
	case "user" :
		if ($pid) {
			$db_query = "DELETE FROM playsms_tblUserPhonebook WHERE pid='$pid' AND uid='$uid'";
			$db_result = dba_query($db_query);
		}
		header("Location: fr_right.php");
		break;
}
?>
