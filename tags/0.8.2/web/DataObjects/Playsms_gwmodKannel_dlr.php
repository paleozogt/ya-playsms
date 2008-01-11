<?php
/**
 * Table Definition for playsms_gwmodKannel_dlr
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_gwmodKannel_dlr extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_gwmodKannel_dlr';         // table name
    public $kannel_dlr_id;                   // int(11)  not_null primary_key auto_increment
    public $smslog_id;                       // int(11)  not_null
    public $kannel_dlr_type;                 // int(4)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_gwmodKannel_dlr',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
