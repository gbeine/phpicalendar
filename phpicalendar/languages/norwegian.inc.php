<?php

// Norwegian language include
// For version 0.5 PHP iCalendar
//
// Translation by Geir Kielland (geir.kielland@jus.uio.no)
//
// Submit new translations to chad@chadsdomain.com



$day_lang			= "Dag";
$week_lang			= "Uke";
$month_lang			= "M&aring;ned";
$year_lang			= "&aring;r";
$calendar_lang		= "Kalender";
$next_day_lang		= "Neste Dag";
$next_month_lang	= "Neste M&aring;ned";
$next_week_lang		= "Neste Uke";
$next_year_lang		= "Neste &Aring;r";
$last_day_lang		= "Forrige Dag";
$last_month_lang	= "Forrige M&aring;ned";
$last_week_lang		= "Forrige Uke";
$last_year_lang		= "Forrige &Aring;r";
$subscribe_lang		= "Abonn&eacute;r";
$download_lang		= "Last Ned";
$powered_by_lang 	= "Powered by";
$version_lang		= "0.5";
$event_lang			= "Hendelse";
$event_start_lang	= "Start Tid";
$event_end_lang		= "Slutt Tid";
$this_months_lang	= "Denne M&aring;nedens Hendelser";
$date_lang			= "Dato";
$summary_lang		= "Sammendrag";
$all_day_lang		= "Hele dagen";
$notes_lang			= "Notater";

// Set Location for date formatting, check out: http://www.php.net/manual/en/function.setlocale.php
setlocale (LC_TIME, 'no_NO');

// For time formatting, check out: http://www.php.net/manual/en/function.date.php
$timeFormat = "H:i";

// For date formatting, check out: http://www.php.net/manual/en/function.strftime.php
$dateFormat_day = "%A, %e. %B ";
$dateFormat_week = "%e. %B";
$dateFormat_week_list = "%a, %e. %b";
$dateFormat_month = "%B %Y";
$dateFormat_month_list = "%A, %e. %B";

?>