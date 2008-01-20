<?php
if (!defined("_SECURE_")) {
	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

function validatelogin($username, $password) {
	$db_query = "SELECT password FROM playsms_tblUser WHERE username='$username'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$res_password = trim($db_row[password]);
	if ($password && $res_password && ($password == $res_password)) {
		$ticket = md5(mktime() . $username);
		return $ticket;
	} else {
		return 0;
	}
}

function valid($var_ticket = "", $var_username = "", $var_multilogin_id = "") {
	global $apps_config;
	$ticket = $_COOKIE[vc1];
	$username = $_COOKIE[vc2];
	$multilogin_id = $_COOKIE[vc3];
	if ($var_ticket && $var_username && $var_multilogin_id) {
		$ticket = $var_ticket;
		$username = $var_username;
		$multilogin_id = $var_multilogin_id;
	}
	if ($apps_config['multilogin']) {
		$db_query = "SELECT password FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		if ($multilogin_id && md5($username . $db_row[password]) && ($multilogin_id == md5($username . $db_row[password]))) {
			return 1;
		} else {
			return 0;
		}
	} else {
		$db_query = "SELECT ticket FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		if ($ticket && $db_row[ticket] && ($ticket == $db_row[ticket])) {
			return 1;
		} else {
			return 0;
		}
	}
}

function isadmin($var_ticket = "", $var_username = "", $var_multilogin_id = "") {
	global $apps_config;
	$ticket = $_COOKIE[vc1];
	$username = $_COOKIE[vc2];
	$multilogin_id = $_COOKIE[vc3];
	if ($var_ticket && $var_username && $var_multilogin_id) {
		$ticket = $var_ticket;
		$username = $var_username;
		$multilogin_id = $var_multilogin_id;
	}
	if ($apps_config['multilogin']) {
		$db_query = "SELECT status,password FROM playsms_tblUser WHERE username='$username' AND ticket='$ticket'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		if ($db_row[status] && ($db_row[status] == 2) && ($multilogin_id == md5($username . $db_row[password]))) {
			return 1;
		} else {
			return 0;
		}
	} else {
		$db_query = "SELECT status FROM playsms_tblUser WHERE username='$username' AND ticket='$ticket'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		if ($db_row[status] && ($db_row[status] == 1)) {
			return 1;
		} else {
			return 0;
		}
	}
}

function forcelogout() {
	setcookie("vc1");
	setcookie("vc2");
	setcookie("vc3");
	header("Location: goodbye.php");
	die();
}

function gpid2gpname($gpid) {
	if ($gpid) {
		$db_query = "SELECT gp_name FROM playsms_tblUserGroupPhonebook WHERE gpid='$gpid'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$gp_name = $db_row[gp_name];
	}
	return $gp_name;
}

function gpid2gpcode($gpid) {
	if ($gpid) {
		$db_query = "SELECT gp_code FROM playsms_tblUserGroupPhonebook WHERE gpid='$gpid'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$gp_code = $db_row[gp_code];
	}
	return $gp_code;
}

function gpcode2gpname($uid, $gp_code) {
	if ($uid && $gp_code) {
		$db_query = "SELECT gp_name FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' AND gp_code='$gp_code'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$gp_name = $db_row[gp_name];
	}
	return $gp_name;
}

function gpcode2gpid($uid, $gp_code) {
	if ($uid && $gp_code) {
		$db_query = "SELECT gpid FROM playsms_tblUserGroupPhonebook WHERE uid='$uid' AND gp_code='$gp_code'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$gpid = $db_row[gpid];
	}
	return $gpid;
}

function uid2username($uid) {
	if ($uid) {
		$db_query = "SELECT username FROM playsms_tblUser WHERE uid='$uid'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$username = $db_row[username];
	}
	return $username;
}

function username2uid($username) {
	if ($username) {
		$db_query = "SELECT uid FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$uid = $db_row[uid];
	}
	return $uid;
}

function username2mobile($username) {
	if ($username) {
		$db_query = "SELECT mobile FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$mobile = $db_row[mobile];
	}
	return $mobile;
}

function username2footer($username) {
	if ($username) {
		$db_query = "SELECT sender FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$footer = $db_row[sender];
	}
	return $footer;
}

function username2email($username) {
	if ($username) {
		$db_query = "SELECT email FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$email = $db_row[email];
	}
	return $email;
}

function username2name($username) {
	if ($username) {
		$db_query = "SELECT name FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$name = $db_row[name];
	}
	return $name;
}

function username2status($username) {
	if ($username) {
		$db_query = "SELECT status FROM playsms_tblUser WHERE username='$username'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$status = $db_row[status];
	}
	return $status;
}

function pid2pnum($pid) {
	global $username;
	if ($pid) {
		$uid = username2uid($username);
		$db_query = "SELECT p_num FROM playsms_tblUserPhonebook WHERE pid='$pid' AND uid='$uid'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$p_num = $db_row[p_num];
	}
	return $p_num;
}

function pnum2pdesc($p_num) {
	global $username;
	if ($p_num) {
		$uid = username2uid($username);
		$db_query = "SELECT p_desc FROM playsms_tblUserPhonebook WHERE p_num='$p_num' AND uid='$uid'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$p_desc = $db_row[p_desc];
	}
	return $p_desc;
}

function pnum2pemail($p_num) {
	global $username;
	if ($p_num) {
		$uid = username2uid($username);
		$db_query = "SELECT p_email FROM playsms_tblUserPhonebook WHERE p_num='$p_num' AND uid='$uid'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$p_email = $db_row[p_email];
	}
	return $p_email;
}

function appendFooter($message, $footer) {
    global $SMS_MAXCHARS;
	$max_length = $SMS_MAXCHARS;
	$max_length-= strlen($footer);
	if (strlen($message) > $max_length) {
		$message = substr($message, 0, $max_length);
	}
	return $message . $footer;
}

function cleanSmsMessage($message) {
	$message = str_replace("\r\n", "\n", $message);
	$message = str_replace("\r", "\n", $message);
	return $message;    
}

// hit a webpage in the background
// (this can be used to hit one of our own
// webpages asyncronously)
//
function asyncCall($url) {
    global $apps_path;
	$cmd= $apps_path[bin] . "/async-call.php '$url' > /dev/null 2>&1 &";
	exec($cmd);
}

function sendmail($mail_from, $mail_to, $mail_subject = "", $mail_body = "") {
	global $apps_path;
	if (!class_exists(email_message_class)) {
		include_once $apps_path[libs] . "/gpl/mimemessage/email_message.php";
	}
	if (!class_exists(smtp_message_class)) {
		include_once $apps_path[libs] . "/gpl/mimemessage/smtp_message.php";
	}
	if (!class_exists(smtp_class)) {
		include_once $apps_path[libs] . "/gpl/mimemessage/smtp/smtp.php";
	}

	$from_name = "";
	$from_address = $mail_from;
	$reply_name = $from_name;
	$reply_address = $from_address;
	$error_delivery_name = $from_name;
	$error_delivery_address = $from_address;
	$to_name = "";
	$to_address = $mail_to;
	$cc_name = "";
	$cc_address = "";
	$bcc_name = "";
	$bcc_address = "";
	$subject = $mail_subject;
	$text_message = $mail_body;

	$email_message = new smtp_message_class;
	$email_message->localhost = "localhost";
	$email_message->smtp_realm = _SMTP_RELM_;
	$email_message->smtp_user = _SMTP_USER_;
	$email_message->smtp_password = _SMTP_PASS_;
	$email_message->smtp_host = _SMTP_HOST_;
	$email_message->smtp_port = _SMTP_PORT_;
	$email_message->smtp_debug = 0;
	$email_message->smtp_direct_delivery = 0;

	$email_message->SetEncodedEmailHeader("To", $to_address, $to_name);
	if ($cc_address)
		$email_message->SetEncodedEmailHeader("Cc", $cc_address, $cc_name);
	if ($bcc_address)
		$email_message->SetEncodedEmailHeader("Bcc", $bcc_address, $bcc_name);
	$email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
	$email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
	$email_message->SetEncodedEmailHeader("Errors-To", $error_delivery_address, $error_delivery_name);
	$email_message->AddQuotedPrintableTextPart($email_message->WrapText($text_message));
	/*
	 *  Set the Return-Path header to define the envelope sender address to which bounced messages are delivered.
	 *  If you are using Windows, you need to use the smtp_message_class to set the return-path address.
	 */
	if (defined("PHP_OS") && strcmp(substr(PHP_OS, 0, 3), "WIN"))
		$email_message->SetHeader("Return-Path", $error_delivery_address);
	$email_message->SetEncodedHeader("Subject", $subject);

	if (isset($attachment, $filename, $contenttype)) {
		$file_attachment = array (
			"Data" => "$attachment",
			"Name" => "$filename",
			"Content-Type" => "$contenttype",
			"Disposition" => "attachment"
		);
		$email_message->AddFilePart($file_attachment);
	}

	/*
	 *  The message is now ready to be assembled and sent.
	 *  Notice that most of the functions used before this point may fail due to
	 *  programming errors in your script. You may safely ignore any errors until
	 *  the message is sent to not bloat your scripts with too much error checking.
	 */
	$error = $email_message->Send();
	if (strcmp($error, ""))
		return false;
	else
		return true;
}

// INCLUDE Custom Functions
// ----------------------------------------------------------------------------
include "$apps_path[libs]/custom_function.php";
if (file_exists("$apps_path[plug]/gateway/$gateway_module/fn.php")) {
	include "$apps_path[plug]/gateway/$gateway_module/fn.php";
} else {
	if ($gateway_module) {
		die("ERROR: Gateway module '$gateway_module' does not exists - please contact system administrator");
	} else {
		die("ERROR: No selected gateway module available - please contact system administrator");
	}
}
?>
