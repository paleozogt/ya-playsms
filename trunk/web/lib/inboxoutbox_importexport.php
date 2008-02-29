<?php

require_once 'HTML/QuickForm.php';

define(EXPORT_ALLDATA, "alldata");
define(EXPORT_HISTOGRAM, "hist");

function makeExportForm($selfurl, $inbox) {
    $form = new HTML_QuickForm('export', 'post', "$selfurl&op=export&noheaderfooter=true");

    $msg= "<p>You can export this data as a CSV (comma-separated values) file, " .
          "<br/>which can then be imported into Excel for analysis and graphing." .
          "</p><br/>";
    $form->addElement('header', '', 'Export');
    $form->addElement('static', '', '', $msg);
    $datatype= $form->addElement('select', 'datatype', "Export What", array(EXPORT_ALLDATA=>"All Data", EXPORT_HISTOGRAM=>"Histogram"));
    $form->addElement('submit', 'submit', 'Export');
    
    if ($form->validate()) {
        
        $datatypeval= $datatype->getValue();
        switch ($datatypeval[0]) {
            case EXPORT_ALLDATA:
                exportAllData($inbox);
                break;
            case EXPORT_HISTOGRAM:    
                $counts= generateHistogram($inbox);
                $filename= $inbox ? 'inbox-hist-export.csv' : 'outbox-hist-export.csv';
                exportHistogram($counts, $filename);
                break;
        }
    
        exit;
    }
    
    $form->display();    
}

function exportAllData($inbox) {
    global $uid;
    $delim= ',';
    $db = DB_DataObject::factory($inbox ? 'playsms_tblUserInbox' : 'playsms_tblSMSOutgoing');

    if (isadmin() && !$uid) {
    } else {
        if ($inbox)
            $db->in_uid= $uid;
        else
            $db->uid= $uid;
    }
    if ($inbox) {
        $db->in_hidden= '0';
        $orderBy= "in_id DESC";
        $filename= "inbox-export.csv";
    } else {
        $db->flag_deleted= '0';
        $filename= "outbox-export.csv";
        $orderBy= "smslog_id DESC";
    }

    // send the file contents to the browser
    // so that it'll prompt the user to save it
    header('Content-type: text/plain');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $line= "id,date,phone number";
    if (!$inbox) $line.= ",status";
    $line.= ",message\n";
    echo $line;
 
    $db->orderBy($orderBy);
    $db->find();
    while ($db->fetch()) {
        if ($inbox) {
            $msg= prepLongFieldForCsv($db->in_msg);
            $line=  $db->in_id . $delim .
                    $db->in_datetime . $delim .
                    $db->in_sender . $delim .
                    "\"$msg\"" . "\n";
        } else {
            $msg= prepLongFieldforCsv($db->p_msg);
        
            $line=  $db->smslog_id . $delim .
                    $db->p_datetime . $delim .
                    $db->p_dst . $delim .
                    getStatusName($db->p_status) . $delim .
                    "\"$msg\"" . "\n";
        }
        echo $line;
    }
}

function generateHistogram($inbox) {
    global $uid;
    $delim= ',';
    $db = DB_DataObject::factory($inbox ? 'playsms_tblUserInbox' : 'playsms_tblSMSOutgoing');

    if (isadmin() && !$uid) {
    } else {
        if ($inbox)
            $db->in_uid= $uid;
        else
            $db->uid= $uid;
    }
    if ($inbox)        
        $db->in_hidden= '0';
    else
        $db->flag_deleted= '0';    

    $counts= array(); 
    $db->find();
    while ($db->fetch()) {
        $msg= $inbox ? $db->in_msg : $db->p_msg;        
        $counts[$msg]++;
    }
    
    arsort($counts);
    return $counts;
}

function exportHistogram($counts, $filename) {
    // send the file contents to the browser
    // so that it'll prompt the user to save it
    header('Content-type: text/plain');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    foreach ($counts as $key=>$count) {
        $key= prepLongFieldForCsv($key);    
        echo "\"$key\",$count\n";
    }
}

function prepLongFieldForCsv($field) {
    // in csv format, if fields contain quote 
    // literals they are doubled
    $field= str_ireplace('"', '""', $field);
    
    // the carriage return situation may
    // be inconsistent. make it all cr-lf,
    // which is what excel csv likes
    //
    $field= str_ireplace("\r\n", "\n", $field);
    $field= str_ireplace("\r", "\n", $field);
    $field= str_ireplace("\n", "\r\n", $field);
    
    return $field;
}

?>