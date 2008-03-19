<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

require_once 'DB/DataObject.php';
require_once 'DB/DataObject/FormBuilder.php';

function websend2pv($username, $sms_to, $message, $sms_type = "text", $unicode = "0") {
	global $apps_path;
	global $datetime_now, $gateway_module;
	$uid = username2uid($username);
	$mobile_sender = username2mobile($username);
	$sms_footer= username2footer($username);
	$sms_msg= cleanSmsMessage(appendFooter($message, $sms_footer));
	if (is_array($sms_to)) {
		$array_sms_to = $sms_to;
	} else {
		$array_sms_to[0] = $sms_to;
	}
	for ($i = 0; $i < count($array_sms_to); $i++) {
		$c_sms_to = $array_sms_to[$i];
		$gp_code = "PV";
		$to[$i] = $c_sms_to;
		$ok[$i] = 0;
		
		$db = DB_DataObject::factory(playsms_tblSMSOutgoing);
		$db->uid= $uid;
		$db->p_gateway= $gateway_module;
		$db->p_src= $mobile_sender;
		$db->p_dst= $c_sms_to;
		$db->p_footer= $sms_footer;
		$db->p_msg= $sms_msg;
		$db->p_datetime= $datetime_now;
		$db->p_sms_type= $sms_type;
		$db->unicode= $unicode;
		$db->send_tries= 1;
		$db->p_status= DLR_FAILED;	// default to failure
		if ($db->insert()) {
			if (gw_send_sms($mobile_sender, $c_sms_to, $sms_msg, $gp_code, $uid, $db->smslog_id, $sms_type, $unicode)) {
				$ok[$i] = $db->smslog_id;
			}
		}
	}
	return array (
		$ok,
		$to
	);
}

function websend2group($username, $gp_code, $message, $sms_type = "text", $unicode = "0") {
	global $apps_path;
	global $datetime_now, $gateway_module;
	$uid = username2uid($username);
	$mobile_sender = username2mobile($username);
	$sms_footer= username2footer($username);
	$sms_msg= cleanSmsMessage(appendFooter($message, $sms_footer));
	
	if (is_array($gp_code)) {
		$array_gp_code = $gp_code;
	} else {
		$array_gp_code[0] = $gp_code;
	}
	$j = 0;
	for ($i = 0; $i < count($array_gp_code); $i++) {
		$c_gp_code = strtoupper($array_gp_code[$i]);
		$gpid = gpcode2gpid($uid, $c_gp_code);
		$dbPhonebook= DB_DataObject::factory(playsms_tblUserPhonebook);
		$dbPhonebook->gpid= $gpid;
		$dbPhonebook->find();
		
		while ($dbPhonebook->fetch()) {
			$sms_to = $dbPhonebook->p_num;
			$to[$j] = $sms_to;
			$ok[$j] = 0;
	
			$db = DB_DataObject::factory(playsms_tblSMSOutgoing);
			$db->uid= $uid;
			$db->p_gateway= $gateway_module;
			$db->p_src= $mobile_sender;
			$db->p_dst= $sms_to;
			$db->p_footer= $sms_footer;
			$db->p_msg= $sms_msg;
			$db->p_datetime= $datetime_now;
			$db->p_sms_type= $sms_type;
			$db->unicode= $unicode;
			$db->send_tries= 1;
			$db->p_status= DLR_FAILED;	// default to failure
			if ($db->insert()) {
				if (gw_send_sms($mobile_sender, $sms_to, $sms_msg, $c_gp_code, $uid, $db->smslog_id, $sms_type, $unicode)) {
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

function send2group($mobile_sender, $gp_code, $message, $sms_type = "text", $unicode = "0") {
	global $apps_path;
	global $datetime_now, $gateway_module;
	$ok = false;
	if ($mobile_sender && $gp_code && $message) {
		$db_query = "SELECT uid,username,sender FROM playsms_tblUser WHERE mobile='$mobile_sender'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$uid = $db_row[uid];
		$username = $db_row[username];
		$sms_footer= $db_row[sender];
		$sms_msg = cleanSmsMessage(appendFooter($message, $sms_footer));
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
					$sms_to = $db_row[p_num];
					$send_code = md5(mktime() . $sms_to);
					
					$db = DB_DataObject::factory(playsms_tblSMSOutgoing);
					$db->uid= $uid;
					$db->p_gateway= $gateway_module;
					$db->p_src= $mobile_sender;
					$db->p_dst= $sms_to;
					$db->p_footer= $sms_footer;
					$db->p_msg= $sms_msg;
					$db->p_datetime= $datetime_now;
					$db->p_sms_type= $sms_type;
					$db->unicode= $unicode;
					$db->send_tries= 1;
					$db->p_status= DLR_FAILED;	// default to failure
					if ($db->insert()) {
						$ok= gw_send_sms($mobile_sender, $sms_to, $sms_msg, $gp_code, $uid, $db->smslog_id);
					}
				}
			}
		}
	}
	return $ok;
}

// resend
// 
// Load the sms from the db and resend it.  Since
// we give the smslog_id to the gateway, it will
// update the smslog with the new status.
// 
function resend($smslog_id, $override= false) {
    if (!$override) {
        // pause for a bit to allow
        // whatever's wrong with the gateway
        // to clear itself out
        sleep(RESEND_SLEEP);
    }

	$db = DB_DataObject::factory(playsms_tblSMSOutgoing);
	if ($db->get($smslog_id)) {
	    $newtrycount= $db->send_tries+1;

	    if ($override || 0 != (int)fmod($newtrycount, SEND_TRY_MAX)) {
            $db->send_tries= $newtrycount;
            $db->p_update= date();
            $db->update();
            
	    	error_log("resending (attempt $db->send_tries)");    
		    gw_send_sms($db->p_src, $db->p_dst, $db->p_msg, PV, $db->uid, 
		    			$db->smslog_id, $db->p_sms_type, $db->unicode);
	    }
	}
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
	global $datetime_now, $web_url;
	
	$db = DB_DataObject::factory(playsms_tblSMSOutgoing);
	$db->get($smslog_id);
	$db->p_update=$datetime_now;
	$db->p_status=$p_status;
	$ok= $db->update();

	if ($p_status == DLR_FAILED) {
	    error_log("sms send failure; invoking resend");
	    $url= "$web_url/resend.php?smslog_id=$smslog_id";
	    asyncCall($url);
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
	$connection = fsockopen($url['host'], $url['port'], $error_number, $error_description, 60);
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

define(KEYWORD_MAX, 8);
define(VARMARKER  , '##');
define(REMATCH    , '##REMATCH##');			    // invoke a rematching
define(KEYWORDS   , '##KEYWORDS##');		    // all keywords
define(SUBKEYWORDS, '##SUBKEYWORDS##');		    // all keywords but first one
define(KEYWORD    , '##KEYWORD');			    // specific keyword (partial)
define(KEYWORD0   , KEYWORD . '0' . VARMARKER); // first (main) keyword
define(KEYWORD7   , KEYWORD . '7' . VARMARKER); // last keyword
define(UNKNOWN    , '_UNKNOWN_');               // special 'unknown' match

function simpleMatchAutoreply($keywords) {
	error_log("simpleMatchAutoreply: " . print_r($keywords, true));

    $autoreply= DB_DataObject::factory('playsms_featAutoreply');
    $scenario = DB_DataObject::factory('playsms_featAutoreply_scenario');

    // make sure each keyword is set, even if its blank
    $keywords= array_pad($keywords, KEYWORD_MAX, "");

	$i= 0;
    $autoreply->autoreply_code= $keywords[$i++];
	$scenario->autoreply_scenario_param1= $keywords[$i++];
	$scenario->autoreply_scenario_param2= $keywords[$i++];
	$scenario->autoreply_scenario_param3= $keywords[$i++];
	$scenario->autoreply_scenario_param4= $keywords[$i++];
	$scenario->autoreply_scenario_param5= $keywords[$i++];
	$scenario->autoreply_scenario_param6= $keywords[$i++];
	$scenario->autoreply_scenario_param7= $keywords[$i++];
	$autoreply->limit(1);
	$autoreply->joinAdd($scenario);

	if ($autoreply->find() && $autoreply->fetch()) {
		$match= $autoreply->toArray();
		$match['keywords']= $keywords;
		return $match;
	}
}


function stringMatchAutoreply($keywords) {
    error_log("stringMatchAutoreply");

    $autoreply= DB_DataObject::factory('playsms_featAutoreply');
    $scenario = DB_DataObject::factory('playsms_featAutoreply_scenario');

    // Glom all the keywords together with no delimiters.
    // Note the % wildcard at the end of kewords.
    //
    $message= implode('', $keywords);
    $keywordExpr= 'concat(
        autoreply_code           , autoreply_scenario_param1,
        autoreply_scenario_param2, autoreply_scenario_param3,
        autoreply_scenario_param4, autoreply_scenario_param5,
        autoreply_scenario_param6, autoreply_scenario_param7,
        "%") as keywords';
    $autoreply->selectAdd($keywordExpr);

    // Do a like match for the message.  This way,
    // if any autoreply is a prefix of the message
    // then it will match.
    //
    $autoreply->having("\"$message\" like keywords");

    // Since we're going to be doing some lenient string
    // matching, we don't want to be *too* lenient.
    // If we're going to match against the first keyword
    // with no subkeywords, we want it to be exact.
    //
    $where= "autoreply_scenario_param1 != \"\"
            OR
            autoreply_code=\"$message\"";
    $autoreply->whereAdd($where);

    // Our lenient string matching could match against
    // several autoreplies, so sort it so the longest
    // set of keywords is first.
    $autoreply->orderBy("char_length(keywords) DESC");
    $autoreply->limit(1);
    $autoreply->joinAdd($scenario);

    if ($autoreply->find() && $autoreply->fetch()) {  
        $match= $autoreply->toArray();
        $match['keywords']= array(
            $autoreply->autoreply_code, $autoreply->autoreply_scenario_param1, 
            $autoreply->autoreply_scenario_param2, $autoreply->autoreply_scenario_param3, 
            $autoreply->autoreply_scenario_param4, $autoreply->autoreply_scenario_param5, 
            $autoreply->autoreply_scenario_param6, $autoreply->autoreply_scenario_param7);
        return $match;
    }    
}

function multiMatchAutoreply($message) {    

	// get the keywords using more creative delimiters
	//
	$delimiter= "/[\s,_#\.]+/";
	$keywords= preg_split($delimiter, $message, KEYWORD_MAX, PREG_SPLIT_NO_EMPTY);
	
    // try matching the keywords treating
    // it all like one big string
    //
    $match= stringMatchAutoreply($keywords);
    if ($match) return $match;
	
    // if we haven't found anything so far, then
    // match against the special unknown code,
    // either for the first keyword or, as a last
    // resort, the top-level unknown code
    //
    $autoreply= DB_DataObject::factory('playsms_featAutoreply');
    $scenario = DB_DataObject::factory('playsms_featAutoreply_scenario');
    $autoreply->joinAdd($scenario);
    $autoreply->whereAdd("autoreply_code = '" . UNKNOWN . "' OR " .
                        "(autoreply_code = '$keywords[0]' AND autoreply_scenario_param1 = '" . UNKNOWN . "')");
    $autoreply->orderBy("autoreply_scenario_param1 DESC, autoreply_code");
    $autoreply->limit(1);
    if ($autoreply->find(true)) {
        // (make sure we keep the original keywords, not the unknown keywords,
        // as this matters for later autreply evaluation)
        $match= $autoreply->toArray();
        $match['keywords']= $keywords;
        $match[UNKNOWN]= true;
        return $match;
    }

	return $match;
}

function matchAutoreply($message, $simple= true) {
error_log("matchAutoreply " . $message);    
//DB_DataObject::debugLevel(5);

	if ($simple) {
		// try simple (and quick!) keyword delimiters
		//
		$delimiter= ' ';
		$keywords= explode($delimiter, $message, KEYWORD_MAX);      
		$match= simpleMatchAutoreply($keywords);
		if (!$match) return $match;
	} else {
	    $match= multiMatchAutoreply($message);
		if (!$match) return $match;
	}

	$match= evaluateAutoreply($match);
	error_log("match= \"" . $match['autoreply_scenario_result'] . "\"");
	return $match;
}

function evaluateAutoreply($match) {
    $keywords= &$match['keywords'];
    $message= &$match['autoreply_scenario_result'];
    error_log("evaluateAutoreply " . print_r($keywords, true));
 
	// To save time, check if there are any special variable markers.
	// If there are any, then we replace them with the
	// appropriate values and/or take special action
	//
	if (stristr($message, VARMARKER)) {
		if (stristr($message, KEYWORDS))
			$message= str_ireplace(KEYWORDS, implode(' ', $keywords), $message);
		if (stristr($message, SUBKEYWORDS)) {
	    	$message= str_ireplace(SUBKEYWORDS, implode(' ', array_slice($keywords, 1)), $message);
		}
		
		// to save time, check if there are any
		// specific keyword references
		//
		if (stristr($message, KEYWORD)) {
		    for ($i= 0; $i < KEYWORD_MAX; $i++) {
		    	$var= KEYWORD . $i . VARMARKER;
		        $message= str_ireplace($var, $keywords[$i], $message);
		    }
		}
		
		// if the result of this match
		// says to do a rematch, then do a match
		// using the result itself.  this
		// allows autoreplies to 'point' to each
		// other for variations in spelling, aliases, etc
		//
		if (stristr($message, REMATCH)) {
		    $message= str_ireplace(REMATCH, "", $message);
            $message= trim($message);
		    $match= matchAutoreply($message, false);
		}
	}

    return $match;
}

function processAutoreply($sms_datetime, $sms_sender, $message, $simple= true) {
	global $datetime_now;

	// find the autoreply
	$match= matchAutoreply($message, $simple);
	if (!$match) return false;

	// save a log of the match
	$log= DB_DataObject::factory('playsms_featAutoreply_log');    
    $log->sms_sender= $sms_sender;
    $log->autoreply_log_datetime= $datetime_now;
    $log->autoreply_log_code= $match['keywords'][0];
    $log->autoreply_log_request= $message;
	$ok= $log->insert();
	if (!$ok) return $ok;

	// send the autoreply
	$c_username = uid2username($match['uid']);
	$ok= websend2pv($c_username, $sms_sender, $match['autoreply_scenario_result']);
    if (!$ok) return false;

    // since unknown matches are
    // really error messages, we
    // count them as failures
    //
    if ($match[UNKNOWN]) {
        $ok= false;
    }

	return $ok;
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
function setsmsincomingaction($sms_datetime, $sms_sender, $message) {
	global $system_from;
	$ok = false;
	$keywords= explode(' ', $message);
	$target_code= strtoupper($keywords[0]);
	
	switch ($target_code) {
		case 'BC' :
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
		case 'PV' :
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
			// try as autoreply
			$ok= processAutoreply($sms_datetime, $sms_sender, $message);
			
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
				    $saveToInbox= true;
					$ok= processSystemMessage($sms_sender, "$target_code $message");
				}
			}
			break;
	}
    	
	if (!$ok) {
		$saveToInbox= true;

		// If all else failed, then check the autoreplies again,
		// this time with a more sophisticated match.
		//
		// Note that since this can result in autoreply error messages,
		// we only do this if its a regular number, not a shortcode or 
		// some special cell provider number
		// (if we don't check we can get into an infinite loop, us
		// sending an error message to another autmoated system, which
		// sends us an error message...)
		
		if (strlen($sms_sender) > 4 && ereg('^\+?[0-9]+$', $sms_sender)) {
			$ok= processAutoreply($sms_datetime, $sms_sender, $message, false);
            $saveToInbox= !$ok;
		}
	}
	
	if ($saveToInbox) {
	    error_log("saving to inbox...");
		if (insertsmstoinbox($sms_datetime, $sms_sender, "admin", $message)) {
			$ok = true;
		}    
	}
	return $ok;
}

// TODO: have this return an http header
// for success or failure
//
function doAutosend($frequency) {
	echo("autosending for '$frequency' <br/>\n");
	$do = DB_DataObject::factory(playsms_featAutoSend);
	$do->frequency= $frequency;
	$do->find();

	if ($frequency == "startup")
		gw_waitForStartup();

	while ($do->fetch()) {
		echo("sending $do->id, $do->frequency, $do->number, \"$do->msg\"... <br/>\n");
		websend2pv("admin", $do->number, $do->msg);
	}
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

function setupSmsCounting($form, $nameSmsTextBox, $nameInsertBefore=null) {	
	$form->updateElementAttr($nameSmsTextBox, 
				array("onKeyUp"   => 'this.updateSmsCounts();',
					  "onKeyDown" => 'this.updateSmsCounts();',
					  "cols" => "39", "rows" => "5"));

	$elem=& HTML_QuickForm::createElement('static', "counters", null, 
				   	            generateSmsCounters($form->getAttribute('name'), $nameSmsTextBox));

	if (isset($nameInsertBefore)) {
		$form->insertElementBefore($elem, $nameInsertBefore);
	} else {
	    $form->addElement($elem);
	}
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

function generateActionSubmitter($actionName, $actionUrl, $varName= "id") {
	$actionFormName= $actionName . "_Form";
    return "<form name=\"$actionFormName\" method=\"post\" action=\"$actionUrl\">
                <input type=\"hidden\" name=\"$varName\" value=\"\"/>
            </form>
            <script language=\"JavaScript\"><!--
                function $actionName($varName, msg) {
                    if (msg == undefined || confirm(msg)) {
                        var form= document.forms.$actionFormName;
                        form.$varName.value=$varName;
                        form.submit();
                    }
                }
             --></script>";
}