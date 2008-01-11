<?php
/**
 * Table Definition for playsms_gwmodClickatell_apidata
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_gwmodClickatell_apidata extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_gwmodClickatell_apidata';    // table name
    public $apidata_id;                      // int(11)  not_null primary_key auto_increment
    public $smslog_id;                       // int(11)  not_null
    public $apimsgid;                        // string(100)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_gwmodClickatell_apidata',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
