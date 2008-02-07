#!/usr/bin/php
<?php
	include '../web/init.php';
	include '../web/config.php';
	include "$apps_path[libs]/function.php";

	$frequency= @$_SERVER['argv'][1];
	error_log("playsms cron - frequency \"$frequency\"");

	// restart the gateway module?
	if ($frequency == gw_get_restart_freq()) {
		gw_restart();
		gw_waitForStartup();
	}

	// do any autosending for this frequency
	doAutoSend($frequency);

	// restart the system?
    $dbMainConfig= DB_DataObject::factory('playsms_tblConfig_main');
	$dbMainConfig->get(1);
	if ($frequency == $dbMainConfig->cfg_system_restart_frequency) {
		$output= array();
		$rebootwait= 5;
		exec("sudo shutdown -r +$rebootwait 'playsms $frequency reboot' > /dev/null 2>&1 &", $output);
		echo implode('\n', $output);
	}
?>
