<?
include "init.php";
include "$apps_path[libs]/function.php";
$username = $_COOKIE[vc2];

include "html_header.php";
?>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
    <td width=100%>
<?

echo "<center><h1>Hello $username..</h1></center>";
?>
    </td>
</tr>
<table>

<?

include "html_footer.php";
?>
