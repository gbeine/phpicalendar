<?$day_array = array ("0700", "0730", "0800", "0830", "0900", "0930", "1000", "1030", "1100", "1130", "1200", "1230", "1300", "1330", "1400", "1430", "1500", "1530", "1600", "1630", "1700", "1730", "1800", "1830", "1900", "1930", "2000", "2030", "2100", "2130", "2200", "2230", "2300", "2330");// what date we want to get data for (for day calendar)if (!$getdate) $getdate = date("Ymd");ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);$this_day = $day_array2[3];$this_month = $day_array2[2];$this_year = $day_array2[1];// open the iCal file, read it into a string$fp = fopen($filename, "r");$contents = fread ($fp, filesize ($filename));fclose ($fp);// turn that string into an array$contents = ereg_replace("\n ", "", $contents);$contents = split ("\n", $contents);// parse our new arrayforeach($contents as $line) {	if (strstr($line, "BEGIN:VEVENT")) {		$start_time = "";		$end_time = "";		$summary = "";		$allday_start = "";		$allday_end = "";		$start = "";		$end = "";		$the_duration = "";		$beginning = "";		$rrule_array = "";	} elseif (strstr($line, "END:VEVENT")) {				//echo "<b>Start</b> $start_time <b>End</B> $end_time <b>Summary</b> $summary<br>\n";		if ($start_time != "") {			ereg ("([0-9]{2})([0-9]{2})", $start_time, $time);			$hour = $time[1];			$minute = $time[2];									if ($minute < 15) {				$minute = "00";			} elseif ($minute >=15 && $minute < 45) {				$minute = "30";			} elseif ($minute >= 45) {				$hour = sprintf("%.02d", ($hour + 1));				$minute = "00";			}			ereg ("([0-9]{2})([0-9]{2})", $end_time, $time2);			$length = round((($time2[1]*60+$time2[2]) - ($time[1]*60+$time[2]))/30);		}						// Handling of the all day events			if ($allday_start != "") {			$start = strtotime("$allday_start");			$end = strtotime("$allday_end");			do {				$start_date = date("Ymd", $start);				$master_array[($start_date)][("0001")]["event_text"][] = "$summary";				$i++;				$start = ($start + (24*3600));			} while ($start != $end);		}						// Handling of the recurring events, RRULE		if (is_array($rrule_array)) {			if ($allday_start != "") {				$rrule_array["START_DAY"] = $allday_start;			} else {				$rrule_array["START_TIME"] = $start_time;				$rrule_array["END_TIME"] = $end_time;			}			// print_r($rrule_array);			foreach ($rrule_array as $key => $val) {				if ($key["FREQ"]) {					if ($val == "YEARLY") {					$freq_time = strtotime("+1 year");										} elseif ($val == "MONTHLY") {					$freq_time = ($val * 60 * 60 * 24 * 7);										} elseif ($val == "WEEKLY") {					$freq_time = ($val * 60 * 60 * 24 * 7);										} elseif ($val == "DAILY") {					$freq_time = ($val * 60 * 60 * 24);										} elseif ($val == "HOURLY") {					$freq_time = ($val * 60 * 60);										} elseif ($val == "MINUTELY") {					$freq_time = ($val * 60);										} elseif ($val == "SECONDLY") {					$freq_time = ($val);										}						// echo "$freq_time";									} elseif ($key["COUNT"]) 		{					$count = $val;				} elseif ($key["UNTIL"]) 		{					$until = $val;				} elseif ($key["INTERVAL"])		{					$interval = $val;				} elseif ($key["BYSECOND"]) 	{					$bysecond = $val;				} elseif ($key["BYMINUTE"]) 	{					$byminute = $val;				} elseif ($key["BYHOUR"])		{					$byhour = $val;				} elseif ($key["BYDAY"]) 		{					$byday = $val;				} elseif ($key["BYMONTHDAY"]) 	{					$bymonthday = $val;				} elseif ($key["BYYEARDAY"]) 	{					$byyearday = $val;				} elseif ($key["BYWEEKNO"]) 	{					$byweekno = $val;				} elseif ($key["BYMONTH"]) 		{					$bymonth = $val;				} elseif ($key["BYSETPOS"]) 	{					$bysetpos = $val;				} elseif ($key["WKST"]) 		{					$wkst = $val;				}			}		}		// Let's write all the data to the master array	if ($start_time != "") {		$master_array[($start_date)][($hour.$minute)][] = array ("event_start" => $start_time, "event_text" => $summary, "event_end" => $end_time, "event_length" => $length);	}									} else {				$field = "";		$data = "";				sscanf($line, "%[^:]:%[^\n]", &$field, &$data);				if(strstr($field, "DTSTART;TZID")) {			$data = ereg_replace("T", "", $data);			ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})", $data, $regs);			$year = $regs[1];			$month = $regs[2];			$day = $regs[3];			$hour = $regs[4];			$minute = $regs[5];						$start_date = $year . $month . $day;			$start_time = $hour . $minute;		} elseif (strstr($field, "DTEND;TZID")) {			$data = ereg_replace("T", "", $data);			ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})", $data, $regs);			$year = $regs[1];			$month = $regs[2];			$day = $regs[3];			$hour = $regs[4];			$minute = $regs[5];					$end_day = $year . $month . $day;			$end_time = $hour . $minute;		} elseif (strstr($field, "SUMMARY")) {			$summary = $data;				} elseif (strstr($field, "X-WR-CALNAME")) {			$calendar_name = $data;				} elseif (strstr($field, "DTSTART;VALUE=DATE")) {			$allday_start = $data;			// echo "$allday_start";				} elseif (strstr($field, "DTEND;VALUE=DATE")) {			$allday_end = $data;					} elseif (strstr($field, "DURATION")) {			ereg ("^P([0-9]{1,2})?([W,D]{0,1})?(T)?([0-9]{1,2})?(H)?([0-9]{1,2})?(M)?([0-9]{1,2})?(S)?", $data, $duration);						if ($durartion[2] = "W") {				$weeks = $durartion[1];			} else {				$days = $durartion[1];			}						$hours = $duration[4];			$minutes = $duration[6];		 	$seconds = $duration[8];		 				$the_duration = ($weeks * 60 * 60 * 24 * 7) + ($days * 60 * 60 * 24) + ($hours * 60 * 60) + ($minutes * 60) + ($seconds);			$beginning = (strtotime($start_time) + $the_duration);			$end_time = date ("Hi", $beginning);						} elseif (strstr($field, "RRULE")) {			// $data = "RRULE:FREQ=YEARLY;INTERVAL=2;BYMONTH=1;BYDAY=SU;BYHOUR=8,9;BYMINUTE=30";			$data = ereg_replace ("RRULE:", "", $data);			$rrule = split (";", $data);			foreach ($rrule as $recur) {				ereg ("(.*)=(.*)", $recur, $regs);				$rrule_array[$regs[1]] = $regs[2];			}			} elseif (strstr($field, "ATTENDEE")) {			$attendee = $data;			// echo "$attendee";					}	}} // If you want to see the values in the arrays, uncomment below.// print_r($master_array);// print_r($day_array);// print_r($rrule);												?>