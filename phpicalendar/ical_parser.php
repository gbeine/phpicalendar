<?
// note from Jared: the _time suffix has been applied to all variables 
// that are timestamps to distinguish between them and Ymd format
// I did not change other variables to use this convention yet

// I started commenting the line above where $master_array gets written to
// I did this because I kept scrolling through looking for them so I decided to mark them

include("./init.inc.php");
include("./functions/date_add.php");

// dateOfWeek() takes a date in Ymd and a day of week as iCal knows them (ie: SU, MO, TU, etc)
// and returns the date of that day. This function may be specific to WEEKLY recurring events.

function dateOfWeek($Ymd, $day) {
	$timestamp = strtotime($Ymd);
	$sunday = strtotime((date("w",$timestamp)==0 ? "sun" : "last sun"), $timestamp);
	if ($day == "SU") $day_longer = "sun";
	elseif ($day == "MO") $day_longer = "mon";
	elseif ($day == "TU") $day_longer = "tue";
	elseif ($day == "WE") $day_longer = "wed";
	elseif ($day == "TH") $day_longer = "thu";
	elseif ($day == "FR") $day_longer = "fri";
	elseif ($day == "SA") $day_longer = "sat";
	return date("Ymd",strtotime($day_longer,$sunday));
}

$day_array = array ("0700", "0730", "0800", "0830", "0900", "0930", "1000", "1030", "1100", "1130", "1200", "1230", "1300", "1330", "1400", "1430", "1500", "1530", "1600", "1630", "1700", "1730", "1800", "1830", "1900", "1930", "2000", "2030", "2100", "2130", "2200", "2230", "2300", "2330");


// what date we want to get data for (for day calendar)
if (!$getdate) $getdate = date("Ymd");
ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);
$this_day = $day_array2[3];
$this_month = $day_array2[2];
$this_year = $day_array2[1];

// open the iCal file, read it into a string
$fp = @fopen($filename, "r");
$contents = @fread ($fp, filesize ($filename));
@fclose ($fp);


// turn that string into an array
$contents = ereg_replace("\n ", "", $contents);
$contents = split ("\n", $contents);


// parse our new array
foreach($contents as $line) {
	if (strstr($line, "BEGIN:VEVENT")) {
		$start_time = "";
		$end_time = "";
		$summary = "";
		$allday_start = "";
		$allday_end = "";
		$start = "";
		$end = "";
		$the_duration = "";
		$beginning = "";
		$rrule_array = "";
		$parse_to_year = "";
		$start_of_vevent = "";
		$end_of_vevent = "";
		$interval = "";
		$number = "";
		$except_dates = array();
		$except_times = array();
	} elseif (strstr($line, "END:VEVENT")) {
		
		// Clean out \n's and other slashes
		$summary = str_replace("\\n", "<br>", $summary);
		$summary = stripslashes($summary);
		
		
		//echo "<b>Start</b> $start_time <b>End</B> $end_time <b>Summary</b> $summary<br>\n";
		if ($start_time != "") {
			ereg ("([0-9]{2})([0-9]{2})", $start_time, $time);
			$hour = $time[1];
			$minute = $time[2];
						
			if ($minute < 15) {
				$minute = "00";
			} elseif ($minute >=15 && $minute < 45) {
				$minute = "30";
			} elseif ($minute >= 45) {
				$hour = sprintf("%.02d", ($hour + 1));
				$minute = "00";
			}
			ereg ("([0-9]{2})([0-9]{2})", $end_time, $time2);
			$length = round((($time2[1]*60+$time2[2]) - ($time[1]*60+$time[2]))/30);
		}
		
		
		// Handling of the all day events	
		if (($allday_start != "") && ($rrule_array == "")) {
			$start = strtotime("$allday_start");
			$end = strtotime("$allday_end");
			do {
				$start_date = date("Ymd", $start);
				$master_array[($start_date)][("0001")]["event_text"][] = "$summary";
				$start = ($start + (24*3600));
			} while ($start != $end);
		}
		
		
		// Handling of the recurring events, RRULE
		// This will be quite a bit of work, thats for sure.
		if (is_array($rrule_array)) {
			if ($allday_start != "") {
				$rrule_array["START_DAY"] = $allday_start;
				$rrule_array["END_DAY"] = $allday_end;
				$rrule_array["END"] = "end";
				$recur_start = $allday_start;
			} else {
				$rrule_array["START_DATE"] = $start_date;
				$rrule_array["START_TIME"] = $start_time;
				$rrule_array["END_TIME"] = $end_time;
				$rrule_array["END"] = "end";
			}
			//print_r($rrule_array);
			foreach ($rrule_array as $key => $val) {
				if ($key == "FREQ") {
					if ($val == "YEARLY") {
						$interval = "yyyy";
					} elseif ($val == "MONTHLY") {
						$interval = "m";
					} elseif ($val == "WEEKLY") {
						$interval = "ww";
					} elseif ($val == "DAILY") {
						$interval = "d";
					} elseif ($val == "HOURLY") {
						$interval = "h";
					} elseif ($val == "MINUTELY") {
						$interval = "n";
					} elseif ($val == "SECONDLY") {
						$interval = "s";
					}		
				} elseif ($key == "COUNT") 		{
					$count = $val;
				
				} elseif ($key == "UNTIL") 		{
					$until = $val;
				
				} elseif ($key == "INTERVAL")	{
					$number = $val;
				
				} elseif ($key == "BYSECOND") 	{
					$bysecond = $val;
					$bysecond = split (",", $bysecond);
				
				} elseif ($key == "BYMINUTE") 	{
					$byminute = $val;
					$byminute = split (",", $byminute);
				
				} elseif ($key == "BYHOUR")		{
					$byhour = $val;
					$byhour = split (",", $byhour);
				
				} elseif ($key == "BYDAY") 		{
					$byday = $val;
					$byday = split (",", $byday);
				
				} elseif ($key == "BYMONTHDAY") {
					$bymonthday = $val;
					$bymonthday = split (",", $bymonthday);
					//print_r ($bymonthday);
				
				} elseif ($key == "BYYEARDAY") 	{
					$byyearday = $val;
					$byyearday = split (",", $byyearday);
				
				} elseif ($key == "BYWEEKNO") 	{
					$byweekno = $val;
					$byweekno = split (",", $byweekno);
				
				} elseif ($key == "BYMONTH") 	{
					$bymonth = $val;
					$bymonth = split (",", $bymonth);
				
				} elseif ($key == "BYSETPOS") 	{
					$bysetpos = $val;
				
				} elseif ($key == "WKST") 		{
					$wkst = $val;
				
				} elseif ($key == "END")		{
					
					if ($allday_start != "") {
						
						// Since we hit the end of the RRULE array, lets do something.
						// Below handles yearly all day events only.
						// $parse_to_year is the year we are parsing, January 10th, next year.
						// $start_of_vevent is the date the recurring event starts.
						// $end_of_vevent is the date the recurring event stops.
						 
						$parse_to_year = $this_year + 1;
						$parse_to_year  = mktime(0,0,0,1,10,$parse_to_year);						
						$start_of_vevent = strtotime("$allday_start");
						$end_of_vevent = strtotime("$allday_end");
						//echo "End = $start_of_vevent, $parse_to_year - $summary, $interval, $number<br>";

						if ($start_of_vevent < $parse_to_year) {
							do {
									
								// This steps through each day of a multiple all-day event and adds to master array
								// Every all day event should pass through here at least once.
								$start = $start_of_vevent;
								$end = $end_of_vevent;
								do {
									$start_date = date("Ymd", $start);
									$master_array[($start_date)][("0001")]["event_text"][] = "$summary";
									$start = ($start + (24*3600));
								} while ($start < $end);
								
								$start_of_vevent = DateAdd ($interval,  $number, $start_of_vevent);
								$end_of_vevent = DateAdd ($interval,  $number, $end_of_vevent);
													
							} while ($start_of_vevent < $parse_to_year); 
						}
						//echo "$interval - $number - $date - $recur_year - $this_year";
					
					// Let's take care of recurring events that are not all day events
					// Nothing is here yet, Jared seems to way to play, so I'll let him do these... muahahahaha.
					} else {
					
						// handling weekly events here, maybe it can be more general, but for now it handles weekly only
						if ($rrule_array["FREQ"] == "WEEKLY") {
							// again, $parse_to_year is set to January 10 of the upcoming year
							$parse_to_year_time  = mktime(0,0,0,1,10,($this_year + 1));
							$start_date_time = strtotime($start_date);
							
							// initializing my range. it takes noticeable time to process the entire year so lets only process
							// what we're looking at. We start out initializing for the year, but hopefully we won't do that.
							$start_range_time = $start_date_time;
							$end_range_time = $parse_to_year_time;
							
							// depending on which view we're looking at, we do one month or one week
							// one day is more difficult, I think, so I wrapped that into the week. We'll have to
							// add another case for "year" once that's added.
							if ($current_view == "month") {
								$start_range_time = strtotime("$this_year-$this_month-01");
								$end_range_time = strtotime("+1 month", $start_range_time);
							} else {
								$start_range_time = strtotime("$this_year-$this_month-$this_day");
								$end_range_time = strtotime("+1 weeks", $start_range_time);
							}
							
							// If the $end_range_time is less than the $start_date_time, we may as well forget the whole thing
							// It doesn't do us any good to spend time adding data we aren't even looking at
							// this will prevent the year view from taking way longer than it needs to
							if ($end_range_time >= $start_date_time) {
							
								// if the beginning of our range is less than the start of the item, we may as well set it equal to it
								if ($start_range_time < $start_date_time) $start_range_time = $start_date_time;
					
								// initialze the time we will increment
								$next_range_time = $start_range_time;
								
								// start at the $start_range and go week by week until we hit the end of our range.
								while ($next_range_time >= $start_range_time && $next_range_time <= $end_range_time) {
								
									// loop through the days on which this event happens
									foreach($byday as $day) {
									
										// use my fancy little function to get the date of each day
										$next_date = dateOfWeek(date("Ymd", $next_range_time),$day);
										if (strtotime($next_date) > $start_date_time && !in_array($next_date, $except_dates)) {
											// add it to the array if it passes inspection, it allows the first time to be
											// written by the master data writer (hence the > instead of >=) otherwise we can special case these
											// before, the first one would get entered twice and show up twice
											// $next_date can fall up to a week behind $next_range_time because of how dateOfWeek works
// writes to $master array here				// so we have to check this again. It uses $except_dates so it doesn't add to $master_array
											// on days that have been deleted by the user
											$master_array[($next_date)][($hour.$minute)][] = array ("event_start" => $start_time, "event_text" => $summary, "event_end" => $end_time, "event_length" => $length);
										}
									}
									$next_range_time = strtotime("+1 week", $next_range_time);
								}
							}
	
						} else {
// writes to $master array here
							$master_array[($start_date)][($hour.$minute)][] = array ("event_start" => $start_time, "event_text" => $summary, "event_end" => $end_time, "event_length" => $length);
						}
					}
				}	
			}
		}
	
	// Let's write all the data to the master array
	if ($start_time != "") {
// writes to $master array here
		$master_array[($start_date)][($hour.$minute)][] = array ("event_start" => $start_time, "event_text" => $summary, "event_end" => $end_time, "event_length" => $length);
	}
		

		
		
		
	} else {
		
		$field = "";
		$data = "";
		
		sscanf($line, "%[^:]:%[^\n]", &$field, &$data);
		
		if(strstr($field, "DTSTART;TZID")) {
			$data = ereg_replace("T", "", $data);
			ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})", $data, $regs);
			$year = $regs[1];
			$month = $regs[2];
			$day = $regs[3];
			$hour = $regs[4];
			$minute = $regs[5];
			
			$start_date = $year . $month . $day;
			$start_time = $hour . $minute;

		} elseif (strstr($field, "DTEND;TZID")) {
			$data = ereg_replace("T", "", $data);
			ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})", $data, $regs);
			$year = $regs[1];
			$month = $regs[2];
			$day = $regs[3];
			$hour = $regs[4];
			$minute = $regs[5];
		
			$end_day = $year . $month . $day;
			$end_time = $hour . $minute;
			
		} elseif (strstr($field, "EXDATE;TZID")) {
			$data = ereg_replace("T", "", $data);
			ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})", $data, $regs);
			$year = $regs[1];
			$month = $regs[2];
			$day = $regs[3];
			$hour = $regs[4];
			$minute = $regs[5];
		
			$except_dates[] = $year . $month . $day;
			$except_times[] = $hour . $minute;
			
		} elseif (strstr($field, "SUMMARY")) {
			$summary = $data;
		
		} elseif (strstr($field, "X-WR-CALNAME")) {
			$calendar_name = $data;
		
		} elseif (strstr($field, "DTSTART;VALUE=DATE")) {
			$allday_start = $data;
			// echo "$allday_start";
		
		} elseif (strstr($field, "DTEND;VALUE=DATE")) {
			$allday_end = $data;
			
		} elseif (strstr($field, "DURATION")) {
			ereg ("^P([0-9]{1,2})?([W,D]{0,1})?(T)?([0-9]{1,2})?(H)?([0-9]{1,2})?(M)?([0-9]{1,2})?(S)?", $data, $duration);
			
			if ($duration[2] = "W") {
				$weeks = $duration[1];
			} else {
				$days = $duration[1];
			}
			
			$hours = $duration[4];
			$minutes = $duration[6];
		 	$seconds = $duration[8];
		 	
			$the_duration = ($weeks * 60 * 60 * 24 * 7) + ($days * 60 * 60 * 24) + ($hours * 60 * 60) + ($minutes * 60) + ($seconds);
			$beginning = (strtotime($start_time) + $the_duration);
			$end_time = date ("Hi", $beginning);	
			
		} elseif (strstr($field, "RRULE")) {
			// $data = "RRULE:FREQ=YEARLY;INTERVAL=2;BYMONTH=1;BYDAY=SU;BYHOUR=8,9;BYMINUTE=30";
			$data = ereg_replace ("RRULE:", "", $data);
			$rrule = split (";", $data);
			foreach ($rrule as $recur) {
				ereg ("(.*)=(.*)", $recur, $regs);
				$rrule_array[$regs[1]] = $regs[2];
			}	
		} elseif (strstr($field, "ATTENDEE")) {
			$attendee = $data;
			// echo "$attendee";
			
		}
	}
}
 
ksort($master_array);
reset($master_array);
//If you want to see the values in the arrays, uncomment below.
//print "<pre>";
//print_r($master_array);
//print_r($day_array);
//print_r($rrule);			
//print "</pre>";
	
					
?>