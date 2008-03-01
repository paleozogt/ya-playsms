<?php
/**
 * Table Definition for playsms_tblUserGroupPhonebook_public
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblUserGroupPhonebook_public extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblUserGroupPhonebook_public';    // table name
    public $gpidpublic;                      // int(11)  not_null primary_key auto_increment
    public $gpid;                            // int(11)  not_null
    public $uid;                             // string(100)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblUserGroupPhonebook_public',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
