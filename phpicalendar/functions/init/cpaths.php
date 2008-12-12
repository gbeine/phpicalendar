<?php
# adjust paths in case they are incorrect
if ($phpiCal_config->default_path == '') {
	$phpiCal_config->setProperty('default_path', BASE);
}

#cpath modifies the calendar path based on the url or cookie values.  This allows you to run multiple calendar subsets from a single phpicalendar installation. Operations on cpath are largely hidden from the end user.
if ($phpiCal_config->calendar_path == '') {
	$phpiCal_config->setProperty('calendar_path', BASE.'calendars');
}
$calendar_path = $phpiCal_config->calendar_path;
$cpath = ''; #initialize cpath to prevent later undef warnings.
if(isset($_REQUEST['cpath'])&& $_REQUEST['cpath'] !=''){
	$cpath 	= str_replace('..','',$_REQUEST['cpath']);				
	$calendar_path 	.= "/$cpath";				
#	$tmp_dir 	.= "/$cpath";				
}elseif(isset($phpiCal_config->default_cpath_check) && $phpiCal_config->default_cpath_check !='' ){
	$cpath 	= str_replace('..','',$default_cpath_check);				
	$calendar_path 	.= "/$cpath";				
#	$tmp_dir 	.= "/$cpath";
}
#these need cpath to be set
#set up specific template folder for a particular cpath
if (isset($user_template["$cpath"])){ 
  $template = $user_template["$cpath"]; 
}
#set up specific webcals for a particular cpath
if (isset($phpiCal_config->more_webcals) && is_array($phpiCal_config->more_webcals[$cpath])){
	$list_webcals = array_merge($phpiCal_config->list_webcals, $phpiCal_config->more_webcals["$cpath"]);
}