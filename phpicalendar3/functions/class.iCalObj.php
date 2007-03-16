<?php
/*=====================class.iCalObj.php=====================
Refactoring of the ical parser in phpicalendar to make the code more maintainable

Base class for icalendar objects. Some methods used by all, others only for timed events
*/

class iCalObj{

#	var $var; # comment
	var 
		$children; # comment

	function iCalObj(){
		$this->children = array();
	}
	
	/* Parser passes 
		key - everything before the first colon or semicolon
		line - the whole line
		From the icalendar spec page 13:
		    	contentline        = name *(";" param ) ":" value CRLF
		    examples:
		    	ATTENDEE;CUTYPE=GROUP:MAILTO:ietf-calsch@imc.org
		    	RRULE:FREQ=MONTHLY;BYDAY=MO,TU,WE,TH,FR;BYSETPOS=-1
		    	
		Thus, note that key and value are both complex entities that can have multipart info   	
	*/
	function process_line($key, $line){
		echo "\tfeed key= $key line=$line to the object of type ".get_class($this)."\n";
		
		switch ($key){
			case '':
		
			default:
				$line = str_replace("$key:","",$line);
				$varname = strtolower($key);
				$this->$varname = $this->clean_string($line);
		
		}
		
			
	}
	function process_child($obj){
		echo "\t".get_class($this)." object processing child of type ".get_class($obj)."\n";
		$this->children[] = $obj;
	}
	function finish(){
		echo "END:tell the ".get_class($this)." object to finish up, pop it off the stack\n";
	
	}
	function clean_string($data){
		$data = str_replace("\\n", "<br />", $data);
		$data = str_replace("\\t", "&nbsp;", $data);
		$data = str_replace("\\r", "<br />", $data);
		$data = str_replace('$', '&#36;', $data);
		$data = stripslashes($data);
		return $data;
	}
}


?>