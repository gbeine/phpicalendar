<?php
/* end_vevent.php

What happens in this file:
1. Initialization: add information not present by default
2. 
*/

if (!isset($url)) $url = '';
if (!isset($type)) $type = '';

// Handle DURATION
if (!isset($end_unixtime)) {
	if(!isset($the_duration)) $the_duration = 0;
	$end_unixtime 	= $start_unixtime + $the_duration;
	$end_time 	= date ('Hi', $end_unixtime);
}
	
// CLASS support
if (isset($class)) {
	if ($class == 'PRIVATE') {
		$summary ='**PRIVATE**';
		$description ='**PRIVATE**';
	} elseif ($class == 'CONFIDENTIAL') {
		$summary ='**CONFIDENTIAL**';
		$description ='**CONFIDENTIAL**';
	}
}	 

// make sure we have some value for $uid
if (!isset($uid)) {
	$uid = $uid_counter;
	$uid_counter++;
	$uid_valid = false;
} else {
	$uid_valid = true;
}

if (!isset($summary)) 		$summary = '';
if (!isset($description)) 	$description = '';
if (!isset($status)) 		$status = '';
if (!isset($class)) 		$class = '';
if (!isset($location)) 		$location = '';


# adjust event start and end times
if (isset($start_time) && isset($end_time)) {
	// Mozilla style all-day events or just really long events
	if (($end_time - $start_time) > 2345) {
		$allday_start = $start_date;
		$allday_end = ($start_date + 1);
	}
}

# look for events that span more than one day
if (isset($start_unixtime,$end_unixtime) && date('Ymd',$start_unixtime) != date('Ymd',$end_unixtime)) {
	$spans_day = true;
	$bleed_check = (($start_unixtime - $end_unixtime) < (60*60*24)) ? '-1' : '0';
} else {
	$spans_day = false;
	$bleed_check = 0;
}

# get hour and minute adjusted to allowed grid times
if (isset($start_time) && $start_time != '') {
	preg_match ('/([0-9]{2})([0-9]{2})/', $start_time, $time);
	preg_match ('/([0-9]{2})([0-9]{2})/', $end_time, $time2);
	if (isset($start_unixtime) && isset($end_unixtime)) {
		$length = $end_unixtime - $start_unixtime;
	} else {
		$length = ($time2[1]*60+$time2[2]) - ($time[1]*60+$time[2]);
	}
	
	$drawKey = drawEventTimes($start_time, $end_time);
	preg_match ('/([0-9]{2})([0-9]{2})/', $drawKey['draw_start'], $time3);
	$hour = $time3[1];
	$minute = $time3[2];
}

// RECURRENCE-ID Support
if (isset($recurrence_d)) {
	
	$recurrence_delete["$recurrence_d"]["$recurrence_t"] = $uid;
}


# treat nonrepeating events as rrule events with one instance
if (!isset($rrule_array) && $start_unixtime < $mArray_end && $end_unixtime > $mArray_begin){
	$rrule_array['FREQ'] = 'YEARLY';
	$rrule_array['START_DATE'] = $start_date;
	$rrule_array['UNTIL'] = $start_date;
#	$rrule_array['END'] = 'end';
}


if (isset($allday_start) && $allday_start != '') {
	$hour = '-';
	$minute = '1';
	$rrule_array['START_DAY'] = $allday_start;
	# $rrule_array['END_DAY'] = $allday_end; # this doesn't seem to be used anywhere.
#	$rrule_array['END'] = 'end';
	$recur_start = $allday_start;
	$start_date = $allday_start;
	if (isset($allday_end)) {
		$diff_allday_days = dayCompare($allday_end, $allday_start);
	 } else {
		$diff_allday_days = 1;
	}
} else {
	$rrule_array['START_DATE'] = $start_date;
	$rrule_array['START_TIME'] = $start_time;
	$rrule_array['END_TIME'] = $end_time;
#	$rrule_array['END'] = 'end';
}
	
$start_date_time = strtotime($start_date);
if (!isset($fromdate)){
	#this should happen if not in one of the rss views
	$this_month_start_time = strtotime($this_year.$this_month.'01');
	if ($current_view == 'year' || ($save_parsed_cals == 'yes' && !$is_webcal)|| $current_view == 'print' && $printview == 'year') {
		$start_range_time = strtotime($this_year.'-01-01 -2 weeks');
		$end_range_time = strtotime($this_year.'-12-31 +2 weeks');
	} else {
		$start_range_time = strtotime('-1 month -2 day', $this_month_start_time);
		$end_range_time = strtotime('+2 month +2 day', $this_month_start_time);
	}
}else{
		$start_range_time = strtotime($fromdate);			
		$end_range_time = strtotime($todate)+60*60*24; 						
}


$freq_type = 'year';
$interval = 1;
# Load $rrule_array
foreach ($rrule_array as $key => $val) {
	switch($key) {
		case 'FREQ':
			switch ($val) {
				case 'YEARLY':		$freq_type = 'year';	break;
				case 'MONTHLY':		$freq_type = 'month';	break;
				case 'WEEKLY':		$freq_type = 'week';	break;
				case 'DAILY':		$freq_type = 'day';		break;
				case 'HOURLY':		$freq_type = 'hour';	break;
				case 'MINUTELY':	$freq_type = 'minute';	break;
				case 'SECONDLY':	$freq_type = 'second';	break;
			}
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = strtolower($val);
			break;
		case 'COUNT':
			$count = $val;
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $count;
			break;
		case 'UNTIL':
			$until = str_replace('T', '', $val);
			$until = str_replace('Z', '', $until);
			if (strlen($until) == 8) $until = $until.'235959';
			$abs_until = $until;
			ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})', $until, $regs);
			$until = mktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[3],$regs[1]);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = localizeDate($dateFormat_week,$until);
			break;
		case 'INTERVAL':
			if ($val > 0){
			$interval = $val;
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $interval;
			}
			break;
		case 'BYSECOND':
			$bysecond = $val;
			$bysecond = split (',', $bysecond);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bysecond;
			break;
		case 'BYMINUTE':
			$byminute = $val;
			$byminute = split (',', $byminute);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byminute;
			break;
		case 'BYHOUR':
			$byhour = $val;
			$byhour = split (',', $byhour);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byhour;
			break;
		case 'BYDAY':
			$byday = $val;
			$byday = split (',', $byday);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byday;
			break;
		case 'BYMONTHDAY':
			$bymonthday = $val;
			$bymonthday = split (',', $bymonthday);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bymonthday;
			break;					
		case 'BYYEARDAY':
			$byyearday = $val;
			$byyearday = split (',', $byyearday);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byyearday;
			break;
		case 'BYWEEKNO':
			$byweekno = $val;
			$byweekno = split (',', $byweekno);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byweekno;
			break;
		case 'BYMONTH':
			$bymonth = $val;
			$bymonth = split (',', $bymonth);
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bymonth;
			break;
		case 'BYSETPOS':
			$bysetpos = $val;
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bysetpos;
			break;
		case 'WKST':
			$wkst = $val;
			$recur_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $wkst;
			break;
	}
}
/*
Load $recur_array
$recur_array is an array of unix times for instances of an event.  This code handles repeats.
Note that dates with exceptions are counted as instances.
RDATE is currently not supported
*/
# $recur is the recurrence info that goes into the master array for this VEVENT
$recur = $recur_array[($start_date)][($hour.$minute)][$uid]['recur']; 
	
// if $until isn't set yet, we set it to the end of our range we're looking at
if (!isset($until)) $until = $end_range_time;
if (!isset($abs_until)) $abs_until = date('YmdHis', $end_range_time);
$end_date_time = $until;
		
// If the $end_range_time is less than the $start_date_time, or $start_range_time is greater
// than $end_date_time, we may as well forget the whole thing
// It doesn't do us any good to spend time adding data we aren't even looking at
// this will prevent the year view from taking way longer than it needs to
if ($end_range_time >= $start_date_time && $start_range_time_tmp <= $end_date_time) {

	// if the beginning of our range is less than the start of the item, we may as well set it equal to it
	if ($start_range_time < $start_date_time){
		$start_range_time = $start_date_time;
	}	
	if ($end_range_time > $end_date_time) $end_range_time = $end_date_time;

	// initialize the time we will increment
	$next_range_time = $start_range_time;
		
	// start at the $start_range and go until we hit the end of our range.
	if(!isset($wkst)) $wkst='SU';
	$wkst3char = two2threeCharDays($wkst);

	# set first instance if it's in range
	$recur_data = array();
	if ($start_unixtime < $mArray_end && $end_unixtime > $mArray_begin){
		$recur_data[] = $start_unixtime; 
	}
	/*
	The while loop below increments $next_range_time by $freq type. For the larger freq types, there is only 
	one $next_range_time per repeat, but the BYXXX rules may write more than one event in that repeat cycle
	$next_date_time handles those instances within a $freq_type
	*/
	#echo "<br><br>$summary<br>next range time:".date("Ymd his",$next_range_time)." <br>start range time ".date("Ymd his",$start_range_time)." <br>end range time ".date("Ymd his",$end_range_time);
	while (($next_range_time >= $start_range_time) && ($next_range_time <= $end_range_time)) {
		# pick the right compare function from date_functions.php
		# $diff is the number of occurrences between start_date and next_range_time
		$func = $freq_type.'Compare';
		$diff = $func(date('Ymd',$next_range_time), $start_date);
		$rcount = $diff;
		if(count($byday) > 1) $rcount = $diff * count($byday);
		if ($rcount < $count && $diff % $interval == 0) {
			$year = date('Y', $next_range_time); 
			$month = date('m', $next_range_time); 
			switch ($rrule_array['FREQ']) {
				case 'DAILY':
					$recur_data[] = $next_range_time;
					break;
				case 'WEEKLY':
					// Populate $byday with the default day if it's not set.
					if (!isset($byday)) $byday[] = strtoupper(substr(date('D', $start_date_time), 0, 2));					
					$the_sunday = dateOfWeek(date("Ymd",$next_range_time), $wkst3char);
					foreach($byday as $key=>$day) {
						$day = two2threeCharDays($day);	
						#need to find the first day of the appropriate week.						
						if ($key == 0){ 
							$next_date_time = strtotime("next $day",strtotime($the_sunday)) + (12 * 60 * 60);
						}else{
							$next_date_time = strtotime("next $day",$next_date_time) + (12 * 60 * 60);						
						}
						$recur_data[] = $next_date_time; #echo "<br>$key $day ".strtotime("Ymd his", $next_date_time);
					}			
					break;
				case 'MONTHLY':
					if (empty($bymonth)) $bymonth = array(1,2,3,4,5,6,7,8,9,10,11,12);
					if (!empty($bysetpos)){
						/* bysetpos code from dustinbutler
						start on day 1 or last day. 
						if day matches any BYDAY the count is incremented. 
						SETPOS = 4, need 4th match 
						SETPOS = -1, need 1st match 
						*/ 
						if ($bysetpos > 0) { 
							$next_day = '+1 day'; 
							$day = 1; 
						} else { 
							$next_day = '-1 day'; 
							$day = $totalDays[$month]; 
						} 
						$day = mktime(0, 0, 0, $month, $day, $year); 
						$countMatch = 0; 
						while ($countMatch != abs($bysetpos)) { 
							/* Does this day match a BYDAY value? */ 
							$thisDay = $day; 
							$textDay = strtoupper(substr(date('D', $thisDay), 0, 2)); 
							if (in_array($textDay, $byday)) { 
								$countMatch++; 
							} 
							$day = strtotime($next_day, $thisDay); 
						} 
						if(in_array(date("m", $next_date_time), $bymonth)) $recur_data[] = $next_date_time;
					}elseif (count($bymonthday) > 0 && empty($byday)) {
						foreach($bymonthday as $day) {
							if ($day < 0) $day = ((date('t', $next_range_time)) + ($day)) + 1;
							if (checkdate($month,$day,$year)) {
								$next_date_time = mktime(0,0,0,$month,$day,$year);
								if(in_array(date("m", $next_date_time), $bymonth)) $recur_data[] = $next_date_time;
							}
						}
					} else {
						foreach($byday as $day) {
							/* set $byday_arr
								    [0] => byday string, e.g. 4TH
									[1] => sign/modifier
									[2] => 4 number
									[3] => TH day abbr
							*/
							ereg ('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
							$on_day = two2threeCharDays($byday_arr[3]);
							$next_date_time = strtotime($byday_arr[1].$byday_arr[2].$on_day, $next_range_time); 
							if(empty($bymonthday) && in_array(date("m", $next_date_time), $bymonth)) $recur_data[] = $next_date_time;
								
							if (isset($bymonthday) && (!empty($bymonthday))) {
								// This supports MONTHLY where BYDAY and BYMONTH are both set
								foreach($bymonthday as $day) {
									if (checkdate($month,$day,$year)) {
										$next_date_time = mktime(0,0,0,$month,$day,$year);
										$daday = strtolower(strftime("%a", $next_date_time));
										if ($daday == $on_day && in_array($month, $bymonth)) {
											if(in_array(date("m", $next_date_time), $bymonth)) $recur_data[] = $next_date_time;
										}
									}
								}
							}
						} # end foreach $byday
					}
					break;
				case 'YEARLY':
					if ((!isset($bymonth)) || (sizeof($bymonth) == 0)) $bymonth = array(date('m', $start_date_time));
					foreach($bymonth as $month) { 
						if (isset($bysetpos)){
							/* bysetpos code from dustinbutler
							start on day 1 or last day. 
							if day matches any BYDAY the count is incremented. 
							SETPOS = 4, need 4th match 
							SETPOS = -1, need 1st match 
							*/ 
							if ($bysetpos > 0) { 
								$next_day = '+1 day'; 
								$day = 1; 
							} else { 
								$next_day = '-1 day'; 
								$day = date("t",$month); 
							} 
							$day = mktime(12, 0, 0, $month, $day, $year); 
							$countMatch = 0; 
							while ($countMatch != abs($bysetpos)) { 
								/* Does this day match a BYDAY value? */ 
								$thisDay = $day;
								$textDay = strtoupper(substr(date('D', $thisDay), 0, 2)); 
								if (in_array($textDay, $byday)) { 
									$countMatch++; 
								} 
								$day = strtotime($next_day, $thisDay); 
							} 
							$recur_data[] = $thisDay; 															
						}
						if ((isset($byday)) && (is_array($byday))) {
							$checkdate_time = mktime(0,0,0,$month,1,$year);
							foreach($byday as $day) {
								ereg ('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
								if ($byday_arr[2] != '') {
									$nth = $byday_arr[2]-1;
								} else {
									$nth = 0;
								}
								$on_day = two2threeCharDays($byday_arr[3]);
								$on_day_num = two2threeCharDays($byday_arr[3],false);
								if ($byday_arr[1] == '-') {
									$last_day_tmp = date('t',$checkdate_time);
									$checkdate_time = strtotime(date('Y-m-'.$last_day_tmp, $checkdate_time));
									$last_tmp = (date('w',$checkdate_time) == $on_day_num) ? '' : 'last ';
									$next_date_time = strtotime($last_tmp.$on_day.' -'.$nth.' week', $checkdate_time);
								} else {															
									$next_date_time = strtotime($on_day.' +'.$nth.' week', $checkdate_time);
								}
							}
						} else {
							$day 	= date('d', $start_date_time);
							$next_date_time = mktime(0,0,0,$month,$day,$year+1);
							//echo date('Ymd',$next_date_time).$summary.'<br>';
						}
						if (isset($next_date_time) && $next_date_time != '') $recur_data[] = $next_date_time;
					}
					if (isset($byyearday)) {
						foreach ($byyearday as $yearday) {
							ereg ('([-\+]{0,1})?([0-9]{1,3})', $yearday, $byyearday_arr);
							if ($byyearday_arr[1] == '-') {
								$ydtime = mktime(0,0,0,12,31,$this_year);
								$yearnum = $byyearday_arr[2] - 1;
								$next_date_time = strtotime('-'.$yearnum.' days', $ydtime);
							} else {
								$ydtime = mktime(0,0,0,1,1,$this_year);
								$yearnum = $byyearday_arr[2] - 1;
								$next_date_time = strtotime('+'.$yearnum.' days', $ydtime);
							}
							if (isset($next_date_time) && $next_date_time != '') $recur_data[] = $next_date_time;
						}
					} 
					break;
				default:
					// anything else we need to end the loop
					$next_range_time = $end_range_time + 100;
			} # end switch
		}
		$next_range_time = strtotime('+'.$interval.' '.$freq_type, $next_range_time); 
	} #end while loop
} # end if time compare 


#foreach($recur_data as $time) echo "<br>".date("Ymd his",$time);



# use recur_data array to write the master array
// use the same code to write the data instead of always changing it 5 times						
$recur_data_hour = @substr($start_time,0,2);
$recur_data_minute = @substr($start_time,2,2);
foreach($recur_data as $recur_data_time) {
	$recur_data_year = date('Y', $recur_data_time);
	$recur_data_month = date('m', $recur_data_time);
	$recur_data_day = date('d', $recur_data_time);
	$recur_data_date = $recur_data_year.$recur_data_month.$recur_data_day;
	if (($recur_data_time >= $start_date_time) && ($recur_data_time <= $end_date_time) && ($count_to != $count) && !in_array($recur_data_date, $except_dates)) {
		if (isset($allday_start) && $allday_start != '') {
			$start_time2 = $recur_data_time;
			$end_time2 = strtotime('+'.$diff_allday_days.' days', $recur_data_time);
			while ($start_time2 < $end_time2) {
				$start_date2 = date('Ymd', $start_time2);
				$master_array[($start_date2)][('-1')][$uid] = array (
					'event_text' => $summary, 
					'description' => $description, 
					'location' => $location, 
					'organizer' => serialize($organizer), 
					'attendee' => serialize($attendee), 
					'calnumber' => $calnumber, 
					'calname' => $actual_calname, 
					'url' => $url, 
					'status' => $status, 
					'class' => $class, 
					'recur' => $recur );
				$start_time2 = strtotime('+1 day', $start_time2);
			}
		} else {
			$start_unixtime_tmp = mktime($recur_data_hour,$recur_data_minute,0,$recur_data_month,$recur_data_day,$recur_data_year);
			$end_unixtime_tmp = $start_unixtime_tmp + $length;
			
			if (($end_time >= $bleed_time) && ($bleed_check == '-1')) {
				$start_tmp = strtotime(date('Ymd',$start_unixtime_tmp));
				$end_date_tmp = date('Ymd',$end_unixtime_tmp);
				while ($start_tmp < $end_unixtime_tmp) {
					$start_date_tmp = date('Ymd',$start_tmp);
					if ($start_date_tmp == $recur_data_year.$recur_data_month.$recur_data_day) {
						$time_tmp = $hour.$minute;
						$start_time_tmp = $start_time;
					} else {
						$time_tmp = '0000';
						$start_time_tmp = '0000';
					}
					if ($start_date_tmp == $end_date_tmp) {
						$end_time_tmp = $end_time;
					} else {
						$end_time_tmp = '2400';
						$display_end_tmp = $end_time;
					}
					
					// Let's double check the until to not write past it
					$until_check = $start_date_tmp.$time_tmp.'00'; 
					if ($abs_until > $until_check) {
						$master_array[$start_date_tmp][$time_tmp][$uid] = array (
							'event_start' => $start_time_tmp, 
							'event_end' => $end_time_tmp, 
							'start_unixtime' => $start_unixtime_tmp, 
							'end_unixtime' => $end_unixtime_tmp, 
							'event_text' => $summary, 
							'event_length' => $length, 
							'event_overlap' => 0, 
							'description' => $description, 
							'status' => $status, 
							'class' => $class, 
							'spans_day' => true, 
							'location' => $location, 
							'organizer' => serialize($organizer), 
							'attendee' => serialize($attendee), 
							'calnumber' => $calnumber, 
							'calname' => $actual_calname, 
							'url' => $url, 
							'recur' => $recur);
						if (isset($display_end_tmp)){
							$master_array[$start_date_tmp][$time_tmp][$uid]['display_end'] = $display_end_tmp;
						}
						checkOverlap($start_date_tmp, $time_tmp, $uid);
					}
					$start_tmp = strtotime('+1 day',$start_tmp);
				}
			} else {
				if ($bleed_check == '-1') {
					$display_end_tmp = $end_time;
					$end_time_tmp1 = '2400';
						
				}
				if (!isset($end_time_tmp1)) $end_time_tmp1 = $end_time;
			
				// Let's double check the until to not write past it
				$until_check = $recur_data_date.$hour.$minute.'00';
				if ($abs_until > $until_check) {
					$master_array[($recur_data_date)][($hour.$minute)][$uid] = array (
						'event_start' => $start_time, 
						'event_end' => $end_time_tmp1, 
						'start_unixtime' => $start_unixtime_tmp, 
						'end_unixtime' => $end_unixtime_tmp, 
						'event_text' => $summary, 
						'event_length' => $length, 
						'event_overlap' => 0, 
						'description' => $description, 
						'status' => $status, 
						'class' => $class, 
						'spans_day' => false, 
						'location' => $location, 
						'organizer' => serialize($organizer), 
						'attendee' => serialize($attendee), 
						'calnumber' => $calnumber, 
						'calname' => $actual_calname, 
						'url' => $url, 
						'recur' => $recur);
					if (isset($display_end_tmp)){
						$master_array[($recur_data_date)][($hour.$minute)][$uid]['display_end'] = $display_end_tmp;
					}
					checkOverlap($recur_data_date, ($hour.$minute), $uid);
				}
			}
		}
	}
}

unset($recur_data);


// This should remove any exdates that were missed.
// Added for version 0.9.5 modified in 2.22 remove anything that doesn't have an event_start
if (is_array($except_dates)) {
	foreach ($except_dates as $key => $value) {
		if (isset ($master_array[$value])){
			foreach ($master_array[$value] as $time => $value2){
				if (!isset($value2[$uid]['event_start'])){
					unset($master_array[$value][$time][$uid]);
				}
			}
		}
	}
}

// Clear event data now that it's been saved.
unset($start_time, $start_time_tmp, $end_time, $end_time_tmp, $start_unixtime, $start_unixtime_tmp, $end_unixtime, $end_unixtime_tmp, $summary, $length, $description, $status, $class, $location, $organizer, $attendee);

?>