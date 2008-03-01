<?php
include "init.php";
include "$apps_path[libs]/function.php";
include "html_header.php";

$err = $_GET[err];

if ($err) {
	echo "<font color=red>$err</font><br><br>";
}

$content = "
    <table width=$box_width cellpadding=1 cellspacing=1 border=0>
    <form action=login.php method=POST>
    <tr>
        <td width=30%>Username</td>
        <td width=3 align=center>:</td>
        <td><input type=text name=username maxlength=100 size=10></td>
    </tr>
    <tr>
        <td width=30%>Password</td>
        <td width=3 align=center>:</td>
        <td><input type=password name=password maxlength=100 size=10></td>
    </tr>
    <tr>
        <td width=100% colspan=3><input type=submit class=button value=Login></td>
    </tr>
    </form>
    </table>
";
echo $content;
?>

<br><br>
<h2>Dont forget to stop by at:</h2>
<li><a href=http://playsms.sourceforge.net target=_blank>http://playsms.sourceforge.net</a></li>
<li><a href=http://sleepless.ngoprek.org target=_blank>http://sleepless.ngoprek.org</a></li>
<br><br>

<?php

include "html_footer.php";
?>
