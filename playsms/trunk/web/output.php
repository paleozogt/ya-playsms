<?
include "init.php";
include "$apps_path[libs]/function.php";

$refresh = strtoupper($_GET[refresh]);
$backagain = strtoupper($_GET[backagain]);
if (($refresh == "YES") && ($backagain != "YES")) {
	$url = base64_encode($_SERVER[REQUEST_URI] . "&backagain=yes");
	header("Location: daemon.php?url=$url");
	die();
}

$show = $_GET[show];

switch ($show) {
	case "vote" :
	case "poll" :
		$code = $_GET[code];
		$db_query = "SELECT poll_id,poll_title FROM playsms_featPoll WHERE poll_code='$code'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$poll_id = $db_row[poll_id];
		$poll_title = $db_row[poll_title];
		$db_query = "SELECT result_id FROM playsms_featPoll_result WHERE poll_id='$poll_id'";
		$total_voters = @ dba_num_rows($db_query);
		if ($poll_id) {
			$mult = $_GET[mult];
			$bodybgcolor = $_GET[bodybgcolor];
			if (!isset ($mult)) {
				$mult = "2";
			}
			if (!isset ($bodybgcolor)) {
				$bodybgcolor = "#FEFEFE";
			}
			$content = "
				<html>
				<head>
				<title>$web_title</title>
				<meta name=\"author\" content=\"http://playsms.sourceforge.net\">
				<link rel=\"stylesheet\" type=\"text/css\" href=\"./inc/jscss/common.css\">
				</head>
				<body bgcolor=\"$bodybgcolor\" topmargin=\"0\" leftmargin\"0\">
				<table cellpadding=1 cellspacing=1 border=0>
				<tr><td colspan=2 width=100% class=box_text><font size=-2>$poll_title</font></td></tr>
			    ";
			$db_query = "SELECT * FROM playsms_featPoll_choice WHERE poll_id='$poll_id' ORDER BY choice_code";
			$db_result = dba_query($db_query);
			while ($db_row = dba_fetch_array($db_result)) {
				$choice_id = $db_row[choice_id];
				$choice_title = $db_row[choice_title];
				$choice_code = $db_row[choice_code];
				$db_query1 = "SELECT result_id FROM playsms_featPoll_result WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
				$choice_voted = @ dba_num_rows($db_query1);
				if ($total_voters) {
					$percentage = round(($choice_voted / $total_voters) * 100);
				} else {
					$percentage = "0";
				}
				$content .= "
					    <tr>
						<td width=90% nowrap class=box_text valign=middle align=left>
						    <font size=-2>[ <b>$choice_code</b> ] $choice_title</font>
						</td>
						<td width=10% nowrap class=box_text valign=middle align=right>
						    <font size=-2>$percentage%, $choice_voted</font>
						</td>
					    </tr>
					    <tr>
						<td width=100% nowrap class=box_text valign=middle align=left colspan=2>
						    <img src=\"./images/bar.gif\" height=\"12\" width=\"" . ($mult * $percentage) . "\" alt=\"" . ($percentage) . "% ($choice_voted)\"></font><br>
						</td>
					    </tr>
					";
			}
			$content .= "
				<tr><td colspan=2><font size=-2><b>Total: $total_voters</b></font></td></tr>
				</table>
				</body>
				</html>
			    ";
			echo $content;
		}
		break;
	case "board" :
	default :
		// Use code, tag deprecated
		$code = $_GET[code];
		if (!$code) {
			$code = $_GET[tag];
		}
		if ($code) {
			$code = strtoupper($code);
			$line = $_GET[line];
			$type = $_GET[type];
			switch ($type) {
				case "xml" :
					$content = outputtorss($code, $line);
					echo $content;
					break;
				case "html" :
				default :
					$bodybgcolor = $_GET[bodybgcolor];
					$oddbgcolor = $_GET[oddbgcolor];
					$evenbgcolor = $_GET[evenbgcolor];
					$content = outputtohtml($code, $line, $bodybgcolor, $oddbgcolor, $evenbgcolor);
					echo $content;
			}
		}
}
?>
