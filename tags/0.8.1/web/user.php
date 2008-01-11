<? 
include "init.php"; 
include "$apps_path[libs]/function.php";
if (!valid()) { forcelogout(); };
?>

<html>
<head>
<title><?=$web_title?></title>
<frameset cols="24%,76%" framespacing=0>
    <frame name=fr_left src=fr_left.php>
    <frame name=fr_right src=fr_right.php>
</frameset>
</head>
</html>
