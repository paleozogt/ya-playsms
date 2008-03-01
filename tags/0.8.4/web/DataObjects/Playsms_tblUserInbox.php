<?php
/**
 * Table Definition for playsms_tblUserInbox
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblUserInbox extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblUserInbox';            // table name
    public $in_id;                           // int(11)  not_null primary_key auto_increment
    public $in_sender;                       // string(20)  not_null
    public $in_uid;                          // int(11)  not_null
    public $in_msg;                          // blob(65535)  not_null blob
    public $in_datetime;                     // string(20)  not_null
    public $in_hidden;                       // int(4)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblUserInbox',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
