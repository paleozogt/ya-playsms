<?php
/**
 * Table Definition for playsms_tblUser_country
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblUser_country extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblUser_country';         // table name
    public $country_id;                      // int(11)  not_null primary_key auto_increment
    public $country_name;                    // string(200)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblUser_country',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
