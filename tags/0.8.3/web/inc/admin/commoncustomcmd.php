<?php


// update user's age
// playsmsd process daemon every 20s, therefor 180 counter means 20*180s = 3600s = 1h
if (!($DAEMON_COUNTER % 180)) {
	$db_query = "SELECT uid,birthday FROM playsms_tblUser WHERE birthday NOT LIKE '0000-00-00 00:00:00'";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result)) {
		$c_uid = $db_row["uid"];
		$c_birthday = strtotime($db_row["birthday"]);
		$c_age = floor(intval(time() - $c_birthday) / 31536000);
		$db_query1 = "UPDATE playsms_tblUser SET age='$c_age' WHERE uid='$c_uid'";
		$db_result1 = dba_query($db_query1);
	}
}
?>
