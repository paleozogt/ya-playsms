<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP [" . $_SERVER['REMOTE_ADDR'] . "] logged");
};

$op = $_GET[op];
$gpid = $_GET[gpid];
$pid = $_GET[pid];
$tid = $_GET[tid];

switch ($op) {
	case "list" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$fm_name = "fm_smstemp";
		$content .= "
		    <h2>Message template list</h2>
		    <p>
		    <p><a href=\"menu.php?inc=sms_template&op=add_template\">[ Add message template ]</a>
		    <p>
		    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\">
		    <form name=\"$fm_name\" action=\"menu.php?inc=sms_template&op=delete\" method=post>
		    <tr>
			<td class=\"box_title\" width=\"4\">&nbsp;</td>
			<td class=\"box_title\" width=\"40%\">&nbsp;Name</td>
			<td class=\"box_title\" width=\"60%\">&nbsp;Content</td>
			<td class=\"box_title\" width=\"\" align=\"center\"><input type=checkbox onclick=CheckUncheckAll(document." . $fm_name . ")></td>
		    </tr>
		";
		$db_query = "SELECT * FROM playsms_tblSMSTemplate WHERE uid='$uid'";
		$db_result = dba_query($db_query);
		$i = 0;
		while ($db_row = dba_fetch_array($db_result)) {
			$i++;
			$tid = $db_row[tid];
			$temp_title = $db_row[t_title];
			$temp_text = $db_row[t_text];
			$content .= "
				<tr>
				    <td class=\"box_text\">&nbsp;$i.&nbsp;</td>
				    <td class=\"box_text\">&nbsp;<a href=\"menu.php?inc=sms_template&op=edit_template&tid=$tid\">$temp_title</a></td>
				    <td class=\"box_text\">&nbsp;$temp_text</td>
				    <td class=\"box_text\" align=\"center\"><input type=hidden name=tid" . $i . " value=\"" . $db_row['tid'] . "\"><input type=checkbox name=chkid" . $i . "></td>
		            	    <input type=hidden name=tid" . $i . " value=\"" . $db_row['tid'] . "\">
				</tr>
			    ";
		}
		// FIXME: EDIT TEMPLATES SHOULD BE LIKE DELETE OPTIONS TOO!
		$content .= "
			    </table>
			    <table width=\"100%\"><tr><td align=\"right\">
				Select Action : <select name=\"action\">
				    <option value=\"delete\">Delete Selections</option>
				</select>
				<input type=\"submit\" value=\"Go\" class=\"button\"></td></tr>
			    </table>
			    <p>
			    <a href=\"menu.php?inc=sms_template&op=add_template\">[ Add message template ]</a>
			    <p>
			    <input type=\"hidden\" name=\"item_count\" value=\"$i\">
			    </form>
			";
		echo $content;
		break;
	case "add_template" :
		$content = "
			    <h2>Add message template</h2>
			    <p>
			    <form action=\"menu.php?inc=sms_template&op=add_yes\" method=\"post\">
			    <p>Message template name: <input type=\"text\" maxlength=\"100\" name=\"t_title\">
			    <p>Message template content: <input type=text name=t_text size=60 maxlength=130>
			    <br>(Max 130 character)
			    <p><input type=\"submit\" class=\"button\" value=\"Save Template\">
			    </form>
			    <p><li><a href=\"menu.php?inc=sms_template&op=list\">Back</a>
			    </form>
			";
		echo $content;
		break;
	case "add_yes" :
		$t_title = $_POST[t_title];
		$t_text = $_POST[t_text];
		$db_query = "INSERT INTO playsms_tblSMSTemplate (uid,t_title,t_text) VALUES ('$uid','$t_title','$t_text')";
		$db_result = dba_insert_id($db_query);
		if ($db_result > 0) {
			$error_string = "Message template is saved";
		} else {
			// FIXME
		}
		header("Location: menu.php?inc=sms_template&op=list&err=" . urlencode($error_string));
		break;
	case "edit_template" :
		$db_query = "SELECT * FROM playsms_tblSMSTemplate WHERE tid='$tid'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$content = "
			    <h2>Edit message template</h2>
			    <p>
			    <form action=\"menu.php?inc=sms_template&op=edit_yes&tid=$tid\" method=\"post\">
			    <p>Message template name: <input type=\"text\" maxlength=\"100\" name=\"t_title\" value=\"" . $db_row[t_title] . "\">
			    <p>Message template content: <input type=text name=t_text size=60 maxlength=130 values=\"" . $db_row[t_text] . "\">
			    <br>(Max 130 character)
			    <p><input type=\"submit\" class=\"button\" value=\"Save Template\">
			    <input type=\"hidden\" name=\"item_count\" value=\"$i\">
			    </form>
			    <p><li><a href=\"menu.php?inc=sms_template&op=list\">Back</a>
			    </form>
			";
		echo $content;
		break;
	case "edit_yes" :
		$t_title = $_POST[t_title];
		$t_text = $_POST[t_text];
		$db_query = "UPDATE playsms_tblSMSTemplate SET t_title='$t_title', t_text='$t_text' WHERE tid='$tid'";
		$db_result = dba_affected_rows($db_query);
		if ($db_result > 0) {
			$error_string = "Message template has been edited";
		} else {
			$error_string = "Fail to edit message template";
		}
		header("Location: menu.php?inc=sms_template&op=list&err=$err=" . urlencode($error_string));
		break;
	case "delete" :
		$item_count = $_POST[item_count];
		for ($i = 1; $i <= $item_count; $i++) {
			${ "tid" . $i } = $_POST["tid" . $i];
			${ "chkid" . $i } = $_POST["chkid" . $i];
		}
		$content = "
			    <h2>Delete message template</h2>
			    <p>
			    <form action=\"menu.php?inc=sms_template&op=delete_yes\" method=\"post\">
			    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\">
			    <tr>
				<td class=\"box_title\" width=\"4\">&nbsp;*&nbsp;</td>
				<td class=\"box_title\" width=\"40%\">&nbsp;Name</td>
				<td class=\"box_title\" width=\"60%\">&nbsp;Content</td>
			    </tr>
			";
		$j = 0;
		for ($i = 1; $i <= $item_count; $i++) {
			if (${ "chkid" . $i } == "on") {
				$j++;
				$db_query = "SELECT * FROM playsms_tblSMSTemplate WHERE tid='" . ${"tid" . $i} . "'";
				$db_result = dba_query($db_query);
				$db_row = dba_fetch_array($db_result);
				$content .= "
						    <tr>
					    		<td class=\"box_text\">&nbsp;$j&nbsp;</td>
					    		<td class=\"box_text\">&nbsp;" . $db_row[t_title] . "</td>
					    		<td class=\"box_text\">&nbsp;" . $db_row[t_text] . "</td>
					    		<input type=\"hidden\" name=\"tid" . $j . "\" value=\"" . ${ "tid" . $i } . "\">
						    </tr>
						";
			}
		}
		$content .= "
			    </table>
			    <input type=\"hidden\" name=\"item_count\" value=\"$j\">
			    <p> Delete all templates ?
			    <p><input type=\"submit\" value=\"Delete\" class=\"button\">
			    </form>
			    <li><a href=\"menu.php?inc=sms_template&op=list\">Back</a>
			";
		echo $content;
		break;
	case "delete_yes" :
		$item_count = $_POST[item_count];
		for ($i = 1; $i <= $item_count; $i++) {
			${ "tid" . $i } = $_POST["tid" . $i];
		}
		for ($i = 1; $i <= $item_count; $i++) {
			$db_query = "DELETE FROM playsms_tblSMSTemplate WHERE tid='" . ${"tid" . $i} . "'";
			$db_result = dba_affected_rows($db_query);
		}
		$error_string = "Selected message template has been deleted";
		header("Location: menu.php?inc=sms_template&op=list&err=" . urlencode($error_string));
		break;

}
?>
