<?php

// Swedish language include
// For version 0.9 PHP iCalendar
//
// Translation by Jonas Hjelm (jonas@hnet.se)
//
// Submit new translations to chad@chadsdomain.com



$day_lang			= 'Dag';
$week_lang			= 'Vecka';
$month_lang			= 'M�nad';
$year_lang			= '�r';
$calendar_lang		= 'kalender';
$next_day_lang		= 'Imorgon';
$next_month_lang	= 'N�sta m�nad';
$next_week_lang		= 'N�sta vecka';
$next_year_lang		= 'N�sta �r';
$last_day_lang		= 'F�reg�ende dag';
$last_month_lang	= 'F�reg�ende m�nad';
$last_week_lang		= 'F�reg�ende vecka';
$last_year_lang		= 'F�reg�ende �r';
$subscribe_lang		= 'Prenumerera';
$download_lang		= 'H�mta fil';
$powered_by_lang 	= 'Powered by';
$event_lang			= 'H�ndelse';
$event_start_lang	= 'Start tid';
$event_end_lang		= 'Slut tid';
$this_months_lang	= 'Denna m�nads h�ndelser';
$date_lang			= 'Datum';
$summary_lang		= 'Summering';
$all_day_lang		= 'Heldags h�ndelse';
$notes_lang			= 'Notering';
$this_years_lang	= '�rest h�ndelser';
$today_lang			= 'Idag';
$this_week_lang		= 'Denna vecka';
$this_month_lang	= 'Denna m�nad';
$jump_lang			= 'G� till';
$tomorrows_lang		= 'Morgondagens h�ndelser';
$goday_lang			= 'G� till dagens datum';
$goweek_lang		= 'G� till denna vecka';
$gomonth_lang		= 'G� till denna m�nad';
$goyear_lang		= 'G� till detta �r';
$search_lang		= 'S�k  '; // the verb
$results_lang		= 'Resultat av s�kning  ';
$query_lang			= 'S�kord  : '; // will be followed by the search query
$no_results_lang	= 'Inga kalenderh�ndelser funna  ';
$goprint_lang		= 'Utskriftsv�nlig  ';
$time_lang			= 'Tid';
$summary_lang		= 'Sammanfattning  ';
$description_lang	= 'F�rklaring  ';
$this_site_is_lang		= 'Denna hemsida �r  ';
$no_events_day_lang		= 'Inga kalenderh�ndelser idag.  ';
$no_events_week_lang	= 'Inga kalenderh�ndelser denna vecka.  ';
$no_events_month_lang	= 'Inga kalenderh�ndelser denna m�nad.  ';
$rss_day_date			= 'g:i A';  // Lists just the time (har ej kollat dessa)
$rss_week_date			= '%b %e';  // Lists just the day (har ej kollat dessa)
$rss_month_date			= '%b %e';  // Lists just the day (har ej kollat dessa)

// new in 0.9 ---------------------- (har ej heller kollat n�gra av dessa ... live)

$search_took_lang		= 'S�kningen tog %s sekunder';
$recurring_event_lang	= '�terkommande h�ndelse';
$exception_lang			= 'Undantag';
$no_query_lang			= 'Ingen fr�ga st�lld';
$preferences_lang		= 'Inst�llningar';
$printer_lang			= 'Skrivare';
$select_lang_lang		= 'St�ll in f�rvalt spr�k:';
$select_cal_lang		= 'St�ll in f�rvald kalender:';
$select_view_lang		= 'St�ll in f�rvald kalender utseende:';
$select_time_lang		= 'St�ll in f�rvald starttid:';
$select_day_lang		= 'St�ll in dag veckan b�rjar med:';
$select_style_lang		= 'V�lj utseende:';
$completed_lang			= 'f�rdig den';
$created_lang			= 'Skapad:';
$due_lang				= 'till den:';
$no_priority_lang		= 'ingen';
$priority_lang			= 'prioritet:';
$status_lang			= 'status: ';
$todo_lang				= 'att g�ra';
$unfinished_lang		= 'ouppklarade';

// $format_recur, items enclosed in % will be substituted with variables
$format_recur_lang['delimiter']	= ', ';								// ie, 'one, two, three'

$format_recur_lang['yearly']		= array('�r','�r');		// for these, put singular
$format_recur_lang['monthly']		= array('m�nad','m�nader');		// and plural forms
$format_recur_lang['weekly']		= array('vecka','veckor');		// these will be %freq%
$format_recur_lang['daily']			= array('dag','dagar');			// in the replacement below
$format_recur_lang['hourly']		= array('timme','timmar');
$format_recur_lang['minutely']		= array('minut','minuter');
$format_recur_lang['secondly']		= array('sekund','sekunder');

$format_recur_lang['start']			= 'Varje %int% %freq% %for%';	// ie, 'Every 1 day until January 4' or 'Every 1 day for a count of 5'
$format_recur_lang['until']			= 'fram till %date%';				// ie, 'until January 4'
$format_recur_lang['count']			= 'for a count of %int%';		// ie, 'for 5 times'

$format_recur_lang['bymonth']		= 'I m�nad: %list%';			// ie, 'In months: January, February, March'
$format_recur_lang['bymonthday']	= 'P� datum: %list%';			// ie, 'On dates: 1, 2, 3, 4'
$format_recur_lang['byday']			= 'P� dag: %list%';			// ie, 'On days: Mon, Tues, Wed, Thurs'

// ---------------------------------

$daysofweek_lang			= array ('S�ndag','M�ndag','Tisdag','Onsdag','Torsdag','Fredag','L�rdag');
$daysofweekshort_lang		= array ('S�n','M�n','Tis','Ons','Tor','Fre','L�r');
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
$error_calendar_lang = 'Den var "%s" kalendern som jobbades med n�r felet h�nde.';
$error_path_lang = 'Kan inte �ppna s�kv�g: "%s"';
$error_back_lang = 'Anv�nd "Back" knappen p� din webbl�sare f�r att backa.';
$error_remotecal_lang = 'Denna server blockerar kalendrar p� andra servrar som �nnu inte blivit accepterade an administrat�ren.';
$error_restrictedcal_lang = 'Du har f�rs�kt att komma �t en kalender som du ej har l�sr�ttigher p�.';
$error_invalidcal_lang = 'Fel p� kalenderfilen. Prova g�rna med en annan kalender.';

?>