<?php
	
define('BASE', './');
include(BASE.'functions/ical_parser.php');
$cal_displayname2 = $calendar_name . " $calendar_lang";
if (strlen($cal_displayname2) > 24) {
	$cal_displayname2 = substr("$cal_displayname2", 0, 21);
	$cal_displayname2 = $cal_displayname2 . "...";
}

$start_week_time = strtotime(dateOfWeek($getdate, $week_start_day));
$end_week_time = $start_week_time + (6 * 25 * 60 * 60);
$parse_month = date ("Ym", strtotime($getdate));
$printview = $HTTP_GET_VARS['printview'];
$cal_displayname = str_replace("32", " ", $cal);
$events_week = 0;
$unix_time = strtotime("$getdate");

if ($printview == 'day') {
	$print_title = localizeDate ($dateFormat_day, strtotime($getdate));
	$next = date("Ymd", strtotime("+1 day", $unix_time));
	$prev = date("Ymd", strtotime("-1 day", $unix_time));
	$zero_events = $no_events_day_lang;
} elseif ($printview == 'week') {
	$start_week = localizeDate($dateFormat_week, $start_week_time);
	$end_week =  localizeDate($dateFormat_week, $end_week_time);
	$print_title = "$start_week - $end_week";
	$week_start = date("Ymd", $start_week_time);
	$week_end = date("Ymd", $end_week_time);
	$next = date("Ymd", strtotime("+1 week", $unix_time));
	$prev = date("Ymd", strtotime("-1 week", $unix_time));
	$zero_events = $no_events_week_lang;
} elseif ($printview == 'month') {
	$print_title = localizeDate ($dateFormat_month, strtotime($getdate));
	$next = date("Ymd", strtotime("+1 month", $unix_time));
	$prev = date("Ymd", strtotime("-1 month", $unix_time));
	$zero_events = $no_events_month_lang;
}

	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title><?php echo "$calendar_name: $print_title"; ?></title>
  	<link rel="stylesheet" type="text/css" href="styles/<?php echo $style_sheet.'/default.css'; ?>">
</head>
<body bgcolor="#FFFFFF">
<center>
<table border="0" width="700" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" class="calborder">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
      			<tr>
      				<td align="left" width="90" class="navback"><?php echo '<a href="'.$printview.'.php?cal='.$cal.'&getdate='.$getdate.'"><img src="styles/'.$style_sheet.'/back.gif" alt="" border="0" align="left"></a>'; ?></td>
      				<td class="navback">
      					<table width="100%" border="0" cellspacing="0" cellpadding="0">
      						<tr>
								<td align="right" width="40%" class="navback"><?php echo "<a class=\"psf\" href=\"print.php?cal=$cal&getdate=$prev&printview=$printview\"><img src=\"styles/$style_sheet/left_day.gif\" alt=\"\" border=\"0\" align=\"right\"></a>"; ?></td>
								<td align="center" width="20%" class="navback" nowrap valign="middle"><font class="H20"><?php echo $print_title; ?></font></td>
      							<td align="left" width="40%" class="navback"><?php echo "<a class=\"psf\" href=\"print.php?cal=$cal&getdate=$next&printview=$printview\"><img src=\"styles/$style_sheet/right_day.gif\" alt=\"\" border=\"0\" align=\"left\"></a>"; ?></td>
      						</tr>
      					</table>
      				</td>
      				<td align="right" width="90" class="navback">	
      					<table width="90" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td><?php echo '<a class="psf" href="print.php?cal='.$cal.'&getdate='.$getdate.'&printview=day"><img src="styles/'.$style_sheet.'/day_on.gif" alt="" border="0"></td>'; ?>
								<td><?php echo '<a class="psf" href="print.php?cal='.$cal.'&getdate='.$getdate.'&printview=week"><img src="styles/'.$style_sheet.'/week_on.gif" alt="" border="0"></td>'; ?>
								<td><?php echo '<a class="psf" href="print.php?cal='.$cal.'&getdate='.$getdate.'&printview=month"><img src="styles/'.$style_sheet.'/month_on.gif" alt="" border="0"></td>'; ?>
							</tr>
						</table>
					</td>
      			</tr>
      		</table>
      	</td>
    </tr>
	<tr>
		<td colspan="3"><img src="images/spacer.gif" width="1" height="5"></td>
	</tr>
	<tr>
		<td colspan="3">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td align="center" valign="top">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td colspan="3" height="1"></td>
								</tr>
								<?php	
									// Iterate the entire master array
									foreach($master_array as $key => $val) {
										
										// Pull out only this months
										ereg ("([0-9]{6})([0-9]{2})", $key, $regs);
										if ((($regs[1] == $parse_month) && ($printview == "month")) || (($key == $getdate) && ($printview == "day")) || ((($key >= $week_start) && ($key <= $week_end)) && ($printview == "week"))) {
											$events_week++;
											$dayofmonth = strtotime ($key);
											$dayofmonth = localizeDate ($dateFormat_day, $dayofmonth);
											echo "<tr><td width=\"10\"><img src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>\n";
											echo "<td align=\"left\" colspan=\"2\"><font class=\"V12\"><b>$dayofmonth</b></font></td></tr>";
											echo "<tr><td colspan=\"3\"><img src=\"images/spacer.gif\" width=\"1\" height=\"5\"></td></tr>\n";
											
											// Pull out each day
											foreach ($val as $new_val) {
												
												// Pull out each time
												foreach ($new_val as $new_key2 => $new_val2) {
												if ($new_val2["event_text"]) {	
													$event_text 	= stripslashes(urldecode($new_val2["event_text"]));
													$description 	= stripslashes(urldecode($new_val2["description"]));
													$event_start 	= $new_val2["event_start"];
													$event_end 		= $new_val2["event_end"];
													$event_start 	= date ($timeFormat, strtotime ("$event_start"));
													$event_end 		= date ($timeFormat, strtotime ("$event_end"));
													$event_start 	= "$event_start - $event_end";
													if (!$new_val2["event_start"]) { 
														$event_start = "$all_day_lang";
														$event_start2 = '';
														$event_end = '';
													}
													echo "<tr>\n";
													echo "<td width=\"10\"><img src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>\n";
													echo "<td width=\"10\"><img src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>\n";
													echo "<td align=\"left\">\n";
													echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">\n";
													echo "<tr>\n";
													echo "<td width=\"100\" class=\"G10BOLD\">$time_lang:</td>\n";
													echo "<td align=\"left\" class=\"G10B\">$event_start</td>\n";
													echo "</tr>\n";
													echo "<tr>\n";
													echo "<td valign=\"top\" width=\"100\" class=\"G10BOLD\">$summary_lang:</td>\n";
													echo "<td valign=\"top\" align=\"left\" class=\"G10B\">$event_text</td>\n";
													echo "</tr>\n";
													if ($new_val2["description"]) {
														echo "<tr>\n";
														echo "<td valign=\"top\" width=\"100\" class=\"G10BOLD\">$description_lang:</td>\n";
														echo "<td valign=\"top\" align=\"left\" class=\"G10B\">$description</td>\n";
														echo "</tr>\n";
													}
													echo "</table>\n";
													echo "</td>\n";
													echo "</tr>\n";			
													echo "<tr><td colspan=\"3\"><img src=\"images/spacer.gif\" width=\"1\" height=\"10\"></td></tr>\n";
													}
												}
											}
										}
									}
									
									if ($events_week < 1) {
										echo "<tr><td width=\"10\"><img src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>\n";
										echo "<td align=\"left\" colspan=\"2\"><font class=\"V12\"><br><center><b>$zero_events</b></center></font><br></td></tr>";
										echo "<tr><td colspan=\"3\"><img src=\"images/spacer.gif\" width=\"1\" height=\"5\"></td></tr>\n";
									}
								
								?>
						</table>
					</td>
				</tr>
			</table>		
		</td>
	</tr>
</table>
<?php include (BASE.'footer.inc.php'); ?>