<?php
if (!defined("_SECURE_")) {
	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};
if (!isadmin()) {
	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

include "$apps_path[plug]/gateway/kannel/config.php";

$selfurl = $_SERVER['PHP_SELF'] . "?inc=gwmod_kannel";

require_once 'DB/DataObject.php';
require_once 'DB/DataObject/FormBuilder.php';

$op = $_GET[op];

switch ($op) {
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
		
        $html=
        "<form name=\"smsc_bind\" action=\"$selfurl&op=smsc_bind\" method=\"post\">
            <p/>$smsc_sel
            <p/><input type=\"submit\" class=\"button\" value=\"Bind\"/>
        </form>
        ";
        
        echo $html;
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
		
	default:
		makeEditForm($selfurl);
		break;
}


function makeEditForm($selfurl) {    
	// nice names for each field
    $formNames = array (
		'cfg_incoming_path' => 'Kannel Incoming Path: ',
		'cfg_username' => 'Username:',
		'cfg_password' => 'Password:',
		'cfg_global_sender' => 'Global Sender:',
		'cfg_bearerbox_host' => 'Bearerbox IP:',
		'cfg_sendsms_port' => 'Send SMS Port:',
		'cfg_playsms_web' => 'PlaySMS web url:',
		'cfg_restart_frequency' => 'Restart Kannel Regularly?'
    );
    
    // infer the fields we're going to show
    // from the list of formNames
	$renderFields= array();
	foreach ($formNames as $field => $name) {
		$renderFields[]= $field;
	}

    // find the first record in the config table    
    $do = DB_DataObject :: factory('playsms_gwmodKannel_config');
    $do->get(1);

    // create the form with the user-showable names
    $fb = DB_DataObject_FormBuilder::create($do, 
    		array("fieldLabels" => $formNames,
    		      "fieldsToRender" => $renderFields));

    // set up enums
    $fb->enumFields = array ('cfg_restart_frequency');

    $form = $fb->getForm("$selfurl");
    if ($form->validate()) {
        $err= "Gateway module configurations has been saved";
    	print "<p><font color=red>$err</font><p>";
    	
        $form->process(array(&$fb,'processForm'), false);
    }
    $form->display();
    
    echo "<p/><hr/><p/>";
    echo "<a href=\"plugin/gateway/kannel/kannel-monitor/\">[ Monitor Status ]</a> \n";
    echo "<a href=\"$selfurl&op=manage_smsc\">[ Bind SMSC ]</a>";    
}

?>
