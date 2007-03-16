<?php
if (!defined('BASE')) define('BASE', './');

# autoload
function __autoload($class_name) {
 require_once (BASE.'functions/class.'.$class_name . '.php');
}
echo "Starting...<pre>";

require_once (BASE.'functions/init.inc.php');

$settings = new Settings;
$calendar = new Vcalendar;
#print_r($settings);

$parser = new Parser;

# test line folder
if (!$parser->process_file('calendars/Deutsche_Feiertage.ics')) echo 'failed open';

$start = 42929730712;
echo "start:".date("Ymd",strtotime($start))."\n";
echo "end:".date("Ymd", strtotime('+1 day', $start))."\n";


echo "</pre><br>Done!";

?>