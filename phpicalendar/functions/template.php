<?php

//print_r($master_array);



class Page {
	var $page;
	function draw_month($template, $offset = '+0', $type) {
		global $getdate, $master_array, $this_year, $this_month, $dateFormat_month, $week_start_day, $cal, $minical_view, $daysofweekreallyshort_lang, $daysofweek_lang, $timeFormat_small, $timeFormat;
		preg_match("!<\!-- loop weekday on -->(.*)<\!-- loop weekday off -->!is", $template, $match1);
		preg_match("!<\!-- loop monthdays on -->(.*)<\!-- loop monthdays off -->!is", $template, $match2);
		preg_match("!<\!-- loop monthweeks on -->(.*)<\!-- loop monthdays on -->!is", $template, $match6);
		preg_match("!<\!-- loop monthdays off -->(.*)<\!-- loop monthweeks off -->!is", $template, $match7);
		
		$loop_wd 			= trim($match1[1]);
		$loop_md 			= trim($match2[1]);
		$startweek 			= trim($match6[1]);
		$endweek 			= trim($match7[1]);
		$fake_getdate_time 	= strtotime($this_year.'-'.$this_month.'-15');
		$fake_getdate_time	= strtotime("$offset month", $fake_getdate_time);
		$start_day 			= strtotime($week_start_day);
		$month_title 		= localizeDate ($dateFormat_month, $fake_getdate_time);
		if ($type == 'small') {
			$type = $daysofweekreallyshort_lang;
		} elseif ($type == 'medium') {
			$type = $daysofweekshort_lang;
		} elseif ($type == 'large') {
			$type = $daysofweek_lang;	
		}
		
		for ($i=0; $i<7; $i++) {
			$day_num 		= date("w", $start_day);
			$weekday 		= $type[$day_num];
			$start_day 		= strtotime("+1 day", $start_day);
			$loop_tmp 		= str_replace('{LOOP_WEEKDAY}', $weekday, $loop_wd);
			$weekday_loop  .= $loop_tmp;
		}
		
		$minical_month 		= date("m", $fake_getdate_time);
		$minical_year 		= date("Y", $fake_getdate_time);
		$first_of_month 	= $minical_year.$minical_month."01";
		$start_day 			= strtotime(dateOfWeek($first_of_month, $week_start_day));
		$i 					= 0;
		$whole_month 		= TRUE;
		
		do {
			if ($i == 0) $middle .= $startweek; $i++;
			$temp_middle			= $loop_md;
			$switch					= array('notthismonth' => '', 'istoday' => '', 'ismonth' => '', 'noevent' => '', 'anyevent' => '', 'allday' => '', 'cal' => $cal, 'minical_view' => $minical_view);
			$check_month 			= date ("m", $start_day);
			$daylink 				= date ("Ymd", $start_day);
			$switch['day']	 		= date ("j", $start_day);
			$switch['daylink'] 		= date ("Ymd", $start_day);
			$switch['notthismonth'] = ($check_month != $minical_month) ? 'set' : '';
			$switch['istoday'] 		= ($switch['daylink'] == $getdate) ? 'set' : '';
			$switch['ismonth'] 		= (($switch['istoday'] == '') && ($switch['notthismonth'] == '')) ? 'set' : '';
			if ($master_array[$daylink]) {
				$switch['anyevent'] = 'set';
				foreach ($master_array[$daylink] as $event_times) {
					foreach ($event_times as $val) {
						$event_calno 	= $val['calnumber'];
						$event_calna 	= $val['calname'];
						$event_url 		= $val['url'];
						if (!isset($val['event_start'])) {
							$switch['allday'] .= '<div align="left" class="V10">';
							$switch['allday'] .= openevent($event_calna, '', '', $val, $month_event_lines, 15, '', '', 'psf', $event_url);
							$switch['allday'] .= '</div>';
						} else {	
							$event_start = $val['start_unixtime'];
							$event_end 	 = (isset($val['display_end'])) ? $val['display_end'] : $val["event_end"];
							$event_start = date($timeFormat, $val['start_unixtime']);
							$start2		 = date($timeFormat_small, $val['start_unixtime']);
							$event_end   = date($timeFormat, @strtotime ($event_end));
							$switch['event'] .= '<div align="left" class="V9">';
							$switch['event'] .= openevent($event_calna, $event_start, $event_end, $val, $month_event_lines, 10, "$start2 ", '', 'ps3', $event_url);
							$switch['event'] .= '</div>';
						}
					}
				}
			}
			
			foreach ($switch as $tag => $data) {
				if (!$data) {
					$temp_middle = ereg_replace('<!-- switch ' . $tag . ' on -->(.*)<!-- switch ' . $tag . ' off -->', '', $temp_middle);
				} else {
					$temp_middle = str_replace('{' . strtoupper($tag) . '}', $data, $temp_middle);
				}
			}
			$middle .= $temp_middle;
			
			$start_day = strtotime("+1 day", $start_day); 
			if ($i == 7) { 
				$i = 0;
				$middle .= $endweek;
				$checkagain = date ("m", $start_day);
				if ($checkagain != $minical_month) $whole_month = FALSE;	
			}
		} while ($whole_month == TRUE);
		
		$return = preg_replace('!<\!-- loop weekday on -->(.*)<\!-- loop weekday off -->!is', $weekday_loop, $template);
		$return = ereg_replace('<!-- loop monthweeks on -->(.*)<!-- loop monthweeks off -->', $middle, $return);
		$return = str_replace('{MONTH_TITLE}', $month_title, $return);
		
		return $return;
		
	}
	
	
	function Page($template = 'std.tpl') {
		if (file_exists($template))
			$this->page = join('', file($template));
		else
			die("Template file $template not found.");
		}

	function parse($file) {
		ob_start();
		include($file);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
	
	function replace_tags($tags = array()) {
		if (sizeof($tags) > 0)
			foreach ($tags as $tag => $data) {
				
				// This opens up another template and parses it as well.
				$data = (file_exists($data)) ? $this->parse($data) : $data;
				
				// This removes any unfilled tags
				if (!$data) {
					$this->page = ereg_replace('<!-- switch ' . $tag . ' on -->(.*)<!-- switch ' . $tag . ' off -->', '', $this->page);
				}
				
				// This replaces any tags
				$this->page = str_replace('{' . strtoupper($tag) . '}', $data, $this->page);
			}
			
		else
			die('No tags designated for replacement.');
		}
	
	function output() {
		global $template, $php_started, $lang;
		// Small month builder
		preg_match_all ('!(\{MONTH_SMALL\|[+|-][0-9]\})!is', $this->page, $match);
		if (sizeof($match) > 0) {
			$template_file = $this->parse('templates/'.$template.'/month_small.tpl');
			foreach ($match[1] as $key => $val) {
				$offset = str_replace('}', '', $val);
				$offset = str_replace('{MONTH_SMALL|', '', $offset);
				$data = $this->draw_month($template_file, $offset, 'small');
				$this->page = str_replace($val, $data, $this->page);
			}
		}
		
		// Small month builder
		preg_match_all ('!(\{MONTH_LARGE\|[+|-][0-9]\})!is', $this->page, $match);
		if (sizeof($match) > 0) {
			$template_file = $this->parse('templates/'.$template.'/month_large.tpl');
			foreach ($match[1] as $key => $val) {
				$offset = str_replace('}', '', $val);
				$offset = str_replace('{MONTH_LARGE|', '', $offset);
				$data = $this->draw_month($template_file, $offset, 'large');
				$this->page = str_replace($val, $data, $this->page);
			}
		}
		foreach ($lang as $tag => $data) {
			$this->page = str_replace('{' . strtoupper($tag) . '}', $data, $this->page);
		}
		
		$php_ended = getmicrotime();
		$generated = number_format(($php_ended-$php_started),3);
		echo $generated;
		print($this->page);
	}
}
?> 