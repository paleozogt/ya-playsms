<?php
/**
 * Table Definition for playsms_featAutoreply_scenario
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_featAutoreply_scenario extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_featAutoreply_scenario';    // table name
    public $autoreply_scenario_id;           // int(11)  not_null primary_key auto_increment
    public $autoreply_id;                    // int(11)  not_null
    public $autoreply_scenario_param1;       // string(20)  not_null
    public $autoreply_scenario_param2;       // string(20)  not_null
    public $autoreply_scenario_param3;       // string(20)  not_null
    public $autoreply_scenario_param4;       // string(20)  not_null
    public $autoreply_scenario_param5;       // string(20)  not_null
    public $autoreply_scenario_param6;       // string(20)  not_null
    public $autoreply_scenario_param7;       // string(20)  not_null
    public $autoreply_scenario_result;       // string(130)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_featAutoreply_scenario',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
