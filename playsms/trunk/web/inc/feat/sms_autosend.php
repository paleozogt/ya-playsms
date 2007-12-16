<?
$op = $_GET[op];
$selfurl = "menu_admin.php?inc=sms_autosend";

// cron will call us directly to do autosending,
// so skip the security check in that case
// but only if its a local connection
//
if ($op == "autosend" && $_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
	include "../../init.php";
	include "$apps_path[libs]/function.php";
}

// security check
//
if (!defined("_SECURE_")) {
	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

// print any errors from a 
// previous load of this page
//
if ($err) {
	echo "<p><font color=red>$err</font><p>\n";
}

switch ($op) {
	case "list" :
		echo makeList($selfurl);
		break;

	case "add" :
		echo makeEditForm($selfurl);
		break;

	case "edit" :
		echo makeEditForm($selfurl, getRecord($_GET[id]));
		break;

	case "save" :
		doSave($selfurl, $_POST);
		break;

	case "del" :
		doDelete($_POST[id], $selfurl);
		break;

	case "autosend" :
		doAutoSend($_GET[when]);
		break;
}

function makeList($selfurl) {
	$html = "
			<h2>List/Manage/Delete SMS autosend</h2>
			<p/>
			<a href=\"$selfurl&op=add\">[ Add ]</a>
			<p/>";

	// create hidden form with the 
	// id to delete, this way it will
	// get POSTed
	//
	$formName = "delForm";
	$html .= "
			<form name=\"$formName\" method=\"post\" action=\"$selfurl&op=del\">
				<input type=\"hidden\" name=\"id\" value=\"\"/>
				<script language=\"JavaScript\"><!--
   					function del(id, msg) {
						if (confirm(msg)) {
							document.forms.$formName.id.value=id;
							document.forms.$formName.submit();
						}
				   }
				--></script>
			 </form>";

	$db_query = "SELECT * FROM playsms_featAutoSend";
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result)) {
		$html .= "
			<a href=\"$selfurl&op=edit&id=$db_row[id]\">[e]</a>

			<a href=\"javascript: 
					del($db_row[id], 'Are you sure you want to delete this autosend?');
					\">[x]</a>
 
			$db_row[when] $db_row[number] \"$db_row[msg]\"
			<br/>
			";
	}
	return $html;
}

function getRecord($id) {
	$db_query = "SELECT * FROM playsms_featAutoSend WHERE id='$id'";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result)) {
		return $db_row;
	} else {
		return array ();
	}
}

function makeEditForm($selfurl, $vals = array ()) {
	$formName = "edit";

	// create choices for the when to run autosend
	$whenchoices = array (
		"daily",
		"hourly",
		"weekly",
		"monthly"
	);
	$whenselect = "<select name=\"when\">\n";
	foreach ($whenchoices as $whenchoice) {
		if ($vals[when] == $whenchoice)
			$sel = "selected";
		else
			$sel = "";
		$whenselect .= "\t<option value=\"$whenchoice\" $sel>$whenchoice</option>\n";
	}
	$whenselect .= "</select>";

	$smsinput = generateSmsInput($formName, "Message:", $vals[msg], "msg");

	$html = "
			<h2>$usertext SMS Autosend</h2>
			<p/>
			<form name=\"$formName\" action=\"$selfurl&op=save\" method=\"post\">
			<input type=\"hidden\" name=\"id\" value=\"$vals[id]\"/>
			<p/>Number: 
			<input type=\"text\" size=\"10\" maxlength=\"10\" name=\"number\" value=\"$vals[number]\">
			<p/>When: $whenselect
			<p/>$smsinput
			<p/><input type=submit class=button value=\"Save\">
			</form>
			<p/><p/>
			<a href=\"$selfurl&op=list\">Back</a>
			";
	return $html;
}

function doSave($selfurl, $vals) {
	$table = "playsms_featAutoSend";

	// if there's no id that means
	// its a new record
	//
	if ($vals[id] == "") {
		$db_query = "INSERT INTO $table (`when`,`number`,`msg`) " .
		"VALUES ('$vals[when]','$vals[number]','$vals[msg]')";
		error_log($db_query, 0);
		$ok = ($autoreply_id = @ dba_insert_id($db_query));
	} else {
		$db_query = "UPDATE $table SET " .
		"`when`='$vals[when]', " .
		"`number`='$vals[number]', " .
		"`msg`='$vals[msg]' " .
		"WHERE `id`='$vals[id]'";
		error_log($db_query, 0);
		$ok = ($db_result = @ dba_affected_rows($db_query));
	}

	if ($ok) {
		$error_string = "SMS AutoSend has been saved";
		$gotourl = "$selfurl&op=list";
	} else {
		$error_string = "Failed to save SMS AutoSend!";
		$gotourl = "$selfurl&op=edit&id=$vals[id]";
	}

	header("Location: $gotourl&err=" . urlencode($error_string));
}

function doDelete($id, $selfurl) {
	$db_query = "DELETE FROM playsms_featAutoSend WHERE `id`=$id";
	if (@ dba_affected_rows($db_query)) {
		$error_string = "SMS autosend has been deleted!";
	} else {
		$error_string = "Failed to delete SMS autosend";
	}

	header("Location: $selfurl&op=list&err=" . urlencode($error_string));
}

// TODO: have this return an http header
// for success or failure
//
function doAutosend($when) {
	error_log("autosending for '$when'");
	$db_query = "SELECT * FROM playsms_featAutoSend WHERE `when`='$when'";
	error_log($db_query);

	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result)) {
		error_log("sending $db_row[id], $db_row[number], '$db_row[msg]'...");
		websend2pv("admin", $db_row[number], $db_row[msg]);
	}

}