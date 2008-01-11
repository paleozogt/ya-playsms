<?php
/**
 * Table Definition for playsms_gwmodKannel_config
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_gwmodKannel_config extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_gwmodKannel_config';      // table name
    public $cfg_name;                        // string(20)  
    public $cfg_incoming_path;               // string(250)  
    public $cfg_username;                    // string(100)  
    public $cfg_password;                    // string(100)  
    public $cfg_global_sender;               // string(20)  
    public $cfg_bearerbox_host;              // string(250)  
    public $cfg_sendsms_port;                // string(10)  
    public $cfg_playsms_web;                 // string(250)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_gwmodKannel_config',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
