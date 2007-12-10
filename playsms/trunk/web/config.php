<?

// PEAR DB compatible database engine: 
// dbase, fbsql, interbase, informix, msql, mssql, mysql, oci8, odbc, pgsql, sqlite, sybase 
$db_param[type]	= "mysql";			// database engine
$db_param[host]	= "localhost";			// database host/server
$db_param[port]	= "";				// database port
$db_param[user]	= "playsms";			// database username
$db_param[pass]	= "playsms";			// database password
$db_param[name]	= "playsms";			// database name

// SMTP sendmail
define("_SMTP_RELM_","");
define("_SMTP_USER_","");
define("_SMTP_PASS_","");
define("_SMTP_HOST_","localhost");
define("_SMTP_PORT_","25");

// base application directory
$apps_path[base]	= "/usr/share/playsms";

// Do not change anything below this line unless you know what to do
// -----------------------------------------------------------------


// you can turn off PHP error reporting by uncommenting below line
// on production level you should turn off PHP error reporting
//error_reporting(0);

// libraries directory
$apps_path[libs]	= "$apps_path[base]/lib";

// plugins directory
$apps_path[plug]	= "$apps_path[base]/plugin";

// includes directories
$apps_path[incs]	= "$apps_path[base]/inc";

// SMS command security parameter
$feat_command_path[bin]	= $apps_path[base]."/bin";

// more configuration
$apps_config['multilogin']      = 1; // 0 for single session login; 1 for multi session login

?>