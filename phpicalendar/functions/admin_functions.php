<?php
// Is the user logged in
//
// returns boolean is the user logged in
function is_loggedin () {
	global $HTTP_SESSION_VARS;
	if (!isset($HTTP_SESSION_VARS['phpical_loggedin']) || $HTTP_SESSION_VARS['phpical_loggedin'] == FALSE) {
		return FALSE;
	}
	else
		return TRUE;
}

// Attempt to login. If login is valid, set the session variable 'phpical_loggedin' to TRUE and store the username and password in the session
//
// arg0: string username
// arg1: string password
// returns boolean was the login successful
function login ($username, $password) {
	global $HTTP_SESSION_VARS;
	global $auth_method;
	
	switch ($auth_method) {
		case 'ftp':
			$loggedin = login_ftp($username, $password);
			break;
		case 'internal':
			$loggedin = login_internal($username, $password);
			break;
		default:
			$loggedin = FALSE;
	}
	
	$HTTP_SESSION_VARS['phpical_loggedin'] = $loggedin;
	if ($loggedin) {
		$HTTP_SESSION_VARS['phpical_username'] = $username;
		$HTTP_SESSION_VARS['phpical_password'] = $password;
	}
	
	return $loggedin;
}


// Attempt to login to the ftp server
//
// arg0: string username
// arg1: string password
// returns boolean was login successful
function login_ftp ($username, $password) {
	global $ftp_server;
	
	// set up basic connection
	$conn_id = ftp_connect($ftp_server); 
	
	// login with username and password
	$login_result = ftp_login($conn_id, $username, $password); 
	
	// check connection
	if ((!$conn_id) || (!$login_result)) { 
		return FALSE;
	}
	
	// close the FTP stream 
	ftp_close($conn_id);
	
	return TRUE;
}

// Attempt to login using username and password defined in config.inc.php
//
// arg0: string username
// arg1: string password
// returns boolean was login successful
function login_internal ($username, $password) {
	global $auth_internal_username;
	global $auth_internal_password;
	
	if ($auth_internal_username == $username && $auth_internal_password == $password)
		return TRUE;
	else
		return FALSE;
}

// Delete a calendar. If using ftp for authentication, use ftp to delete. Otherwise, use file system functions.
//
// arg0: string calendar file - not the full path
// returns boolean was delete successful
function delete_cal ($filename) {
	global $HTTP_SESSION_VARS;
	global $auth_method;
	global $ftp_server;
	global $calendar_path;
	global $ftp_calendar_path;
	
	if ($auth_method == 'ftp') {
		$filename = get_ftp_calendar_path() . "/" . $filename;
		
		// set up basic connection
		$conn_id = ftp_connect($ftp_server); 
		
		// login with username and password
		$login_result = ftp_login($conn_id, $HTTP_SESSION_VARS['phpical_username'], $HTTP_SESSION_VARS['phpical_password']); 
		
		// check connection
		if ((!$conn_id) || (!$login_result))
			return FALSE;
		
		// delete the file
		$delete = ftp_delete($conn_id, $filename); 
		
		// check delete status
		if (!$delete)
			return FALSE;
		
		// close the FTP stream 
		ftp_close($conn_id);
		
		return TRUE;
	}
	else {
		$filename = $calendar_path . "/" . $filename;
	
		$delete = @unlink($filename); 
		clearstatcache();
		if (@file_exists($filename)) { 
			$filesys = eregi_replace("/","\\", $filename); 
			$delete = @system("del $filesys");
			clearstatcache();
			if (@file_exists($filename)) { 
				$delete = @chmod ($filename, 0775); 
				$delete = @unlink($filename); 
				$delete = @system("del $filesys");
			}
		}
		clearstatcache();
		if (@file_exists($filename)) {
			return FALSE;
		}
		else {
			return TRUE;
		}
		
		return TRUE;
	}
}

// Copy the uploaded calendar. If using ftp for authentication, use ftp to copy. Otherwise, use file system functions.
//
// arg0: string full path to calendar file
// arg1: string destination filename
// returns boolean was copy successful
function copy_cal ($source, $destination) {
	global $HTTP_SESSION_VARS;
	global $auth_method;
	global $ftp_server;
	global $calendar_path;
	
	if ($auth_method == 'ftp') {
		$destination = get_ftp_calendar_path() . "/" . basename($destination);
		$destination = str_replace ("\\", "/", realpath($destination));
		
		// set up basic connection
		$conn_id = ftp_connect($ftp_server); 
		
		// login with username and password
		$login_result = ftp_login($conn_id, $HTTP_SESSION_VARS['phpical_username'], $HTTP_SESSION_VARS['phpical_password']); 
		
		// check connection
		if ((!$conn_id) || (!$login_result))
			return FALSE;
		
		// upload the file
		$upload = ftp_put($conn_id, $destination, $source, FTP_ASCII); 
		
		// check upload status
		if (!$upload)
			return FALSE;
		
		// close the FTP stream 
		ftp_close($conn_id);
		
		return TRUE;
	}
	else {
		$destination = $calendar_path . "/" . basename($destination);
		
		if (check_php_version('4.0.3')) {
			return move_uploaded_file($source, $destination);
		}
		else {
			return copy($source, $destination);
		}
	}
}

// Find the full path to the caledar directory for use with ftp
//  if $ftp_calendar_path == '', sends back the full path to the $calendar_path - this may not work depending 
//  on ftp server config, but would be a best guess
//
// return string path to calendar directory for ftp operations
function get_ftp_calendar_path() {
	global $ftp_calendar_path;
	global $calendar_path;
	
	if ($ftp_calendar_path != '')
		return $ftp_calendar_path;
	else {
		return str_replace ("\\", "/", realpath($calendar_path));
	}
}

// Check to see if the current version of php is >= to the arguement
//
// arg0: string version of php to check against
// return boolean true if $version is >= current php version
function check_php_version($version) {
	// intval used for version like "4.0.4pl1"
	$testVer=intval(str_replace(".", "",$version));
	$curVer=intval(str_replace(".", "",phpversion()));
	if( $curVer < $testVer )
		return FALSE;
	return TRUE;
}

// Is the file uploaded truly a file via HTTP POST - used to thwart a user from trying to trick the script from working on other files
//
// arg0: string filename
// returns boolean is the uploaded a file
function is_uploaded_file_v4 ($filename) {
    if (!$tmp_file = get_cfg_var('upload_tmp_dir')) {
        $tmp_file = dirname(tempnam('', ''));
    }
    $tmp_file .= '/' . basename($filename);
    // For Windows compat
    $filename = str_replace ("\\", "/", $filename);
    $tmp_file = str_replace ("\\", "/", $tmp_file);
    // User might have trailing slash in php.ini... 
    return (ereg_replace('/+', '/', $tmp_file) == $filename);
}

// return the appropriate error message if the file upload had an error
//
// arg0: array error number from $HTTP_POST_FILES[file]['error']
// returns string error message
function get_upload_error ($upload_error) {
	global $php_error_lang;
	global $upload_error_lang;
	global $upload_error_gen_lang;
	
	if (isset($upload_error)) {
		// This is only available in PHP >= 4.2.0
		$error = $php_error_lang . " ";
		switch($upload_error) {
			case 0: //no error; possible file attack!
			case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
			case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
			case 3: //uploaded file was only partially uploaded
			case 4: //no file was uploaded
				$error = $error . $upload_error . ": " . $upload_error_lang[$upload_error];
				break;
			default: //a default error, just in case!  :)
				$error = $error . $upload_error . ": " . $upload_error_gen_lang;
				break;
		}
	}
	else {
		$error = $upload_error_gen_lang;
	}
	
	return $error;
}

// Check to see that the file has an .ics extension
//
// arg0: string filename
// returns booloean does the filename end in .ics
function is_uploaded_ics ($filename) {
	// Check the file extension for .ics. Can also check the the mime type, but it's not reliable so why bother...
	if(preg_match("/.ics$/i", $filename)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

// Get all calendar filenames (not including path)
//
// argo: string path to calendar files
// returns array filenames (not including path)
function get_calendar_files($calendar_path) {
	global $error_path_lang;
	
	$dir_handle = @opendir($calendar_path) or die(error(sprintf($error_path_lang, $calendar_path)));
	$filelist = array();
	while ($file = readdir($dir_handle)) {
		if (substr($file, -4) == ".ics") {
			array_push($filelist, $file);
		}
	}
	closedir($dir_handle);
	natcasesort($filelist);
	return $filelist;
}

?>