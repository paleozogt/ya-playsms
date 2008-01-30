<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};
if (!isadmin()) {
	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

include "$apps_path[plug]/gateway/kannel/config.php";

$op = $_GET[op];

if ($gateway_module == $kannel_param[name]) {
	$status_active = "(<font color=green><b>Active</b></font>)";
} else {
	$status_active = "(<font color=red><b>Inactive</b></font>) (<a href=\"menu_admin.php?inc=gwmod_kannel&op=manage_activate\">click here to activate</a>)";
}

switch ($op) {
	case "manage" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$content .= "
	            <h2>Manage Gateway Module</h2>
	            <p>
	            
	            <form action=menu_admin.php?inc=gwmod_kannel&op=manage_save method=post>
	            <p>Gateway Name: <b>$kannel_param[name]</b> $status_active
	             <p/><a href=\"plugin/gateway/kannel/kannel-monitor/\">[ Monitor Status ]</a>
	             <p/><a href=\"menu_admin.php?inc=gwmod_kannel&op=manage_smsc\">[ Bind SMSC ]</a>
	
	            <p>Username: 
	             <input type=text size=30 maxlength=30 name=up_username value=\"$kannel_param[username]\">
	            <p>Password: 
	             <input type=text size=30 maxlength=30 name=up_password value=\"$kannel_param[password]\">
	            <p>Global Sender:
	              <input type=text size=16 maxlength=16 name=up_global_sender value=\"$kannel_param[global_sender]\"> (Max. 16 numeric or 11 alphanumeric char.)
	            <p>Bearerbox IP: 
	              <input type=text size=30 maxlength=250 name=up_bearerbox_host value=\"" . $kannel_param[bearerbox_host] . "\"> (Kannel's specific)
	            <p>Send SMS Port: 
	              <input type=text size=10 maxlength=10 name=up_sendsms_port value=\"$kannel_param[sendsms_port]\"> (Kannel's specific)
	            <p>Playsms Web URL: 
	              <input type=text size=30 maxlength=250 name=up_playsms_web value=\"$kannel_param[playsms_web]\">
	            <p>Kannel Incoming Path: 
	              <input type=text size=40 maxlength=250 name=up_incoming_path value=\"$kannel_param[path]\"> (No trailing slash \"/\")
	            <p>Note :<br>
	            - When you put <b>/usr/local</b> above, the real path is <b>/usr/local/cache/smsd</b>
	            <!-- <p><input type=checkbox name=up_trn $checked> Send SMS message without footer banner ($username) -->
	            <p><input type=submit class=button value=Save>
	            </form>
	        ";
		echo $content;
		break;
	case "manage_save" :
		$up_username = $_POST['up_username'];
		$up_password = $_POST['up_password'];
		$up_global_sender = $_POST['up_global_sender'];
		$up_bearerbox_host = $_POST['up_bearerbox_host'];
		$up_sendsms_port = $_POST['up_sendsms_port'];
		$up_playsms_web = $_POST['up_playsms_web'];
		$up_incoming_path = $_POST['up_incoming_path'];
		$error_string = "No changes made!";
		if ($up_username && $up_password && $up_bearerbox_host && $up_sendsms_port && $up_playsms_web && $up_incoming_path) {
			$db_query = "
			        UPDATE playsms_gwmodKannel_config 
			        SET 
			            cfg_username='$up_username',
			            cfg_password='$up_password',
			            cfg_global_sender='$up_global_sender',
			            cfg_bearerbox_host='$up_bearerbox_host',
			            cfg_sendsms_port='$up_sendsms_port',
			            cfg_playsms_web='$up_playsms_web',
			            cfg_incoming_path='$up_incoming_path'
		            ";
			if (@ dba_affected_rows($db_query)) {
				$error_string = "Gateway module configurations has been saved";
			}
		}
		header("Location: menu_admin.php?inc=gwmod_kannel&op=manage&err=" . urlencode($error_string));
		break;
	case "manage_activate" :
		$db_query = "UPDATE playsms_tblConfig_main SET cfg_gateway_module='kannel'";
		$db_result = dba_query($db_query);
		$error_string = "Gateway has been activated";
		header("Location: menu_admin.php?inc=gwmod_kannel&op=manage&err=" . urlencode($error_string));
		break;

	case "manage_smsc" :
		echo "<p/>binding smsc...<br/>\n";

		// run the script that lists the available bluetooth
		// devices in an os-indep manner
		$output = exec("$apps_path[bin]/bind-bluetooth-smsc.pl");
		preg_match_all("/(.*?)\s*(..:..:..:..:..:..)/", $output, $matches, PREG_SET_ORDER);
		$smsc_sel = "<select name=\"smsc\">\n";
		$smsc_sel .= "\t<option value=\"none\" selected>Select Modem</option>\n";
		foreach ($matches as $match) {
			$smsc_sel .= "\t<option value=\"$match[2]\">$match[1] ($match[2])</option>\n";
		}
		$smsc_sel .= "</select>";
		?>
        <form name="smsc_bind" action="menu_admin.php?inc=gwmod_kannel&op=smsc_bind" method="post">
            <p/><?echo $smsc_sel?>
            <p/><input type="submit" class="button" value="Bind"/>
        </form>
        <?php


		break;

	case "smsc_bind" :
		$smsc = $_POST['smsc'];
		echo "you chose $smsc <br/>\n";

		// now run the binding script with the smsc address
		// the user chose earlier (note we must run the 
		// script as root)
		//
		$output = exec("sudo $apps_path[bin]/bind-bluetooth-smsc.pl $smsc");
		echo "$output<br/>\n";
		break;
}
?>
