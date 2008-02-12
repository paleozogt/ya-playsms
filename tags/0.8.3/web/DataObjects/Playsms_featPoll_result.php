<?php
/**
 * Table Definition for playsms_featPoll_result
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_featPoll_result extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_featPoll_result';         // table name
    public $result_id;                       // int(11)  not_null primary_key auto_increment
    public $poll_id;                         // int(11)  not_null
    public $choice_id;                       // int(11)  not_null
    public $poll_sender;                     // string(20)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_featPoll_result',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
