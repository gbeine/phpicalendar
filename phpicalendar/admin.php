<?php
session_start();

define('BASE', './');
include (BASE.'functions/init.inc.php');
include (BASE.'functions/admin_functions.php');

// Redirect if administration is not allowed
if ($allow_admin != "yes") {
	header("Location: index.php");
	die();
}

// Load variables from forms and query strings into local scope
if($HTTP_POST_VARS) 	{extract($HTTP_POST_VARS, EXTR_PREFIX_SAME, "post_");}
if($HTTP_GET_VARS)  	{extract($HTTP_GET_VARS, EXTR_PREFIX_SAME, "get_");}

// Logout by clearing session variables
if ($action == "logout") {
	$HTTP_SESSION_VARS['phpical_loggedin'] = FALSE;
	unset($HTTP_SESSION_VARS['phpical_username']);
	unset($HTTP_SESSION_VARS['phpical_password']);
}


// if $auth_method == 'none', don't do any authentication
if ($auth_method == "none") {
	$is_loged_in = TRUE;
}
// Check if The User is Identified
else {
	$is_loged_in = FALSE;
	
	if (is_loggedin()) {
		$is_loged_in = TRUE;
	}
	if (isset($username) && $action != "logout") {
		$is_loged_in = login ($username, $password);
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title><?php echo "$admin_header_lang"; ?></title>
  	<link rel="stylesheet" type="text/css" href="<?php echo BASE."styles/$style_sheet/default.css"; ?>">
</head>
<body bgcolor="#FFFFFF">
<center>

<?php include (BASE.'includes/header.inc.php'); ?>

<table width="640" border="0" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td align="center" valign="middle">

			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" width="20" class="navback">&nbsp;</td>
					<td align="center" class="navback" nowrap valign="middle"><font class="H20"><?php echo "$admin_header_lang"; ?></font></td>
					<td align="right" width="20" class="navback" nowrap valign="middle"><font class="G10"><?php if ($auth_method != "none" && $is_loged_in == TRUE) { echo "<a href=\"{$HTTP_SERVER_VARS['PHP_SELF']}?action=logout\">{$logout_lang}</a>"; } ?></font>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="dayborder"><img src="images/spacer.gif" width="1" height="5" alt=" "></td>
				</tr>
				<tr>
					<td align="left" width="20">&nbsp;</td>
					<td colspan="2">

<?php 



// If User is Not Logged In, Display The Login Page
if ($is_loged_in == FALSE) {
	if (isset($username))
		$login_error =  "<font color=\"red\">$invalid_login_lang</font>";
		
    echo <<<EOT
	<form action="{$HTTP_SERVER_VARS['PHP_SELF']}" method="post">
		<table cellspacing="0" cellpadding="0">
			<tr>
				<td nowrap>{$username_lang}: </td>
				<td align="left"><input type="text" name="username"></td>
			</tr>
			<tr>
				<td>{$password_lang}: </td>
				<td align="left"><input type="password" name="password"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td align="left"><input type="submit" value="{$login_lang}"></td>
			</tr>
			<tr>
				<td align="center" colspan="3">{$login_error}&nbsp;</td>
			</tr>
	    </table>
	</form>
EOT;


	echo "
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>";

	include (BASE.'includes/footer.inc.php');
	
	echo "
	</center>
	</body>
	</html>";
	
	die();
}


// The user is logged in if we get here



// Add or Update a calendar
if ($action == "addupdate") {
	$addupdate_msg = "";

	for($filenumber=1; $filenumber<6; $filenumber++) {
		$file = $HTTP_POST_FILES['calfile'];
		$addupdate_success = FALSE;

		if (!is_uploaded_file_v4($file['tmp_name'][$filenumber])) {
			$upload_error = get_upload_error($file['error'][$filenumber]);
		}
		elseif (!is_uploaded_ics($file['name'][$filenumber])) {
			$upload_error = $upload_error_type_lang;
		}
		elseif (!copy_cal($file['tmp_name'][$filenumber], $file['name'][$filenumber])) {
			$upload_error = $copy_error_lang . " " . $file['tmp_name'][$filenumber] . " - " . $calendar_path . "/" . $file['name'][$filenumber];
		}
		else {
			$addupdate_success = TRUE;
		}
		
		if ($addupdate_success)
			$addupdate_msg = $addupdate_msg . "<font color=\"green\">{$cal_file_lang} {$filenumber}: {$action_success_lang}</font><br>";
		else
			$addupdate_msg = $addupdate_msg . "<font color=\"red\">{$cal_file_lang} {$filenumber}: {$upload_error}</font><br>";
	}
}

// Delete a calendar
//  Not at all secure - need to strip out path info if used by users besides admin in the future
if ($action == "delete") {
	$delete_msg = "";

	foreach ($delete_calendar as $filename) {
		if (!delete_cal(urldecode($filename))) {
			$delete_msg = $delete_msg . "<font color=\"red\">" . $delete_error_lang . " " . urldecode(substr($filename,0,-4)) . "</font><br>";
		}
		else {
			$delete_msg = $delete_msg . "<font color=\"green\">" . urldecode(substr($filename,0,-4)) . " " . $delete_success_lang . "</font><br>";
		}
	}
}

?>


<h2><?php echo $addupdate_cal_lang; ?></h2>
<p><?php echo $addupdate_desc_lang; ?></p>
<form action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="addupdate">
	<table border="0" cellspacing="0">
		<tr>
			<td nowrap><?php echo $cal_file_lang; ?> 1: </td>
			<td><input type="file" name="calfile[1]"></td>
		</tr>
		<tr>
			<td nowrap><?php echo $cal_file_lang; ?> 2: </td>
			<td><input type="file" name="calfile[2]"></td>
		</tr>
		<tr>
			<td nowrap><?php echo $cal_file_lang; ?> 3: </td>
			<td><input type="file" name="calfile[3]"></td>
		</tr>
		<tr>
			<td nowrap><?php echo $cal_file_lang; ?> 4: </td>
			<td><input type="file" name="calfile[4]"></td>
		</tr>
		<tr>
			<td nowrap><?php echo $cal_file_lang; ?> 5: </td>
			<td><input type="file" name="calfile[5]"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="<?php echo $submit_lang; ?>"></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><?php echo $addupdate_msg; ?>&nbsp;</td>
		</tr>
    </table>
</form>

<h2><?php echo $delete_cal_lang; ?></h2>
<form action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="action" value="delete">
	<table border="0" cellspacing="0">
		<?php
				
			// Print Calendar Checkboxes
			//
			$COLUMNS_TO_PRINT = 3;
			$column = 1;
			$filelist = get_calendar_files($calendar_path);
			foreach ($filelist as $file) {
				if ($column > $COLUMNS_TO_PRINT) {
					echo "</tr>";
					$column = 1;
				}
				if ($column == 1) {
					echo "<tr>";
				}
				
				$cal_filename_tmp = substr($file,0,-4);
				$cal_tmp = urlencode($file);
				$cal_displayname_tmp = str_replace("32", " ", $cal_filename_tmp);
				
				echo "<td align=\"left\"><input name=\"delete_calendar[]\" value=\"$cal_tmp\" type=\"checkbox\">$cal_displayname_tmp</td>\n";
				
				$column++;
			}
			// Print remaining empty columns if necessary
			$number_of_columns = count($filelist);
			while (gettype($number_of_columns/$COLUMNS_TO_PRINT) != "integer") {
				echo "<td>&nbsp;</td>";
				$number_of_columns++;
			}
		?>
		</tr>
	</table>
	<p><input type="submit" value="<?php echo $delete_lang; ?>"></p>
	<p><?php echo $delete_msg; ?>&nbsp;</p>
</form>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>


<?php include (BASE.'includes/footer.inc.php'); ?>


</center>
</body>
</html>


