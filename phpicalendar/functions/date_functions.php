<?php// date_functions.php// functions for returning or comparing dates// takes Apple's 2 character day format and makes it into 3 charactersfunction two2threeCharDays($day) {	if ($day == "SU") $day_longer = "sun";	elseif ($day == "MO") $day_longer = "mon";	elseif ($day == "TU") $day_longer = "tue";	elseif ($day == "WE") $day_longer = "wed";	elseif ($day == "TH") $day_longer = "thu";	elseif ($day == "FR") $day_longer = "fri";	elseif ($day == "SA") $day_longer = "sat";	return $day_longer;}// dateOfWeek() takes a date in Ymd and a day of week in 3 letters or more// and returns the date of that day. (ie: "sun" or "sunday" would be acceptable values of $day but not "su")function dateOfWeek($Ymd, $day) {	global $week_start_day;	if (!$week_start_day) $week_start_day = "Sunday";	$timestamp = strtotime($Ymd);	$num = date("w", strtotime($week_start_day));	$start_day_time = strtotime((date("w",$timestamp)==$num ? "$week_start_day" : "last $week_start_day"), $timestamp);	return date("Ymd",strtotime($day,$start_day_time));}// function to compare to dates in Ymd and return the number of weeks // that differ between them. requires dateOfWeek()function weekCompare($now, $then) {	global $week_start_day;	$sun_now = dateOfWeek($now, $week_start_day);	$sun_then = dateOfWeek($then, $week_start_day);	$seconds_now = strtotime($sun_now);	$seconds_then =  strtotime($sun_then);	$diff_seconds = $seconds_now - $seconds_then;	$diff_minutes = $diff_seconds/60;	$diff_hours = $diff_minutes/60;	$diff_days = round($diff_hours/24);	$diff_weeks = $diff_days/7;		return $diff_weeks;}// function to compare to dates in Ymd and return the number of days // that differ between them.function dayCompare($now, $then) {	$seconds_now = strtotime($now);	$seconds_then =  strtotime($then);	$diff_seconds = $seconds_now - $seconds_then;	$diff_minutes = $diff_seconds/60;	$diff_hours = $diff_minutes/60;	$diff_days = round($diff_hours/24);		return $diff_days;}// function to compare to dates in Ymd and return the number of months // that differ between them.function monthCompare($now, $then) {	ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $now, $date_now);	ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $then, $date_then);	$diff_years = $date_now[1] - $date_then[1];	$diff_months = $date_now[2] - $date_then[2];	if ($date_now[2] < $date_then[2]) {		$diff_years -= 1;		$diff_months = ($diff_months + 12) % 12;	}	$diff_months = ($diff_years * 12) + $diff_months;	return $diff_months;}?>