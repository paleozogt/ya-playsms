<?php
/**
 * Table Definition for playsms_tblUserGroupPhonebook
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblUserGroupPhonebook extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblUserGroupPhonebook';    // table name
    public $gpid;                            // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null
    public $gp_name;                         // string(100)  not_null
    public $gp_code;                         // string(10)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblUserGroupPhonebook',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
