<?php

define('BASE','../');
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/calendar_functions.php');

$default_path = 'http://'.$HTTP_SERVER_VARS['SERVER_NAME'].substr($HTTP_SERVER_VARS['PHP_SELF'],0,strpos($HTTP_SERVER_VARS['PHP_SELF'], '/rss/'));

$current_view = "rssindex";
$display_date = "$calendar_lang - RSS Info";

$filelist = availableCalendarNames($username, $password, $ALL_CALENDARS_COMBINED);
foreach ($filelist as $file) {
	// $cal_filename is the filename of the calendar without .ics
	// $cal is a urlencoded version of $cal_filename
	// $cal_displayname is $cal_filename with occurrences of "32" replaced with " "
	$cal_filename_tmp = substr($file,0,-4);
	$cal_tmp = urlencode($cal_filename_tmp);
	$cal_displayname_tmp = str_replace("32", " ", $cal_filename_tmp);
	$rss_list = '<font class="V12" color="blue"><b>'.$cal_displayname_tmp.' '. $calendar_lang.'</b></font><br>';
	$rss_list .= $default_path.'/rss/rss.php?cal='.$cal_tmp.'&rssview=day<br>';
	$rss_list .= $default_path.'/rss/rss.php?cal='.$cal_tmp.'&rssview=week<br>';
	$rss_list .= $default_path.'/rss/rss.php?cal='.$cal_tmp.'&rssview=month<br>';
	$footer_check = $default_path.'/rss/rss.php?cal='.$default_cal.'&rssview='.$default_view;
}


$page = new Page(BASE.'templates/'.$template.'/rss_index.tpl');

$page->replace_tags(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
	'sidebar'			=> BASE.'templates/'.$template.'/sidebar.tpl',
	'event_js'			=> BASE.'functions/event.js',
	'default_path'		=> $default_path,
	'template'			=> $template,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'calendar_name'		=> $calendar_name,
	'display_date'		=> $display_date,
	'current_view'		=> $current_view,
	'sidebar_date'		=> $sidebar_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_list'	 		=> $rss_list,
	'rss_available' 	=> '',
	'rssdisable'	 	=> '',
	'rss_valid' 		=> '',
	'todo_js' 			=> '',
	'show_search' 		=> ''			
	));

$page->output();
								
								
?>
								