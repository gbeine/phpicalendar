<?php

$current_view = "week";
include("./ical_parser.php");

//if ($use_sessions == "yes") {
//	session_start();
//	if (is_array($aArray)) $master_array = $aArray;
//	echo "using sessions";
//}

$starttime = "0500";
$weekstart = 1;
// dpr 20020926: moved variable gridLength to config.inc.php
//$gridLength = 30;
$unix_time = strtotime($getdate);
$today_today = date ("Ymd");
$next_week = date("Ymd", strtotime("+1 week",  $unix_time));
$prev_week = date("Ymd", strtotime("-1 week",  $unix_time));

$start_week_time = strtotime(dateOfWeek($getdate, $week_start_day));
$end_week_time = $start_week_time + (6 * 25 * 60 * 60);

$start_week = strftime($dateFormat_week, $start_week_time);
$end_week =  strftime($dateFormat_week, $end_week_time);

$display_date = "$start_week - $end_week";


// For the side months
ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);
$this_day = $day_array2[3]; 
$this_month = $day_array2[2];
$this_year = $day_array2[1];

$date = strtotime($getdate);
$month1 = date("m", DateAdd ("m", "-1", $date));
$month2 = date("m", $date);
$month3 = date("m", DateAdd ("m", "+1", $date));
$year1 = date("Y", DateAdd ("m", "-1", $date));
$year2 = date("Y", $date);
$year3 = date("Y", DateAdd ("m", "+1", $date));
$first_sunday1 = sundayOfWeek($year1, $month1, "1");
$first_sunday2 = sundayOfWeek($year2, $month2, "1");
$first_sunday3 = sundayOfWeek($year3, $month3, "1");
$display_month1 = strftime ($dateFormat_month, strtotime("-1 month", $date));
$display_month2 = strftime ($dateFormat_month, $date);
$display_month3 = strftime ($dateFormat_month, strtotime("+1 month", $date));
$parse_month = date ("Ym", $date);
$thisday2 = strftime($dateFormat_week_list, $date);

$dayborder = 0;

$thisdate = $start_week_time;
for ($i=0;$i<7;$i++) {
	$thisday = date("Ymd", $thisdate);
	$nbrGridCols[$thisday] = 1;
	if ($master_array[($thisday)]) {
		foreach($master_array[($thisday)] as $ovlKey => $ovlValue) {
			if ($ovlKey != "-1") {
				foreach($ovlValue as $ovl2Value) {
					$nbrGridCols[$thisday] = kgv($nbrGridCols[$thisday], ($ovl2Value["event_overlap"] + 1));
				}
			}
		} 
	}
	$thisdate = ($thisdate + (25 * 60 * 60));
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title><?php echo "$calendar_name"; ?></title>
  	<link rel="stylesheet" type="text/css" href="styles/<?php echo "$style_sheet"; ?>">
	<?php include "functions/event.js"; ?>
</head>
<body bgcolor="#FFFFFF">
<center>
<table border="0" width="720" cellspacing="0" cellpadding="0">
	<tr>
		<td width="540" valign="top">
<table width="540" border="0" cellspacing="0" cellpadding="0" class="calborder">
    <tr>
     	<td align="center" valign="middle">
      		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="G10B">
      			<tr>
      				<td width="75" background="images/time_bg.gif"><?php echo "<a class=\"psf\" href=\"week.php?cal=$cal&getdate=$prev_week\"><img src=\"images/left_day.gif\" alt=\"\" width=\"28\" height=\"28\" border=\"0\" align=\"left\"></a>"; ?></td>
					<td class="H20" align="center" bgcolor="#DDDDDD" background="images/time_bg.gif"><?php echo "$display_date"; ?></td>
      				<td width="75" background="images/time_bg.gif"><?php echo "<a class=\"psf\" href=\"week.php?cal=$cal&getdate=$next_week\"><img src=\"images/right_day.gif\" alt=\"\" width=\"28\" height=\"28\" border=\"0\" align=\"right\"></a>"; ?></td>
      			</tr>
      			<tr>
					<td align="center" valign="top" colspan="3">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="60"><img src="images/spacer.gif" width="60" height="1" alt=""></td>
								<td width="1"></td>
								<td width="70"><img src="images/spacer.gif" width="70" height="1" alt=""></td>
								<td width="70"><img src="images/spacer.gif" width="70" height="1" alt=""></td>
								<td width="70"><img src="images/spacer.gif" width="70" height="1" alt=""></td>
								<td width="70"><img src="images/spacer.gif" width="70" height="1" alt=""></td>
								<td width="70"><img src="images/spacer.gif" width="70" height="1" alt=""></td>
								<td width="70"><img src="images/spacer.gif" width="70" height="1" alt=""></td>
								<td width="70"><img src="images/spacer.gif" width="70" height="1" alt=""></td>
							</tr>
							<?php
							
							// print out the day names here
							echo "<tr>";
							$thisdate = $start_week_time;
							$i = 0;
							echo "<td bgcolor=\"#eeeeee\" width=\"60\"><img src=\"images/spacer.gif\" width=\"1\" height=\"12\" alt=\"\"></td>";
							echo "<td bgcolor=\"#eeeeee\" width=\"1\"></td>";
							do {
								$thisday = date("Ymd", $thisdate);
								$thisday2 = strftime($dateFormat_week_list, $thisdate);
								echo "<td width=\"70\" colspan=\"" . $nbrGridCols[$thisday] . "\" valign=\"top\" align=\"center\" bgcolor=\"#eeeeee\" class=\"V9\">\n";
								echo "<a class=\"psf\" href=\"day.php?cal=$cal&getdate=$thisday\">$thisday2</a>\n";
								echo "</td>\n";
								$thisdate = ($thisdate + (25 * 60 * 60));
								$i++;
							} while ($i < 7);
							echo "</tr>";
							
							// The all day events returned here.
							$thisdate = $start_week_time;
							$i = 0;
							echo "<tr>\n";
							echo "<td bgcolor=\"#f5f5f5\" colspan=\"2\"></td>";
					//		echo "<td  height=\"11\" width=\"60\" nowrap bgcolor=\"#f5f5f5\">&nbsp;</td>";
							do {
								$thisday = date("Ymd", $thisdate);

								if ($master_array[($thisday)]["-1"]) {
									echo "<td  height=\"24\">\n";
									echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"4\">\n";
									foreach($master_array[($thisday)]["-1"] as $allday) {
										$all_day_text = $allday["event_text"];
										$description = $allday["description"];
										echo "<tr>\n";
										echo "<td colspan=\"" . $nbrGridCols[$thisday] . "\" valign=\"top\" align=\"center\" bgcolor=\"#6699CC\"><a class=\"psf\" href=\"javascript:openEventInfo('$event_text2', '$calendar_name', '$event_start', '$event_end', '$description')\"><font color=\"#ffffff\"><i>$all_day_text</i></font></a></td>\n";
										echo "</tr>\n";
									}
									echo "</table>\n";
									echo "</td>\n";
								}
								$thisdate = ($thisdate + (25 * 60 * 60));
								$i++;
							} while ($i < 7);
							echo "</tr>\n";
							
								// $master_array[($getdate)]["$day_time"]
								
								$thisdate = $start_week_time;
								for ($i=0;$i<7;$i++) {
									$thisday = date("Ymd", $thisdate);
									$event_length[$thisday] = array ();
									$thisdate = ($thisdate + (25 * 60 * 60));
								}
								foreach ($day_array as $key) {
									$cal_time = $key;	
									$key = strtotime ("$key");
									$key = date ($timeFormat, $key);
																		

									if (ereg("([0-9]{1,2}):00", $key)) {
										echo "<tr height=\"" . $gridLength . "\">\n";
										echo "<td rowspan=\"" . (60 / $gridLength) . "\" align=\"center\" valign=\"top\" background=\"images/time_bg.gif\" width=\"60\" class=\"timeborder\">$key</td>\n";
										echo "<td width=\"1\" height=\"" . $gridLength . "\"></td>\n";
									} else {

										echo "<tr height=\"" . $gridLength . "\">\n";
										echo "<td width=\"1\" height=\"" . $gridLength . "\"></td>\n";
									}
									
									// initialize $thisdate again
									$thisdate = $start_week_time;
									
									// loop this part 7 times, one for each day
									
									for ($week_loop=0; $week_loop<7; $week_loop++) {
										$thisday = date("Ymd", $thisdate);
										$dayborder = 0;
										
										
										// check for eventstart 
										if (sizeof($master_array[($thisday)]["$cal_time"]) > 0) {
											foreach ($master_array[($thisday)]["$cal_time"] as $eventKey => $loopevent) {
												$drawEvent = drawEventTimes ($loopevent["event_start"], $loopevent["event_end"]);
												$j = 0;
												while ($event_length[$thisday][$j]) {
													if ($event_length[$thisday][$j]["state"] == "ended") {
														$event_length[$thisday][$j] = array ("length" => ($drawEvent["draw_length"] / $gridLength), "key" => $eventKey, "overlap" => $loopevent["event_overlap"],"state" => "begin");
														break;
													}
													$j++;
												}
												if ($j == sizeof($event_length[$thisday])) {
													array_push ($event_length[$thisday], array ("length" => ($drawEvent["draw_length"] / $gridLength), "key" => $eventKey, "overlap" => $loopevent["event_overlap"],"state" => "begin"));
												}
											}
										}
										
										if (sizeof($event_length[$thisday]) == 0) {
											if ($dayborder == 0) {
												$class = " class=\"weekborder\"";
												$dayborder++;
											} else {
												$class = "";
												$dayborder = 0;
											}
											
											echo "<td bgcolor=\"#ffffff\" colspan=\"" . $nbrGridCols[$thisday] . "\" $class>&nbsp;</td>\n";
											
										} else {
											$emptyWidth = $nbrGridCols[$thisday];
											for ($i=0;$i<sizeof($event_length[$thisday]);$i++) {
											
											//echo $master_array[($thisday)]["$cal_time"][($event_length[$thisday][$i]["key"])]["event_text"] . " ind: " . $i . " / anz: " . $event_length[$thisday][$i]["overlap"] . " = " . eventWidth($i,$event_length[$thisday][$i]["overlap"]) . "<br />";
												$drawWidth = $nbrGridCols[$thisday] / ($event_length[$thisday][$i]["overlap"] + 1);
												$emptyWidth = $emptyWidth - $drawWidth;
												switch ($event_length[$thisday][$i]["state"]) {
													case "begin":
													
														$event_length[$thisday][$i]["state"] = "started";
														$event_text 	= $master_array[($thisday)]["$cal_time"][($event_length[$thisday][$i]["key"])]["event_text"];
														$event_text2 	= addslashes($master_array[($thisday)]["$cal_time"][($event_length[$thisday][$i]["key"])]["event_text"]);
														$event_text2 	= str_replace("\"", "&quot;", $event_text2);
														$event_start 	= $master_array[($thisday)]["$cal_time"][($event_length[$thisday][$i]["key"])]["event_start"];
														$event_end		= $master_array[($thisday)]["$cal_time"][($event_length[$thisday][$i]["key"])]["event_end"];
														$description 	= addslashes($master_array[($thisday)]["$cal_time"][($event_length[$thisday][$i]["key"])]["description"]);
														$description 	= str_replace("\"", "&quot;", $description);
														$event_start 	= strtotime ("$event_start");
														$event_start 	= date ($timeFormat, $event_start);
														$event_end 		= strtotime ("$event_end");
														$event_end 		= date ($timeFormat, $event_end);
														$calendar_name2	= addslashes($calendar_name);
														echo "<td rowspan=\"" . $event_length[$thisday][$i]["length"] . "\" colspan=\"" . $drawWidth . "\" align=\"left\" valign=\"top\" class=\"eventbg2week\">\n";
														echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n";
														echo "<tr>\n";
														echo "<td class=\"eventborder\"><font class=\"eventfont\"><b>$event_start</b></font></td>\n";
														echo "</tr>\n";
														echo "<tr>\n";
														echo "<td>\n";
														echo "<table width=\"100%\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
														echo "<tr>\n";
														echo "<td class=\"eventbg\"><a class=\"psf\" href=\"javascript:openEventInfo('$event_text2', '$calendar_name2', '$event_start', '$event_end', '$description')\"><font class=\"eventfont\">$event_text</font></a></td>\n";
														echo "</tr>\n";
														echo "</table>\n";
														echo "</td>\n";           
														echo "</tr>\n";
														echo "</table>\n";
														echo "</td>\n";
														break;
													case "started":
														break;
													case "ended":
														echo "<td bgcolor=\"#ffffff\" colspan=\"" . $drawWidth . "\">&nbsp;</td>\n";
														break;
												}
												$event_length[$thisday][$i]["length"]--;
												if ($event_length[$thisday][$i]["length"] == 0) {
													$event_length[$thisday][$i]["state"] = "ended";
												}
											}
											//fill emtpy space on the right
											if ($emptyWidth > 0) {
												echo "<td bgcolor=\"#ffffff\" colspan=\"" . $emptyWidth . "\">&nbsp;</td>\n";
											}
											while ($event_length[$thisday][(sizeof($event_length[$thisday]) - 1)]["state"] == "ended") {
												array_pop($event_length[$thisday]);
											}
										}
										$thisdate = ($thisdate + (25 * 60 * 60));
									}
									echo "</tr>\n";
								}

							?>
					</table>
        		</td>
       		</tr>
        	</table>
    	</td>
	</tr>
</table>
</td>
		<td width="20"><img src="images/spacer.gif" width="20" height="1" alt=""></td>
		<td width="160" valign="top"><?php include('./sidebar.php'); ?><center>
		<?php echo "<font class=\"V9\"><br>$powered_by_lang <a class=\"psf\" href=\"http://sourceforge.net/projects/phpicalendar/\">PHP iCalendar $version_lang</a></font>"; ?></center></td>
	</tr>
</table>
</center>
</body>
</html>

