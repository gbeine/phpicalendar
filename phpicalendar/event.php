<? 

include "init.inc.php"; 
$event = stripslashes($event);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <title><? echo "$calendar_name"; ?></title>
	<link rel="stylesheet" type="text/css" href="styles/<? echo "$style_sheet"; ?>">
</head>
 <body bgcolor="#eeeeee">
<table border="0" width="430" cellspacing="2" cellpadding="4">
	<tr>
		<td>  
   <table width="100%" border="0" cellspacing="0" cellpadding="0" class="calborder">
    <tr height="18">
     <td align="right" valign="top" width="80" class="V12">&nbsp;<b>Event:</b></td>
     <td nowrap width="7" height="18"></td>
     <td align="left" valign="top" height="18" class="V12"><? echo "$event"; ?></td>
    </tr>
    <tr height="18">
     <td align="right" valign="top" width="80" class="V12">&nbsp;<b>Start Time:</b></td>
     <td width="7" height="18"></td>
     <td align="left" valign="top" height="18" class="V12"><? echo "$start"; ?></td>
    </tr>
    <tr height="18">
     <td align="right" valign="top" width="80" class="V12">&nbsp;<b>End Time:</b></td>
     <td width="7" height="18"></td>
     <td align="left" valign="top" height="18" class="V12"><? echo "$end"; ?></td>
    </tr>
   </table>
   </td>
	</tr>
</table> 
 </body>
</html>