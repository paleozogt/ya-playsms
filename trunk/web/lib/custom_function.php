<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

function websend2pv($username, $sms_to, $message, $sms_type = "text", $unicode = "0") {
	global $apps_path, $SMS_MAXCHARS;
	global $datetime_now, $gateway_module;
	$uid = username2uid($username);
	$mobile_sender = username2mobile($username);
	$max_length = $SMS_MAXCHARS;
	if ($sms_footer = username2footer($username)) {
		$max_length = $max_length -strlen($sms_footer) - 1;
	}
	if (strlen($message) > $max_length) {
		$message = substr($message, 0, $max_length -1);
	}
	$sms_msg = $message;
	$sms_msg = str_replace("\r\n", "\n", $sms_msg);
	$sms_msg = str_replace("\r", "\n", $sms_msg);
	//$sms_msg = str_replace("\""," ",$sms_msg);
	$mobile_sender = str_replace("\'", "", $mobile_sender);
	$mobile_sender = str_replace("\"", "", $mobile_sender);
	$sms_footer = str_replace("\'", "", $sms_footer);
	$sms_footer = str_replace("\"", "", $sms_footer);
	if (is_array($sms_to)) {
		$array_sms_to = $sms_to;
	} else {
		$array_sms_to[0] = $sms_to;
	}
	for ($i = 0; $i < count($array_sms_to); $i++) {
		$c_sms_to = str_replace("\'", "", $array_sms_to[$i]);
		$c_sms_to = str_replace("\"", "", $array_sms_to[$i]);
		$db_query = "
			    INSERT INTO playsms_tblSMSOutgoing 
			    (uid,p_gateway,p_src,p_dst,p_footer,p_msg,p_datetime,p_sms_type,unicode) 
			    VALUES ('$uid','$gateway_module','$mobile_sender','$c_sms_to','$sms_footer','$message','$datetime_now','$sms_type','$unicode')
			";
		$smslog_id = @ dba_insert_id($db_query);
		$gp_code = "PV";
		$to[$i] = $c_sms_to;
		$ok[$i] = 0;
		if ($smslog_id) {
			if (gw_send_sms($mobile_sender, $sms_footer, $c_sms_to, $sms_msg, $gp_code, $uid, $smslog_id, $sms_type, $unicode)) {
				$ok[$i] = $smslog_id;
			}
		}
	}
	return array (
		$ok,
		$to
	);
}

function websend2group($username, $gp_code, $message, $sms_type = "text", $unicode = "0") {
	global $apps_path, $SMS_MAXCHARS;
	global $datetime_now, $gateway_module;
	$uid = username2uid($username);
	$mobile_sender = username2mobile($username);
	$max_length = $SMS_MAXCHARS;
	if ($sms_footer = username2footer($username)) {
		$max_length = $max_length -strlen($sms_footer) - 1;
	}
	if (strlen($message) > $max_length) {
		$message = substr($message, 0, $max_length -1);
	}
	if (is_array($gp_code)) {
		$array_gp_code = $gp_code;
	} else {
		$array_gp_code[0] = $gp_code;
	}
	$j = 0;
	for ($i = 0; $i < count($array_gp_code); $i++) {
		$c_gp_code = strtoupper($array_gp_code[$i]);
		$gpid = gpcode2gpid($uid, $c_gp_code);
		$db_query = "SELECT * FROM playsms_tblUserPhonebook WHERE gpid='$gpid'";
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$p_num = $db_row[p_num];
			$sms_to = $p_num;
			$sms_msg = $message;
			$sms_msg = str_replace("\r", "", $sms_msg);
			$sms_msg = str_replace("\n", "", $sms_msg);
			$sms_msg = str_replace("\"", " ", $sms_msg);
			$mobile_sender = str_replace("\'", "", $mobile_sender);
			$mobile_sender = str_replace("\"", "", $mobile_sender);
			$sms_footer = str_replace("\'", "", $sms_footer);
			$sms_footer = str_replace("\"", "", $sms_footer);
			$sms_to = str_replace("\'", "", $sms_to);
			$sms_to = str_replace("\"", "", $sms_to);
			$the_msg = "$sms_to\n$sms_msg";
			$db_query1 = "
					INSERT INTO playsms_tblSMSOutgoing 
					(uid,p_gateway,p_src,p_dst,p_footer,p_msg,p_datetime,p_gpid,p_sms_type) 
					VALUES ('$uid','$gateway_module','$mobile_sender','$sms_to','$sms_footer','$message','$datetime_now','$gpid','$sms_type')
				    ";
			$smslog_id = @ dba_insert_id($db_query1);
			$to[$j] = $sms_to;
			$ok[$j] = 0;
			if ($smslog_id) {
				if (gw_send_sms($mobile_sender, $sms_footer, $sms_to, $sms_msg, $c_gp_code, $uid, $smslog_id, $sms_type, $unicode)) {
					$ok[$j] = $sms_to;
				}
			}
			$j++;
		}
	}
	return array (
		$ok,
		$to
	);
}

function send2group($mobile_sender, $gp_code, $message) {
	global $apps_path, $SMS_MAXCHARS;
	global $datetime_now;
	$ok = false;
	if ($mobile_sender && $gp_code && $message) {
		$db_query = "SELECT uid,username,sender FROM playsms_tblUser WHERE mobile='$mobile_sender'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$uid = $db_row[uid];
		$username = $db_row[username];
		$sms_footer = $db_row[sender];
		if ($uid && $username) {
			$gp_code = strtoupper($gp_code);
			$db_query = "SELECT * FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' AND gp_code='$gp_code'";
			$db_result = dba_query($db_query);
			$db_row = dba_fetch_array($db_result);
			$gpid = $db_row[gpid];
			if ($gpid && $message) {
				$db_query = "SELECT * FROM playsms_tblUserPhonebook WHERE gpid='$gpid' AND uid='$uid'";
				$db_result = dba_query($db_query);
				while ($db_row = dba_fetch_array($db_result)) {
					$p_num = $db_row[p_num];
					$sms_to = $p_num;
					$max_length = $SMS_MAXCHARS -strlen($sms_footer) - 3;
					if (strlen($message) > $max_length) {
						$message = substr($message, 0, $max_length -1);
					}
					$sms_msg = $message;
					$sms_msg = str_replace("\r", "", $sms_msg);
					$sms_msg = str_replace("\n", "", $sms_msg);
					$sms_msg = str_replace("\"", " ", $sms_msg);
					$the_msg = "$sms_to\n$sms_msg";
					$mobile_sender = str_replace("\'", "", $mobile_sender);
					$mobile_sender = str_replace("\"", "", $mobile_sender);
					$sms_footer = str_replace("\'", "", $sms_footer);
					$sms_footer = str_replace("\"", "", $sms_footer);
					$sms_to = str_replace("\'", "", $sms_to);
					$sms_to = str_replace("\"", "", $sms_to);
					$send_code = md5(mktime() . $sms_to);
					$db_query1 = "
								INSERT INTO playsms_tblSMSOutgoing (uid,p_src,p_dst,p_footer,p_msg,p_datetime,p_gpid) 
								VALUES ('$uid','$mobile_sender','$sms_to','$sms_footer','$message','$datetime_now','$gpid')";
					$smslog_id = @ dba_insert_id($db_query1);
					$sms_id = "$gp_code.$uid.$smslog_id";
					if ($smslog_id) {
						if (gw_send_sms($mobile_sender, $sms_footer, $sms_to, $sms_msg, $gp_code, $uid, $smslog_id)) {
							$ok = true;
						}
					}
				}
			}
		}
	}
	return $ok;
}

function insertsmstodb($sms_datetime, $sms_sender, $target_code, $message) {
	global $web_title, $email_service, $email_footer, $gateway_module;
	$ok = false;
	if ($sms_sender && $target_code && $message) {
		// masked sender sets here
		$masked_sender = substr_replace($sms_sender, 'xxxx', -4);
		$db_query = "
			    INSERT INTO playsms_tblSMSIncoming 
			    (in_gateway,in_sender,in_masked,in_code,in_msg,in_datetime) 
			    VALUES ('$gateway_module','$sms_sender','$masked_sender','$target_code','$message','$sms_datetime')
			";
		if ($cek_ok = @ dba_insert_id($db_query)) {
			$db_query1 = "SELECT board_forward_email FROM playsms_featBoard WHERE board_code='$target_code'";
			$db_result1 = dba_query($db_query1);
			$db_row1 = dba_fetch_array($db_result1);
			$email = $db_row1[board_forward_email];
			if ($email) {
				$subject = "[SMSGW-$target_code] from $sms_sender";
				$body = "Forward WebSMS ($web_title)\n\n";
				$body .= "Date Time: $sms_datetime\n";
				$body .= "Sender: $sms_sender\n";
				$body .= "Code: $target_code\n\n";
				$body .= "Message:\n$message\n\n";
				$body .= $email_footer . "\n\n";
				sendmail($email_service, $email, $subject, $body);
			}
			$ok = true;
		}
	}
	return $ok;
}

function insertsmstoinbox($sms_datetime, $sms_sender, $target_user, $message) {
	global $web_title, $email_service, $email_footer;
	$ok = false;
	if ($sms_sender && $target_user && $message) {
		$db_query = "SELECT uid,email,mobile FROM playsms_tblUser WHERE username='$target_user'";
		$db_result = dba_query($db_query);
		if ($db_row = dba_fetch_array($db_result)) {
			$uid = $db_row[uid];
			$email = $db_row[email];
			$mobile = $db_row[mobile];
			$db_query = "
					INSERT INTO playsms_tblUserInbox
					(in_sender,in_uid,in_msg,in_datetime) 
					VALUES ('$sms_sender','$uid','$message','$sms_datetime')
				    ";
			if ($cek_ok = @ dba_insert_id($db_query)) {
				if ($email) {
					$subject = "[SMSGW-PV] from $sms_sender";
					$body = "Forward Private WebSMS ($web_title)\n\n";
					$body .= "Date Time: $sms_datetime\n";
					$body .= "Sender: $sms_sender\n";
					$body .= "Receiver: $mobile\n\n";
					$body .= "Message:\n$message\n\n";
					$body .= $email_footer . "\n\n";
					sendmail($email_service, $email, $subject, $body);
				}
				$ok = true;
			}
		}
	}
	return $ok;
}

function getsmsinbox() {
	gw_set_incoming_action();
}

function getsmsstatus() {
	global $gateway_module;
	$db_query = "SELECT * FROM playsms_tblSMSOutgoing WHERE p_status='0' AND p_gateway='$gateway_module'";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result)) {
		$gpid = "";
		$gp_code = "";
		$uid = $db_row[uid];
		$smslog_id = $db_row[smslog_id];
		$p_datetime = $db_row[p_datetime];
		$p_update = $db_row[p_update];
		$gpid = $db_row[p_gpid];
		$gp_code = gpid2gpcode($gpid);
		gw_set_delivery_status($gp_code, $uid, $smslog_id, $p_datetime, $p_update);
	}
}

function execgwcustomcmd() {
	if (function_exists("gw_customcmd")) {
		gw_customcmd();
	}
}

function execcommoncustomcmd() {
	global $apps_path;
	@ include $apps_path[incs] . "/admin/commoncustomcmd.php";
}

function setsmsdeliverystatus($smslog_id, $uid, $p_status) {
	global $datetime_now;
	$ok = false;
	$db_query = "UPDATE playsms_tblSMSOutgoing SET p_update='$datetime_now',p_status='$p_status' WHERE smslog_id='$smslog_id' AND uid='$uid'";
	if ($aff_id = @ dba_affected_rows($db_query)) {
		$ok = true;
	}
	return $ok;
}

function checkavailablecode($code) {
	global $reserved_codes;
	$ok = true;
	$reserved = false;
	for ($i = 0; $i < count($reserved_codes); $i++) {
		if ($code == $reserved_codes[$i]) {
			$reserved = true;
		}
	}
	if ($reserved) {
		$ok = false;
	} else {
		// check for SMS autoreply
		$db_query = "SELECT autoreply_id FROM playsms_featAutoreply WHERE autoreply_code='$code'";
		if ($db_result = dba_num_rows($db_query)) {
			$ok = false;
		}
		// check for SMS board
		$db_query = "SELECT board_id FROM playsms_featBoard WHERE board_code='$code'";
		if ($db_result = dba_num_rows($db_query)) {
			$ok = false;
		}
		// check for SMS command
		$db_query = "SELECT command_id FROM playsms_featCommand WHERE command_code='$code'";
		if ($db_result = dba_num_rows($db_query)) {
			$ok = false;
		}
		// check for SMS custom
		$db_query = "SELECT custom_id FROM playsms_featCustom WHERE custom_code='$code'";
		if ($db_result = dba_num_rows($db_query)) {
			$ok = false;
		}
		// check for SMS poll
		$db_query = "SELECT poll_id FROM playsms_featPoll WHERE poll_code='$code'";
		if ($db_result = dba_num_rows($db_query)) {
			$ok = false;
		}
	}
	return $ok;
}

// part of SMS board
function outputtorss($code, $line = "10") {
	global $apps_path, $web_title;
	include_once "$apps_path[libs]/gpl/feedcreator.class.php";
	$code = strtoupper($code);
	if (!$line) {
		$line = "10";
	};
	$format_output = "RSS0.91";
	$rss = new UniversalFeedCreator();
	$db_query1 = "SELECT * FROM playsms_tblSMSIncoming WHERE in_code='$code' ORDER BY in_datetime DESC LIMIT 0,$line";
	$db_result1 = dba_query($db_query1);
	while ($db_row1 = dba_fetch_array($db_result1)) {
		$title = $db_row1[in_masked];
		$description = $db_row1[in_msg];
		$datetime = $db_row1[in_datetime];
		$items = new FeedItem();
		$items->title = $title;
		$items->description = $description;
		$items->comments = $datetime;
		$items->date = strtotime($datetime);
		$rss->addItem($items);
	}
	$feeds = $rss->createFeed($format_output);
	return $feeds;
}

// part of SMS board
function outputtohtml($code, $line = "10", $pref_bodybgcolor = "#E0D0C0", $pref_oddbgcolor = "#EEDDCC", $pref_evenbgcolor = "#FFEEDD") {
	global $apps_path, $web_title;
	$code = strtoupper($code);
	if (!$line) {
		$line = "10";
	};
	if (!$pref_bodybgcolor) {
		$pref_bodybgcolor = "#E0D0C0";
	}
	if (!$pref_oddbgcolor) {
		$pref_oddbgcolor = "#EEDDCC";
	}
	if (!$pref_evenbgcolor) {
		$pref_evenbgcolor = "#FFEEDD";
	}
	$db_query = "SELECT board_pref_template FROM playsms_featBoard WHERE board_code='$code'";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result)) {
		$template = $db_row[board_pref_template];
		$db_query1 = "SELECT * FROM playsms_tblSMSIncoming WHERE in_code='$code' ORDER BY in_datetime DESC LIMIT 0,$line";
		$db_result1 = dba_query($db_query1);
		$content = "<html>\n<head>\n<title>$web_title - Code: $code</title>\n<meta name=\"author\" content=\"http://playsms.sourceforge.net\">\n</head>\n<body bgcolor=\"$pref_bodybgcolor\" topmargin=\"0\" leftmargin=\"0\">\n<table width=100% cellpadding=2 cellspacing=2>\n";
		$i = 0;
		while ($db_row1 = dba_fetch_array($db_result1)) {
			$i++;
			$sender = $db_row1[in_masked];
			$datetime = $db_row1[in_datetime];
			$message = $db_row1[in_msg];
			$tmp_template = $template;
			$tmp_template = str_replace("##SENDER##", $sender, $tmp_template);
			$tmp_template = str_replace("##DATETIME##", $datetime, $tmp_template);
			$tmp_template = str_replace("##MESSAGE##", $message, $tmp_template);
			if (($i % 2) == 0) {
				$pref_zigzagcolor = "$pref_evenbgcolor";
			} else {
				$pref_zigzagcolor = "$pref_oddbgcolor";
			}
			$content .= "\n<tr><td width=100% bgcolor=\"$pref_zigzagcolor\">\n$tmp_template</td></tr>\n\n";
		}
		$content .= "</table>\n</body>\n</html>\n";
		return $content;
	}
}

// part of SMS command
function execcommand($sms_datetime, $sms_sender, $command_code, $command_param) {
	global $datetime_now;
	$ok = false;
	$db_query = "SELECT command_exec FROM playsms_featCommand WHERE command_code='$command_code'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$command_exec = $db_row[command_exec];
	$command_exec = str_replace("##SMSDATETIME##", "$sms_datetime", $command_exec);
	$command_exec = str_replace("##SMSSENDER##", "$sms_sender", $command_exec);
	$command_exec = str_replace("##COMMANDCODE##", "$command_code", $command_exec);
	$command_exec = str_replace("##COMMANDPARAM##", "$command_param", $command_exec);
	$command_output = shell_exec(stripslashes($command_exec));
	$db_query = "
		INSERT INTO playsms_featCommand_log
		(sms_sender,command_log_datetime,command_log_code,command_log_exec) 
		VALUES
		('$sms_sender','$datetime_now','$command_code','$command_exec')
	    ";
	if ($new_id = @ dba_insert_id($db_query)) {
		$ok = true;
	}
	return $ok;
}

// part of SMS custom
function processcustom($sms_datetime, $sms_sender, $custom_code, $custom_param) {
	global $datetime_now;
	$ok = false;
	$db_query = "SELECT custom_url FROM playsms_featCustom WHERE custom_code='$custom_code'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$custom_url = $db_row[custom_url];
	$custom_url = str_replace("##SMSDATETIME##", urlencode($sms_datetime), $custom_url);
	$custom_url = str_replace("##SMSSENDER##", urlencode($sms_sender), $custom_url);
	$custom_url = str_replace("##CUSTOMCODE##", urlencode($custom_code), $custom_url);
	$custom_url = str_replace("##CUSTOMPARAM##", urlencode($custom_param), $custom_url);
	$url = parse_url($custom_url);
	if (!$url['port']) {
		$url['port'] = 80;
	}
	$connection = fsockopen($url['host'], $url['port'], & $error_number, & $error_description, 60);
	if ($connection) {
		socket_set_blocking($connection, false);
		fputs($connection, "GET $custom_url HTTP/1.0\r\n\r\n");
		$db_query = "
			    INSERT INTO playsms_featCustom_log
			    (sms_sender,custom_log_datetime,custom_log_code,custom_log_url) 
			    VALUES
			    ('$sms_sender','$datetime_now','$custom_code','$custom_url')
			";
		if ($new_id = @ dba_insert_id($db_query)) {
			$ok = true;
		}
	}
	return $ok;
}

// part of SMS autoreply
function processautoreply($sms_datetime, $sms_sender, $autoreply_code, $autoreply_param) {
	global $datetime_now;
	$ok = false;
	$autoreply_request = $autoreply_code . " " . $autoreply_param;
	$array_autoreply_request = explode(" ", $autoreply_request);
	for ($i = 0; $i < count($array_autoreply_request); $i++) {
		$autoreply_part[$i] = trim($array_autoreply_request[$i]);
		$tmp_autoreply_request .= $array_autoreply_request[$i] . " ";
	}
	$autoreply_request = trim($tmp_autoreply_request);
	for ($i = 1; $i < 8; $i++) {
		$autoreply_scenario_param_list .= "autoreply_scenario_param$i='" . $autoreply_part[$i] . "' AND ";
	}
	$db_query = "
		SELECT autoreply_scenario_result FROM playsms_featAutoreply, playsms_featAutoreply_scenario 
		WHERE 
	        playsms_featAutoreply.autoreply_id=playsms_featAutoreply_scenario.autoreply_id AND 
	        autoreply_code='$autoreply_code' AND 
	        $autoreply_scenario_param_list 1=1
	    ";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	if ($autoreply_scenario_result = $db_row[autoreply_scenario_result]) {
		$db_query = "
			    INSERT INTO playsms_featAutoreply_log
			    (sms_sender,autoreply_log_datetime,autoreply_log_code,autoreply_log_request) 
			    VALUES
			    ('$sms_sender','$datetime_now','$autoreply_code','$autoreply_request')
			";
		if ($new_id = @ dba_insert_id($db_query)) {
			$ok = true;
		}
	}
	// if we don't understand the params, then process
	// the message as a special _UNKNOWN_ message
	else if (0 != strcasecmp($autoreply_param, _UNKNOWN_)) {
		$ok= processautoreply($sms_datetime, $sms_sender, $autoreply_code, _UNKNOWN_);
	}

	if ($ok) {
		$ok = false;
		$db_query = "SELECT uid FROM playsms_featAutoreply WHERE autoreply_code='$autoreply_code'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$c_uid = $db_row[uid];
		$c_username = uid2username($c_uid);
		$smslog_id = websend2pv($c_username, $sms_sender, $autoreply_scenario_result);
		if ($smslog_id) {
			$ok = true;
		}
	}
	return $ok;
}

function processUnknown($sms_datetime, $sms_sender, $target_code, $message) {
	// when we don't understand what the user texted us, 
	// reply with the special unknown autoreply
	processautoreply($sms_datetime, $sms_sender, _UNKNOWN_, "");
}

// part of SMS poll
function savepoll($sms_sender, $target_poll, $target_choice) {
	$ok = false;
	$target_poll = strtoupper($target_poll);
	$target_choice = strtoupper($target_choice);
	if ($sms_sender && $target_poll && $target_choice) {
		$db_query = "SELECT poll_id,poll_enable FROM playsms_featPoll WHERE poll_code='$target_poll'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$poll_id = $db_row[poll_id];
		$poll_enable = $db_row[poll_enable];
		$db_query = "SELECT choice_id FROM playsms_featPoll_choice WHERE choice_code='$target_choice' AND poll_id='$poll_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$choice_id = $db_row[choice_id];
		if ($poll_id && $choice_id) {
			$db_query = "SELECT result_id FROM playsms_featPoll_result WHERE poll_sender='$sms_sender' AND poll_id='$poll_id'";
			$already_vote = @ dba_num_rows($db_query);
			if ((!$already_vote) && $poll_enable) {
				$db_query = "
						    INSERT INTO playsms_featPoll_result 
						    (poll_id,choice_id,poll_sender) 
						    VALUES ('$poll_id','$choice_id','$sms_sender')
						";
				dba_query($db_query);
			}
			$ok = true;
		}
	}
	return $ok;
}

// when we process a system message,
// just forward it on to the admin group
//
function processSystemMessage($sms_sender, $message) {
	global $web_title;
	$fwd_msg= "$web_title sys msg: \n'$message'";
	websend2group("admin", "admin", $fwd_msg);
	return true;
}

// check incoming SMS for available codes
// and sets the action
function setsmsincomingaction($sms_datetime, $sms_sender, $target_code, $message) {
	//error_log("setsmsincomingaction: $sms_datetime, $sms_sender, \"$target_code\", \"$message\" \n");

	global $system_from;
	$ok = false;
	switch ($target_code) {
		case BC :
			$array_target_group = explode(" ", $message);
			$target_group = strtoupper(trim($array_target_group[0]));
			$message = $array_target_group[1];
			for ($i = 2; $i < count($array_target_group); $i++) {
				$message .= " " . $array_target_group[$i];
			}
			if (send2group($sms_sender, $target_group, $message)) {
				$ok = true;
			}
			break;
		case PV :
			$array_target_user = explode(" ", $message);
			$target_user = strtoupper(trim($array_target_user[0]));
			$message = $array_target_user[1];
			for ($i = 2; $i < count($array_target_user); $i++) {
				$message .= " " . $array_target_user[$i];
			}
			if (insertsmstoinbox($sms_datetime, $sms_sender, $target_user, $message)) {
				$ok = true;
			}
			break;
		default :
			// maybe its for sms autoreply
			if (!$ok) {
				$db_query = "SELECT autoreply_id FROM playsms_featAutoreply WHERE autoreply_code='$target_code'";
				if ($db_result = dba_num_rows($db_query)) {
					$ok= processautoreply($sms_datetime, $sms_sender, $target_code, $message);
				}
			}
			
			// maybe its for sms poll
			if (!$ok) {
				$db_query = "SELECT poll_id FROM playsms_featPoll WHERE poll_code='$target_code'";
				if ($db_result = dba_num_rows($db_query)) {
					$ok= savepoll($sms_sender, $target_code, $message);
				}
			}
			
			// or maybe its for sms command
			if (!$ok) {
				$db_query = "SELECT command_id FROM playsms_featCommand WHERE command_code='$target_code'";
				if ($db_result = dba_num_rows($db_query)) {
					$ok= execcommand($sms_datetime, $sms_sender, $target_code, $message);
				}
			}
			
			// or maybe its for sms custom
			if (!$ok) {
				$db_query = "SELECT custom_id FROM playsms_featCustom WHERE custom_code='$target_code'";
				if ($db_result = dba_num_rows($db_query)) {
					$ok= processcustom($sms_datetime, $sms_sender, $target_code, $message);
				}
			}
			
			// its for sms board
			if (!$ok) {
				$db_query = "SELECT board_id FROM playsms_featBoard WHERE board_code='$target_code'";
				if ($db_result = dba_num_rows($db_query)) {
					$ok= insertsmstodb($sms_datetime, $sms_sender, $target_code, $message);
				}
			}

			// if its from the known system messsage sender,
			// then process it as a system message
			$syssenders= explode(',', $system_from);
			foreach ($syssenders as $syssender) {
				if (0 == strcasecmp($sms_sender, $syssender)) {
					$ok= processSystemMessage($sms_sender, "$target_code $message");
				}
			}
	}
	if (!$ok) {
		$message = $target_code . " " . $message;
		if (insertsmstoinbox($sms_datetime, $sms_sender, "admin", $message)) {
			$ok = true;
		}
		
		// only do unknown processing if its a regular number,
		// not a shortcode or some special cell provider number
		// (if we don't check we can get into an infinite loop, us
		// sending an error message to another autmoated system, which
		// sends us an error message...)
		if (strlen($sms_sender) > 4 && ereg('^\+?[0-9]+$', $sms_sender)) {
			processUnknown($sms_datetime, $sms_sender, $target_code, $message);
		}
	}
	return $ok;
}

function generateSmsInput($nameForm, $smsDisplayTitle, $smsContents, $nameSmsTextBox) {
	if (!$nameSmsTextBox) {
		$nameSmsTextBox = "message";
	}

	$html .= "<br>
		    <p>$smsDisplayTitle 
		    <br>
	            <textarea cols=\"39\" rows=\"5\"
	                      onKeyUp=\"this.updateSmsCounts();\" onKeyDown=\"this.updateSmsCounts();\" 
	                      name=\"$nameSmsTextBox\" id=\"$nameSmsTextBox\">$smsContents</textarea>
	
		    <br>" . generateSmsCounters($nameForm, $nameSmsTextBox);
	return $html;
}

function generateSmsCounters($nameForm, $nameSmsTextBox) {
	$nameCharCount = "smsCharCount";
	$nameSmsCount = "smsCount";

	$attribsForNoEdits="onKeyPress=\"if (window.event.keyCode == 13){return false;}\" onFocus=\"this.blur();\"";

	$html= "
	    Characters left:
	    <input type=\"text\" name=\"$nameCharCount\" id=\"$nameCharCount\"
	     size=\"3\" value=\"0\" $attribsForNoEdits>
	
	    SMSes:
	    <input type=\"text\" name=\"$nameSmsCount\" id=\"$nameSmsCount\" 
		 size= 3 value=\"1\"\ $attribsForNoEdits>
	
	    <script language=\"JavaScript\"><!--
	        form= document.forms.$nameForm;
	        wireupSmsCountUpdate(form.$nameSmsTextBox, form.$nameCharCount, form.$nameSmsCount);
	        form.$nameSmsTextBox.updateSmsCounts();
	    --></script>
	    ";
	return $html;
}

function setupSmsCounting($form, $nameSmsTextBox, $nameInsertBefore) {	
	$form->updateElementAttr(msg, 
				array("onKeyUp"   => 'this.updateSmsCounts();',
					  "onKeyDown" => 'this.updateSmsCounts();',
					  "cols" => "39", "rows" => "5"));

	$elem=& HTML_QuickForm::createElement('static', "counters", null, 
				   	            generateSmsCounters($form->getAttribute(name), $nameSmsTextBox));
	$form->insertElementBefore($elem, $nameInsertBefore);
}

function getNumSmsMultipart($msg) {
	global $SMS_SINGLE_MAXCHARS, $SMS_SINGLE_MULTIPART_MAXCHARS;
	$len= strlen($msg);

    // the max len of a single sms is different
    // than the max for a single sms that is part
    // of a multipart message, so we calculate
    // the numbe of smses being sent differently
    // if the number of smses is greater than 1
    // (a multi-part message)
    //
    if ($len <= $SMS_SINGLE_MAXCHARS) {
        return 1;
    } else {
        return ceil($len / $SMS_SINGLE_MULTIPART_MAXCHARS);
    }	
}
