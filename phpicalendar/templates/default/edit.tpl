<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset={CHARSET}">
	<title>{CALNAME}</title>
	<link rel="stylesheet" type="text/css" href="../templates/{TEMPLATE}/default.css">
	<script type="text/javascript">
		function verify(form) {
			// FIXME: Force form fields to conform to certain formats
			return false;
		}
	</script>
</head>
<body>
<center>
    <form method="post" action="{ACTION}" onsubmit="return verify(this.form);">
	<table border="0" width="430" cellspacing="0" cellpadding="0" class="calborder">
		<tr>
			<td colspan="2" align="center" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{CALNAME} {L_CALENDAR}</div></td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_SUMMARY}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				<input name="summary" id="summary" style="width: 300px;" type="text" value="{EVENT_TEXT}" />
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_EVENT_START}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				{EVENT_START} {ALLDAY}
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_EVENT_END}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				{EVENT_END}
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_DESCRIPTION}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				<textarea name="description" id="description" style="width: 300px;">{DESCRIPTION}</textarea>
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_ORGANIZER}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				<textarea name="organizer" id="organizer" style="width: 300px;">{ORGANIZER}</textarea>
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_ATTENDEE}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				<textarea name="attendee" id="attendee" style="width: 300px;">{ATTENDEE}</textarea>
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_STATUS}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				<select name="status" id="status" style="width: 300px;">{STATUS}</select>
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_LOCATION}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				<input name="location" id="location" style="width: 300px;" type="text" value="{LOCATION}" />
			</td>
		</tr>
		<tr>
			<td style="width: 115px;" align="left" class="V12">
				<span style="margin-left: 10px; font-weight: bold;">{L_URL}: </span>
			</td>
			<td style="width: 315px;" align="left" class="V12">
				<input name="url" id="url" style="width: 300px;" type="text" value="{URL}" />
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="title" style="padding-top: 3px; padding-bottom: 3px; border-top: 1px solid #ccc;">
				<input name="calnumber" id="calnumber" type="hidden" value="{CALNUMBER}" />
				<input name="uid" id="uid" type="hidden" value="{UID}" />
				<input name="save" id="save" type="submit" value="Save" />
				<input name="cancel" id="cancel" type="reset" value="Cancel" onclick="window.close();" />
			</td>
		</tr>
	</table>
    </form>
</center>
</body>
</html>

