<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$db_query = "SELECT * FROM playsms_gwmodGnokii_config";
$db_result = dba_query($db_query);
if ($db_row = dba_fetch_array($db_result))
{
    $gnokii_param[name]	= $db_row[cfg_name];
    $gnokii_param[path] = $db_row[cfg_path];
}

?>