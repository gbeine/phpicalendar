<?php
if (isset($HTTP_GET_VARS['jumpto_day'])) {
	$jumpto_day_time = strtotime($HTTP_GET_VARS['jumpto_day']);
	if ($jumpto_day_time == -1) {
		$getdate = date('Ymd', strtotime("now + $second_offset seconds"));
	} else {
		$getdate = date('Ymd', $jumpto_day_time);
	}
}
define('BASE', './');
$current_view = 'day';
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/list_functions.php');
require_once(BASE.'functions/template.php');
if ($minical_view == 'current') $minical_view = 'day';

$weekstart 		= 1;
$unix_time 		= strtotime($getdate);
$today_today 	= date('Ymd', strtotime("now + $second_offset seconds")); 
$next_day		= date('Ymd', strtotime("+1 day",  $unix_time));
$prev_day 		= date('Ymd', strtotime("-1 day",  $unix_time));

$display_date = localizeDate($dateFormat_week_list, $unix_time);
$start_week_time = strtotime(dateOfWeek($getdate, $week_start_day));


// select for calendars
$list_icals 	= display_ical_list(availableCalendars($username, $password, $ALL_CALENDARS_COMBINED));
$list_years 	= list_years();
$list_months 	= list_months();
$list_weeks 	= list_weeks();
$list_jumps 	= list_jumps();
$list_calcolors = list_calcolors();

$page = new Page(BASE.'templates/'.$template.'/day.tpl');

$page->replace_tags(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
	'sidebar'			=> BASE.'templates/'.$template.'/sidebar.tpl',
	'event_js'			=> BASE.'functions/event.js',
	'template'			=> $template,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'calendar_name'		=> $calendar_name,
	'display_date'		=> $display_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_available' 	=> '',
	'rss_valid' 		=> '',
	'todo_js' 			=> '',
	'show_search' 		=> '',
	'next_day' 			=> $next_day,
	'prev_day'	 		=> $prev_day,
	'show_goto' 		=> '',
	'is_logged_in' 		=> '',
	'list_icals' 		=> $list_icals,
	'list_years' 		=> $list_years,
	'list_months' 		=> $list_months,
	'list_weeks' 		=> $list_weeks,
	'list_jumps' 		=> $list_jumps,
	'legend'	 		=> $list_calcolors,
	'style_select' 		=> $style_select			
	));
	
$page->draw_day($this->page);

$page->output();

?>