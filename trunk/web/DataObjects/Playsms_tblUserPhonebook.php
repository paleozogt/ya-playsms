<?php
/**
 * Table Definition for playsms_tblUserPhonebook
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblUserPhonebook extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblUserPhonebook';        // table name
    public $pid;                             // int(11)  not_null primary_key auto_increment
    public $gpid;                            // int(11)  not_null
    public $uid;                             // int(11)  not_null
    public $p_num;                           // string(100)  not_null
    public $p_desc;                          // string(250)  not_null
    public $p_email;                         // string(250)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblUserPhonebook',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
