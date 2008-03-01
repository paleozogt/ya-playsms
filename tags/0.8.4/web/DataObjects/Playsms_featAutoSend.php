<?php
/**
 * Table Definition for playsms_featAutoSend
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_featAutoSend extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_featAutoSend';            // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $frequency;                       // string(7)  not_null enum
    public $number;                          // string(100)  not_null
    public $msg;                             // blob(65535)  not_null blob

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_featAutoSend',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
