<?php 

$current_view = "month";
define('BASE', './');
include(BASE.'functions/ical_parser.php');
if ($minical_view == 'current') $minical_view = 'month';

ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);
$this_day = $day_array2[3]; 
$this_month = $day_array2[2];
$this_year = $day_array2[1];

$unix_time = strtotime($getdate);
$today_today = date('Ymd', strtotime("now + $second_offset seconds")); 
$tomorrows_date = date( "Ymd", strtotime("+1 day",  $unix_time));
$yesterdays_date = date( "Ymd", strtotime("-1 day",  $unix_time));
$date = mktime(0,0,0,"$this_month","$this_day","$this_year");

// find out next month
$next_month_month = ($this_month+1 == '13') ? '1' : ($this_month+1);
$next_month_day = $this_day;
$next_month_year = ($next_month_month == '1') ? ($this_year+1) : $this_year;
while (!checkdate($next_month_month,$next_month_day,$next_month_year)) $next_month_day--;
$next_month_time = mktime(0,0,0,$next_month_month,$next_month_day,$next_month_year);

// find out last month
$prev_month_month = ($this_month-1 == '0') ? '12' : ($this_month-1);
$prev_month_day = $this_day;
$prev_month_year = ($prev_month_month == '12') ? ($this_year-1) : $this_year;
while (!checkdate($prev_month_month,$prev_month_day,$prev_month_year)) $prev_month_day--;
$prev_month_time = mktime(0,0,0,$prev_month_month,$prev_month_day,$prev_month_year);


$next_month = date("Ymd", $next_month_time);
$prev_month = date("Ymd", $prev_month_time);

$display_month = localizeDate ($dateFormat_month, $date);
$parse_month = date ("Ym", $date);
$first_of_month = $this_year.$this_month."01";
$start_month_day = dateOfWeek($first_of_month, $week_start_day);
$thisday2 = localizeDate($dateFormat_week_list, $unix_time);
$num_of_events = 0;


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title><?php echo "$calendar_name - $display_month"; ?></title>
	<link rel="stylesheet" type="text/css" href="styles/<?php echo "$style_sheet/default.css"; ?>">
   <?php 
		// if RSS is enabled, set the RSS auto-discovery link
		if ($enable_rss == 'yes') 
		{
    		echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".$default_path."/rss/rss.php?cal=".$cal."&amp;rssview=month\">";
		} 
	?>
</head>
<body>
<?php include (BASE.'includes/header.inc.php'); ?>
<center>
<table width="735" border="0" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td align="center" valign="middle" bgcolor="white">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
      			<tr>
      				<td align="left" width="120" class="navback">&nbsp;</td>
      				<td class="navback">
      					<table width="100%" border="0" cellspacing="0" cellpadding="0">
      						<tr>
								<td align="right" width="40%" class="navback"><?php echo "<a class=\"psf\" href=\"month.php?cal=$cal&amp;getdate=$prev_month\"><img src=\"styles/$style_sheet/left_day.gif\" alt=\"[$last_month_lang]\" border=\"0\" align=\"right\"></a>"; ?></td>
								<td align="center" width="20%" class="navback" nowrap valign="middle"><font class="H20"><?php echo $display_month; ?></font></td>
      							<td align="left" width="40%" class="navback"><?php echo "<a class=\"psf\" href=\"month.php?cal=$cal&amp;getdate=$next_month\"><img src=\"styles/$style_sheet/right_day.gif\" alt=\"[$next_month_lang]\" border=\"0\" align=\"left\"></a>"; ?></td>
      						</tr>
      					</table>
      				</td>
      				<td align="right" width="120" class="navback">	
      					<table width="120" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td><?php echo '<a class="psf" href="day.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="styles/'.$style_sheet.'/day_on.gif" alt="'.$day_view_lang.'" border="0"></a></td>'; ?>
								<td><?php echo '<a class="psf" href="week.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="styles/'.$style_sheet.'/week_on.gif" alt="'.$week_view_lang.'" border="0"></a></td>'; ?>
								<td><?php echo '<a class="psf" href="month.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="styles/'.$style_sheet.'/month_on.gif" alt="'.$month_view_lang.'" border="0"></a></td>'; ?>
								<td><?php echo '<a class="psf" href="year.php?cal='.$cal.'&amp;getdate='.$getdate.'"><img src="styles/'.$style_sheet.'/year_on.gif" alt="'.$year_view_lang.'" border="0"></a></td>'; ?>
							</tr>
						</table>
					</td>
      			</tr>
      		</table>
		</td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<table width="100%" border="0" cellspacing="1" cellpadding="2" class="monthback">
				<tr>
					<?php
						// loops through 7 times, starts with $week_start_day
						$start_day = strtotime($week_start_day);
						for ($i=0; $i<7; $i++) {
							$day_num = date("w", $start_day);
							$day = $daysofweek_lang[$day_num];
							echo '<td valign="top" width="105" height="12" class="dateback"><center class="V9BOLD">'.$day.'</center></td>';
							$start_day = strtotime("+1 day", $start_day);
						}
					?>	
				</tr>
				<tr>	
				<?php 	 
					$sunday 		= strtotime("$start_month_day");
					$i 				= 0;
					$whole_month 	= TRUE;
					$num_of_events 	= 0;
					do {
						$day 			= date ("j", $sunday);
						$daylink 		= date ("Ymd", $sunday);
						$check_month 	= date ("m", $sunday);
						if ($check_month != $this_month) {
							$day		= '<font class="G10G">'.$day.'</font>';
							$bgclass	= 'class="monthoff"';
						} else {
							if ($today_today == $daylink) {
								$bgclass = 'class="monthon"';
							} else {
								$bgclass = 'class="monthreg"';
							}
						}
						if ($i == 0) echo '<tr height="105">';
						echo '<td valign="top" align="left" '.$bgclass.' width="105" height="105">';
						if (isset($master_array[("$daylink")])) {
							echo '<div align="right"><font class="G10"><a class="psf" href="day.php?cal='.$cal.'&amp;getdate='.$daylink.'">'.$day.'</a></font></div>';
							echo '<div align="left">';
							if ($master_array[("$daylink")]) {
								foreach ($master_array[("$daylink")] as $event_times) {
									foreach ($event_times as $val) {
										$num_of_events++;
										if (!isset($val["event_start"])) {
											 openevent("$calendar_name", "", "", $val, $month_event_lines,
											15,
											"<div align=\"center\" class=\"V10\"><i>",
											"</i></div>",
											"psf");
										} else {	
											$event_start = @$val["event_start"];
											$event_end   = @$val["event_end"];
											$event_start = date($timeFormat, @strtotime ("$event_start"));
											$start2		 = date($timeFormat_small,@strtotime("$event_start"));
											$event_end   = date($timeFormat, @strtotime ("$event_end"));
											@openevent("$calendar_name",
											"$event_start",
											"$event_end",
											$val,
											$month_event_lines,
											11,
											"<div align=\"left\" class=\"V9\">&nbsp;$start2 ",
											"</div>",
											"ps3");
										}
									}
								}
							}
							echo '</td>';
						} else {
							echo '<td align="center" valign="top" '.$bgclass.' width="105" height="105">';
							echo '<a class="psf" href="day.php?cal='.$cal.'&amp;getdate='.$daylink.'">'.$day.'</a>';
							echo '</td>';
						}
						$sunday = strtotime("+1 day", $sunday); 
						$i++;
						if ($i == 7) { 
							echo '</tr>';
							$i = 0;
							$checkagain = date ("m", $sunday);
							if ($checkagain != $this_month) $whole_month = FALSE;	
						}
					} while ($whole_month == TRUE);
				?>
			</table>
		</td>
	</tr>
</table>
<?php include (BASE.'includes/calendar_nav.php'); ?>
<?php if ($this_months_events == "yes") { ?>	
<br>
<table border="0" cellspacing="0" cellpadding="0" width="737" bgcolor="#FFFFFF" class="calborder">
	<tr>
		<td align="center" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" valign="top" width="160" class="sideback"><?php echo "<img src=\"images/spacer.gif\" alt=\" \" width=\"16\" height=\"20\" border=\"0\" align=\"left\">"; ?></td>
					<td align="center" class="sideback" width="417" nowrap><font class="G10BOLD"><?php echo "$this_months_lang"; ?></font></td>
					<td align="right" valign="top" width="160" class="sideback"><?php echo "<img src=\"images/spacer.gif\" alt=\" \" width=\"16\" height=\"20\" border=\"0\" align=\"right\">"; ?></td>
				</tr>
				<tr>
					<td colspan="3" height="1"></td>
				</tr>
				<?php	
					// Iterate the entire master array
					foreach($master_array as $key => $val) {
						
						// Pull out only this months
						ereg ("([0-9]{6})([0-9]{2})", $key, $regs);
						if ($regs[1] == $parse_month) {
							$dayofmonth = strtotime ($key);
							$dayofmonth = localizeDate ($dateFormat_week_list, $dayofmonth);
							$i = 0;
							if ($today_today == $key) {
								$fontclass="class=\"G10BOLD\"";
							} else {
								$fontclass="class=\"G10B\"";
							}
							
							// Pull out each day
							foreach ($val as $new_val) {
								
								// Pull out each time
								foreach ($new_val as $new_key2 => $new_val2) {
								if ($new_val2["event_text"]) {	
									if (isset($new_val2["event_start"])) {
										$event_start 	= $new_val2["event_start"];
										$event_end 		= $new_val2["event_end"];
										$event_start 	= date ($timeFormat, strtotime ("$event_start"));
										$event_end 		= date ($timeFormat, strtotime ("$event_end"));
										$event_start2	= $event_start;
									} else {
										$event_start = "$all_day_lang";
										$event_start2 = '';
										$event_end = '';													
									}
		
									echo "<tr>\n";
									echo "<td align=\"left\" valign=\"top\" width =\"160\" class=\"montheventline\" nowrap><font $fontclass>&nbsp;<a class=\"psf\" href=\"day.php?cal=$cal&amp;getdate=$key\">$dayofmonth</a></font> <font class=\"V9G\">($event_start)</font></td>\n";
									echo "<td align=\"left\" valign=\"top\" colspan=\"2\">\n";
									openevent("$calendar_name",
									"$event_start",
									"$event_end",
									$new_val2,
									0,
									65,
									"<font class=\"G10B\">&nbsp;",
									"</font>",
									"psf");
									//echo "&nbsp;<a class=\"psf\" href=\"javascript:openEventInfo('$event_text2', '$calendar_name', '$event_start2', '$event_end', '$description', '$status')\"><font class=\"G10B\">$event_text</font></a>\n";
									echo "</td>\n";
									echo "</tr>\n";
								}

								}
							}
						}
					}
				
				?>
			</table>
		</td>
	</tr>
</table>		
<?php } ?>
<?php include (BASE.'includes/footer.inc.php'); ?>
</center>
</body>
</html>
