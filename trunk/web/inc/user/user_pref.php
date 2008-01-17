<?php
if (!defined("_SECURE_")) {

	die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
};

$op = $_GET[op];

switch ($op) {
	case "user_pref" :
		if ($err) {
			$content = "<p><font color=red>$err</font><p>";
		}
		$db_query = "SELECT * FROM playsms_tblUser WHERE uid='$uid'";
		$db_result = dba_query($db_query);
		$daily = 0;
		if ($db_row = dba_fetch_array($db_result)) {
			$daily = $db_row[dailysms];
			$gender = $db_row[gender];
			$address = $db_row[address];
			$city = $db_row[city];
			$state = $db_row[state];
			$country = $db_row[country];
			$birthday = $db_row[birthday];
			$marital = $db_row[marital];
			$education = $db_row[education];
			$zipcode = $db_row[zipcode];
			$sender = $db_row[sender];
		}
		// get gender option
		$array_gender = array (
			"Please Select",
			"Male",
			"Female"
		);
		for ($i = 0; $i <= 2; $i++) {
			$selected = "";
			if ($i == $gender) {
				$selected = "selected";
			}
			$option_gender .= "<option value=\"$i\" $selected>" . $array_gender[$i] . "</option>\n";
		}
		// get marital option
		$array_marital = array (
			"Please Select",
			"Single",
			"Divorced",
			"Separated",
			"Widowed",
			"Married"
		);
		for ($i = 0; $i <= 5; $i++) {
			$selected = "";
			if ($i == $marital) {
				$selected = "selected";
			}
			$option_marital .= "<option value=\"$i\" $selected>" . $array_marital[$i] . "</option>\n";
		}
		// get education option
		$array_education = array (
			"Please Select",
			"High School",
			"College Student",
			"College Graduate",
			"Post Graduate"
		);
		for ($i = 0; $i <= 4; $i++) {
			$selected = "";
			if ($i == $education) {
				$selected = "selected";
			}
			$option_education .= "<option value=\"$i\" $selected>" . $array_education[$i] . "</option>\n";
		}
		// get country option
		$db_query = "SELECT * FROM playsms_tblUser_country ORDER BY country_name";
		$db_result = dba_query($db_query);
		$option_country = "<option value=\"0\">Please Select</option>\n";
		while ($db_row = dba_fetch_array($db_result)) {
			$country_id = $db_row[country_id];
			$country_name = $db_row[country_name];
			$selected = "";
			if ($country_id == $country) {
				$selected = "selected";
			}
			$option_country .= "<option value=\"$country_id\" $selected>$country_name</option>\n";
		}
		$content .= "
			    <h2>Preferences</h2>
			    <p>
			    <form action=menu.php?inc=user_pref&op=user_pref_save method=post enctype=\"multipart/form-data\">
			    <table width=100% cellpadding=1 cellspacing=1 border=0>
			    <tr><td colspan=3><h2>Login Information</h2><hr></td></tr>
			    <tr><td width=200>Username</td><td>:</td><td><b>$username</b></td></tr>
			    <tr><td width=200>Email $nd</td><td>:</td><td><input type=text size=30 maxlength=30 name=up_email value=\"$email\"></td></tr>
			    <tr><td width=200>Password</td><td>:</td><td><input type=password size=30 maxlength=30 name=up_password></td></tr>
			    <tr><td width=200>Re-Type Password</td><td>:</td><td><input type=password size=30 maxlength=30 name=up_password_conf></td></tr>
			    <tr><td colspan=3>&nbsp;</td></tr>
			    <tr><td colspan=3><h2>Personal Information</h2><hr></td></tr>
			    <tr><td width=200>Name $nd</td><td>:</td><td><input type=text size=40 maxlength=100 name=up_name value=\"$name\"></td></tr>
			    <tr><td width=200>Birthday $nd</td><td>:</td><td><input type=text name=up_birthday value=\"$birthday\" size=10> <img src=\"./inc/jscss/calendar/calendar.gif\" onclick=\"return showCalendar('up_birthday', 'y-mm-dd')\"> (Format: yyyy-mm-dd)</td></tr>
			    <tr><td width=200>Gender $nd</td><td>:</td><td><select name=up_gender>$option_gender</select></td></tr>
			    <tr><td width=200>Marital status</td><td>:</td><td><select name=up_marital>$option_marital</select></td></tr>
			    <tr><td width=200>Education</td><td>:</td><td><select name=up_education>$option_education</select></td></tr>
			    <tr><td width=200>Address $nd</td><td>:</td><td><input type=text size=40 maxlength=250 name=up_address value=\"$address\"></td></tr>
			    <tr><td width=200>City $nd</td><td>:</td><td><input type=text size=40 maxlength=100 name=up_city value=\"$city\"></td></tr>
			    <tr><td width=200>State/Province $nd</td><td>:</td><td><input type=text size=40 maxlength=100 name=up_state value=\"$state\"></td></tr>
			    <tr><td width=200>Country $nd</td><td>:</td><td><select name=up_country>$option_country</select></td></tr>
			    <tr><td width=200>Zipcode</td><td>:</td><td><input type=text size=10 maxlength=10 name=up_zipcode value=\"$zipcode\"></td></tr>
			    <tr><td colspan=3>&nbsp;</td></tr>
			    <tr><td colspan=3><h2>Application Information</h2><hr></td></tr>
			    <tr><td width=200>Mobile Number</td><td>:</td><td><input type=text size=16 maxlength=16 name=up_mobile value=\"$mobile\"> (International format)</td></tr>
			    <tr><td width=200>SMS Footer (SMS Sender ID)</td><td>:</td><td><input type=text size=35 maxlength=30 name=up_sender value=\"$sender\"> (Max. 30 Alphanumeric char.)</td></tr>
			    <tr><td colspan=3>&nbsp;</td></tr>
			    <tr><td colspan=3><hr></td></tr>
			    <tr><td width=200><input type=submit class=button value=Save></td></tr>
			    </table>
			    </form>
			";
		echo $content;
		break;
	case "user_pref_save" :
		$up_name = $_POST[up_name];
		$up_email = $_POST[up_email];
		$up_gender = $_POST[up_gender];
		$up_address = $_POST[up_address];
		$up_city = $_POST[up_city];
		$up_state = $_POST[up_state];
		$up_country = $_POST[up_country];
		$up_mobile = $_POST[up_mobile];
		$up_sender = $_POST[up_sender];
		$up_daily = intval(trim($_POST[up_daily]));
		$up_password = $_POST[up_password];
		$up_password_conf = $_POST[up_password_conf];
		$up_birthday = $_POST[up_birthday];
		$up_marital = $_POST[up_marital];
		$up_education = $_POST[up_education];
		$up_zipcode = $_POST[up_zipcode];
		$up_trn = $_POST[up_trn];
		$db_query = "SELECT photo1,photo2,photo3 FROM playsms_tblUser WHERE uid='$uid'";
		$db_result = dba_query($db_query);
		$error_string = "No changes made!";
		if ($up_name && $up_mobile && $up_email && $up_birthday && $up_gender && $up_address && $up_city && $up_state && $up_country) {
			$up_uname = $username;
			$db_query = "SELECT email FROM playsms_tblUser WHERE email=$up_email' AND NOT username='$up_uname'";
			$db_result = dba_num_rows($db_query);
			if ($db_result > 0) {
				$error_string = "Email `$email` already in use by other username";
			} else {
				$chg_pwd = "";
				if ($up_password && $up_password_conf && ($up_password == $up_password_conf)) {
					$chg_pwd = ",password='$up_password'";
				}
				$db_query = "
						    UPDATE playsms_tblUser 
						    SET 
							name='$up_name',email='$email',mobile='$up_mobile',sender='$up_sender'$chg_pwd,
							gender='$up_gender',address='$up_address',city='$up_city',state='$up_state',country='$up_country',
							birthday='$up_birthday',marital='$up_marital',education='$up_education',zipcode='$up_zipcode',junktimestamp='" . mktime() . "'
						    WHERE uid='$uid'";
				if (@ dba_affected_rows($db_query)) {
					if ($up_password && $up_password_conf && ($up_password == $up_password_conf)) {
						$error_string = "Preferences has been saved and password updated";
					} else {
						$error_string = "Preferences has been saved";
					}
				} else {
					$error_string = "Fail to save preferences for `$up_uname`";
				}
			}
		} else {
			$error_string = "Empty field is not allowed";
		}
		header("Location: menu.php?inc=user_pref&op=user_pref&err=" . urlencode($error_string));
		break;
}
?>
