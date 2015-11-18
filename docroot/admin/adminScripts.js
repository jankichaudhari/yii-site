function confirmDelete(con_Msg,con_Url) { 
if (confirm(con_Msg)) { 
	document.location = con_Url; 
	}
}
function confirmUpdate(con_Msg,con_Url) { 
if (confirm(con_Msg)) { 
	document.location = con_Url; 
	}
}

function WSOver(lnk) { 
lnk.style.cursor = "hand" 
}
function WSOut(lnk) { 
lnk.style.cursor = "hand" 
}
function WSDetail(propID) { 
top.location.href = "PropertyEdit.asp?propID="+propID 
}

function gomap(x,y) {
	window.open('map.php?x='+x+'&y='+y+'','map','width=700,height=571');
	}
function save_coords(x,y) {
	if (window.opener && !window.opener.closed)
	window.opener.document.form.osx.value = x;
	window.opener.document.form.osy.value = y;
	window.close();
	}

function ValidateClientForm() {
	RequiredFields = "";	
	
	var pass=false;
	var el=document.form.elements['Branch[]'];
	for (var i=0; i<el.length; i++){
		if (el[i].checked){
		pass=true;		
		}
	}
	if (pass == false) { 
	RequiredFields += "\n     -  Branch";
	}
	
	//var checkSelected = false;
	//for (i = 0;  i < document.form.elements['Branch[]'].length;  i++)
	//{
	//if (document.form.Branch[i].checked)
	//checkSelected = true;
	//}
	//if (checkSelected == false)
	//{
	//RequiredFields += "\n     -  Branch";
	//}	
	//if (document.form.elements['Branch[]'].checked !== true && document.form.Branch[1].checked !== true && document.form.Branch[2].checked !== true) {
	//RequiredFields = "\n     -  Branch";
	//}
	
	if (document.form.Name.value == "") {
	RequiredFields += "\n     -  Name";
	}
	if ((document.form.Email.value == "") || 
	(document.form.Email.value.indexOf('@') == -1) || 
	(document.form.Email.value.indexOf('.') == -1)) {
	RequiredFields += "\n     -  Email Address";
	}
	if (RequiredFields != "") {
	RequiredFields ="The following fields are required:\n" + RequiredFields + "\n";
	alert(RequiredFields);
	return false;
	}
	else return true;
}

function statusChange() {
	alert("Dont forget to add notes to explain this status change.\nPress the update button first to save the change, \nthen go to notes page and enter explanation.");        
	}