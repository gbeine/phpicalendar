<script language="JavaScript" type="text/javascript">
<!--
function openEventWindow(num) {
	// populate the hidden form
	var data = document.popup_data[num];
	var form = document.forms.eventPopupForm;
	form.elements.date.value = data.date;
	form.elements.time.value = data.time;
	form.elements.uid.value = data.uid;
	form.elements.cpath.value = data.cpath;
	form.elements.event_data.value = data.event_data;

	// open a new window
	var w = window.open('', 'Popup', 'scrollbars=yes,width=550,height=350');
	form.target = 'Popup';
	form.submit();
}

function EventData(date, time, uid, cpath, event_data) {
	this.date = date;
	this.time = time;
	this.uid = uid;
	this.cpath = cpath;
	this.event_data = event_data;
}

document.popup_data = new Array();


function openEditWindow(num) {
	// populate the hidden form
	var data = document.edit_data[num];
	var form = document.forms.editPopupForm;
	form.elements.edit_from.value = data.edit_from;
	form.elements.edit_uid.value = data.edit_uid;
	form.elements.edit_arr.value = data.edit_arr;

	// open a new window
	var w = window.open('', 'Popup', 'scrollbars=yes,width=460,height=350');
	form.target = 'Popup';
	form.submit();
}

function EditData(edit_from, edit_uid, edit_arr) {
	this.edit_from = edit_from;
	this.edit_uid = edit_uid;
	this.edit_arr = edit_arr;
}

document.edit_data = new Array();


function openTodoInfo(num) {
	// populate the hidden form
	var data = document.todo_popup_data[num];
	var form = document.forms.todoPopupForm;

	form.elements.todo_data.value = data.todo_data;

	// open a new window
	var w = window.open('', 'Popup', 'scrollbars=yes,width=550,height=350');
	form.target = 'Popup';
	form.submit();
}

function TodoData(todo_data,todo_text) {
	this.todo_data = todo_data;
	this.todo_text = todo_text;
}

document.todo_popup_data = new Array();
//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--

function submitform(form, value) {
	// Parse value.
	var values = decodeURI(value).split("&");
	var temp = values[0].split("?", 2);
	var action = temp[0];
	values[0] = temp[1];

	try {
		form.setAttribute("action", action);
	}
	catch(e) {
		form.action = action;
	}

	// Stuff the hidden form fields.
	for (var i = 0; i < values.length; i++) {
		temp = values[i].split("=", 2);
		form.elements.namedItem(temp[0]).value = temp[1];
	}

	// Clear the select+option value.
	var select = form.elements.namedItem("form_action")
	select.options[select.selectedIndex].value = "";

	form.submit();
}
//-->
</script>
