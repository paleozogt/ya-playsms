<?
include "config.php";

// --------------------------------------------------------------------------------
if (!$DAEMON_PROCESS)
{
    if (trim($SERVER_PROTOCOL)=="HTTP/1.1")
    {
	header ("Cache-Control: no-cache, must-revalidate");
    }
    else
    {
	header ("Pragma: no-cache");
    }
    ob_start();
}
// --------------------------------------------------------------------------------

// set global variable
$date_format		= "Y-m-d";
$time_format		= "G:i:s";
$datetime_format 	= $date_format." ".$time_format;
$date_now		= date($date_format, time());
$time_now		= date($time_format, time());
$datetime_now		= date($datetime_format, time());
$reserved_codes		= array ("PV","BC"); //,"GET","PUT","INFO","SAVE","DEL","LIST","RETR","POP3","SMTP","BROWSE","NEW","SET","POLL","VOTE","REGISTER","REG","DO","USE","EXECUTE","EXEC","RUN","ACK");
sort ($reserved_codes);
$nd 			= "<font color=red>(*)</font>";

// sms constants
//
$SMS_SINGLE_MAXCHARS= 160;
$SMS_SINGLEMULTIPART_MAXCHARS= ($SMS_SINGLE_MAXCHARS - 7);
$SMS_MULTIPART_MAX= 3;
$SMS_MAXCHARS= ($SMS_SINGLEMULTIPART_MAXCHARS*$SMS_MULTIPART_MAX);

// very important, do not try to remove it or change it
define ("_SECURE_","1");

// connect to database
include_once "$apps_path[libs]/dba.php";
$dba_object = dba_connect($db_param[user],$db_param[pass],$db_param[name],$db_param[host],$db_param[port]);

// get main config
$db_query = "SELECT * FROM playsms_tblConfig_main";
$db_result = dba_query($db_query);
if ($db_row = dba_fetch_array($db_result))
{
    $web_title = $db_row[cfg_web_title];
    $email_service = $db_row[cfg_email_service];
    $email_footer = $db_row[cfg_email_footer];
    $gateway_module = $db_row[cfg_gateway_module];
    $gateway_number = $db_row[cfg_gateway_number];
}

// protect from SQL injection when magic_quotes_gpc sets to "Off"
function pl_addslashes($data)
{
    global $db_param;
    if ($db_param[type]=="mssql")
    {
	$data = str_replace("'", "''", $data); 
    } 
    else
    {
	$data = addslashes($data);
    }
    return $data; 
}
if (!get_magic_quotes_gpc())
{
    foreach($_GET as $key => $val){$_GET[$key]=pl_addslashes($_GET[$key]);}
    foreach($_POST as $key => $val){$_POST[$key]=pl_addslashes($_POST[$key]);}
    foreach($_COOKIE as $key => $val){$_COOKIE[$key]=pl_addslashes($_COOKIE[$key]);}
    foreach($_SERVER as $key => $val){$_SERVER[$key]=pl_addslashes($_SERVER[$key]);}
}

?>