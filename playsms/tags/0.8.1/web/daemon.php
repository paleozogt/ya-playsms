<?
if (!function_exists("validatelogin"))
{
    include "init.php";
    include "$apps_path[libs]/function.php";
}    

getsmsinbox();
getsmsstatus();
execgwcustomcmd();
execcommoncustomcmd();

$manual_refresh = $manual;
if ($_GET[manual])
{
    $manual_refresh = $_GET[manual];
}

if ($manual_refresh == 1)
{
    include "html_header.php";
    echo "<h1><font color=green>Daemon refreshed</font></h1>";
    echo "<p>Back to <a href=user.php target=_top>main menu</a>";
    include "html_footer.php";
    die();
}

$url = $_GET[url];
if (isset($url))
{
    $url = base64_decode($url);
    header ("Location: $url");
}
else
{
    echo "REFRESHED";
}

?>