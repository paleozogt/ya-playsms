<?php
/**
 * Table Definition for playsms_featPoll
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_featPoll extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_featPoll';                // table name
    public $poll_id;                         // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null
    public $poll_title;                      // string(250)  not_null
    public $poll_code;                       // string(10)  not_null
    public $poll_enable;                     // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_featPoll',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
