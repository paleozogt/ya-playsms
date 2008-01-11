<?php
/**
 * Table Definition for playsms_tblUser
 */
require_once 'DB/DataObject.php';

class DataObjects_Playsms_tblUser extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'playsms_tblUser';                 // table name
    public $uid;                             // int(11)  not_null primary_key auto_increment
    public $status;                          // int(4)  not_null
    public $ticket;                          // string(100)  not_null
    public $username;                        // string(100)  not_null
    public $password;                        // string(100)  not_null binary
    public $name;                            // string(100)  not_null
    public $mobile;                          // string(100)  not_null
    public $email;                           // string(250)  not_null
    public $sender;                          // string(30)  not_null
    public $dailysms;                        // int(11)  not_null
    public $gender;                          // int(4)  not_null
    public $age;                             // int(4)  not_null
    public $address;                         // string(250)  not_null
    public $city;                            // string(100)  not_null
    public $state;                           // string(100)  not_null
    public $country;                         // int(11)  not_null
    public $birthday;                        // string(10)  not_null
    public $marital;                         // int(4)  not_null
    public $education;                       // int(4)  not_null
    public $zipcode;                         // string(10)  not_null
    public $junktimestamp;                   // string(30)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Playsms_tblUser',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
