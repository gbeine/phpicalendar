<?php

define('BASE', '../');
include_once(BASE.'functions/init.inc.php');
require_once(BASE.'functions/date_functions.php');
require_once(BASE.'functions/template.php');
require_once(BASE.'functions/edit_functions.php');

# information for the popup is sent via $_POST by a javascript snippet in
# in function openevent() from functions/date_functions.php
# character encoding has been problematic with popups.
$from           = stripslashes($_POST['edit_from']);
$uid            = stripslashes($_POST['edit_uid']);
$arr            = unserialize(stripslashes($_POST['edit_arr']));
$organizers     = unserialize($arr['organizer']);
$attendees      = unserialize($arr['attendee']);


// Sanitize input fields
$arr['event_text']  = sanitizeForWeb(urldecode($arr['event_text']));
$arr['description'] = sanitizeForWeb(urldecode($arr['description']));
$arr['location']    = sanitizeForWeb(urldecode($arr['location']));


// "calnumber" counts from 1. $cal_filelist is indexed from 0.
$calnumber = $arr['calnumber'];
if (!is_numeric($calnumber)) die('!is_numeric($calnumber)');
if (--$calnumber < 0) die('--$calnumber &lt; 0: ' . $calnumber);


//print_r($arr);
$sel_confirmed = '';
$sel_cancelled = '';
$sel_tentative = '';
$sel_blank = '';
switch ($arr['status']) {
    case 'CONFIRMED':
        $sel_confirmed = ' selected="selected"';
        break;

    case 'CANCELLED':
        $sel_cancelled = ' selected="selected"';
        break;

    case 'TENTATIVE':
        $sel_tentative = ' selected="selected"';
        break;

    default:
        $sel_blank = ' selected="selected"';
        break;
}
$status  = '<option value=""' . $sel_blank . '></option>';
$status .= '<option value="CONFIRMED"' . $sel_confirmed . '>' . $lang['l_status_confirmed'] . '</option>';
$status .= '<option value="CANCELLED"' . $sel_cancelled . '>' . $lang['l_status_cancelled'] . '</option>';
$status .= '<option value="TENTATIVE"' . $sel_tentative . '>' . $lang['l_status_tentative'] . '</option>';

$organizer = array();
foreach($organizers as $value) {
    $organizer[] = '"' . $value['name'] . '" <' . $value['email'] . '>';
}
$organizer = implode(', ', $organizer);


$attendee = array();
foreach($attendees as $value) {
    $attendee[] = '"' . $value['name'] . '" <' . $value['email'] . '>';
}
$attendee = implode(', ', $attendee);


$page = new Page(BASE.'templates/'.$phpiCal_config->template.'/edit.tpl');

$page->replace_tags(array(
    'action'            => BASE.'/'.$from,
    'charset'           => $phpiCal_config->charset,
    'calname'           => $arr['calname'],
    'calnumber'         => $calnumber,
    'event_text'        => $arr['event_text'],
    'description'       => $arr['description'],
    'location'          => $arr['location'],
    'organizer'         => $organizer,
    'attendee'          => $attendee,
    'uid'               => $uid,
    'status'            => $status,
    'location'          => $arr['location'],
    'url'               => $arr['url'],
    'template'          => $phpiCal_config->template,
    'l_calendar'        => $lang['l_calendar'],
    'l_description'     => $lang['l_description'],
    'l_summary'         => $lang['l_summary'],
	'l_organizer'		=> $lang['l_organizer'],
	'l_attendee'		=> $lang['l_attendee'],
	'l_status'		    => $lang['l_status'],
	'l_location'		=> $lang['l_location'],
	'l_url'     		=> $lang['l_url']
    ));

$page->output();

?>
