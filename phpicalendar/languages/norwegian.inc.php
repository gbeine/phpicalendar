<?php

// Norwegian language include
// For version 0.8 PHP iCalendar
//
// Translation by Geir Kielland (geir.kielland@jus.uio.no)
//
// Submit new translations to chad@chadsdomain.com



$day_lang			= 'Dag';
$week_lang			= 'Uke';
$month_lang			= 'M&aring;ned';
$year_lang			= '&aring;r';
$calendar_lang		= 'kalender';
$next_day_lang		= 'Neste dag';
$next_month_lang	= 'Neste m&aring;ned';
$next_week_lang		= 'Neste uke';
$next_year_lang		= 'Neste &aring;r';
$last_day_lang		= 'Forrige dag';
$last_month_lang	= 'Forrige m&aring;ned';
$last_week_lang		= 'Forrige uke';
$last_year_lang		= 'Forrige &aring;r';
$subscribe_lang		= 'Abonn&eacute;r';
$download_lang		= 'Last ned';
$powered_by_lang 	= 'Kj&oslash;res med';
$version_lang		= '0.8';
$event_lang			= 'Hendelse';
$event_start_lang	= 'Start tid';
$event_end_lang		= 'Slutt tid';
$this_months_lang	= 'Denne M&aring;nedens Hendelser';
$date_lang			= 'Dato';
$summary_lang		= 'Sammendrag';
$all_day_lang		= 'Hele dagen';
$notes_lang			= 'Notater';
$this_years_lang	= 'Hendelser dette &aring;ret';
$today_lang			= 'I dag';
$this_week_lang		= 'Denne uken';
$this_month_lang	= 'Denne m&aring;neden';
$jump_lang			= 'Hopp til';
$tomorrows_lang		= 'I morgen';
$goday_lang			= 'G&aring; til i dag';
$goweek_lang		= 'G&aring; til denne uken';
$gomonth_lang		= 'G&aring; til denne m&aring;neden';
$goyear_lang		= 'G&aring; til dette &aring;ret';

// new in 0.8 -------------
$search_lang		= 'S&oslash;k'; // the verb
$results_lang		= 'S&oslash;keresultater';
$query_lang			= 'Sp&oslash;rsm&aring;l '; // will be followed by the search query
$no_results_lang	= 'Ingen hendelser funnet';

$goprint_lang		= 'Utskriftsvennlig';
$time_lang			= 'Tid';
$summary_lang		= 'Oppsummering';
$description_lang	= 'Beskrivelse';

// RSS text for 0.8
$this_site_is_lang		= 'Dette nettstedet er';
$no_events_day_lang		= 'Ingen hendleser i dag.';
$no_events_week_lang	= 'Ingen hendelser denne uken.';
$no_events_month_lang	= 'Ingen hendelser denne m&aring;neden.';
$rss_day_date			= 'g:i A';  // Lists just the time
$rss_week_date			= '%b %e';  // Lists just the day
$rss_month_date			= '%b %e';  // Lists just the day
$rss_language			= 'en-us';
// -------------------------

$daysofweek_lang			= array('S&oslash;ndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','L&oslash;rdag');
$daysofweekshort_lang		= array ('S&oslash;n','Man','Tir','Ons','Tor','Fre','L&oslash;r');
$daysofweekreallyshort_lang	= array ('S','M','T','O','T','F','L');
$monthsofyear_lang			= array ('Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember');
$monthsofyearshort_lang		= array ('Jan','Feb','Mar','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Des');


// For time formatting, check out: http://www.php.net/manual/en/function.date.php
$timeFormat = 'H:i';

// For date formatting, see note below
$dateFormat_day = '%A, %e. %B ';
$dateFormat_week = '%e. %B';
$dateFormat_week_list = '%a, %e. %b';
$dateFormat_week_jump = '%b %e';
$dateFormat_month = '%B %Y';
$dateFormat_month_list = '%A, %e. %B';

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
$error_title_lang = 'Feil!';
$error_window_lang = 'Det skjedde en feil!';
$error_calendar_lang = 'Kalenderen "%s" var i bruk n&aring;r denne feilen oppstod.';
$error_path_lang = 'Kunne ikke &aring;pne adressen: "%s"';
$error_back_lang = 'Vennligst bruk "Tilbake" knappen for &aring; returnere.';
$error_remotecal_lang = 'Denne tjenermaskinen blokkerer ikke-lokale kalendere uten godkjenning.';
$error_restrictedcal_lang = 'Du har pr&oslash;vd &aring; &aring;pne en kalender som er sperret p&aring; denne tjenermaskinen.';
$error_invalidcal_lang = 'Ugyldig kalenderfil. Vennligst pr&oslash;v en annen kalenderfil.';


?>
