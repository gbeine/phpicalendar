<?

// start of <select> tag
if (isset($getdate)) {
	$query="&getdate=$getdate";
} else {
	$query="";
}
print "<select name=\"action\" onChange=\"window.location.href=this.options[this.selectedIndex].value+'$query';\">\n";
print "<option value=\"null\">Select a Calendar</option>\n";

// open file
$dir_handle = @opendir($calendar_path) or die("Unable to open $calendar_path");

// build the <option> tags
while ($file = readdir($dir_handle)) {
	if (strstr ($file, ".ics")) {
		// $cal_filename is the filename of the calendar without .ics
		// $cal is a urlencoded version of $cal_filename
		$cal_filename = substr($file,0,-4);
		$cal = urlencode($cal_filename);
		
		if ($_COOKIE["last_view"][$cal]) {
			$last_view = $_COOKIE["last_view"][$cal];
			print "<option value=\"$last_view.php?cal=$cal\">$cal_filename</option>\n";
		} else {
			print "<option value=\"$default_view.php?cal=$cal\">$cal_filename</option>\n";	
		}
		
	}
}

// close file
closedir($dir_handle);

// finish <select>
print "</select>";
?>