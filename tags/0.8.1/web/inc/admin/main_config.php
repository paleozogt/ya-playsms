<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$op = $_GET[op];

switch ($op)
{
    case "main_config":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Main configuration</h2>
	    <p>
	    <form action=menu_admin.php?inc=main_config&op=main_config_save method=post>
	    <p>Website's title: <input type=text size=50 name=edit_web_title value=\"$web_title\">
	    <p>Website's email: <input type=text size=30 name=edit_email_service value=\"$email_service\"> (Format: username@somedomain.com eg: anton@ngoprek.org)
	    <p>Forwarded email footer: <input type=text size=50 name=edit_email_footer value=\"$email_footer\">
	    <p>Gateway number: <input type=text size=20 name=edit_gateway_number value=\"$gateway_number\">
	    <p>Activated gateway module: $gateway_module
	    <p><input type=submit class=button value=Save>
	    </form>
	";
	echo $content;
	break;
    case "main_config_save":
	$edit_web_title = $_POST[edit_web_title];
	$edit_email_service = $_POST[edit_email_service];
	$edit_email_footer = $_POST[edit_email_footer];
	$edit_gateway_number = $_POST[edit_gateway_number];
	$db_query = "
	    UPDATE playsms_tblConfig_main 
	    SET 
		cfg_web_title='$edit_web_title',
		cfg_email_service='$edit_email_service',
		cfg_email_footer='$edit_email_footer',
		cfg_gateway_number='$edit_gateway_number'
	";
	$db_result = dba_query($db_query);
	$error_string = "Main configuration has been saved";
	header ("Location: menu_admin.php?inc=main_config&op=main_config&err=".urlencode($error_string));
	break;
}

?>