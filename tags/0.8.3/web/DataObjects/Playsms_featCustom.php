<?php
/**
 * Table Definition for playsms_featCustom
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_featCustom extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_featCustom';              // table name
    public $custom_id;                       // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null
    public $custom_code;                     // string(10)  not_null
    public $custom_url;                      // blob(65535)  not_null blob

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_featCustom',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
