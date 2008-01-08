<?php
/**
 * Table Definition for playsms_tblSMSTemplate
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblSMSTemplate extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblSMSTemplate';          // table name
    public $tid;                             // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null
    public $t_title;                         // string(100)  not_null
    public $t_text;                          // string(130)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblSMSTemplate',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
