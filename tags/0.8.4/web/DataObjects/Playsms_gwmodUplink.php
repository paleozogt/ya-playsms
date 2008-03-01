<?php
/**
 * Table Definition for playsms_gwmodUplink
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_gwmodUplink extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_gwmodUplink';             // table name
    public $up_id;                           // int(11)  not_null primary_key auto_increment
    public $up_local_slid;                   // int(11)  not_null
    public $up_remote_slid;                  // int(11)  not_null
    public $up_status;                       // int(4)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_gwmodUplink',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
