<?php
/*=====================class.Parser.php=====================
Refactoring of the ical parser in phpicalendar to make the code more maintainable
gets a calendar object and creates a series of event objects.

This object should probably only be invoked in situations where the input is an ics file,
either a local cal or a webcal.  Unserializing a saved cal should go somewhere else.

The function process_file is the meat of the operation.  Note that Parser determines the kind of object
that should handle a content line, but delegates further parsing of that content-line to the object.
In other words, the Parser class just deals with BEGIN and END events, which are involved in creating and
organizing objects.
*/

class Parser{

	var 
		$cal,		#	calendar object
		$fh, 		# 	filehandle for the calendar file being parsed
		$lookahead,	#	buffer for last line read by read_line lookahead
		$mArray;	#	temporary master array entries
		
		
	function Parser(){
		$this->lookahead = '';
	}
	
	function set_cal($cal){
		$this->cal = $cal;
	}

	function process_cal(){
	
		process_file($this->cal->filename);
	}
/*
The structure of an ics file is somewhat like xml.  Objects are hierarchical
The top level object is VCALENDAR, which has children including VTIMEZONE, VEVENT, VTODO etc.
Each of these has child objects, such as DAYLIGHT and STANDARD in VTIMEZONE, DTSTART in the others, etc.
We will use this hiearchy, but not to full granularity.
*/	
	function process_file($filename){
		$obj = null;
		$obj_stack = array();
		if (!$this->open_file($filename)) return "can't open file"; 
		$i = 0;
		while (!feof($this->fh)){
			$line = $this->read_line();
			# echo "$i:$line\n";$i++;
			if($line){
				$tmp = explode(":",	$line);
				$tmp2 = explode(";", $tmp[0]);
				$key = $tmp2[0]; #want the first string before either a colon or semicolon
				# echo "key:$key\n";
				switch ($key){
					case 'BEGIN':
						$type = ucfirst(strtolower($tmp[1]));
						if($type == 'Vcalendar'){
							if (!is_object($this->cal)) $this->cal = new Vcalendar; echo "Make vcal obj\n";
							$obj = $this->cal;
							$obj_stack[] = $obj;
							# echo "BEGIN: make new obj of type ".get_class($obj)." and push it onto the stack\n";
						}elseif(in_array($type, array('Vtimezone','Daylight','Standard','Vevent','Vtodo','Vfreebusy'))){
							$obj = new $type; # 
							$obj_stack[] = $obj;
							# echo "BEGIN: make new obj of type ".get_class($obj)." and push it onto the stack\n";
						}else{
							# Handle BEGIN for undefined object types
							# Parser delegates further parsing to the object
							if(is_object($obj)) $obj->process_line($key,$line);
						}
						break;
					case 'END':
						$obj = array_pop($obj_stack);
						if(is_object(end($obj_stack))){
							$parent_obj = end($obj_stack);
							$parent_obj->process_child($obj); # let the parent object set whatever it needs from the child
						}	
						if(is_object($obj)) $obj->finish();
						# "make the working object the last one on the stack\n";
						if(is_object(end($obj_stack))){
							$obj = $parent_obj;
						}
						break;
					default:
						# Parser delegates further parsing to the object				
						if(is_object($obj)) $obj->process_line($key,$line);
				}
		#	print_r($obj_stack);
			}
		}
		# "finished stack on line:$line.  Lookahead:$this->lookahead\n";
		#deal with possible lack of \n at eof
		if(trim($this->lookahead) != ""  && is_object($obj)){
			$obj = array_pop($obj_stack);
			if(is_object(end($obj_stack))){
				$parent_obj = end($obj_stack);
				$parent_obj->process_child($obj); # let the parent object set whatever it 
			}	
			$obj->finish();
			if(is_object($parent_obj)) $parent_obj->finish();

		}
		print_r($this->cal);
		return true;
	}
	
	function open_file($filename){
		$this->fh = fopen("./".$filename, "r");
		if ($this->fh == FALSE) return false;
		return true;
	}
	
	# takes a filehandle and folds multiple line input to $this->line
	function read_line(){
		if (feof($this->fh)){ 
			return;
		}
		$tmp_line = $this->lookahead;
		$read_more = true;
		do { 
			$this->lookahead = fgets($this->fh, 1024); 
			$this->lookahead = ereg_replace("[\r\n]", "", $this->lookahead);

			if (($this->lookahead !='' && ($this->lookahead{0} == " " || $this->lookahead{0} == "\t")) || $tmp_line == '' || $tmp_line == "\n"){
				$tmp_line = rtrim($tmp_line) . str_replace("\t"," ", $this->lookahead);
			}else{
				$read_more = false;
			}
		}while ($read_more & !feof($this->fh)); 
		return trim($tmp_line);
	}

} # end class parser
?>