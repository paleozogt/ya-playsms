<?php
/**
 * Table Definition for playsms_gwmodTemplate_config
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_gwmodTemplate_config extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_gwmodTemplate_config';    // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $cfg_name;                        // string(20)  not_null
    public $cfg_path;                        // string(250)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_gwmodTemplate_config',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
