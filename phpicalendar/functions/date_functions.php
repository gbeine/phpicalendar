<?php// date_functions.php// functions for returning or comparing dates// dateOfWeek() takes a date in Ymd and a day of week as iCal knows them // (ie: SU, MO, TU, etc)and returns the date of that day. // This function may be specific to WEEKLY recurring events.function dateOfWeek($Ymd, $day) {	global $week_start_day;	if (!$week_start_day) $week_start_day = "Sunday";	$timestamp = strtotime($Ymd);	$sunday = strtotime((date("w",$timestamp)==0 ? "$week_start_day" : "last $week_start_day"), $timestamp);	if ($day == "SU") $day_longer = "sun";	elseif ($day == "MO") $day_longer = "mon";	elseif ($day == "TU") $day_longer = "tue";	elseif ($day == "WE") $day_longer = "wed";	elseif ($day == "TH") $day_longer = "thu";	elseif ($day == "FR") $day_longer = "fri";	elseif ($day == "SA") $day_longer = "sat";	return date("Ymd",strtotime($day_longer,$sunday));}// function to compare to dates in Ymd and return the number of weeks // that differ between them. requires dateOfWeek()function weekCompare($now, $then) {	global $week_start_day;	$day = substr($week_start_day, 0, 2);	$sun_now = dateOfWeek($now, $day);	$sun_then = dateOfWeek($then, $day);	$seconds_now = strtotime($sun_now);	$seconds_then =  strtotime($sun_then);	$diff_seconds = $seconds_now - $seconds_then;	$diff_minutes = $diff_seconds/60;	$diff_hours = $diff_minutes/60;	$diff_days = round($diff_hours/24);	$diff_weeks = $diff_days/7;		return $diff_weeks;}// function to compare to dates in Ymd and return the number of days // that differ between them. requires dateOfWeek()function dayCompare($now, $then) {	$seconds_now = strtotime($now);	$seconds_then =  strtotime($then);	$diff_seconds = $seconds_now - $seconds_then;	$diff_minutes = $diff_seconds/60;	$diff_hours = $diff_minutes/60;	$diff_days = round($diff_hours/24);		return $diff_days;}?>