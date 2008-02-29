<?php
if (!defined("_SECURE_")) {
	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

require_once "DB/DataObject.php";
require_once 'HTML/QuickForm.php';
require_once 'HTML/Table.php';
require_once "lib/readwriteIniFile.php";

define(INI_SECTION_AUTOREPLIES, 'autoreplies');
define(INI_COMMENTS, ';');

$op = $_GET[op];
$selfurl = $_SERVER['PHP_SELF'] . "?inc=sms_autoreply";
error_log("op=$op");

$br= "\n<br/>";
$guiPlus= '[+]';
$guiMinus='[-]';
$editText="[edit]";
$addText ="[add]";
$delText ="[delete]";
$backText="[back]";

function makeHelpTable() {
    $unknownHelp= "Special keyword that is used when the system receives a text it can't match; use it for error replies.";
    $rematchHelp= "Special variable used in reply that tells the system to match the reply itself as if it were a text that was sent.  Useful for 'pointing' one reply to another.";
    $keywordsHelp= "Variable that is replaced with all the keywords.";
    $subkeywordsHelp= "Variable that is replaced with all the keywords except the first one.";
    $anykeywordHelp= "Variable that is replaced with the keyword whose number is specified.";
    $anyKeywordsItem= KEYWORD0 . ' through ' . KEYWORD7;    
    
    $table= new HTML_Table(array('width'=>'75%'));
    $rowAttribs= array('valign'=>'top');
    $table->addRow(array(UNKNOWN, $unknownHelp), $rowAttribs);
    $table->addRow(array(REMATCH, $rematchHelp), $rowAttribs);
    $table->addRow(array(KEYWORDS, $keywordsHelp), $rowAttribs);
    $table->addRow(array(SUBKEYWORDS, $subkeywordsHelp), $rowAttribs);
    $table->addRow(array($anyKeywordsItem, $anykeywordHelp), $rowAttribs);
    return $table->toHtml();
}


$special_codes_notice= 
    "<p align='center'>" . makeHelpTable() . "</p>";

if ($_GET['err']) {
    $content = "<p><font color=red>$err</font><p>";
}

switch ($op) {
    case "list":
        makeAutoreplyList($selfurl);		    
		break;
        
        
    case "addScenario":
        makeEditScenarioForm($selfurl, null, $_GET['autoreply_id']);
        break;
        
    case "editScenario":
        makeEditScenarioForm($selfurl, $_GET['id']);
        break;
    
    case "delScenario":
        doDeleteScenario($selfurl, $_POST['id']);  
        break;


	case "add" :
        echo makeEditAutoreplyForm($selfurl);
		break;

    case "edit":
		echo makeEditAutoreplyForm($selfurl, $_GET['id']);
		break;

    case "delAutoreply":
        doDeleteAutoreply($selfurl, $_POST['id']);
        break;


	case "test":
		testAutoreply($selfurl);
		break;
        
    case "import":
        importAutoreply($selfurl);
        break;
    
    case "export":
        exportAutoreply($selfurl);
        break;
        
    case 'help':
        showAutoreplyHelp($selfurl);
        break;
}

function makeAutoreplyList($selfurl) {
    global $uid, $special_codes_notice, $br, $guiPlus, $guiMinus, $editText, $addText, $delText;
    $content .= "
            <h2>List/Manage/Delete SMS autoreplies</h2>
            <p>
            <a href=\"$selfurl&op=add\">[ Add ]</a>
            <a href=\"$selfurl&op=export\">[ Export ]</a>
            <a href=\"$selfurl&op=import\">[ Import ]</a>
            <a href=\"$selfurl&op=test\">[ Test ]</a>
            <a href=\"$selfurl&op=help\">[ Help ]</a>
            <hr><p>
        ";
    $content.= genDelForm("delAutoreply", "$selfurl&op=delAutoreply");
    $content.= genDelForm("delScenario", "$selfurl&op=delScenario");
    
    $autoreplies= DB_DataObject::factory('playsms_featAutoreply');
    if (!isadmin()) $autoreplies->uid= $uid;
    $autoreplies->orderBy("autoreply_code");
    $autoreplies->find();
    while ($autoreplies->fetch()) {
        $owner = uid2username($autoreplies->uid);
        $autoreplyInfo= generateScenarios($selfurl, $autoreplies->autoreply_id, false);
    
        $showhideLink= "<a href=\"javascript:;\" onClick=\"javascript: toggleShow('$autoreplies->autoreply_code', this, '$guiPlus', '$guiMinus');\" title=\"Show/Hide\">$guiPlus</a>";
        $editLink= "<a href=\"$selfurl&op=edit&id=$autoreplies->autoreply_id\" title=\"Edit\">$editText</a>";
        $deleteMsg= "Are you sure you want to delete SMS autoreply `$autoreplies->autoreply_code`? Note that this will delete all autoreply scenarios under this autoreply.";
        $deleteLink= "<a href=\"javascript: delAutoreply($autoreplies->autoreply_id, '$deleteMsg');\" title=\"Delete\">$delText</a>";
        $content.= "$showhideLink \n $editLink \n $deleteLink \n <b>$autoreplies->autoreply_code &nbsp;</b>" .
                   "<span id='$autoreplies->autoreply_code' style='display: none;'>" .
                     "<span><b>User:</b> $owner<br><hr/></span>" .
                     "<span style='position:relative; left:30px;'>$autoreplyInfo</span>" .
                     "<span><hr/></span>" .
                   "</span> \n<br/>";
    
        $content.= "<br/>\n\n";           
    }
    echo $content;
    
    echo "<hr><p/><b>Special codes:</b> $br$special_codes_notice";    
}

function generateScenarios($selfurl, $autoreply_id, $editable= true) {
    global $br, $guiPlus, $guiMinus, $editText, $addText, $delText;
    
    // glom all the keywords together
    $scenarioSelect= "concat(playsms_featAutoreply.autoreply_code";
    for ($i= 1; $i < KEYWORD_MAX; $i++) {
        $scenarioSelect.= ", ' ', playsms_featAutoreply_scenario.autoreply_scenario_param" . $i;
    }
    $scenarioSelect.= ") as keywords";

    $scenarios= DB_DataObject::factory('playsms_featAutoreply');
    $scenarios->joinAdd(DB_DataObject::factory('playsms_featAutoreply_scenario'));
    $scenarios->autoreply_id= $autoreply_id;
    $scenarios->selectAdd($scenarioSelect);
    $scenarios->orderBy("keywords");
    $scenarios->find();
    while ($scenarios->fetch()) {
        // make sure to normalize line endings
        // before doing a char-count
        $msg= $scenarios->autoreply_scenario_result;
        $msg = str_replace("\r\n", "\n", $msg);
        $msg = str_replace("\r", "\n", $msg);
        $result_len= strlen($msg);
        $result_num_smses= getNumSmsMultipart($msg);
        $editScenarioLink="<a href=\"$selfurl&op=editScenario&id=$scenarios->autoreply_scenario_id\">$editText</a>";
        $deleteScenarioText="Are you sure you want to delete the autoreply scenario `$keywords`?";
        $deleteScenarioLink= "<a href=\"javascript: delScenario($scenarios->autoreply_scenario_id, '$deleteScenarioText');\">" .
                             "$delText</a>";
    
        $result= nl2br($scenarios->autoreply_scenario_result);
        $keywords= trim($scenarios->keywords);
        if ($editable)
            $scenarioHtml.= "$editScenarioLink $deleteScenarioLink &nbsp;";
        $scenarioHtml.= "<b>Keywords:&nbsp;</b>\"$keywords\" $br" .
                         "<b>Reply length:&nbsp;</b> $result_len chars ; $result_num_smses SMSes $br" .
                         "<b>Reply:&nbsp;</b>$br$result $br$br";
    }
    
    return $scenarioHtml;
}

function genDelForm($name, $action) {
    $html .= "
            <form name=\"$name\" id= \"$name\" method=\"post\" action=\"$action\">
                <input type=\"hidden\" name=\"id\" value=\"\"/>
                <script language=\"JavaScript\"><!--
                    function $name(id, msg) {
                        if (confirm(msg)) {
                            document.forms.$name.id.value=id;
                            document.forms.$name.submit();
                        }
                   }
                --></script>
             </form>";
    return $html;   
}

function makeEditAutoreplyForm($selfurl, $id= null) {    
    global $uid, $addText, $backText;
    
    $do = DB_DataObject::factory('playsms_featAutoreply');
    if ($id)
        $do->get($id);
    else
        $do->uid= $uid;
    $fb = DB_DataObject_FormBuilder::create($do, array(
            "fieldsToRender" => array("autoreply_code"))
          );
    
    $form = $fb->getForm("{$selfurl}&op=edit&id=$id");

    if ($form->validate()) {
        $form->process(array(&$fb,'processForm'), false);
        
        // if we're adding a new item, then forward
        // on to editing that item so that the user doesn't
        // add it over and over again
        header("Location: $selfurl&op=edit&id=$do->autoreply_id");          
    }
    $form->display();

    $addLink= "<a href=\"$selfurl&op=addScenario&autoreply_id=$id\" title=\"Add Scenario\">[add scenario]</a>";
    $backLink= "<a href=\"$selfurl&op=list\">$backText</a>";

    // only show the add-scenario link if
    // we're not in the process of adding the autoreply
    if (isset($do->autoreply_id))
        echo "$addLink &nbsp;";
    echo "$backLink";
    echo "<hr/>";
    if ($id) {
        echo genDelForm("delScenario", "$selfurl&op=delScenario");
        echo generateScenarios($selfurl, $id);
    }

}

// makeEditScenarioForm
//
// Note that, unlike in most cases, we don't use FormBuilder
// here.  This is because FormBuilder has a bug with NOT_NULL database
// fields, making the fields required for the user to enter.  The bug
// is that there's not way to remove this requirement once its set.
//
// Also, we put all the keywords/params into one form field,
// which would be fairly hard to pull off with FormBuilder.
//
function makeEditScenarioForm($selfurl, $id= null, $autoreply_id= null) {
    global $backText;
    
    $scenarios = DB_DataObject::factory('playsms_featAutoreply_scenario');
    $autoreplies= DB_DataObject::factory('playsms_featAutoreply');
    if ($id) {
        $action= "${selfurl}&op=editScenario&id=$id";
        
        // glom all the keywords together
        $select= "trim(concat(''";
        for ($i= 1; $i < KEYWORD_MAX; $i++) {
            $select.= ", ' ', playsms_featAutoreply_scenario.autoreply_scenario_param" . $i;
        }
        $select.= ")) as keywords";
        
        $scenarios->selectAdd($select);
        $scenarios->get($id);
        $autoreplies->get($scenarios->autoreply_id);
    } else {
        $action= "${selfurl}&op=addScenario&autoreply_id=$autoreply_id";
        $autoreplies->get($autoreply_id);
        $scenarios->autoreply_id= $autoreply_id;
    }
    $scenarioArray= $scenarios->toArray();

    $keywordsField= 'keywords';
    $resultField= 'autoreply_scenario_result';

    $form= new HTML_QuickForm('scenario', 'post', $action);
    $form->addElement('header', 'header', 'AutoReply Scenario');
    $form->addElement('static', 'autoreply_code', 'Keyword 0:', "<b><em>$autoreplies->autoreply_code</em></b>");
    $form->addElement('text', $keywordsField, 'Keywords:', array("size"=>40));

    $form->addElement('textarea', $resultField, 'AutoReply:');
    setupSmsCounting($form, $resultField);
    
    $form->addElement('submit', 'submit', 'Save');
    $form->setDefaults(array('keywords'=>$scenarios->keywords, 
                             $resultField => $scenarioArray[$resultField]));

    if ($form->validate()) {
        $k= 0;
        $keywords= explode(' ', $form->getElementValue($keywordsField), KEYWORD_MAX);
        $keywords= array_pad($keywords, KEYWORD_MAX, "");
        $scenarios->autoreply_scenario_param1= $keywords[$k++];
        $scenarios->autoreply_scenario_param2= $keywords[$k++];
        $scenarios->autoreply_scenario_param3= $keywords[$k++];
        $scenarios->autoreply_scenario_param4= $keywords[$k++];
        $scenarios->autoreply_scenario_param5= $keywords[$k++];
        $scenarios->autoreply_scenario_param6= $keywords[$k++];
        $scenarios->autoreply_scenario_param7= $keywords[$k++];
        $scenarios->autoreply_scenario_result= $form->getElementValue($resultField);

        if (isset($scenarios->autoreply_scenario_id))
            $scenarios->update();
        else {
            $scenarios->insert();

            // if we're adding a new item, then forward
            // on to editing that item so that the user doesn't
            // add it over and over again
            header("Location: $selfurl&op=editScenario&id=$scenarios->autoreply_scenario_id");    
        }
    }
    $form->display();
    
    echo "<a href=\"$selfurl&op=edit&id=$scenarios->autoreply_id\">$backText</a>";
}

function doDeleteAutoreply($selfurl, $id) {    
    $autoreply= DB_DataObject::factory('playsms_featAutoreply');
    $scenario = DB_DataObject::factory('playsms_featAutoreply_scenario');

    // delete all scenarios under the autoreply    
    $scenario->autoreply_id= $id;
    $ok= $scenario->delete();

    // delete the autoreply itself
    $autoreply->autoreply_id= $id;
    $ok= $autoreply->delete();

    if ($ok) {
        $error_string = "Autoreplies have been deleted!";
    } else {
        $error_string = "Failed to delete autoreplies!";
    }
    
    header("Location: $selfurl&op=list&err=" . urlencode($error_string));    
}

function doDeleteScenario($selfurl, $id) {
    $scenario = DB_DataObject::factory('playsms_featAutoreply_scenario');
    $scenario->autoreply_scenario_id= $id;
    $scenario->find(true);
    $autoreply_id= $scenario->autoreply_id;
    $ok= $scenario->delete();

    if ($ok) {
        $error_string = "Autoreplies have been deleted!";
    } else {
        $error_string = "Failed to delete autoreplies!";
    }
    
    header("Location: $selfurl&op=edit&id=$autoreply_id&err=" . urlencode($error_string)); 
}

function testAutoreply($selfurl) {
    $form = new HTML_QuickForm('autoreply_test', 'post', "$selfurl&op=test");

	// Add some elements to the form
	$form->addElement('textarea', 'message', 'Test Message:');
	setupSmsCounting($form, 'message', null);
	$form->addElement('submit', 'submit', 'Test');
	
	if ($form->validate()) {
	    $match= matchAutoreply($form->exportValue('message'), false);
        echo "<b>reply:</b> <br/>" . nl2br($match['autoreply_scenario_result']);
	    exit;
	}
	
	$form->display();
}

function exportAutoreply($selfurl) {
    $form = new HTML_QuickForm('autoreply_export', 'post', "$selfurl&op=export&noheaderfooter=true");

    $msg= "<p>This will export your autoreplies.</p><br/>";
    $form->addElement('static', '', '', $msg);
    $form->addElement('submit', 'submit', 'Export');
    
    if ($form->validate()) {
        doAutoreplyExport();
        exit;
    }
    
    $form->display();
}

function importAutoreply($selfurl) {
    $form = new HTML_QuickForm('autoreply_import', 'post', "$selfurl&op=import");

    $msg= "<p>This will import your autoreplies.</p><br/>";
    $form->addElement('static', '', '', $msg);
    $fileupload= &$form->addElement('file', 'importfile', 'Autoreply file');
    $form->addElement('submit', 'submit', 'Import');
    
    if ($form->validate()) {

        if ($fileupload->isUploadedFile()) {
            $fileinfo= $fileupload->getValue();
            $importfile=$fileinfo['tmp_name'];

            doAutoreplyImport($importfile);
            unlink($importfile);
        }

        exit;
    }
    
    $form->display();
}

function doAutoreplyImport($importfile) {
    global $uid;
    
    $imports= readINIFile($importfile, INI_COMMENTS);
    $imports= $imports[INI_SECTION_AUTOREPLIES];
echo nl2br(print_r($imports, true));

    echo "<b>importing...</b><br/><br/>";    
    foreach ($imports as $keywords => $reply) {
        $autoreply= DB_DataObject::factory('playsms_featAutoreply');
        $scenario = DB_DataObject::factory('playsms_featAutoreply_scenario');
        echo "<br/><b>$keywords</b> <br/>" . nl2br($reply);

        $keywords= explode(' ', $keywords, KEYWORD_MAX);
        $keywords= array_pad($keywords, KEYWORD_MAX, "");        
        $k= 0;

        // try to find the autoreply
        $autoreply->autoreply_code= $keywords[$k++];
        $found= $autoreply->find(true);

        // update/insert the autoreply
        $autoreply->uid= $uid;
        if ($found)
            $autoreply->update();
        else
            $autoreply->insert();

        // try to find the scenario
        $scenario->autoreply_id= $autoreply->autoreply_id;
        $scenario->autoreply_scenario_param1= $keywords[$k++];
        $scenario->autoreply_scenario_param2= $keywords[$k++];
        $scenario->autoreply_scenario_param3= $keywords[$k++];
        $scenario->autoreply_scenario_param4= $keywords[$k++];
        $scenario->autoreply_scenario_param5= $keywords[$k++];
        $scenario->autoreply_scenario_param6= $keywords[$k++];
        $scenario->autoreply_scenario_param7= $keywords[$k++];
        $found= $scenario->find(true);

        // update/insert the scenario
        $scenario->autoreply_scenario_result= $reply;
        if ($found) {
            $scenario->update();
            echo "<br/><b>...updated</b><br/>";
        } else {
            $added= $scenario->insert();
            echo "<br/><b>...added</b><br/>";
        }        
    }
}

function doAutoreplyExport() {
    $autoreply= DB_DataObject::factory('playsms_featAutoreply');
    $scenario = DB_DataObject::factory('playsms_featAutoreply_scenario');

    // glom all the keywords together
    $select= "concat(playsms_featAutoreply.autoreply_code";
    for ($i= 1; $i < KEYWORD_MAX; $i++) {
        $select.= ", ' ', playsms_featAutoreply_scenario.autoreply_scenario_param" . $i;
    }
    $select.= ") as keywords";
    
    $autoreply->selectAdd($select);    
    $autoreply->orderBy('keywords');    
    $autoreply->joinAdd($scenario);
    
    $autoreply->find();
    while ($autoreply->fetch()) {
        $keywords= trim($autoreply->keywords);
        $result= trim($autoreply->autoreply_scenario_result);

        // escape special characters
        $result= str_ireplace("\\", "\\\\", $result);
        $result= str_ireplace("\"", "\\\"", $result);
        $result= str_ireplace("\r\n", "\n", $result);
        $result= str_ireplace("\n", '\n', $result);
        
        $export[$keywords]= $result;
    }

    // create a temporary ini file
    $comment= "autoreply export made on " . date(DATE_RFC822) . "\n";
    $exportfile=tempnam(sys_get_temp_dir(), "playsms");
    writeINIFile($exportfile, array(INI_SECTION_AUTOREPLIES=>$export), 
                 INI_COMMENTS, $comment);

    // send the file contents to the browser
    // so that it'll prompt the user to save it
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="autoreplies.ini"');
    echo file_get_contents($exportfile);

    // delete the temp file    
    unlink($exportfile);
}

function showAutoreplyHelp($selfurl) {
    global $special_codes_notice;
    $backLink= "<a href=\"$selfurl&op=list\">[ Back ]</a>";  
    $l0tr= "L<b>0</b>TR";
    
    $header= array('First Keyword', 'Other Keywords', 'Reply');
    $lotrMain= array('LOTR', '', 'Welcome to Lord of the Rings Text!  Text LOTR and the name of the character you would like to know more about.');
    $lotrAragorn= array('LOTR', 'Aragorn', 'Aragorn, also called Strider, was raised by Elves and became king of Gondor.');
    $unknown= array(UNKNOWN, '', 'Sorry I didnt understand that.  Please text LOTR and the character you would like to know more about.');
    $lotrUnknown= array('LOTR', UNKNOWN, 'Sorry we havent entered that character yet.  Please check back later.');
    $lotrUnknownUpdate= 'Sorry we havent entered ' . SUBKEYWORDS . ' yet.  Please check back later.';
    $lotrStrider= array('LOTR', 'Strider', REMATCH . ' ' . KEYWORD0 . ' Aragorn');
    $l0trUnknown= array($l0tr, UNKNOWN, REMATCH . ' ' . 'LOTR' . ' ' . SUBKEYWORDS);
    $rowAttribs= array('valign'=>'top');
    
    $table= new HTML_Table(array('width'=>'50%'));
    foreach ($header as $index=>$headerCell) {
        $table->setHeaderContents(0, $index, $headerCell);
    }

    $table->addRow($lotrMain, $rowAttribs);
    $table->addRow($lotrAragorn, $rowAttribs);
    
    ?>

    <h2>Autoreply Help</h2>
    <?php echo $backLink; ?>
    <hr/>

    <p>
    An autoreply is a text that the system sends out in response to a given set of keywords it receives.
    </p>
    
    <p>
    Autoreply "scenarios" are grouped by their first keyword.  Keywords are separated by spaces.
    Replies are sent by <em>matching</em> keywords that are texted to PlaySms with keywords in the autoreply scenarios.
    </p>

    <p>
    <b>Special keywords and variables</b>    
    </p>
    <p>
    Through the use of special keywords and variables, autoreplies can be made to be 'smart'.  That is,
    they can cope with misspellings or alternate names for keywords:  
    
    <?php echo $special_codes_notice; ?>
    
    <p>
    In the discussion below we will be using the following example.
    Suppose we are making a Lord of the Rings info line.  We set up the following autoreplies:
    </p>
        
    <p align='center'>
    <?php echo $table->toHtml(); ?>
    </p>

    <p>
    <b>Using <?php echo UNKNOWN ?></b>    
    </p>
    
    <p>
    If someone texts us "LOTR Aragorn" the system will reply with "Aragorn, also called Strider...".
    
    What happens if someone texts us something we don't understand?  Currently, the system won't reply at all,
    which is not very friendly.  We should at least reply with an error message.  How do we reply when there
    are no keyword matches?  For this we use the special keyword <?php echo UNKNOWN ?>:
    </p>
    
    <p align='center'>
    <?php $table->addRow($unknown, $rowAttribs); ?>
    <?php echo $table->toHtml(); ?>
    </p>

    <p>
    So now if someone texts us something like "HARRY POTTER", the system will reply with "Sorry I didn't understand that...".
    </p>
    
    <p>
    What happens if we get a text for a character we haven't entered yet?  "LOTR FRODO", for example?
    Of course the system will respond with the error message.  But we'd like to have a more specific, LOTR-message.
    For that we can use the <?php echo UNKNOWN ?> special keyword again, but this time <em>within</em> the LOTR keyword:
    </p>
    
    <p align='center'>
    <?php $rowIdx= $table->addRow($lotrUnknown, $rowAttribs); ?>
    <?php echo $table->toHtml(); ?>
    </p>
        
    <p>
    Now if someone texts "LOTR FRODO" the system will reply with "Sorry we haven't entered that character...".
    </p>

    <p>
    <b>Using Variables</b>
    </p>

    <p>
    But wouldn't it be nice if we could use the character's name <em>in</em> the error message?
    We can do that with the <?php echo SUBKEYWORDS ?> variable.
    Just put it into the reply and it will be replaced with all the keywords after the first one.
    </p>

    <p align='center'>
    <?php $table->setCellContents($rowIdx, 2, $lotrUnknownUpdate); ?>
    <?php echo $table->toHtml(); ?>
    </p>

    <p>
    Now "LOTR FRODO" will get a reply of "Sorry we haven't entered FRODO yet...".
    </p>

    <p>
    <b>Using Rematching</b>
    </p>

    <p>
    Some users of the system may text "LOTR STRIDER", which is another name for Aragorn.  Currently, this will get 
    a reply of "Sorry we haven't entered STRIDER yet...".  We could of course make a separate entry for Strider,
    perhaps even copying and pasting the reply for Aragorn into it.  However, this would get to be very hard to 
    maintain for large numbers of characters, especially if you wanted to correct any typos or add new info.
    (Aragorn has <em>lots</em> of names, by the way.)
    </p>
    
    <p>
    What we need is some way to have one autoreply <em>point</em> to another autoreply.  This is done with the
    special variable <?php echo REMATCH ?>.  It tells PlaySms to use the autoreply <em>as if</em> it were
    an incoming text, rematching it all over again.  With <?php echo REMATCH ?> we can add a STRIDER entry
    that tells PlaySms to really use ARAGORN:
    </p>
    
    <p align='center'>
    <?php $rowIdx= $table->addRow($lotrStrider, $rowAttribs); ?>
    <?php echo $table->toHtml(); ?>
    </p>   
    
    
    <p>
    Note that we also used the variable <?php echo KEYWORD0 ?>.  Why?  We want to transform the reply into a text
    that will match the aragorn entry, which means it must be "LOTR ARAGORN".  When we use <?php echo KEYWORD0 ?>
    it gets replaced by "LOTR".
    </p>
    
    <p>
    Finally, what if someone gets confused and texts us "<?php echo $l0tr; ?> ARAGORN" (note the zero rather than letter oh)?
    We can use rematching to point all <?php echo $l0tr; ?> texts over to LOTR using one <?php echo UNKNOWN ?> entry.
    </p>

    <p align='center'>
    <?php $rowIdx= $table->addRow($l0trUnknown, $rowAttribs); ?>
    <?php echo $table->toHtml(); ?>

    </p>   

    <p>
    Any <?php echo $l0tr; ?> text that comes in will match against the <?php echo UNKNOWN ?> entry (since there are no other <?php echo $l0tr; ?> entries).
    That entry tells PlaySms to use that reply as if it were an incoming text, which says to use LOTR plus the rest of the
    original keywords.  So "<?php echo $l0tr; ?> ARAGORN" should give us "Aragorn, also called Strider..."
    </p>
    
    <p>
    What happens if we get "<?php echo $l0tr; ?> STRIDER"?  The rematches chain off each other:<br/><br/>  
        "<?php echo $l0tr; ?> STRIDER" => <br/>
        "<?php echo REMATCH ?> LOTR <?php echo SUBKEYWORDS ?>" => <br/>
        "LOTR STRIDER" => <br/>
        "<?php echo REMATCH ?> <?php echo KEYWORD0 ?> Aragorn" => <br/>
        "LOTR Aragorn" => <br/>
        "Aragorn, also called Strider..." 
    </p>

    <p>
    <b>Testing</b>
    </p>

    Its easy to make a mistake when using rematching and <?php echo UNKNOWN ?> and the variables.  Plus,
    its expensive to text the system to find out if what you did is working.  To test your autoreplies,
    you can click on the "[ Test ]" link on the Autoreplies page.  This will let you enter in a test text.
    When you click the 'Test' button, PlaySms will show you the autoreply corresponding to what you typed.

    By the way, try creating the last example in this discussion and trying it out using the testing feature.
    
    <?php
}

?>
