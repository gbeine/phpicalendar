<?php

if (!defined('BASE')) define('BASE', './');
include(BASE.'functions/init.inc.php');
include(BASE.'functions/date_functions.php');
include(BASE.'functions/draw_functions.php');
include(BASE.'functions/overlapping_events.php');
include(BASE.'functions/timezones.php');

$fillTime = $day_start;
$day_array = array ();
while ($fillTime < $day_end) {
	array_push ($day_array, $fillTime);
	ereg ('([0-9]{2})([0-9]{2})', $fillTime, $dTime);
	$fill_h = $dTime[1];
	$fill_min = $dTime[2];
	$fill_min = sprintf('%02d', $fill_min + $gridLength);
	if ($fill_min == 60) {
		$fill_h = sprintf('%02d', ($fill_h + 1));
		$fill_min = '00';
	}
	$fillTime = $fill_h . $fill_min;
}

// what date we want to get data for (for day calendar)
if (!isset($getdate) || $getdate == '') $getdate = date('Ymd');
ereg ('([0-9]{4})([0-9]{2})([0-9]{2})', $getdate, $day_array2);
$this_day = $day_array2[3];
$this_month = $day_array2[2];
$this_year = $day_array2[1];

// reading the file if it's allowed
$parse_file = true;
if (($is_webcal == false) && ($save_parsed_cals == 'yes') && ($cal != $ALL_CALENDARS_COMBINED)) {	
	$realcal_mtime = filemtime($filename);
	$parsedcal = $tmp_dir.'/parsedcal-'.$cal_filename.'-'.$this_year;
	if (file_exists($parsedcal)) {
		$parsedcal_mtime = filemtime($parsedcal);
		if ($realcal_mtime == $parsedcal_mtime) {
			$fd = fopen($parsedcal, 'r');
			$contents = fread($fd, filesize($parsedcal));
			fclose($fd);
			$master_array = unserialize($contents);
			if ($master_array['-1'] == 'valid cal file') {
				$parse_file = false;
				$calendar_name = $master_array['calendar_name'];
				$calendar_tz = $master_array['calendar_tz'];
			}
		}
	}
}

if ($parse_file) {	
// some initializations, that have to be outside the calnumber loop
	// auxiliary array for determining overlaps of events
	$overlap_array = array ();
		
	// using $uid to set specific points in array, if $uid is not in the 
	// .ics file, we need to have some unique place in the array
	$uid_counter = 0;
}

$calnumber = 1;
foreach ($cal_filelist as $filename) {

	if ($parse_file) {	
		// patch to speed up parser
		
		$ifile = fopen($filename, "r");
		if ($ifile == FALSE) exit(error($error_invalidcal_lang, $filename));
		$nextline = fgets($ifile, 1024);
		if (trim($nextline) != 'BEGIN:VCALENDAR') exit(error($error_invalidcal_lang, $filename));
		
		// Set a value so we can check to make sure $master_array contains valid data
		$master_array['-1'] = 'valid cal file';
	
		// Set default calendar name - can be overridden by X-WR-CALNAME
		$calendar_name = $cal_filename;
		$master_array['calendar_name'] = $calendar_name;
		
	// read file in line by line
	// XXX end line is skipped because of the 1-line readahead
		while (!feof($ifile)) {
			$line = $nextline;
			$nextline = fgets($ifile, 1024);
			$nextline = ereg_replace("[\r\n]", "", $nextline);
			while (substr($nextline, 0, 1) == " ") {
				$line = $line . substr($nextline, 1);
				$nextline = fgets($ifile, 1024);
				$nextline = ereg_replace("[\r\n]", "", $nextline);
			}
			$line = trim($line);
			if ($line == 'BEGIN:VEVENT') {
				// each of these vars were being set to an empty string
				unset (
					$start_time, $end_time, $start_date, $end_date, $summary, 
					$allday_start, $allday_end, $start, $end, $the_duration, 
					$beginning, $rrule_array, $start_of_vevent, $description, 
					$valarm_description, $start_unixtime, $end_unixtime,
					$recurrence_id, $uid, $class, $location, $rrule
				);
					
				$except_dates 	= array();
				$except_times 	= array();
				$first_duration = TRUE;
				$count 			= 1000000;
				$valarm_set 	= FALSE;
				$attendee		= array();
				$organizer	= array();
				
				unset(
					$until, $bymonth, $byday, $bymonthday, $byweek, $byweekno, 
					$byminute, $byhour, $bysecond, $byyearday, $bysetpos, $wkst,
					$interval, $number
				);
				
			} elseif ($line == 'END:VEVENT') {
				
				$end_unixtime 	= $start_unixtime + $the_duration;
				$end_time 		= date ('Hi', $end_unixtime);
				
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
				
				if ($uid_valid && isset($processed[$uid]) && isset($recurrence_id['date'])) {
					$old_start_date = $processed[$uid][0];
					$old_start_time = $processed[$uid][1];
					$start_date_tmp = $recurrence_id['date'];
					if (!isset($start_date)) $start_date = $old_start_date;
					if (!isset($start_time)) $start_time = $master_array[$old_start_date][$old_start_time][$uid]['event_start'];
					if (!isset($start_unixtime)) $start_unixtime = $master_array[$old_start_date][$old_start_time][$uid]['start_unixtime'];
					// Handle DURATION
					if (!isset($end_unixtime) && isset($the_duration)) {
						$end_unixtime 	= $start_unixtime + $the_duration;
						$end_time 	= date ('Hi', $end_unixtime);
					}
					if (!isset($end_unixtime)) $end_unixtime = $master_array[$old_start_date][$old_start_time][$uid]['end_unixtime'];
					if (!isset($end_time)) $end_time = $master_array[$old_start_date][$old_start_time][$uid]['event_end'];
					if (!isset($summary)) $summary = $master_array[$old_start_date][$old_start_time][$uid]['event_text'];
					if (!isset($length)) $length = $master_array[$old_start_date][$old_start_time][$uid]['event_length'];
					if (!isset($description)) $description = $master_array[$old_start_date][$old_start_time][$uid]['description'];
					removeOverlap($start_date_tmp, $old_start_time, $uid);
					if (isset($master_array[$start_date_tmp][$old_start_time][$uid])) {
						unset($master_array[$start_date_tmp][$old_start_time][$uid]);  // SJBO added $uid twice here
					}
					$write_processed = false;
				} else {
					$write_processed = true;
				}
				
				if (!isset($summary)) 		$summary = '';
				if (!isset($description)) 	$description = '';
				if (!isset($status)) 		$status = '';
				if (!isset($class)) 		$class = '';
				if (!isset($location)) 		$location = '';
				
				$mArray_begin = mktime (0,0,0,12,21,($this_year - 1));
				$mArray_end = mktime (0,0,0,1,12,($this_year + 1));
				
				if (isset($start_time) && isset($end_time)) {
					// Mozilla style all-day events or just really long events
					if (($end_time - $start_time) > 2345) {
						$allday_start = $start_date;
						$allday_end = ($start_date + 1);
					}
				}
				if (isset($start_unixtime,$end_unixtime) && date('d',$start_unixtime) != date('d',$end_unixtime)) {
					$spans_day = true;
				} else {
					$spans_day = false;
				}
				if (isset($start_time) && $start_time != '') {
					ereg ('([0-9]{2})([0-9]{2})', $start_time, $time);
					ereg ('([0-9]{2})([0-9]{2})', $end_time, $time2);
					if (isset($start_unixtime) && isset($end_unixtime)) {
						$length = $end_unixtime - $start_unixtime;
					} else {
						$length = ($time2[1]*60+$time2[2]) - ($time[1]*60+$time[2]);
					}
					
					$drawKey = drawEventTimes($start_time, $end_time);
					ereg ('([0-9]{2})([0-9]{2})', $drawKey['draw_start'], $time3);
					$hour = $time3[1];
					$minute = $time3[2];
				}
	
				// RECURRENCE-ID Support
				if (isset($recurrence_d)) {
					
					$recurrence_delete["$recurrence_d"]["$recurrence_t"] = $uid;
				}
					
				// handle single changes in recurring events
				// Maybe this is no longer need since done at bottom of parser? - CL 11/20/02
				if ($uid_valid && $write_processed) {
					if (!isset($hour)) $hour = 00;
					if (!isset($minute)) $minute = 00;
					$processed[$uid] = array($start_date,($hour.$minute));
				}
							
				// Handling of the all day events
				if ((isset($allday_start) && $allday_start != '')) {
  					$start = strtotime($allday_start);
 					if ($spans_day) {
 						$allday_end = date('Ymd',$end_unixtime);
 					}
  					if (isset($allday_end)) {
  						$end = strtotime($allday_end);
  					} else {
						$end = strtotime('+1 day', $start);
					}
					if (($end > $mArray_begin) && ($end < $mArray_end)) {
						while ($start != $end) {
							$start_date2 = date('Ymd', $start);
							$master_array[($start_date2)][('-1')][$uid]= array ('event_text' => $summary, 'description' => $description, 'calnumber' => $calnumber);
							$start = strtotime('+1 day', $start);
						}
						if (!$write_processed) $master_array[($start_date)]['-1'][$uid]['exception'] = true;
					}
				}
				
				// Handling regular events
				if ((isset($start_time) && $start_time != '') && (!isset($allday_start) || $allday_start == '')) {
					if ($spans_day) {
						$start_tmp = strtotime(date('Ymd',$start_unixtime));
						$end_date_tmp = date('Ymd',$end_unixtime);
						while ($start_tmp < $end_unixtime) {
							$start_date_tmp = date('Ymd',$start_tmp);
							if ($start_date_tmp == $start_date) {
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
							}
							$nbrOfOverlaps = checkOverlap($start_date_tmp, $start_time_tmp, $end_time_tmp, $uid);
							$master_array[$start_date_tmp][$time_tmp][$uid] = array ('event_start' => $start_time_tmp, 'event_end' => $end_time_tmp, 'start_unixtime' => $start_unixtime, 'end_unixtime' => $end_unixtime, 'event_text' => $summary, 'event_length' => $length, 'event_overlap' => $nbrOfOverlaps, 'description' => $description, 'status' => $status, 'class' => $class, 'spans_day' => true, 'location' => $location, 'organizer' => serialize($organizer), 'attendee' => serialize($attendee) );
							$start_tmp = strtotime('+1 day',$start_tmp);
						}
						if (!$write_processed) $master_array[$start_date][($hour.$minute)][$uid]['exception'] = true;
					} else {
						// This if statement should prevent writing of an excluded date if its the first recurrance - CL
						if (!in_array($start_date, $except_dates)) {
							$nbrOfOverlaps = checkOverlap($start_date, $start_time, $end_time, $uid);
							$master_array[($start_date)][($hour.$minute)][$uid] = array ('event_start' => $start_time, 'event_end' => $end_time, 'start_unixtime' => $start_unixtime, 'end_unixtime' => $end_unixtime, 'event_text' => $summary, 'event_length' => $length, 'event_overlap' => $nbrOfOverlaps, 'description' => $description, 'status' => $status, 'class' => $class, 'spans_day' => false, 'location' => $location, 'organizer' => serialize($organizer), 'attendee' => serialize($attendee), 'calnumber' => $calnumber );
							if (!$write_processed) $master_array[($start_date)][($hour.$minute)][$uid]['exception'] = true;
						}
					}
				}
				
				// Handling of the recurring events, RRULE
				if (isset($rrule_array) && is_array($rrule_array)) {
					if (isset($allday_start) && $allday_start != '') {
						$hour = '-';
						$minute = '1';
						$rrule_array['START_DAY'] = $allday_start;
						$rrule_array['END_DAY'] = $allday_end;
						$rrule_array['END'] = 'end';
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
						$rrule_array['END'] = 'end';
					}
					
					$start_date_time = strtotime($start_date);
					$this_month_start_time = strtotime($this_year.$this_month.'01');
					if ($save_parsed_cals == 'yes' && !$is_webcal) {
						$start_range_time = strtotime($this_year.'-01-01 -1 month -2 days');
						$end_range_time = strtotime($this_year.'-12-31 +1 month +2 days');
					} else {
						$start_range_time = strtotime('-1 month -2 day', $this_month_start_time);
						$end_range_time = strtotime('+2 month +2 day', $this_month_start_time);
					}
					
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
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = strtolower($val);
								break;
							case 'COUNT':
								$count = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $count;
								break;
							case 'UNTIL':
								$until = ereg_replace('T', '', $val);
								$until = ereg_replace('Z', '', $until);
								ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $until, $regs);
								$year = $regs[1];
								$month = $regs[2];
								$day = $regs[3];
								$until = mktime(0,0,0,$month,$day,$year);
								if (ereg('^([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2})([0-9]{2})([0-9]{2})$', $val)) {
									// RFC 2445 says that if an UNTIL has a date-time value,
									// it MUST be in UTC (i.e. trailing Z).  iCal tends to
									// put an end date on the next day early in the morning,
									// not in UTC time, so we try to correct for it.
									//
									// Bill's guess: iCal stores the UNTIL internally as
									// 23:59:59 UTC, then accidentally converts that to local
									// time when exporting the event.  Thus, if the UNTIL time
									// is before noon, it is a day ahead; if it's after noon
									// it's the right day.
									if ($regs[4] < 12)
										$until = strtotime('-1 day', $until);
								}
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = localizeDate($dateFormat_week,$until);
								break;
							case 'INTERVAL':
								$number = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $number;
								break;
							case 'BYSECOND':
								$bysecond = $val;
								$bysecond = split (',', $bysecond);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bysecond;
								break;
							case 'BYMINUTE':
								$byminute = $val;
								$byminute = split (',', $byminute);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byminute;
								break;
							case 'BYHOUR':
								$byhour = $val;
								$byhour = split (',', $byhour);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byhour;
								break;
							case 'BYDAY':
								$byday = $val;
								$byday = split (',', $byday);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byday;
								break;
							case 'BYMONTHDAY':
								$bymonthday = $val;
								$bymonthday = split (',', $bymonthday);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bymonthday;
								break;					
							case 'BYYEARDAY':
								$byyearday = $val;
								$byyearday = split (',', $byyearday);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byyearday;
								break;
							case 'BYWEEKNO':
								$byweekno = $val;
								$byweekno = split (',', $byweekno);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byweekno;
								break;
							case 'BYMONTH':
								$bymonth = $val;
								$bymonth = split (',', $bymonth);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bymonth;
								break;
							case 'BYSETPOS':
								$bysetpos = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bysetpos;
								break;
							case 'WKST':
								$wkst = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $wkst;
								break;
							case 'END':
						
							if (!isset($number)) $number = 1;
							// if $until isn't set yet, we set it to the end of our range we're looking at
							// The FREQ switch array will always kick our early, so lets try this workaround.
							// if (isset($until)) $until = strtotime('+'.$interval.' '.$freq_type, $until);;
							
							if (!isset($until)) $until = $end_range_time;
							$end_date_time = $until;
							$start_range_time_tmp = $start_range_time;
							$end_range_time_tmp = $end_range_time;
							
							// If the $end_range_time is less than the $start_date_time, or $start_range_time is greater
							// than $end_date_time, we may as well forget the whole thing
							// It doesn't do us any good to spend time adding data we aren't even looking at
							// this will prevent the year view from taking way longer than it needs to
							if ($end_range_time_tmp >= $start_date_time && $start_range_time_tmp <= $end_date_time) {
							
								// if the beginning of our range is less than the start of the item, we may as well set it equal to it
								if ($start_range_time_tmp < $start_date_time) $start_range_time_tmp = $start_date_time;
								if ($end_range_time_tmp > $end_date_time) $end_range_time_tmp = $end_date_time;
					
								// initialze the time we will increment
								$next_range_time = $start_range_time_tmp;
								
								// FIXME: This is a hack to fix repetitions with $interval > 1 
								if ($count > 1 && $number > 1) $count = 1 + ($count - 1) * $number; 
								
								$count_to = 0;
								// start at the $start_range and go until we hit the end of our range.
								while (($next_range_time >= $start_range_time_tmp) && ($next_range_time <= $end_range_time_tmp) && ($count_to != $count)) {
									$func = $freq_type.'Compare';
									$diff = $func(date('Ymd',$next_range_time), $start_date);
									if ($diff < $count) {
										if ($diff % $number == 0) {
											$interval = $number;
											switch ($rrule_array['FREQ']) {
												case 'DAILY':
													$next_date_time = $next_range_time;
													$recur_data[] = $next_date_time;
													break;
												case 'WEEKLY':
													if (!isset($byday)) {
														$next_date = date('Ymd', $next_range_time);
														$next_date_time = strtotime($next_date);
														$recur_data[] = $next_date_time;
													} elseif (is_array($byday)) {
														// loop through the days on which this event happens
														foreach($byday as $day) {
															// use my fancy little function to get the date of each day
															$day = two2threeCharDays($day);														
															$next_date = dateOfWeek(date('Ymd', $next_range_time),$day);
															$next_date_time = strtotime($next_date);
															$recur_data[] = $next_date_time;
														}
													}
													break;
												case 'MONTHLY':
													if (!isset($bymonth)) $bymonth = array(1,2,3,4,5,6,7,8,9,10,11,12);
													$next_range_time = strtotime(date('Y-m-01', $next_range_time));
													// month has two cases, either $bymonthday or $byday
													if ((is_array($bymonthday)) && (!is_array($byday))) {
														// loop through the days on which this event happens
														foreach($bymonthday as $day) {
															$year = date('Y', $next_range_time);
															$month = date('m', $next_range_time);
															if (checkdate($month,$day,$year)) {
																$next_date_time = mktime(0,0,0,$month,$day,$year);
																$recur_data[] = $next_date_time;
															}
														}
													// our other case
													} else {
														// loop through the days on which this event happens
														foreach($byday as $day) {
															ereg ('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
															$nth = $byday_arr[2]-1;
															$on_day = two2threeCharDays($byday_arr[3]);
															$on_day_num = two2threeCharDays($byday_arr[3],false);
															if ((isset($byday_arr[1])) && ($byday_arr[1] == '-')) {
																$last_day_tmp = date('t',$next_range_time);
																$next_range_time = strtotime(date('Y-m-'.$last_day_tmp, $next_range_time));
																$last_tmp = (date('w',$next_range_time) == $on_day_num) ? '' : 'last ';
																$next_date_time = strtotime($last_tmp.$on_day.' -'.$nth.' week', $next_range_time);
																$month = date('m', $next_date_time);
																if (in_array($month, $bymonth)) {
																	$recur_data[] = $next_date_time;
																}
															} elseif (is_array($bymonthday)) {
																// This supports MONTHLY where BYDAY and BYMONTH are both set
																foreach($bymonthday as $day) {
																	$year 	= date('Y', $next_range_time);
																	$month 	= date('m', $next_range_time);
																	if (checkdate($month,$day,$year)) {
																		$next_date_time = mktime(0,0,0,$month,$day,$year);
																		$daday = strtolower(strftime("%a", $next_date_time));
																		if ($daday == $on_day && in_array($month, $bymonth)) {
																			$recur_data[] = $next_date_time;
																		}
																	}
																}
															} elseif ((isset($byday_arr[1])) && ($byday_arr[1] != '-')) {
																$next_date_time = strtotime($on_day.' +'.$nth.' week', $next_range_time);
																$month = date('m', $next_date_time);
																if (in_array($month, $bymonth)) {
																	$recur_data[] = $next_date_time;
																}
															}
															$next_date = date('Ymd', $next_date_time);
															//$recur_data[] = $next_date_time;
														}
													}
													break;
												case 'YEARLY':
												
													if (!isset($bymonth)) {
														$m = date('m', $start_date_time);
														$bymonth = array("$m");
													}	
													foreach($bymonth as $month) {
														$year = date('Y', $next_range_time);
														if ((isset($byday)) && (is_array($byday))) {
															$checkdate_time = mktime(0,0,0,$month,1,$year);
															foreach($byday as $day) {
																ereg ('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
																$nth = $byday_arr[2]-1;
																$on_day = two2threeCharDays($byday_arr[3]);
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
															$next_date_time = mktime(0,0,0,$month,$day,$year);
														}
														$recur_data[] = $next_date_time;
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
															$recur_data[] = $next_date_time;
														}
													}
													break;
												default:
													// anything else we need to end the loop
													$next_range_time = $end_range_time_tmp + 100;
													$count_to = $count;
											}
										} else {
											$interval = 1;
										}
										$next_range_time = strtotime('+'.$interval.' '.$freq_type, $next_range_time);
									} else {
										// end the loop because we aren't going to write this event anyway
										$count_to = $count;
									}
									// use the same code to write the data instead of always changing it 5 times						
									if (isset($recur_data) && is_array($recur_data)) {
										$recur_data_hour = substr($start_time,0,2);
										$recur_data_minute = substr($start_time,2,2);
										foreach($recur_data as $recur_data_time) {
											$recur_data_year = date('Y', $recur_data_time);
											$recur_data_month = date('m', $recur_data_time);
											$recur_data_day = date('d', $recur_data_time);
											$recur_data_date = $recur_data_year.$recur_data_month.$recur_data_day;

											if (($recur_data_time > $start_date_time) && ($recur_data_time <= $end_date_time) && ($count_to != $count) && !in_array($recur_data_date, $except_dates)) {
												if (isset($allday_start) && $allday_start != '') {
													$start_time2 = $recur_data_time;
													$end_time2 = strtotime('+'.$diff_allday_days.' days', $recur_data_time);
													while ($start_time2 < $end_time2) {
														$start_date2 = date('Ymd', $start_time2);
														$master_array[($start_date2)][('-1')][]= array ('event_text' => $summary, 'description' => $description, 'calnumber' => $calnumber);
														$start_time2 = strtotime('+1 day', $start_time2);
													}
												} else {
													$start_unixtime_tmp = mktime($recur_data_hour,$recur_data_minute,0,$recur_data_month,$recur_data_day,$recur_data_year);
													$end_unixtime_tmp = $start_unixtime_tmp + $length;
													
													if ($spans_day) {
														$start_tmp = strtotime(date('Ymd',$start_unixtime_tmp));
														$end_date_tmp = date('Ymd',$end_unixtime_tmp);
														while ($start_tmp < $end_unixtime_tmp) {
															$start_date_tmp = date('Ymd',$start_tmp);
															if ($start_date_tmp == $start_date) {
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
															}
															$nbrOfOverlaps = checkOverlap($start_date_tmp, $start_time_tmp, $end_time_tmp, $uid);
															$master_array[$start_date_tmp][$time_tmp][$uid] = array ('event_start' => $start_time_tmp, 'event_end' => $end_time_tmp, 'start_unixtime' => $start_unixtime_tmp, 'end_unixtime' => $end_unixtime_tmp, 'event_text' => $summary, 'event_length' => $length, 'event_overlap' => $nbrOfOverlaps, 'description' => $description, 'status' => $status, 'class' => $class, 'spans_day' => true, 'location' => $location, 'organizer' => serialize($organizer), 'attendee' => serialize($attendee), 'calnumber' => $calnumber);
															$start_tmp = strtotime('+1 day',$start_tmp);
														}
													} else {
														$nbrOfOverlaps = checkOverlap($recur_data_date, $start_time, $end_time, $uid);
														$master_array[($recur_data_date)][($hour.$minute)][$uid] = array ('event_start' => $start_time, 'event_end' => $end_time, 'start_unixtime' => $start_unixtime_tmp, 'end_unixtime' => $end_unixtime_tmp, 'event_text' => $summary, 'event_length' => $length, 'event_overlap' => $nbrOfOverlaps, 'description' => $description, 'status' => $status, 'class' => $class, 'spans_day' => false, 'location' => $location, 'organizer' => serialize($organizer), 'attendee' => serialize($attendee), 'calnumber' => $calnumber);
													}
												}
											}
										}
										unset($recur_data);
									}
								}
							}
						}	
					}
				}

				// This should remove any exdates that were missed.
				// Added for version 0.9.5
				if (is_array($except_dates)) {
					foreach ($except_dates as $key => $value) {
						$time = $except_times[$key];
						unset($master_array[$value][$time][$uid]);
						if (count($master_array[$value][$time]) < 1) {
							unset($master_array[$value][$time]);
							if (count($master_array[$value]) < 1) {
								unset($master_array[$value]);	
							}
						}
					}
				}
				
			   // Clear event data now that it's been saved.
			   unset($start_time, $start_time_tmp, $end_time, $end_time_tmp, $start_unixtime, $start_unixtime_tmp, $end_unixtime, $end_unixtime_tmp, $summary, $length, $nbrOfOverlaps, $description, $status, $class, $location, $organizer, $attendee);


			// Begin VTODO Support
			} elseif ($line == 'END:VTODO') {
				if ((!$vtodo_priority) && ($status == 'COMPLETED')) {
					$vtodo_sort = 11;
				} elseif (!$vtodo_priority) { 
					$vtodo_sort = 10;
				} else {
					$vtodo_sort = $vtodo_priority;
				}
				$master_array['-2']["$vtodo_sort"]["$uid"] = array ('start_date' => $start_date, 'start_time' => $start_time, 'vtodo_text' => $summary, 'due_date'=> $due_date, 'due_time'=> $due_time, 'completed_date' => $completed_date, 'completed_time' => $completed_time, 'priority' => $vtodo_priority, 'status' => $status, 'class' => $class, 'categories' => $vtodo_categories, 'description' => $description);
				unset ($start_date, $start_time, $due_date, $due_time, $completed_date, $completed_time, $vtodo_priority, $status, $class, $vtodo_categories, $summary, $description);
				$vtodo_set = FALSE;
			} elseif ($line == 'BEGIN:VTODO') {
				$vtodo_set = TRUE;
			} elseif ($line == 'BEGIN:VALARM') {
				$valarm_set = TRUE;
			} elseif ($line == 'END:VALARM') {
				$valarm_set = FALSE;
			} else {
		
				unset ($field, $data, $prop_pos, $property);
				ereg ("([^:]+):(.*)", $line, $line);
				$field = $line[1];
				$data = $line[2];
				
				$property = $field;
				$prop_pos = strpos($property,';');
				if ($prop_pos !== false) $property = substr($property,0,$prop_pos);
				$property = strtoupper($property);
				
				switch ($property) {
					
					// Start VTODO Parsing
					//
					case 'DUE':
						$data = str_replace ('/softwarestudio.org/Olson_20011030_5/', '', $data);
						$zulu_time = false;
						if (substr($data,-1) == 'Z') $zulu_time = true;
						$data = ereg_replace('T', '', $data);
						$data = ereg_replace('Z', '', $data);
						if (preg_match("/^DUE;VALUE=DATE/i", $field))  {
							$allday_start = $data;
							$start_date = $allday_start;
							$start_unixtime = strtotime($data);
                            $due_date = date('Ymd', $start_unixtime);
						} else {
							if (preg_match("/^DUE;TZID=/i", $field)) {
								$tz_tmp = explode('=', $field);
								$tz_due = $tz_tmp[1];
								unset($tz_tmp);
							} elseif ($zulu_time) {
								$tz_due = 'GMT';
							}
			
							ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $data, $regs);
							$start_date = $regs[1] . $regs[2] . $regs[3];
							$start_time = $regs[4] . $regs[5];
							$start_unixtime = mktime($regs[4], $regs[5], 0, $regs[2], $regs[3], $regs[1]);
			
							$dlst = date('I', $start_unixtime);
							$server_offset_tmp = chooseOffset($start_unixtime);
							if (isset($tz_due)) {
								if (array_key_exists($tz_due, $tz_array)) {
									$offset_tmp = $tz_array[$tz_due][$dlst];
								} else {
									$offset_tmp = '+0000';
								}
							} elseif (isset($calendar_tz)) {
								if (array_key_exists($calendar_tz, $tz_array)) {
									$offset_tmp = $tz_array[$calendar_tz][$dlst];
								} else {
									$offset_tmp = '+0000';
								}
							} else {
								$offset_tmp = $server_offset_tmp;
							}
							$start_unixtime = calcTime($offset_tmp, $server_offset_tmp, $start_unixtime);
							$due_date = date('Ymd', $start_unixtime);
							$due_time = date('Hi', $start_unixtime);
							unset($server_offset_tmp);
						}
						break;
						
					case 'COMPLETED':
						$data = str_replace ('/softwarestudio.org/Olson_20011030_5/', '', $data);
						$zulu_time = false;
						if (substr($data,-1) == 'Z') $zulu_time = true;
						$data = ereg_replace('T', '', $data);
						$data = ereg_replace('Z', '', $data);
						if (preg_match("/^COMPLETED;VALUE=DATE/i", $field))  {
							$allday_start = $data;
							$start_date = $allday_start;
						} else {
							if (preg_match("/^COMPLETED;TZID=/i", $field)) {
								$tz_tmp = explode('=', $field);
								$tz_completed = $tz_tmp[1];
								unset($tz_tmp);
							} elseif ($zulu_time) {
								$tz_completed = 'GMT';
							}
			
							ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $data, $regs);
							$start_date = $regs[1] . $regs[2] . $regs[3];
							$start_time = $regs[4] . $regs[5];
							$start_unixtime = mktime($regs[4], $regs[5], 0, $regs[2], $regs[3], $regs[1]);
			
							$dlst = date('I', $start_unixtime);
							$server_offset_tmp = chooseOffset($start_unixtime);
							if (isset($tz_completed)) {
								if (array_key_exists($tz_completed, $tz_array)) {
									$offset_tmp = $tz_array[$tz_completed][$dlst];
								} else {
									$offset_tmp = '+0000';
								}
							} elseif (isset($calendar_tz)) {
								if (array_key_exists($calendar_tz, $tz_array)) {
									$offset_tmp = $tz_array[$calendar_tz][$dlst];
								} else {
									$offset_tmp = '+0000';
								}
							} else {
								$offset_tmp = $server_offset_tmp;
							}
							$start_unixtime = calcTime($offset_tmp, $server_offset_tmp, $start_unixtime);
							$completed_date = date('Ymd', $start_unixtime);
							$completed_time = date('Hi', $start_unixtime);
							unset($server_offset_tmp);
						}
						break;	
					
					case 'PRIORITY':
						$vtodo_priority = "$data";
						break;
						
					case 'STATUS':
						// VEVENT: TENTATIVE, CONFIRMED, CANCELLED
						// VTODO: NEEDS-ACTION, COMPLETED, IN-PROCESS, CANCELLED
						$status = "$data";
						break;
						
					case 'CLASS':
						// VEVENT, VTODO: PUBLIC, PRIVATE, CONFIDENTIAL
						$class = "$data";
						break;
						
					case 'CATEGORIES':
						$vtodo_categories = "$data";
						break;		
					//
					// End VTODO Parsing				
						
					case 'DTSTART':
						$data = str_replace ('/softwarestudio.org/Olson_20011030_5/', '', $data);
						$zulu_time = false;
						if (substr($data,-1) == 'Z') $zulu_time = true;
						$data = ereg_replace('T', '', $data);
						$data = ereg_replace('Z', '', $data);
						$field = ereg_replace(';VALUE=DATE-TIME', '', $field); 
						if (preg_match("/^DTSTART;VALUE=DATE/i", $field))  {
							ereg ('([0-9]{4})([0-9]{2})([0-9]{2})', $data, $dtstart_check);
							if ($dtstart_check[1] < 1969) { 
								$dtstart_check[1] = '1990';
								$data = $dtstart_check[1].$dtstart_check[2].$dtstart_check[3];
							}
							$allday_start = $data;
							$start_date = $allday_start;
							$start_unixtime = strtotime($data);
						} else {
							if (preg_match("/^DTSTART;TZID=/i", $field)) {
								$tz_tmp = explode('=', $field);
								$tz_dtstart = $tz_tmp[1];
								unset($tz_tmp);
							} elseif ($zulu_time) {
								$tz_dtstart = 'GMT';
							}
			
							ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $data, $regs);
							$start_date = $regs[1] . $regs[2] . $regs[3];
							$start_time = $regs[4] . $regs[5];
							$start_unixtime = mktime($regs[4], $regs[5], 0, $regs[2], $regs[3], $regs[1]);
			
							$dlst = date('I', $start_unixtime);
							$server_offset_tmp = chooseOffset($start_unixtime);
							if (isset($tz_dtstart)) {
								if (array_key_exists($tz_dtstart, $tz_array)) {
									$offset_tmp = $tz_array[$tz_dtstart][$dlst];
								} else {
									$offset_tmp = '+0000';
								}
							} elseif (isset($calendar_tz)) {
								if (array_key_exists($calendar_tz, $tz_array)) {
									$offset_tmp = $tz_array[$calendar_tz][$dlst];
								} else {
									$offset_tmp = '+0000';
								}
							} else {
								$offset_tmp = $server_offset_tmp;
							}
							$start_unixtime = calcTime($offset_tmp, $server_offset_tmp, $start_unixtime);
							$start_date = date('Ymd', $start_unixtime);
							$start_time = date('Hi', $start_unixtime);
							unset($server_offset_tmp);
						}
						break;
						
					case 'DTEND';
						$data = str_replace ('/softwarestudio.org/Olson_20011030_5/', '', $data);
						$zulu_time = false;
						if (substr($data,-1) == 'Z') $zulu_time = true;
						$data = ereg_replace('T', '', $data);
						$data = ereg_replace('Z', '', $data);
						$field = ereg_replace(';VALUE=DATE-TIME', '', $field); 
						if (preg_match("/^DTEND;VALUE=DATE/i", $field))  {
							$allday_end = $data;
						} else {
							if (preg_match("/^DTEND;TZID=/i", $field)) {
								$tz_tmp = explode('=', $field);
								$tz_dtend = $tz_tmp[1];
								unset($tz_tmp);
							} elseif ($zulu_time) {
								$tz_dtend = 'GMT';
							}
							
							ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $data, $regs);
							$end_date = $regs[1] . $regs[2] . $regs[3];
							$end_time = $regs[4] . $regs[5];
							$end_unixtime = mktime($regs[4], $regs[5], 0, $regs[2], $regs[3], $regs[1]);
			
							$dlst = date('I', $end_unixtime);
							$server_offset_tmp = chooseOffset($end_unixtime);
							if (isset($tz_dtend)) {
								$offset_tmp = $tz_array[$tz_dtend][$dlst];
							} elseif (isset($calendar_tz)) {
								$offset_tmp = $tz_array[$calendar_tz][$dlst];
							} else {
								$offset_tmp = $server_offset_tmp;
							}
							$end_unixtime = calcTime($offset_tmp, $server_offset_tmp, $end_unixtime);
							$end_date = date('Ymd', $end_unixtime);
							$end_time = date('Hi', $end_unixtime);
							unset($server_offset_tmp);
			
						}
						break;
						
					case 'EXDATE':
						$data = split(",", $data);
						foreach ($data as $exdata) {
							$exdata = ereg_replace('T', '', $exdata);
							$exdata = ereg_replace('Z', '', $exdata);
							ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $exdata, $regs);
							$except_dates[] = $regs[1] . $regs[2] . $regs[3];
							$except_times[] = $regs[4] . $regs[5];
						}
						break;
						
					case 'SUMMARY':
						$data = str_replace("\\n", "<br>", $data);
						$data = str_replace("\\r", "<br>", $data);
						$data = htmlentities(urlencode($data));
						if ($valarm_set == FALSE) { 
							$summary = $data;
						} else {
							$valarm_summary = $data;
						}
						break;
						
					case 'DESCRIPTION':
						$data = str_replace("\\n", "<br>", $data);
						$data = str_replace("\\r", "<br>", $data);
						$data = htmlentities(urlencode($data));
						if ($valarm_set == FALSE) { 
							$description = $data;
						} else {
							$valarm_description = $data;
						}
						break;
						
					case 'RECURRENCE-ID':
						$parts = explode(';', $field);
						foreach($parts as $part) {
							$eachval = split('=',$part);
							if ($eachval[0] == 'RECURRENCE-ID') {
								// do nothing
							} elseif ($eachval[0] == 'TZID') {
								$recurrence_id['tzid'] = $eachval[1];
							} elseif ($eachval[0] == 'RANGE') {
								$recurrence_id['range'] = $eachval[1];
							} elseif ($eachval[0] == 'VALUE') {
								$recurrence_id['value'] = $eachval[1];
							} else {
								$recurrence_id[] = $eachval[1];
							}
						}
						unset($parts, $part, $eachval);
						
						$data = ereg_replace('T', '', $data);
						$data = ereg_replace('Z', '', $data);
						ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $data, $regs);
						$recurrence_id['date'] = $regs[1] . $regs[2] . $regs[3];
						$recurrence_id['time'] = $regs[4] . $regs[5];
			
						$recur_unixtime = mktime($regs[4], $regs[5], 0, $regs[2], $regs[3], $regs[1]);
			
						$dlst = date('I', $recur_unixtime);
						$server_offset_tmp = chooseOffset($recur_unixtime);
						if (isset($recurrence_id['tzid'])) {
							$tz_tmp = $recurrence_id['tzid'];
							$offset_tmp = $tz_array[$tz_tmp][$dlst];
						} elseif (isset($calendar_tz)) {
							$offset_tmp = $tz_array[$calendar_tz][$dlst];
						} else {
							$offset_tmp = $server_offset_tmp;
						}
						$recur_unixtime = calcTime($offset_tmp, $server_offset_tmp, $recur_unixtime);
						$recurrence_id['date'] = date('Ymd', $recur_unixtime);
						$recurrence_id['time'] = date('Hi', $recur_unixtime);
						$recurrence_d = date('Ymd', $recur_unixtime);
						$recurrence_t = date('Hi', $recur_unixtime);
						unset($server_offset_tmp);
						break;
						
					case 'UID':
						$uid = $data;
						break;
					case 'X-WR-CALNAME':
						$calendar_name = $data;
						$master_array['calendar_name'] = $calendar_name;
						break;
					case 'X-WR-TIMEZONE':
						$calendar_tz = $data;
						$master_array['calendar_tz'] = $calendar_tz;
						break;
					case 'DURATION':
						if (($first_duration == TRUE) && (!stristr($field, '=DURATION'))) {
							ereg ('^P([0-9]{1,2}[W])?([0-9]{1,2}[D])?([T]{0,1})?([0-9]{1,2}[H])?([0-9]{1,2}[M])?([0-9]{1,2}[S])?', $data, $duration); 
							$weeks 			= ereg_replace('W', '', $duration[1]); 
							$days 			= ereg_replace('D', '', $duration[2]); 
							$hours 			= ereg_replace('H', '', $duration[4]); 
							$minutes 		= ereg_replace('M', '', $duration[5]); 
							$seconds 		= ereg_replace('S', '', $duration[6]); 
							$the_duration 	= ($weeks * 60 * 60 * 24 * 7) + ($days * 60 * 60 * 24) + ($hours * 60 * 60) + ($minutes * 60) + ($seconds);
							// Do this in the END:VEVENT now so that it doesn't have to come after DTSTART
							//$end_unixtime 	= $start_unixtime + $the_duration;
							//$end_time 		= date ('Hi', $end_unixtime);
							$first_duration = FALSE;
						}	
						break;
					case 'RRULE':
						$data = ereg_replace ('RRULE:', '', $data);
						$rrule = split (';', $data);
						foreach ($rrule as $recur) {
							ereg ('(.*)=(.*)', $recur, $regs);
							$rrule_array[$regs[1]] = $regs[2];
						}
						break;
					// Attendee support only testing in Apple iCal 1.0.2	
					case 'ATTENDEE':
						$field 		= ereg_replace("ATTENDEE;CN=", "", $field);
						$data 		= ereg_replace ("mailto:", "", $data);
						$attendee[] = array ('name' => $field, 'email' => $data);
						#print_r($attendee);
						break;
					case 'ORGANIZER':
						$field 		 = ereg_replace("ORGANIZER;CN=", "", $field);
						$data 		 = ereg_replace ("mailto:", "", $data);
						$organizer[] = array ('name' => $field, 'email' => $data);
						break;
					case 'LOCATION':
						$location = $data;
						break;
				}
			}
		}
	}
	$calnumber = $calnumber + 1;
}


if ($parse_file) {	
	// Sort the array by absolute date.
	if (isset($master_array) && is_array($master_array)) { 
		ksort($master_array);
		reset($master_array);
		
		// sort the sub (day) arrays so the times are in order
		foreach (array_keys($master_array) as $k) {
			if (isset($master_array[$k]) && is_array($master_array[$k])) {
				ksort($master_array[$k]);
				reset($master_array[$k]);
			}
		}
	}
	
	// write the new master array to the file
	if (isset($master_array) && is_array($master_array) && $save_parsed_cals == 'yes' && $is_webcal == FALSE && $cal != $ALL_CALENDARS_COMBINED) {
		$write_me = serialize($master_array);
		$fd = fopen($parsedcal, 'w');
		fwrite($fd, $write_me);
		fclose($fd);
		touch($parsedcal, $realcal_mtime);
	}
}



//If you want to see the values in the arrays, uncomment below.

//print '<pre>';
//print_r($master_array);
//print_r($overlap_array);
//print_r($day_array);
//print_r($rrule_array);
//print_r($recurrence_delete);	
//print '</pre>';

// Set a calender name for all calenders combined
if ($cal == $ALL_CALENDARS_COMBINED) {
	$calendar_name = $all_cal_comb_lang;
}
					
?>
