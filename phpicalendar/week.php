<? 
$current_view = "week";
include("ical_parser.php"); 

//might not need this, depending on implimentation, doesn't work correctly in current form anyway
//setcookie("last_view", "week");


ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);
$this_day = $day_array2[3];
$this_week = $day_array2[2];
$this_year = $day_array2[1];
$sunday = sundayOfWeek($this_year, $this_month, $this_day);
$sunday_date = strtotime("$sunday");
$saturday = ($sunday_date + (6 * 25 * 60 * 60));
$start_week = date ("F jS", strtotime("$sunday"));
$end_week =  date ("F jS", $saturday);

$date = strtotime ("$getdate");
$next_week2 = ($date + (7 * 24.4 * 60 * 60));
$prev_week2 = ($date - (7 * 24.4 * 60 * 60));
$next_week = date("Ymd", $next_week2);
$prev_week = date("Ymd", $prev_week2);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title><? echo "$calendar_name"; ?></title> 
	<link rel="stylesheet" type="text/css" href="styles/default.css">
	<? include "functions/event.js"; ?>
</head>
<body bgcolor="#FFFFFF">
<center>
	<table width="700" border="0" cellspacing="0" cellpadding="0" class="V12">
	<tr>
		<td align="left" width="5%"><!--[[a class="psf" href="day.php"]]Today[[/a]]--></td>
		<td align="center" width="90%"><? echo "<a class=\"psf\" href=\"day.php?cal=$cal&getdate=$getdate\">$day_lang</a> | <a class=\"psf\" href=\"week.php?cal=$cal&getdate=$getdate\">$week_lang</a> | <a class=\"psf\" href=\"month.php?cal=$cal&getdate=$getdate\">$month_lang</a>"; ?></td>
		<td align="right" width="5%"><!--[[a class="psf" href="preferences.php"]]Preferences[[/a]]--></td>
	</tr>
	<tr>
		<td colspan="3"><img src="images/spacer.gif" height="10" width="1"></td>
	</tr>
</table>
	<table width="700" border="0" cellspacing="1" cellpadding="2" class="calborder">
<tr>
<td>
	<table width="700" border="0" cellspacing="0" cellpadding="0">
    <tr>
     	<td align="center" valign="middle">
      		<table border="0" cellspacing="0" cellpadding="0" bgcolor="#a1a5a9" class="G10B">
      			<tr>
					<td align="center" valign="top">
						<table border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td colspan="3" bgcolor="white" nowrap>
									<table width="100%" border="0" cellspacing="4" cellpadding="0">
										<tr>
											<td colspan="3">
												<table width="100%" border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td class="G10B" align="left" valign="top" width="5%" nowrap><? echo "<a class=\"psf\" href=\"week.php?cal=$cal&getdate=$prev_week\">$last_week_lang</a>"; ?></td>
														<td class="H20" align="center" valign="middle" width="90%" nowrap><? echo "$start_week - $end_week"; ?></td>
														<td class="G10B" align="right" valign="top" width="5%" nowrap><? echo "<a class=\"psf\" href=\"week.php?cal=$cal&getdate=$next_week\">$next_week_lang</a>"; ?></td>
													</tr>
												</table>
											</td>
										</tr>	
										<tr>
											<td align="left" valign="middle"><? include('./list_icals.php'); ?></td>
											<td align="right" valign="middle" class="G10B"><? echo "<a class=\"psf\" href=\"$fullpath\">$subscribe_lang</a>&nbsp;|&nbsp;<a class=\"psf\" href=\"$filename\">$download_lang</a>"; ?></td>
										</tr>
									</table>
								</td>
							</tr>

					<tr>
						<td align="center" valign="top">
							<table border="0" cellspacing="1" cellpadding="0" class="G10B">
								<tr height="12">
									<td colspan="2" height="11" width="60" nowrap bgcolor="#f5f5f5">
										&nbsp; 
									</td>
									<?	
										$thisdate = strtotime ("$sunday");
										$i = 0;
										do {
											$thisday = date("Ymd", $thisdate);
											$thisday2 = date("D, M j", $thisdate);
											echo "<td height=\"12\" width=\"101\" colspan=\"2\" valign=\"top\" align=\"center\" bgcolor=\"#f5f5f5\" class=\"V9\">\n";
											echo "<a class=\"psf\" href=\"day.php?cal=$cal&getdate=$thisday\">$thisday2</a>\n";
											echo "</td>\n";
											$thisdate = ($thisdate + (25 * 60 * 60));
											$i++;
										
										} while ($i != 7);
										
										
									
									?>

								</tr>
								<tr>
									<td colspan="2" width="60" valign="top" align="center" bgcolor="#f5f5f5">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									
									
								<?
										$thisdate = strtotime ("$sunday");
										$i = 0;
										do {
											$thisday = date("Ymd", $thisdate);
											if ($master_array[("$thisday")]["0001"]["event_text"]) {
												echo "<td colspan=\"2\" valign=\"top\" align=\"center\" bgcolor=\"#ffffff\">\n";
												foreach ($master_array[("$thisday")]["0001"]["event_text"] as $event_text) {
													$event_text2 = addslashes($event_text);
													if (strlen($event_text) > 14) {
														$event_text = substr("$event_text", 0, 11);
														$event_text = $event_text . "...";
													}
													echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
													echo "<tr height=\"20\">\n";
													echo "<td height=\"20\" valign=\"middle\" align=\"center\" class=\"eventbg\">\n";
													echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
													echo "<tr>\n";
													echo "<td valign=\"top\" align=\"center\">\n";
													echo "<a class=\"psf\" href=\"javascript:openEventInfo('$event_text2', '$calendar_name', '$event_start', '$event_end')\"><font class=\"eventfont\"><i>$event_text</i></font></a>\n";
													echo "</td>\n";
													echo "</tr>\n";
													echo "</table>\n";
													echo "</td>\n";
													echo "</tr>\n";
													echo "<tr height=\"1\">\n";
													echo "<td height=\"1\">\n";
													echo "<img src=\"images/spacer.gif\" width=\"1\" height=\"1\"> \n";
													echo "</td>\n";
													echo "</tr>\n";
													echo "</table>\n";
												}
												echo "</td>\n";
											} else {
												echo "<td colspan=\"2\" valign=\"top\" align=\"center\" bgcolor=\"#ffffff\">\n";
												echo "<img src=\"images/spacer.gif\" width=\"1\" height=\"1\">\n";
												echo "</td>\n";
											}
											$thisdate = ($thisdate + (25 * 60 * 60));
											$i++;
										
										} while ($i != 7);
								
								?>	
								
								</tr>
								<tr>
									<td width="60" bgcolor="#a1a5a9" nowrap>
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="1" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
									<td width="45" nowrap bgcolor="#a1a5a9">
										<img src="images/spacer.gif" width="1" height="1"> 
									</td>
								</tr>
								<?
								
								$event_length = 0;
								$k = 0;
								
								foreach ($day_array as $key) {
									$count = 0;
									$k = 0;
									$cal_time = $key;	
									$key = strtotime ("$key");
									if ($time_format == "24") {
										$key = date ("G:i", $key);
									} else {
										$key = date ("g:i A", $key);
									}
									
									// The first <TR> (on the hour)
									if (ereg("^([0-9]{1,2}):00", $key)) {
										echo "<tr height=\"30\">\n";
										echo "<td rowspan=\"2\" align=\"center\" valign=\"top\" bgcolor=\"#f5f5f5\" width=\"60\">$key</td>\n";
										echo "<td height=\"30\" width=\"1\"><img src=\"images/spacer.gif\" width=\"1\" height=\"1\"></td>\n";
										$thisdate = strtotime ("$sunday");
										$thisday = date("Ymd", $thisdate);
										do {
											if ($master_array["$thisday"]["$cal_time"]) {
												$event_text = $master_array["$thisday"]["$cal_time"][$k]["event_text"];
												$event_text2 = addslashes($master_array["$thisday"]["$cal_time"][$k]["event_text"]);
												$event_start = $master_array["$thisday"]["$cal_time"][$k]["event_start"];
												$event_end = $master_array["$thisday"]["$cal_time"][$k]["event_end"];
												$event_start = strtotime ("$event_start");
												$event_end = strtotime ("$event_end");
												if ($time_format == "24") {
													$event_start = date ("G:i", $event_start);
													$event_end = date ("G:i", $event_end);
												} else {
													$event_start = date ("g:i a", $event_start);
													$event_end = date ("g:i a", $event_end);
												}
												if (strlen($event_text) > 14) {
													$event_text = substr("$event_text", 0, 11);
													$event_text = $event_text . "...";
												}
												echo "<td colspan=\"2\" bgcolor=\"#ffffff\">&nbsp;<a class=\"psf\" href=\"javascript:openEventInfo('$event_text2', '$calendar_name', '$event_start', '$event_end')\">$event_text</a></td>\n";
											} else {
												echo "<td colspan=\"2\" bgcolor=\"#ffffff\">&nbsp;</td>\n";
											}
											$thisdate = ($thisdate + (25 * 60 * 60));
											$thisday = date("Ymd", $thisdate);
											$count++;
										} while ($count != 7);
										
										// End Week
										echo "</tr>\n";
										$count = 0;
									}
									
									
									// The second <TR> (on the half hour)
									
									if (ereg("([0-9]{1,2}):30", $key)) {
										echo "<tr height=\"30\">\n";
										echo "<td height=\"30\" width=\"1\"><img src=\"images/spacer.gif\" width=\"1\" height=\"1\"></td>\n";
										$thisdate = strtotime ("$sunday");
										$thisday = date("Ymd", $thisdate);
										do {
											if ($master_array["$thisday"]["$cal_time"]) {
												$event_start = $master_array["$thisday"]["$cal_time"][$k]["event_start"];
												$event_end = $master_array["$thisday"]["$cal_time"][$k]["event_end"];
												$event_start = strtotime ("$event_start");
												$event_end = strtotime ("$event_end");
												if ($time_format == "24") {
													$event_start = date ("G:i", $event_start);
													$event_end = date ("G:i", $event_end);
												} else {
													$event_start = date ("g:i a", $event_start);
													$event_end = date ("g:i a", $event_end);
												}
												$event_text = $master_array["$thisday"]["$cal_time"][$k]["event_text"];
												$event_text2 = addslashes($master_array["$thisday"]["$cal_time"][$k]["event_text"]);
												if (strlen($event_text) > 14) {
													$event_text = substr("$event_text", 0, 11);
													$event_text = $event_text . "...";
												}
												echo "<td colspan=\"2\" bgcolor=\"#ffffff\">&nbsp;<a class=\"psf\" href=\"javascript:openEventInfo('$event_text2', '$calendar_name', '$event_start', '$event_end')\">$event_text</a></td>\n";
											} else {
												echo "<td colspan=\"2\" bgcolor=\"#ffffff\">&nbsp;</td>\n";
											}
											$thisdate = ($thisdate + (25 * 60 * 60));
											$thisday = date("Ymd", $thisdate);
											$count++;
										} while ($count != 7);
										echo "</tr>\n";
									}
									
									
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
</tr>
</table>
</td>
</tr>
</table>
<br>
<? echo "<font class=\"V9\">$powered_by_lang <a class=\"psf\" href=\"http://sourceforge.net/projects/phpicalendar/\">PHP iCalendar $version_lang</a></font>"; ?>
</center>
</body>
</html>
