<?php 
// uncomment when developing, comment for shipping version
//error_reporting (E_ALL);

// Retain some compatibility backwards like.
//jared.20021003 I think we're always going to make sure these are extracted by hand
//so I'll comment this out for now, otherwise, uncomment this
//chad - navigation breaks after 4.2.0 without this.
if(phpversion() >= '4.2.0') 

	{
		extract($HTTP_GET_VARS);	
		extract($HTTP_POST_VARS);
	}
include('./config.inc.php');
include('./functions/error.php');

// subscribe link prefix, doesn't need to be user configureable

// cheap trick... until timezones are implemented, make the server think we're at
// central time.

putenv("TZ=US/Central");

// language support
$language = strtolower($language);
$lang_file = './languages/'.$language.'.inc.php';

if (file_exists($lang_file)) {
	include($lang_file);
} else {
	exit(error('The requested language "'.$language.'" is not a supported language. Please use the configuration file to choose a supported language.'));
}

/*
if (isset($HTTP_GET_VARS['getdate']) && ($HTTP_GET_VARS['getdate'] !== '')) {
	$getdate = $HTTP_GET_VARS['getdate'];
} else {
	$getdate = date('Ymd');
}
*/
ini_set('max_execution_time', '60');

$is_webcal = FALSE;
if (isset($HTTP_GET_VARS['cal']) && $HTTP_GET_VARS['cal'] != '') {
	$cal_decoded = urldecode($HTTP_GET_VARS['cal']);
	if (substr($cal_decoded, 0, 7) == 'http://' || substr($cal_decoded, 0, 9) == 'webcal://') {
		$is_webcal = TRUE;
		$cal_webcalPrefix = str_replace('http://','webcal://',$cal_decoded);
		$cal_httpPrefix = str_replace('webcal://','http://',$cal_decoded);
		$cal_filename = $cal_httpPrefix;
	} else {
		$cal_filename = stripslashes($cal_decoded);
	}
} else {
	$cal_filename = $default_cal;
}


if ($is_webcal) {
	if ($allow_webcals == 'yes' || in_array($cal_webcalPrefix, $list_webcals) || in_array($cal_httpPrefix, $list_webcals)) {
		$cal_displayname = substr(str_replace('32', ' ', basename($cal_filename)), 0, -4);
		$cal = urlencode($cal_filename);
		$filename = $cal_filename;
		$subscribe_path = $cal_webcalPrefix;
	} else {
		exit(error($error_remotecal_lang, $HTTP_GET_VARS['cal']));
	}
} else {
	$cal_displayname = str_replace('32', ' ', $cal_filename);
	$cal = urlencode($cal_filename);
	if (in_array($cal_filename, $blacklisted_cals)) {
		exit(error($error_restrictedcal_lang, $cal_filename));
	} else {
		if (!isset($filename)) {
			$filename = $calendar_path.'/'.$cal_filename.'.ics';
			if (!file_exists($filename)) {
				$dir_handle = @opendir($calendar_path) or die(error(sprintf($error_path_lang, $calendar_path), $cal_filename));
				while ($file = readdir($dir_handle)) {
					if (substr($file, -4) == '.ics') {
						$cal = urlencode(substr($file, 0, -4));
						$filename = $calendar_path.'/'.$file;
						break;
					}
				}
			}
		}
		$subscribe_path = 'webcal://'.$HTTP_SERVER_VARS['SERVER_NAME'].dirname($HTTP_SERVER_VARS['PHP_SELF']).'/'.$filename;
	}
}
?>