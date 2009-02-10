<?php

define('BASE', '../');
include_once(BASE.'functions/init.inc.php');
require_once(BASE.'functions/date_functions.php');
require_once(BASE.'functions/template.php');

# information for the popup is sent via $_POST by a javascript snippet in
# in function openevent() from functions/date_functions.php
# character encoding has been problematic with popups.
$cal    		= stripslashes($_POST['edit_cal']);
$uid    		= stripslashes($_POST['edit_uid']);

$page = new Page(BASE.'templates/'.$phpiCal_config->template.'/edit.tpl');

$page->replace_tags(array(
	'charset'			=> $phpiCal_config->charset,
	'cal' 				=> $cal,
	'uid'      			=> $uid,
	'template'			=> $phpiCal_config->template,
	'l_calendar' 		=> $lang['l_calendar']

	));

$page->output();

?>
