<?php
if (!defined("_SECURE_")) {

    die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

include "$apps_path[plug]/gateway/$gateway_module/config.php";

define(KANNEL_DLR_DELIVERY_SUCCESS,  1);
define(KANNEL_DLR_DELIVERY_FAILURE,  2);
define(KANNEL_DLR_MESSAGE_BUFFERED,  4);
define(KANNEL_DLR_SMSC_SUBMIT     ,  8);
define(KANNEL_DLR_SMSC_REJECT     , 16);

define(KANNEL_SMSTYPE_FLASH, 1);
define(KANNEL_SMSTYPE_TEXT , 2);

define(KANNEL_MSG_ACCEPTED, "0: Accepted for delivery");


function convertKannelDlrToPlaysmsDlr($kannel_dlr) {
    switch ($kannel_dlr) {
        case KANNEL_DLR_DELIVERY_SUCCESS:
            $playsms_dlr = DLR_DELIVERED;
            break;

        case KANNEL_DLR_SMSC_REJECT:
        case KANNEL_DLR_DELIVERY_FAILURE:
        case KANNEL_DLR_SMSC_REJECT | KANNEL_DLR_DELIVERY_FAILURE:
            $playsms_dlr = DLR_FAILED;
            break;

        case KANNEL_DLR_SMSC_SUBMIT:
        case KANNEL_DLR_SMSC_SUBMIT | KANNEL_DLR_DELIVERY_SUCCESS:
        case KANNEL_DLR_SMSC_SUBMIT | KANNEL_DLR_MESSAGE_BUFFERED :
            $playsms_dlr = DLR_SENT;
            break;

        case KANNEL_DLR_MESSAGE_BUFFERED:
        default:
        	$playsms_dlr = DLR_PENDING;
        	break;
    }
    return $playsms_dlr;
}
function gw_customcmd() {
    // nothing
}

function gw_send_sms($mobile_sender, $sms_to, $sms_msg, $gp_code = "", $uid = "", $smslog_id = "", $flash = false) {
    //error_log("gw_send_sms: $mobile_sender, $sms_to, $sms_msg \n");
    global $kannel_param;
    global $gateway_number;
    $ok = false;
    if ($gateway_number) {
        $sms_from = $gateway_number;
    } else {
        $sms_from = $mobile_sender;
    }
    if ($flash) {
    	$sms_type= KANNEL_SMSTYPE_FLASH;
    } else {
    	$sms_type= KANNEL_SMSTYPE_TEXT;
    }

    // we can give kannel a callback url where it
    // will give us the dlr of the sms we're sending
    // (%d is where kannel will put the status, the rest of
    // the params are for us)
    //
    $dlr_url= urlencode($kannel_param['playsms_web'] . "/plugin/gateway/kannel/dlr.php?dlr=%d&smslog_id=$smslog_id&uid=$uid");
    $dlr_mask="31";
    
    // now build the url to send
    // this sms to kannel
    //
    $URL = "/cgi-bin/sendsms?";
    $URL .= "username=" . urlencode($kannel_param['username']);
	$URL .= "&password=" . urlencode($kannel_param['password']);
    $URL .= "&from=" . urlencode($sms_from) . "&to=" . urlencode($sms_to) . "&text=" . urlencode($sms_msg);
    $URL .= "&mclass=$sms_type";
    $URL .= "&dlr-mask=$dlr_mask&dlr-url=$dlr_url";

    // TODO: replace the fsockopen stuff with php's file_get_contents()
    // but for some reason it doesn't seem to work with kannel!
    //
	//$server= 'http://' . $kannel_param['bearerbox_host'] . ':' . $kannel_param['sendsms_port'];
    //$URL= $server . $URL;
    //$response= file_get_contents($URL);
    //if ($response == KANNEL_MSG_ACCEPTED) {
    //    $ok = true;
    //}
    
    $connection = fsockopen($kannel_param['bearerbox_host'], $kannel_param['sendsms_port'], $error_number, $error_description, 60);
    if ($connection) {
        socket_set_blocking($connection, false);
        fputs($connection, "GET $URL HTTP/1.0\r\n\r\n");
        while (!feof($connection)) {
            $myline = fgets($connection, 128);
            if ($myline == KANNEL_MSG_ACCEPTED) {
                $ok = true;
            }
        }
    }
    fclose($connection);
    
    return $ok;
}

function kannel_gw_set_delivery_status($smslog_id, $uid, $kannel_dlr) {   
	$playsms_dlr= convertKannelDlrToPlaysmsDlr($kannel_dlr);
    setsmsdeliverystatus($smslog_id, $uid, $playsms_dlr);

    // log dlr
    $db_query = "SELECT kannel_dlr_id FROM playsms_gwmodKannel_dlr WHERE smslog_id='$smslog_id'";
    $db_result = dba_num_rows($db_query);
    if ($db_result > 0) {
        $db_query = "UPDATE playsms_gwmodKannel_dlr SET kannel_dlr_type='$kannel_dlr' WHERE smslog_id='$smslog_id'";
        $db_result = dba_query($db_query);
    } else {
        $db_query = "INSERT INTO playsms_gwmodKannel_dlr (smslog_id,kannel_dlr_type) VALUES ('$smslog_id','$kannel_dlr')";
        $db_result = dba_query($db_query);
    }    
}

function gw_set_delivery_status($gp_code = "", $uid = "", $smslog_id = "", $p_datetime = "", $p_update = "") {
    global $kannel_param;
    // not used, depend on kannel delivery status updater
}

function gw_set_incoming_action() {
    global $kannel_param;
    $handle = @ opendir($kannel_param['path'] . "/cache/smsd");
    while ($sms_in_file = @ readdir($handle)) {
        if (eregi("^ERR.in", $sms_in_file) && !eregi("^[.]", $sms_in_file)) {
            $fn = $kannel_param['path'] . "/cache/smsd/$sms_in_file";
            $tobe_deleted = $fn;
            $lines = @ file($fn);
            $sms_datetime = urldecode(trim($lines[0]));
            $sms_sender = urldecode(trim($lines[1]));
            $message = "";
            for ($lc = 2; $lc < count($lines); $lc++) {
                $message .= trim($lines[$lc]);
            }
            $array_target_code = explode(" ", urldecode($message));
            $target_code = strtoupper(trim($array_target_code[0]));
            $message = $array_target_code[1];
            for ($i = 2; $i < count($array_target_code); $i++) {
                $message .= " " . $array_target_code[$i];
            }
            // collected:
            // $sms_datetime, $sms_sender, $target_code, $message
            if (setsmsincomingaction($sms_datetime, $sms_sender, $target_code, $message)) {
                @ unlink($tobe_deleted);
            }
        }
    }
}


?>
