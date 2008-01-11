<?php
/**
 * Table Definition for playsms_featBoard
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_featBoard extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_featBoard';               // table name
    public $board_id;                        // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null
    public $board_code;                      // string(100)  not_null
    public $board_forward_email;             // string(250)  not_null
    public $board_pref_template;             // blob(65535)  not_null blob

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_featBoard',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
