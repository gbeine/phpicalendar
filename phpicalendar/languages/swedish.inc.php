<?php

// Swedish language include
// For version 0.9 PHP iCalendar
//
// Translation by Jonas Hjelm (jonas@hnet.se)
//
// Submit new translations to chad@chadsdomain.com



$day_lang			= 'Dag';
$week_lang			= 'Vecka';
$month_lang			= 'M&aring;nad';
$year_lang			= '&Aring;r';
$calendar_lang		= 'kalender';
$next_day_lang		= 'Imorgon';
$next_month_lang	= 'N&auml;sta m&aring;nad';
$next_week_lang		= 'N&auml;sta vecka';
$next_year_lang		= 'N&auml;sta &aring;r';
$last_day_lang		= 'F&ouml;reg&aring;ende dag';
$last_month_lang	= 'F&ouml;reg&aring;ende m&aring;nad';
$last_week_lang		= 'F&ouml;reg&aring;ende vecka';
$last_year_lang		= 'F&ouml;reg&aring;ende &aring;r';
$subscribe_lang		= 'Prenumerera';
$download_lang		= 'H&auml;mta fil';
$powered_by_lang 	= 'Powered by';
$event_lang			= 'H&auml;ndelse';
$event_start_lang	= 'Start tid';
$event_end_lang		= 'Slut tid';
$this_months_lang	= 'Denna m&aring;nads h&auml;ndelser';
$date_lang			= 'Datum';
$summary_lang		= 'Summering';
$all_day_lang		= 'Heldags h&auml;ndelse';
$notes_lang			= 'Notering';
$this_years_lang	= '&Aring;rest h&auml;ndelser';
$today_lang			= 'Idag';
$this_week_lang		= 'Denna vecka';
$this_month_lang	= 'Denna m&aring;nad';
$jump_lang			= 'G&aring; till';
$tomorrows_lang		= 'Morgondagens h&auml;ndelser';
$goday_lang			= 'G&aring; till dagens datum';
$goweek_lang		= 'G&aring; till denna vecka';
$gomonth_lang		= 'G&aring; till denna m&aring;nad';
$goyear_lang		= 'G&aring; till detta &aring;r';
$search_lang		= 'S&ouml;k  '; // the verb
$results_lang		= 'Resultat av s&ouml;kning  ';
$query_lang			= 'S&ouml;kord  : '; // will be followed by the search query
$no_results_lang	= 'Inga kalenderh&auml;ndelser funna  ';
$goprint_lang		= 'Utskriftsv&auml;nlig  ';
$time_lang			= 'Tid';
$summary_lang		= 'Sammanfattning  ';
$description_lang	= 'F&ouml;rklaring  ';
$this_site_is_lang		= 'Denna hemsida &auml;r  ';
$no_events_day_lang		= 'Inga kalenderh&auml;ndelser idag.  ';
$no_events_week_lang	= 'Inga kalenderh&auml;ndelser denna vecka.  ';
$no_events_month_lang	= 'Inga kalenderh&auml;ndelser denna m&aring;nad.  ';
$rss_day_date			= 'g:i A';  // Lists just the time (har ej kollat dessa)
$rss_week_date			= '%b %e';  // Lists just the day (har ej kollat dessa)
$rss_month_date			= '%b %e';  // Lists just the day (har ej kollat dessa)

// new in 0.9 ----------------------

$search_took_lang		= 'Search took %s seconds';
$recurring_event_lang	= 'Recurring event';
$exception_lang			= 'Exception';
$no_query_lang			= 'No query given';
$preferences_lang		= 'Preferences';
$printer_lang			= 'Printer';
$select_lang_lang		= 'Select your default language:';
$select_cal_lang		= 'Select your default calendar:';
$select_view_lang		= 'Select your default view:';
$select_time_lang		= 'Select your default start time:';
$select_day_lang		= 'Select your default start day of week:';
$select_style_lang		= 'Select your default style:';
$completed_lang			= 'Completed on';
$created_lang			= 'Created:';
$due_lang				= 'Due:';
$no_priority_lang		= 'None';
$priority_lang			= 'Priority:';
$status_lang			= 'Status: ';
$todo_lang				= 'To do items';
$unfinished_lang		= 'Unfinished';

// $format_recur, items enclosed in % will be substituted with variables
$format_recur_lang['delimiter']	= ', ';								// ie, 'one, two, three'

$format_recur_lang['yearly']		= array('year','years');		// for these, put singular
$format_recur_lang['monthly']		= array('month','months');		// and plural forms
$format_recur_lang['weekly']		= array('week','weeks');		// these will be %freq%
$format_recur_lang['daily']			= array('day','days');			// in the replacement below
$format_recur_lang['hourly']		= array('hour','hours');
$format_recur_lang['minutely']		= array('minute','minutes');
$format_recur_lang['secondly']		= array('second','seconds');

$format_recur_lang['start']			= 'Every %int% %freq% %for%';	// ie, 'Every 1 day until January 4' or 'Every 1 day for a count of 5'
$format_recur_lang['until']			= 'until %date%';				// ie, 'until January 4'
$format_recur_lang['count']			= 'for a count of %int%';		// ie, 'for 5 times'

$format_recur_lang['bymonth']		= 'In months: %list%';			// ie, 'In months: January, February, March'
$format_recur_lang['bymonthday']	= 'On dates: %list%';			// ie, 'On dates: 1, 2, 3, 4'
$format_recur_lang['byday']			= 'On days: %list%';			// ie, 'On days: Mon, Tues, Wed, Thurs'

// ---------------------------------

$daysofweek_lang			= array ('S&ouml;ndag','M&aring;ndag','Tisdag','Onsdag','Torsdag','Fredag','L&ouml;rdag');
$daysofweekshort_lang		= array ('S&ouml;n','M&aring;n','Tis','Ons','Tor','Fre','L&ouml;r');
$daysofweekreallyshort_lang	= array ('S','M','T','O','T','F','L');
$monthsofyear_lang			= array ('Januari','Februari','Mars','April','Maj','Juni','Juli','Augusti','September','Oktober','November','December');
$monthsofyearshort_lang		= array ('Jan','Feb','Mar','Apr','Maj','Jun','Jul','Aug','Sep','Okt','Nov','Dec');

// For time formatting, check out: http://www.php.net/manual/en/function.date.php
$timeFormat = "H:i";
$timeFormat_small = 'H:i';

// For date formatting, see note below
$dateFormat_day = '%Aen den %e %B';
$dateFormat_week = "%e %B";
$dateFormat_week_list = '%a, %e %b';
$dateFormat_week_jump = "%e %b";
$dateFormat_month = '%B %Y';
$dateFormat_month_list = '%A, %B %e';

/*
Notes about dateFormat_*
	The pieces are similar to that of the PHP function strftime(), 
	however only the following is supported at this time:
	
	%A - the full week day name as specified in $daysofweek_lang
	%a - the shortened week day name as specified in $daysofweekshort_lang
	%B - the full month name as specified in $monthsofyear_lang
	%b - the shortened month name as specified in $monthsofyearshort_lang
	%e - the day of the month as a decimal number (1 to 31)
	%Y - the 4-digit year

	If this causes problems with representing your language accurately, let
	us know. We will be happy to modify this if needed.
*/

// Error messages - %s will be replaced with a variable
$error_title_lang = 'Fel!';
$error_window_lang = 'Det har blivit ett fel!';
$error_calendar_lang = 'Den var "%s" kalendern som jobbades med n&auml;r felet h&auml;nde.';
$error_path_lang = 'Kan inte &ouml;ppna s&ouml;kv&auml;g: "%s"';
$error_back_lang = 'Anv&auml;nd "Back" knappen p&aring; din webbl&auml;sare f&ouml;r att backa.';
$error_remotecal_lang = 'Denna server blockerar kalendrar p� andra servrar som &auml;nnu inte blivit accepterade an administrat&ouml;ren.';
$error_restrictedcal_lang = 'Du har f&ouml;rs&ouml;kt att komma &aring;t en kalender som du ej har l&auml;sr&auml;ttigher p&aring;.';
$error_invalidcal_lang = 'Fel p&aring; kalenderfilen. Prova g&auml;rna med en annan kalender.';

?>