function showHideStatusRow(thisField)
{

	if ((getCheckedValue(thisField) == 'sale')) {
		$('#cli_salestatus').parent().show();
		$('#cli_letstatus').parent().hide();
	} else {
		$('#cli_salestatus').parent().hide();
		$('#cli_letstatus').parent().show();
	}
	if ((getCheckedValue(thisField) == 'let')) {
		$('#cli_salestatus').parent().hide();
		$('#cli_letstatus').parent().show();
	} else {
		$('#cli_salestatus').parent().show();
		$('#cli_letstatus').parent().hide();
	}

}

function writeCal(calendar_url, calendar_method)
{
	if (!calendar_method) {
		calendar_method = "iframe";
	}
	if (calendar_method == "object") {
		document.write('<object type="text/html" data="' + calendar_url + '" width="100%" height="550" id="cal_iframe" name="cal_iframe">\n');
		document.write('Sorry, your browser does not support the calendar object. Please contact tech support.</object>\n');
	} else {
		document.write('<iframe src="' + calendar_url + '" height="550" id="cal_iframe" name="cal_iframe"></iframe>\n');

	}
	// alert("method:"+calendar_method);
}


function writeCalHeight(theCal, allDayHeight)
{
	/* dynamically change the height of the calendar element onLoad and onResize */
	/* tested working in IE6 and IE7 and FF */
	/* allDayHeight is the height of the allDayDiv, and the calendar div height needs to be reduced accordingly */
	var theHeight;
	allDayHeight = allDayHeight || 0;

	if (document.documentElement.clientHeight) {
		theHeight = document.documentElement.clientHeight;
	} else {
		//theHeight = document.body.clientHeight;
		theHeight = window.innerHeight;
	}

//	console.log(theHeight);

	var el = document.getElementById("calendar-container");
	console.log(el.offsetTop, theHeight);
	theHeight -= el.offsetTop + 2;
//	console.log(theHeight)
//	var offsetTop = 0;
//	while (el && el.offsetTop) {
//		offsetTop += el.offsetTop;
//		el = el.parentNode
//	}
//
//	console.log(offsetTop);
//	theHeight = theHeight - offsetTop; // 30 is for the filter bar, this is too big in FF
//	console.log(theHeight);
	// reduce bit more. for webkit Vitaly.


	var elem = document.getElementById(theCal);
	elem.style.height = theHeight + "px";
//	elem.style.width = "100%";

//	elem.style.width = elem.clientWidth - 6 + "px";
}

// disables time drop-downs when allday is checked
function allDayCheck(allDayId)
{
	var elem = document.getElementById(allDayId);
	var dis1 = document.getElementById('app_time_hour');
	var dis2 = document.getElementById('app_time_min');

	if (elem.checked == true) {
		dis1.disabled = true;
		dis2.disabled = true;
	} else {
		dis1.disabled = false;
		dis2.disabled = false;
	}
}
function colourPickWindow()
{
	window.open("colour_pick.php?", null, "height=400,width=400,status=no,toolbar=no,menubar=no,location=no,resizable=yes");
}

function addUserToAppointment(app, use, carry)
{
	if (use == 0) {
		alert("Please select a user");
	} else {
		document.location.href = "add_user_to_appointment.php?app_id=" + app + "&use_id=" + use + "&carry=" + carry;
	}
}
function windowPrint()
{
	// hide the top and left navigation ready for printing
	var page_header = document.getElementById('page_header');
	var main_menu = document.getElementById('main_menu');
	var back_button = document.getElementById('navbar_Back');
	var print_button = document.getElementById('navbar_Print');

	// the main content div needs margin resetting, and is not always the same id...
	if (document.getElementById('content') != null) {
		var content = document.getElementById('content');
	}
	else {
		if (document.getElementById('content_wide') != null) {
			var content = document.getElementById('content_wide');
		}
	}

	/*if (page_header.style.display == "none") {
	 page_header.style.display = "";
	 main_menu.style.display = "";
	 content.style.margin = "";
	 back_button.href = "";
	 }
	 else {*/
	page_header.style.display = "none";
	main_menu.style.display = "none";
	content.style.margin = "0px";
	back_button.href = "javascript:location.reload();";

	window.print();
	//}
}
function dealPrint(dea_id)
{
//	window.open("deal_print.php?dea_id="+dea_id,null,"height=950,width=700,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes");
	window.open("/property/pdf/" + dea_id, null, "height=950,width=700,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes");
}
function dealPrintOld(dea_id)
{
	window.open("deal_print.php?dea_id=" + dea_id, null, "height=950,width=700,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes");
}
function calendarPrint(url)
{
	window.open(url, null, "height=842,width=595,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes");
}
function appointmentPrint(app_id, use_id)
{
	window.open("appointment_print.php?app_id=" + app_id + "&use_id=" + use_id, null, "height=842,width=595,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes");
}
function toggleField(theField)
{
	var elem = document.getElementById(theField);
	elem.disabled = !elem.disabled;
}
function disableTermField(thisField, otherField)
{

	var elem = document.getElementById(otherField);

	if ((getCheckedValue(thisField) == 'Sales') || (getCheckedValue(thisField) == 'sale')) {
		elem.disabled = true;
	} else {
		elem.disabled = false;
	}
}

function toggleDivRadio(thisField, otherField)
{

	var elem = document.getElementById(otherField);
	//var toHide = document.getElementById('toHide');
	//alert(getCheckedValue(thisField));
	if ((getCheckedValue(thisField) == 'Yes')) {
		elem.style.display = "";
		//toHide.style.display = "none";
	} else {
		elem.style.display = "none";
		//toHide.style.display = "";
	}
}

function getCheckedValue(myObject)
{
	var radioObj = document.getElementsByName(myObject);
	if (!radioObj) {
		return "";
	}
	var radioLength = radioObj.length;
	if (radioLength == undefined) {
		if (radioObj.checked) {
			return radioObj.value;
		}
		else {
			return "";
		}
	}
	for (var i = 0; i < radioLength; i++) {
		if (radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function toggleDiv(theDiv)
{
	var elem = document.getElementById(theDiv);
	if (elem.style.display == "none") {
		elem.style.display = "";
	} else {
		elem.style.display = "none";
	}
}

function swapDiv(theDiv, theDiv2)
{
	var elem = document.getElementById(theDiv);
	var elem2 = document.getElementById(theDiv2);
	elem.style.display = "";
	elem2.style.display = "none";
}

function confirmDelete(msg, url)
{
	if (!msg) {
		msg = 'Are you sure you want to delete?';
	}
	if (confirm(msg)) {
		document.location.href = url;
	}
}

function setDealStatus(id, sot)
{
	document.location.href = "deal_change.php?dea_id=" + id + "&sot=" + sot;
}

function controlUnderOffer(selectedStatus, selectID, currentStatus)
{
	// alerts user to not set da_status to under offer, but to use offer_submit instead
	if (selectedStatus.value == 'Under Offer') {
		var elem = document.getElementById(selectID);
		elem.value = currentStatus;
		alert('You cannot set the status to Under Offer from here, you must submit the offer in the proper way.\n\nYou will be shown the relevant form after you click this button...');
		showHide('form7');
	}
}

function controlAppointmentType(selectedStatus, selectID)
{
	// alerts user to not set da_status to under offer, but to use offer_submit instead
	var elem = document.getElementById('notetype');
	if (selectedStatus.value == 'Note') {
		elem.style.display = "";
	} else {
		elem.style.display = "none";
	}
}


function closeReloadParent()
{
	window.close();
	window.opener.location.reload();
}

function goForward()
{
	if (window.history.forward(1)) {
		history.go(1);
	} else {
		alert('No forward pages');
	}
}

/*function framePrint(whichFrame){
 parent[whichFrame].focus();
 parent[whichFrame].print();
 }*/
function framePrint()
{
	/*window.parent.mainFrame.focus();*/
	window.print();
}

function trOver(lnk)
{
	lnk.className = lnk.className.replace('trOff', 'trOn', lnk.className)
}
function trOut(lnk)
{
	lnk.className = lnk.className.replace('trOn', 'trOff', lnk.className);
}
function trClick(href)
{
	document.location.href = href;
}


function calEventOver(lnk, newHeight)
{
	lnk.style.overflow = 'visible';
	lnk.style.zIndex = '1000';
	/*lnk.style.height = newHeight;	*/
}

function calEventOut(lnk, newHeight)
{
	lnk.style.overflow = 'hidden';
	lnk.style.zIndex = '1';
	/*lnk.style.height = newHeight;		*/
	/*lnk.style.width = 230;*/
}

function addArea(pc, ref)
{
	document.location.href = 'area_add.php?are_postcode=' + pc + '&return=' + ref;
}

// tooltip
/***********************************************
 * Show Hint script- � Dynamic Drive (www.dynamicdrive.com)
 * This notice MUST stay intact for legal use
 * Visit http://www.dynamicdrive.com/ for this script and 100s more.
 ***********************************************/

var horizontal_offset = "9px" //horizontal offset of hint box from anchor link

/////No further editting needed

var vertical_offset = "0" //horizontal offset of hint box from anchor link. No need to change.
var ie = document.all
var ns6 = document.getElementById && !document.all

function getposOffset(what, offsettype)
{
	var totaloffset = (offsettype == "left") ? what.offsetLeft : what.offsetTop;
	var parentEl = what.offsetParent;
	while (parentEl != null) {
		totaloffset = (offsettype == "left") ? totaloffset + parentEl.offsetLeft : totaloffset + parentEl.offsetTop;
		parentEl = parentEl.offsetParent;
	}
	return totaloffset;
}

function iecompattest()
{
	return (document.compatMode && document.compatMode != "BackCompat") ? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge)
{
	var edgeoffset = (whichedge == "rightedge") ? parseInt(horizontal_offset) * -1 : parseInt(vertical_offset) * -1
	if (whichedge == "rightedge") {
		var windowedge = ie && !window.opera ? iecompattest().scrollLeft + iecompattest().clientWidth - 30 : window.pageXOffset + window.innerWidth - 40
		dropmenuobj.contentmeasure = dropmenuobj.offsetWidth
		if (windowedge - dropmenuobj.x < dropmenuobj.contentmeasure) {
			edgeoffset = dropmenuobj.contentmeasure + obj.offsetWidth + parseInt(horizontal_offset)
		}
	}
	else {
		var windowedge = ie && !window.opera ? iecompattest().scrollTop + iecompattest().clientHeight - 15 : window.pageYOffset + window.innerHeight - 18
		dropmenuobj.contentmeasure = dropmenuobj.offsetHeight
		if (windowedge - dropmenuobj.y < dropmenuobj.contentmeasure) {
			edgeoffset = dropmenuobj.contentmeasure - obj.offsetHeight
		}
	}
	return edgeoffset
}

function showhint(menucontents, obj, e, tipwidth)
{
	if ((ie || ns6) && document.getElementById("hintbox")) {
		dropmenuobj = document.getElementById("hintbox")
		dropmenuobj.innerHTML = menucontents
		dropmenuobj.style.left = dropmenuobj.style.top = -500
		if (tipwidth != "") {
			dropmenuobj.widthobj = dropmenuobj.style
			dropmenuobj.widthobj.width = tipwidth
		}
		dropmenuobj.x = getposOffset(obj, "left")
		dropmenuobj.y = getposOffset(obj, "top")
		dropmenuobj.style.left = dropmenuobj.x - clearbrowseredge(obj, "rightedge") + obj.offsetWidth + "px"
		dropmenuobj.style.top = dropmenuobj.y - clearbrowseredge(obj, "bottomedge") + "px"
		dropmenuobj.style.visibility = "visible"
		obj.onmouseout = hidetip
	}
}

function hidetip(e)
{
	dropmenuobj.style.visibility = "hidden"
	dropmenuobj.style.left = "-500px"
}

function createhintbox()
{
	var divblock = document.createElement("div")
	divblock.setAttribute("id", "hintbox")
	document.body.appendChild(divblock)
}

if (window.addEventListener) {
	window.addEventListener("load", createhintbox, false)
}
else {
	if (window.attachEvent) {
		window.attachEvent("onload", createhintbox)
	}
	else {
		if (document.getElementById) {
			window.onload = createhintbox
		}
	}
}


// Updates the title of the frameset if possible (ns4 does not allow this)
if (typeof(parent.document) != 'undefined' && typeof(parent.document) != 'unknown'
		&& typeof(parent.document.title) == 'string') {
	parent.document.title = window.document.title;
}


// clear specified value from form field
function clearField(thefield, thevalue)
{
	if (thefield.value == thevalue) {
		thefield.value = "";
	}
}

// check all checkboxes by class identifier
function checkAll(theForm, cName)
{
	for (i = 0, n = theForm.elements.length; i < n; i++) {
		if (theForm.elements[i].className.indexOf(cName) != -1) {
			theForm.elements[i].checked = true;
		}
	}
}
// toggle all checkboxes by class identifier
function checkToggle(theForm, cName)
{
	for (i = 0, n = theForm.elements.length; i < n; i++) {
		if (theForm.elements[i].className.indexOf(cName) != -1) {
			if (theForm.elements[i].checked == true) {
				theForm.elements[i].checked = false;
			} else {
				theForm.elements[i].checked = true;
			}
		}
	}
}


function showHideDiv(theDiv)
{
	var elem = document.getElementById(theDiv);
	if (elem.style.display == "") {
		elem.style.display = "none";
	} else {
		elem.style.display = "";
	}
}

function showForm(formID)
{
	var viewForm = "form" + formID;
	document.getElementById(viewForm).style.display = "";
}

function showHide(showID)
{
	if (!previousID) {
		return;
	}
	document.getElementById(previousID).style.display = "none";

	document.getElementById(showID).style.display = "";
	previousID = showID;
}

function resetDirectoryAddress(id)
{
	if (confirm("Warning: This will remove the current address.\nYou must select a new address or this entry will not appear in the directory.")) {
		document.location.href = "?action=reset_directory_address&dir_id=" + id;
	}
}

function removeAddress(id, link)
{
	if (confirm("Warning: This will remove the current address.\nYou must select a new address or this entry will not appear in the directory.")) {
		document.location.href = "?action=reset_directory_address&dir_id=" + id;
	}
}

function reOrder(id, direction)
{
	alert("This dosen't work yet");
}


/* AJAX stuff */
var myGlobalHandlers = {
	onCreate : function ()
	{
		Element.show('systemWorking');
	},

	onComplete : function ()
	{
		if (Ajax.activeRequestCount == 0) {
			Element.hide('systemWorking');
			Element.hide('lookup');
			Element.show('placeholder');
		}
	}
};

function getAddr()
{
	var pc_number = $F('ajax_number');
	var pc_postcode = $F('ajax_postcode');
	if (pc_postcode.length < 6) {
		alert("Please enter full postcode");
	} else {
		var url = 'ajax_client_postcode.php';
		var pars = 'number=' + pc_number + '&postcode=' + pc_postcode;
		//alert(pars);
		Ajax.Responders.register(myGlobalHandlers);
		var myAjax = new Ajax.Updater(
				'placeholder',
				url,
				{
					method     : 'get',
					parameters : pars
				}
		);
	}
}


function getAddrUDPRN()
{
	var pc_pcid = $F('cli_pcid2');
	var url = 'ajax_client_postcode.php';
	var pars = 'pcid=' + pc_pcid;

	var myAjax = new Ajax.Updater(
			'placeholder',
			url,
			{
				method     : 'get',
				parameters : pars
			});
}


/*property lookup*/
function getAddrP()
{
	var pc_number = $F('ajax_number');
	var pc_postcode = $F('ajax_postcode');
	if (pc_postcode.length < 6) {
		alert("Please enter full postcode");
	} else {
		var url = 'ajax_property_postcode.php';
		var pars = 'number=' + pc_number + '&postcode=' + pc_postcode;
		//alert(pars);
		Ajax.Responders.register(myGlobalHandlers);
		var myAjax = new Ajax.Updater(
				'placeholder',
				url,
				{
					method     : 'get',
					parameters : pars
				}
		);
	}
}

function getAddrUDPRNP()
{
	var pc_pcid = $F('pro_pcid2');
	var url = 'ajax_property_postcode.php';
	var pars = 'pcid=' + pc_pcid;

	var myAjax = new Ajax.Updater(
			'placeholder',
			url,
			{
				method     : 'get',
				parameters : pars
			});
}


function showResponse(originalRequest)
{
	//put returned XML in the textarea
	$('result').value = originalRequest.responseText;
}

function cancelResponse()
{
	Element.show('lookup');
	Element.hide('placeholder');
	var thebutton = document.getElementById('getAddress');
	thebutton.disabled = false;
	thebutton.value = "Get Address";
}

function manualAddress()
{
	var url = 'ajax_client_postcode.php';
	var pars = 'number=manual&postcode=manual';
	//alert(pars);
	Ajax.Responders.register(myGlobalHandlers);
	var myAjax = new Ajax.Updater(
			'placeholder',
			url,
			{
				method     : 'get',
				parameters : pars
			}
	);
}

/* revised ajax functions for postcode lookups - 03/08/06 */

function ajax_lookup()
{
	/* lookup type can be by_freetext,by_postcode,browse */
	/*if (!lookup_type) {*/
	var lookup_type = $F('lookup_type');
	/*}*/

	/* scope: cli, pro or con */
	var scope = $F('scope');
	/* search string must be formatted to suit lookup type */
	if (lookup_type == 'by_postcode') {
		var postcode = $F('postcode');
		var number = $F('number');
		/* by_postcode requires a full postcode */
		if (postcode.length < 6) {
			alert("Please enter full postcode");
			return;
		}
		var search_string = postcode;
		/* add number to the end if present */
		if (number) {
			search_string = search_string + ',' + number;
		}
	}
	else {
		if (lookup_type == 'by_freetext') {
			var number = $F('number');
			var street = $F('street');
			if (street.length < 3) {
				alert("Please enter full or part of street name");
				return;
			}
			var postcode = $F('postcode');
			if (postcode.length < 2) {
				alert("Please enter full or part of postcode");
				return;
			}
			/* remove spaces, and trim postcode to first part only */
			var postcode2 = postcode.replace(/ /, "");
			if (postcode2.length == 6) {
				postcode = postcode.slice(0, 3);
			}
			else {
				if (postcode2.length == 7) {
					postcode = postcode.slice(0, 4);
				}
			}
			/* trim end of postcode (not used, cant guarantee a space was entered)
			 var postcode_split = postcode.split(" ");*/
			var search_string = number + ',' + street + ',' + postcode;

		}
	}

	var thebutton = document.getElementById('getAddress');
	thebutton.disabled = true;
	thebutton.value = "Please wait...";

	var url = '/v3.0/live/admin/ajax_postcode.php';
	var pars = 'scope=' + scope + '&lookup_type=' + lookup_type + '&search_string=' + search_string;
	Ajax.Responders.register(myGlobalHandlers);
	var myAjax = new Ajax.Updater(
			'placeholder',
			url,
			{
				method     : 'get',
				parameters : pars
			}
	);

}


/* perform query by udprn - 03/08/06 */
function ajax_udrpn()
{
	var search_string = $F('udprn');
	var scope = $F('scope');
	var url = '/v3.0/live/admin/ajax_postcode.php';

	var pars = 'scope=' + scope + '&lookup_type=udprn&search_string=' + search_string;

	var myAjax = new Ajax.Updater(
			'placeholder',
			url,
			{
				method     : 'get',
				parameters : pars
			}
	);
}


function ajax_manual()
{
	var scope = $F('scope');
	var url = '/v3.0/live/admin/ajax_postcode.php';
	var pars = 'scope=' + scope + '&lookup_type=manual';

	Ajax.Responders.register(myGlobalHandlers);
	var myAjax = new Ajax.Updater(
			'placeholder',
			url,
			{
				method     : 'get',
				parameters : pars
			}
	);
}

function ajax_select_address(button_id)
{
	var udprn = $F('udprn');
	if (udprn.length < 1) {
		alert("Please select an address from the list");
		return;
	}
	var thebutton = document.getElementById(button_id);
	thebutton.disabled = true;
	thebutton.value = "Please wait...";

	ajax_udrpn();
}


/* fix google's autocomplete yellow field highlighting */
if (window.attachEvent) {
	window.attachEvent("onload", setListeners);
}

function setListeners()
{
	inputList = document.getElementsByTagName("INPUT");
	for (i = 0; i < inputList.length; i++) {
		inputList[i].attachEvent("onpropertychange", restoreStyles);
		inputList[i].style.backgroundColor = "";
	}
	selectList = document.getElementsByTagName("SELECT");
	for (i = 0; i < selectList.length; i++) {
		selectList[i].attachEvent("onpropertychange", restoreStyles);
		selectList[i].style.backgroundColor = "";
	}
}

function restoreStyles()
{
	if (event.srcElement.style.backgroundColor != "") {
		event.srcElement.style.backgroundColor = "";
	}
}
/* end google fix */


/***********************************************
 * Local Time script- � Dynamic Drive (http://www.dynamicdrive.com)
 * This notice MUST stay intact for legal use
 * Visit http://www.dynamicdrive.com/ for this script and 100s more.
 ***********************************************/

function showLocalTime(container, servertimestring)
{
	return;
	if (!document.getElementById || !document.getElementById(container)) return
	this.container = document.getElementById(container)
	this.localtime = this.serverdate = new Date(servertimestring)
	this.updateTime()
	this.updateContainer()
}

showLocalTime.prototype.updateTime = function ()
{
	var thisobj = this
	this.localtime.setSeconds(this.localtime.getSeconds() + 1)
	setTimeout(function ()
			   {
				   thisobj.updateTime()
			   }, 1000) //update time every second
}

showLocalTime.prototype.updateContainer = function ()
{
	var thisobj = this

	var hour = this.localtime.getHours()
	var minutes = this.localtime.getMinutes()
	var seconds = this.localtime.getSeconds()
	var ampm = (hour >= 12) ? "PM" : "AM"
	this.container.innerHTML = formatField(hour, 1) + ":" + formatField(minutes) + " " + ampm

	setTimeout(function ()
			   {
				   thisobj.updateContainer()
			   }, 10000) //update container every 10 seconds
}

function formatField(num, isHour)
{
	if (typeof isHour != "undefined") { //if this is the hour field
		var hour = (num > 12) ? num - 12 : num
		return (hour == 0) ? 12 : hour
	}
	return (num <= 9) ? "0" + num : num//if this is minute or sec field
}