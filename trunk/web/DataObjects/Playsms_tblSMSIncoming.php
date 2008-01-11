<?php
/**
 * Table Definition for playsms_tblSMSIncoming
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblSMSIncoming extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblSMSIncoming';          // table name
    public $in_id;                           // int(11)  not_null primary_key auto_increment
    public $in_gateway;                      // string(100)  not_null
    public $in_sender;                       // string(20)  not_null
    public $in_masked;                       // string(20)  not_null
    public $in_code;                         // string(20)  not_null
    public $in_msg;                          // string(200)  not_null
    public $in_datetime;                     // string(20)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblSMSIncoming',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
