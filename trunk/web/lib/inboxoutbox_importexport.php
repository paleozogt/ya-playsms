<?php

require_once 'HTML/QuickForm.php';

define(EXPORT_ALLDATA, "alldata");
define(EXPORT_HISTOGRAM_MSG, "histmessage");
define(EXPORT_HISTOGRAM_DAY, "histday");
define(EXPORT_HISTOGRAM_MONTH, "histmonth");

function makeExportForm($selfurl, $inbox) {
    global $uid;
    $form = new HTML_QuickForm('export', 'post', "$selfurl&op=export&noheaderfooter=true");

    $msg= "<p>You can export this data as a CSV (comma-separated values) file, " .
          "<br/>which can then be imported into Excel for analysis and graphing." .
          "</p><br/>";
    $form->addElement('header', '', 'Export');
    $form->addElement('static', '', '', $msg);
    $datatype= $form->addElement('select', 'datatype', "Export What", 
        array(EXPORT_ALLDATA=>'All Data', 
              EXPORT_HISTOGRAM_MSG=>'Histogram by Message',
              EXPORT_HISTOGRAM_DAY=>'Histogram by Day',
              EXPORT_HISTOGRAM_MONTH=>'Histogram by Month')
        );
    $form->addElement('submit', 'submit', 'Export');
    
    if ($form->validate()) {
        
        $datatypeval= $datatype->getValue();
        switch ($datatypeval[0]) {
            case EXPORT_ALLDATA:
                exportAllData($inbox);
                break;
            default:               
                $uidquery= $uid;
                if (isadmin() && !$uid) {
                    unset($uidquery);
                }

                if ($inbox) {
                    $counts= generateHistogramInbox($datatypeval[0], $uid);
                } else {
                    $counts= generateHistogramOutbox($datatypeval[0], $uid);
                }
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
    header('Content-type: text/csv');
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

function histInboxMsg($db) {
    return $db->in_msg;
}

function histOutboxMsg($db) {
    return $db->p_msg;
}

define(DATEFMT_DAY, 'Y/m/d');
define(DATEFMT_MONTH, 'Y/m');

function histInboxDay($db) {
    return date_format(date_create($db->in_datetime), DATEFMT_DAY);
}

function histOutboxDay($db) {
    return date_format(date_create($db->p_datetime), DATEFMT_DAY);
}

function histInboxMonth($db) {
    return date_format(date_create($db->in_datetime), DATEFMT_MONTH);
}

function histOutboxMonth($db) {
    return date_format(date_create($db->p_datetime), DATEFMT_MONTH);
}


function generateHistogramInbox($histtype, $uid) {
    global $uid;
    $db = DB_DataObject::factory('playsms_tblUserInbox');

    switch ($histtype) {
        case EXPORT_HISTOGRAM_MSG:
            $callback= 'histInboxMsg';
            break;
        case EXPORT_HISTOGRAM_DAY:
            $callback= 'histInboxDay';
            break;
        case EXPORT_HISTOGRAM_MONTH: 
            $callback= 'histInboxMonth';
            break;
    }

    $db->in_uid= $uid;
    $db->in_hidden= '0';
    return generateHistogram($db, $callback);
}

function generateHistogramOutbox($histtype, $uid) {
    global $uid;
    $db = DB_DataObject::factory('playsms_tblSMSOutgoing');

    switch ($histtype) {
        case EXPORT_HISTOGRAM_MSG:
            $callback= 'histOutboxMsg';
            break;
        case EXPORT_HISTOGRAM_DAY:
            $callback= 'histOutboxDay';
            break;
        case EXPORT_HISTOGRAM_MONTH: 
            $callback= 'histOutboxMonth';
            break;
    }

    $db->p_uid= $uid;
    $db->p_hidden= '0';
    return generateHistogram($db, $callback);
}

function generateHistogram($db, $getFieldCallback) {
    global $uid;
    $delim= ',';

    $counts= array();
    $db->find();
    while ($db->fetch()) {
        $val= $getFieldCallback($db);        
        $counts[$val]++;
    }
    
    arsort($counts);
    return $counts;
}

function exportHistogram($counts, $filename) {
    // send the file contents to the browser
    // so that it'll prompt the user to save it
    header('Content-type: text/csv');
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