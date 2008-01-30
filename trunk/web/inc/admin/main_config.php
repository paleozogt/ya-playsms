<?php
if (!defined("_SECURE_")) {

    die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$selfurl = $_SERVER['PHP_SELF'] . "?inc=main_config";

require_once 'DB/DataObject.php';
require_once 'DB/DataObject/FormBuilder.php';

makeEditForm($selfurl);
return;

function makeEditForm($selfurl) {
    
	// nice names for each field
    $formNames = array (
        'cfg_web_title' => 'Website title:',
        'cfg_email_service' => 'Website email',
        'cfg_email_footer' => 'Forwarded email footer:',
        'cfg_gateway_module' => 'Activated gateway module:',
        'cfg_gateway_number' => 'Gateway number:',
        'cfg_system_from' => 'System messages <br/> (e.g., balance updates) are sent from:',
        'cfg_web_url' => 'Website URL:'
    );
    
    // infer the fields we're going to show
    // from the list of formNames
	$renderFields= array();
	foreach ($formNames as $field => $name) {
		$renderFields[]= $field;
	}

    // List of gateway plugins.
    // TODO: make this get listing of the gw plugin files
    // rather than be hard-coded
    $gw_mods = array (
        "kannel" => "Kannel",
        "gnokii" => "Gnokii",
        "uplink" => "UpLink",
        "clickatell" => "ClickAtell"
    );

    // find the first record in the config table    
    $do = DB_DataObject :: factory(playsms_tblConfig_main);
    $do->find();
    $do->fetch();

    // create the form with the user-showable names
    $fb = DB_DataObject_FormBuilder::create($do, 
    		array("fieldLabels" => $formNames,
    		      "fieldsToRender" => $renderFields));

    // set up gw_mod enum
    $fb->enumFields = array ('cfg_gateway_module');
    $fb->enumOptions = array ('cfg_gateway_module' => $gw_mods);

    $form = $fb->getForm("$selfurl");
    if ($form->validate()) {
        $err= "Main configuration has been saved";
    	print "<p><font color=red>$err</font><p>";
    	
        $form->process(array(&$fb,'processForm'), false);
    }
    $form->display();
}
?>
