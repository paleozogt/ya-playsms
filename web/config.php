<?

// PEAR DB compatible database engine: 
// dbase, fbsql, interbase, informix, msql, mssql, mysql, oci8, odbc, pgsql, sqlite, sybase 
$db_param[type] = "mysql"; // database engine
$db_param[host] = "localhost"; // database host/server
$db_param[port] = ""; // database port
$db_param[user] = "playsms"; // database username
$db_param[pass] = "playsms"; // database password
$db_param[name] = "playsms"; // database name

// SMTP sendmail
define("_SMTP_RELM_", "");
define("_SMTP_USER_", "");
define("_SMTP_PASS_", "");
define("_SMTP_HOST_", "localhost");
define("_SMTP_PORT_", "25");

define(LOGPFX, "playsms: ");

// Do not change anything below this line unless you know what to do
// -----------------------------------------------------------------

// base application directory
$base = "/usr/share/playsms";

// you can turn off PHP error reporting by uncommenting below line
// on production level you should turn off PHP error reporting
//error_reporting(0);

$apps_path[base] = "$base/web";

$apps_path[sql] = "$base/db/playsms-makedb.sql";

// libraries directory
$apps_path[libs] = "$apps_path[base]/lib";

// plugins directory
$apps_path[plug] = "$apps_path[base]/plugin";

// includes directories
$apps_path[incs] = "$apps_path[base]/inc";

$apps_path[bin] = "$base/bin";

// SMS command security parameter
$feat_command_path[bin] = $apps_path[base] . "/bin";

// more configuration
$apps_config['multilogin'] = 1; // 0 for single session login; 1 for multi session login

// add our own private pear stuff
$custompearpath = "$apps_path[libs]/gpl/pear"; 
set_include_path(get_include_path() . PATH_SEPARATOR . $custompearpath);

require_once 'PEAR.php';


// configure DB_DataObject
$dboptions = &PEAR::getStaticProperty('DB_DataObject','options');
$dataobjname="DataObjects";
$dboptions = array(
    'database'         => "mysql://$db_param[user]:$db_param[pass]@localhost/$db_param[name]",
    'schema_location'  => "$apps_path[base]/$dataobjname",
    'class_location'   => "$apps_path[base]/$dataobjname",
    'require_prefix'   => "{$dataobjname}/",
    'class_prefix'     => "{$dataobjname}_",
);
?>
