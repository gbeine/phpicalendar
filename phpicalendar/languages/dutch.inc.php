<?php

// Dutch language include
// For version 0.9 PHP iCalendar
//
// Translation by Lieven Gekiere (Lieven@gekiere.com)
//
// Submit new translations to chad@chadsdomain.com



$day_lang			= 'Dag';
$week_lang			= 'Week';
$month_lang			= 'Maand';
$year_lang			= 'Jaar';
$calendar_lang		= 'Kalender';
$next_day_lang		= 'Volgende Dag';
$next_month_lang	= 'Volgende Maand';
$next_week_lang		= 'Volgende Week';
$next_year_lang		= 'Volgend Jaar';
$last_day_lang		= 'Vorige Dag';
$last_month_lang	= 'Vorige Maand';
$last_week_lang		= 'Vorige Week';
$last_year_lang		= 'Vorig Jaar';
$subscribe_lang		= 'Abonneer';
$download_lang		= 'Download';
$powered_by_lang 	= 'Gemaakt met';
$event_lang			= 'Activiteit';
$event_start_lang	= 'Start Tijd';
$event_end_lang		= 'Eind Tijd';
$this_months_lang	= 'Activiteiten Deze Maand';
$date_lang			= 'Datum';
$summary_lang		= 'Overzicht';
$all_day_lang		= 'Dag Activiteit';
$notes_lang			= 'Opmerkingen';
$this_years_lang	= 'Activiteiten Dit Jaar';
$today_lang			= 'Vandaag';
$this_week_lang		= 'Deze Week';
$this_month_lang	= 'Deze Maand';
$jump_lang			= 'Ga naar';
$tomorrows_lang		= 'Activiteiten voor morgen';
$goday_lang			= 'Ga Naar Vandaag';
$goweek_lang		= 'Ga Naar Deze Week';
$gomonth_lang		= 'Ga Naar Deze Maand';
$goyear_lang		= 'Ga Naar Dit Jaar';
$search_lang		= 'Zoeken'; // the verb
$results_lang		= 'Zoek Resultaten';
$query_lang			= 'Zoekopdracht: '; // will be followed by the search query
$no_results_lang	= 'Geen activiteiten gevonden';
$goprint_lang		= 'Makkelijk Printbaar';
$time_lang			= 'Tijd';
$summary_lang		= 'Overzicht';
$description_lang	= 'Beschrijving';
$this_site_is_lang		= 'Deze site is';
$no_events_day_lang		= 'Geen activiteiten vandaag.';
$no_events_week_lang	= 'Geen activiteiten deze week.';
$no_events_month_lang	= 'Geen activiteiten deze maand.';
$rss_day_date			= 'g:i A';  // Lists just the time
$rss_week_date			= '%b %e';  // Lists just the day
$rss_month_date			= '%b %e';  // Lists just the day
$rss_language			= 'en-us';

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
$set_prefs_lang			= 'Set preferences';
$completed_date_lang	= 'Completed on';
$completed_lang			= 'Completed';
$created_lang			= 'Created:';
$due_lang				= 'Due:';
$priority_lang			= 'Priority:';
$priority_high_lang		= 'High';
$priority_low_lang		= 'Low';
$priority_medium_lang	= 'Medium';
$priority_none_lang		= 'None';
$status_lang			= 'Status:';
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

$daysofweek_lang			= array ('Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag');
$daysofweekshort_lang		= array ('Zon','Maa','Din','Woe','Don','Vrij','Zat');
$daysofweekreallyshort_lang	= array ('Z','M','D','W','D','V','Z');
$monthsofyear_lang			= array ('Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December');
$monthsofyearshort_lang		= array ('Jan','Feb','Maa','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec');

// For time formatting, check out: http://www.php.net/manual/en/function.date.php
$timeFormat = 'G:i';
$timeFormat_small = 'G:i';

// For date formatting, see note below
$dateFormat_day = '%A %e %B';
$dateFormat_week = '%e %B';
$dateFormat_week_list = '%a %e %b';
$dateFormat_week_jump = '%e %b';// new since last translation
$dateFormat_month = '%B %Y';
$dateFormat_month_list = '%A %e %B';

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
$error_title_lang = 'Fout!';
$error_window_lang = 'Er is een fout opgetreden!';
$error_calendar_lang = 'De kalender "%s" was aan het processen toen de fout gebeurde.';
$error_path_lang = 'Onmogelijk om het path te openen: "%s"';
$error_back_lang = 'Gebruik de knop "Vorige" om terug te keren.';
$error_remotecal_lang = 'Deze server blokt alle niet-geaccepteerde kalenders.';
$error_restrictedcal_lang = 'Je probeerde een beveiligde kalender te bekijken.';
$error_invalidcal_lang = 'Ongeldige Kalender file. Probeer een andere kalender aub.';


?>