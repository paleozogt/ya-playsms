<?php
/**
 * Table Definition for playsms_tblSMSOutgoing
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblSMSOutgoing extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblSMSOutgoing';          // table name
    public $smslog_id;                       // int(11)  not_null primary_key auto_increment
    public $flag_deleted;                    // int(4)  not_null
    public $uid;                             // int(11)  not_null
    public $p_gateway;                       // string(100)  not_null
    public $p_src;                           // string(100)  not_null
    public $p_dst;                           // string(100)  not_null
    public $p_footer;                        // string(11)  not_null
    public $p_msg;                           // blob(65535)  not_null blob
    public $p_datetime;                      // string(20)  not_null
    public $p_update;                        // string(20)  not_null
    public $p_status;                        // int(4)  not_null
    public $p_gpid;                          // int(4)  not_null
    public $p_credit;                        // int(4)  not_null
    public $p_sms_type;                      // string(100)  not_null
    public $unicode;                         // int(4)  not_null
    public $send_tries;                      // int(4)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblSMSOutgoing',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
