<?php
require_once("inx/global.inc.php");
/*
new version of appointment page, allows adding and editing (adding of some will relocate, i.e. viewing_add.php)
switches to break this page up by app_type. each appointmenmt type is handles individually
common data will be collected at the top of the page to include:
all values from the appointment table (type, datetime, etc), linked user record for createdby
we will also get a list of branches and a list of negotiators for general use before starting the switch






All
all appointments have a single user, which is used for colouring and tracking

Viewing
many properties(link_deal_to_appointment), many clients(viewer - cli2app), many attendees(use2app)

Valuation
many properies(link_deal_to_appointment), many clients(vendor/landlord/tenant- cli2app), many attendees(use2app)

Production
many properties(link_deal_to_appointment), many clients, many attendees(use2app)

Survey
single property(link_deal_to_appointment), many clients, single surveyor(conact), many attendees(use2app)

Meeting
subject, location, many attendees(use2app)

Lunch
location

Holiday
subject, location

*/
// get list of negs (currently all staff)
$negotiators[] = "(not assigned)"; // blank option
$attendees = '<option value="">Add Attendee</option>'."\n";
$sql = "SELECT use_id,CONCAT(use_fname,' ',use_sname) AS use_name FROM user WHERE use_status = 'Active'
ORDER BY CONCAT(use_fname,' ',use_sname) ASC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$negotiators[$row["use_id"]] = $row["use_name"];
	$attendees .= '<option value="'.$row["use_id"].'">'.$row["use_name"].'</option>'."\n";
	}

// list branches
$sql = "SELECT bra_id,bra_title FROM calendar WHERE bra_status = 'Active'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$branches[$row["bra_id"]] = $row["bra_title"];
	}

if (!$_GET["app_id"]) {
	$pagemode = "add";
	} else {
	$app_id = $_GET["app_id"];
	$pagemode = "edit";
	}

if ($pagemode == "add") {

	// diverts for wizards
	if ($_GET["app_type"] == "Viewing") {
		header("Loctation:viewing_add.php");
		exit;
		} elseif ($_GET["app_type"] == "Valuation") {
		header("Loctation:valuation_add.php");
		exit;
		} elseif ($_GET["app_type"] == "Production") {

		exit;
		} elseif ($_GET["app_type"] == "Survey") {
		header("Loctation:survey_add.php");
		exit;
		} elseif ($_GET["app_type"] == "Meeting") {

		exit;
		} elseif ($_GET["app_type"] == "Lunch") {

		exit;
		} elseif ($_GET["app_type"] == "Holiday") {

		exit;
		}


	}
elseif ($pagemode == "edit") {

	// appointment fields, plus lead user and booked by user.
	$sql = "SELECT
	appointment.*,
	user.use_id, CONCAT(user.use_fname,' ',user.use_sname) AS use_name,
	bookedby.use_id, CONCAT(bookedby.use_fname,' ',bookedby.use_sname) AS app_bookedbyname
	FROM appointment
	LEFT JOIN user ON appointment.app_user = user.use_id
	LEFT JOIN user AS bookedby ON appointment.app_bookedby = bookedby.use_id
	WHERE app_id = $app_id
	LIMIT 1";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	while ($row = $q->fetchRow()) {
		$app_type = $row["app_type"];
		$app_start = $row["app_start"];
		$app_user = $row["app_user"];
		$use_id = $row["use_id"];
		$use_name = $row["use_name"];
		$app_bookedbyname = $row["app_bookedbyname"];
		$app_created = $row["app_created"];
		$split = explode(" ",$app_start);
		// date
		if ($split[0] == '0000-00-00') {
			$app_date = date('d/m/Y');
			} else {
			$parts = explode("-",$split[0]);
			$y = $parts[0];
			$m = $parts[1];
			$d = $parts[2];
			$app_date = $d.'/'.$m.'/'.$y;
			}
		// time
		if ($split[1] == '00:00:00') {
			$app_time = date('G:i');
			} else {
			$app_time = $split[1];
			}
		}

	}


// app_type has been set, either from db or querystring
switch ($app_type):
case "Viewing":

$sql = "SELECT
dea_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
GROUP_CONCAT(CONCAT(vendor.cli_fname,' ',vendor.cli_sname) SEPARATOR ', ') AS vendor_name,
viewer.cli_id AS viewer_id,CONCAT(viewer.cli_salutation,' ',viewer.cli_fname,' ',viewer.cli_sname) AS viewer_name,
attendee.use_id AS attendee_id,CONCAT(attendee.use_fname,' ',attendee.use_sname) AS attendee_name,attendee.use_colour,
d2a_id,d2a_ord

FROM appointment

LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id

LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id

LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id

LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
LEFT JOIN user AS attendee ON use2app.u2a_use = user.use_id
WHERE
appointment.app_id = $app_id
GROUP BY deal.dea_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
$count = 1;
while ($row = $q->fetchRow()) {

	if ($row["viewer_id"]) { // array of viewers
		$clients[$row["viewer_id"]] = $row["viewer_name"];
		}
	if ($row["attendee_id"]) { // array of attendees
		$users[$row["attendee_id"]] = $row["attendee_name"];
		}

$deal_table .= '
<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" valign="top">
<td width="10">'.$row["d2a_ord"].':</td>
<td class="bold">'.$row["pro_addr"].'</td>
<td width="200">'.$row["vendor_name"].'</td>
<td width="50" nowrap="nowrap">';

// disable first arrow
if ($row["d2a_ord"] == 1) {
	$deal_table .= '<img src="/images/sys/admin/icons/arrow_up_sm_grey.gif" border="0" alt="Move Up" height="16" width="16">';
	} else {
	$deal_table .= '<a href="?do=reorder&app_id='.$app_id.'&d2a_id='.$row["d2a_id"].'&cur='.$row["d2a_ord"].'&new='.($row["d2a_ord"]-1).'"><img src="/images/sys/admin/icons/arrow_up_sm.gif" border="0" alt="Move Up" height="16" width="16"></a>';
	}
// disable last arrow
if ($count == $numRows) {
	$deal_table .= '<img src="/images/sys/admin/icons/arrow_down_sm_grey.gif" border="0" alt="Move Down" height="16" width="16">';
	} else {
	$deal_table .= '<a href="?do=reorder&app_id='.$app_id.'&d2a_id='.$row["d2a_id"].'&cur='.$row["d2a_ord"].'&new='.($row["d2a_ord"]+1).'"><img src="/images/sys/admin/icons/arrow_down_sm.gif" border="0" alt="Move Down" height="16" width="16"></a>';
	}
// prevent last deal from being deleted
if ($numRows == 1) {
	$deal_table .= '<img src="/images/sys/admin/icons/cross_sm_grey.gif" border="0" width="16" height="16" hspace="1" alt="Remove from appointment" />';
	} else {
	$deal_table .= '<a href="?do=delete&app_id='.$app_id.'&d2a_id='.$row["d2a_id"].'"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" width="16" height="16" hspace="1" alt="Remove from appointment" /></a>';
	}
$deal_table .= '</td>
</tr>
';
	$count++;
	}

$deal_table = '
<div id="results_table">
<table>
<tr>
<th colspan="2">Property</th><th>Vendor(s)</th><th>&nbsp;</th></tr>
'.$deal_table.'</table>
</div>';


// build table of linked clients
if ($clients) {
	foreach($clients AS $cli_id => $cli_name) {
		$client_table .= '<tr><td height="20"><a href="client_edit.php?cli_id='.$cli_id.'&searchLink='.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'">'.$cli_name.'</a></td><td align="right">';
		if (count($clients) == 1) {
			$client_table .= '';
			} else {
			$client_table .= '<a href="?do=remove_client&app_id='.$app_id.'&cli_id='.$cli_id.'&return='.urlencode('?'.$_SERVER['QUERY_STRING']).'"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove '.$cli_name.' from this appointment"></a>';
			}
		$client_table .= '</td></tr>';
		}
	}
$client_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Viewer(s)</td>
	<td>
	  <table width="200" cellpadding="0" cellspacing="0" border="0">
	  '.$client_table.'
	    <tr>
		  <td height="20"><input type="button" value="Add Viewer" onClick="document.location.href = \'client_lookup.php?dest=add_client_to_viewing&app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';" class="button"></td>
	    </tr>
	  </table>
	</td>
  </tr>
</table>';

// build table of linked users (attendees)
if ($attendee) {
	asort($attendee);
	foreach($attendee AS $use_id => $use_name) {
		$user_table .= '<tr><td height="20">'.$use_name.'</td><td align="right">';
		$user_table .= '<a href="?do=remove_user&app_id='.$app_id.'&use_id='.$use_id.'&return='.urlencode('?'.$_SERVER['QUERY_STRING']).'"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove '.$use_name.' from this appointment"></a>';
		$user_table .= '</td></tr>';
		}
	}
$user_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Attendee(s)</td>
	<td>
	  <table width="200" cellpadding="0" cellspacing="0" border="0">
	  '.$user_table.'
	    <tr>
		  <td height="20"><select name="attendee" onChange="javascript:addUserToAppointment('.$app_id.',document.forms[0].attendee.options[document.forms[0].attendee.selectedIndex].value,\''.urlencode($_GET['searchLink']).'\')">'.$attendees.'</select><!--<input type="button" value="Add Neg" onClick="javascript:addUserToAppointment('.$app_id.',document.forms[0].neg.options[document.forms[0].neg.selectedIndex].value)" class="button">--></td>
	    </tr>
	  </table>
	</td>
  </tr>
</table>';


for ($i = 5; $i <= 300;) {
	$app_duration_data[$i] = duration($i);
	$i = $i+5;
	}

$formData1 = array(
	'calendarID'=>array(
		'type'=>'select',
		'label'=>'Calendar',
		'value'=>$calendarID,
		'attributes'=>array('class'=>'medium'),
		'options'=>$branches
		),
	'app_user'=>array(
		'type'=>'select',
		'label'=>'Negotiator',
		'value'=>$app_user,
		'attributes'=>array('class'=>'medium'),
		'options'=>$negotiators
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app_date,
		'attributes'=>array('class'=>'medium','readonly'=>'readonly')
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app_time
		),
	'app_duration'=>array(
		'type'=>'select_duration',
		'label'=>'Duration',
		'value'=>$duration,
		'attributes'=>array('class'=>'medium')
		)
	);


break;

case "Valuation":
break;

case "Production":
break;

case "Survey":
break;

case "Meeting":
break;

case "Lunch":
break;

case "Holiday":
break;

endswitch;




if (!$_GET["action"]) {

$form = new Form();

$form->addForm("app_form","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));


$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app_type . '</div>');
$form->addHtml($survey_table);
if ($formData0) {
	$form->addData($formData0,$_GET);
	}
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addSeperator();
$form->addHtml($client_table);
$form->addHtml($user_table);
$form->addHtml($deal_table);
if ($app_type == 'Viewing' || $app_type == 'Valuation') {
	$form->addHtml($form->addDiv($form->makeField("button",$formName,"","Add Properties",array('class'=>'submit','onClick'=>'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&cli_id='.$cli_id.'&app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'))));
	}
$form->addHtml("</fieldset>\n");
$form->addHtml($appointment_info);
$form->addHtml('</div>');

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>str_replace("%3F","?",replaceQueryStringArray($_GET["searchLink"],array('app_id'))).'&jumpto='.$hour),
	'search'=>array('title'=>'Appointment Search','label'=>'Appointment Search','link'=>'appointment_search.php')
	);
$navbar = navbar2($navbar_array); //replaceQueryStringArray($_GET["searchLink"],array('app_id'))

$page = new HTML_Page2($page_defaults);
$page->setTitle("Appointment");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addScript('js/CalendarPopup.js');
$page->addScriptDeclaration('document.write(getCalendarStyles());var popcalapp_date = new CalendarPopup("popCalDivapp_date");popcalapp_date.showYearNavigation(); ');
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

}




$appointment_info = "
<pre>
$app_type (id: $app_id)
User: $use_name
Booked by: $app_bookedbyname
Created on: $app_created
</pre>";
echo $appointment_info;
exit;







// remove deal from current appointment, and reorder
if ($_GET["action"] == "delete") {
	$d2a_id = $_GET["d2a_id"];

	$sql = "DELETE FROM link_deal_to_appointment WHERE d2a_id = $d2a_id";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

	// re-number the order of all remaining deals in this appointment
	$sql = "SELECT d2a_id FROM link_deal_to_appointment WHERE d2a_appointment = $app_id ORDER BY d2a_order ASC";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$count = 1;
	while ($row = $q->fetchRow()) {
		$sql2 = "UPDATE link_deal_to_appointment SET d2a_order = $count WHERE d2a_id = ".$row["d2a_id"];
		$q2 = $db->query($sql2);
		$count++;
		}

	header("Location:?app_id=$app_id");
	}

// reorder deals within appointment
if ($_GET["action"] == "reorder") {

	$this_d2a_id = $_GET["d2a_id"];
	$cur = $_GET["cur"]; // current position (dont need this, it is in the table already)
	$new = $_GET["new"]; // new position (position to move the deal to, we need to update this position with the postiion it replaces)

	// get id of deal in position we want to move our deal to
	$sql = "SELECT d2a_id,d2a_order FROM link_deal_to_appointment WHERE d2a_appointment = $app_id AND d2a_order = $new";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$other_d2a_id = $row["d2a_id"];
		$other_d2a_order = $row["d2a_order"];
		}

	// update this row with new position
	$db_data["d2a_order"] = $new;
	db_query($db_data,"UPDATE","link_deal_to_appointment","d2a_id",$this_d2a_id);
	unset($db_data);

	// update other row with new position
	$db_data["d2a_order"] = $cur;
	db_query($db_data,"UPDATE","link_deal_to_appointment","d2a_id",$other_d2a_id);
	unset($db_data);

	header("Location:?app_id=$app_id");
	}



// first we get the type of appointment, as the sql and joins will differ for each type
// also get details which are to be used throughout this page, to make later queries simpler...
$sql = "SELECT
appointment.*,
cli_id,CONCAT(cli_fname,' ',cli_sname) AS cli_name,CONCAT(cli_tel1,' (',cli_tel1type,')') AS cli_tel,
cli_tel1,cli_tel1type,cli_tel2,cli_tel2type,cli_tel3,cli_tel3type
FROM appointment
LEFT JOIN client ON appointment.app_client = client.cli_id
WHERE app_id = $app_id
LIMIT 1";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$cli_id = $row["cli_id"];
	$cli_name = $row["cli_name"];
	$cli_tel = $row["cli_tel"];
	$app_type = $row["app_type"];
	$app_start = $row["app_start"];
	$split = explode(" ",$app_start);
	// date
	if ($split[0] == '0000-00-00') {
		$app_date = date('d/m/Y');
		} else {
		$parts = explode("-",$split[0]);
		$y = $parts[0];
		$m = $parts[1];
		$d = $parts[2];
		$app_date = $d.'/'.$m.'/'.$y;
		}
	// time
	if ($split[1] == '00:00:00') {
		$app_time = date('G:i');
		} else {
		$app_time = $split[1];
		}


	// to put all client's phone numbers in a drop down, for primary contact selection (might not use this)
	$tels[$row["cli_tel1"]] = $row["cli_tel1type"].' ('.$row["cli_tel1"].')';
	if ($row["cli_tel2"]) {
		$tels[$row["cli_tel2"]] = $row["cli_tel2type"].' ('.$row["cli_tel2"].')';
		}
	if ($row["cli_tel3"]) {
		$tels[$row["cli_tel3"]] = $row["cli_tel3type"].' ('.$row["cli_tel3"].')';
		}
	}

$page = new HTML_Page2($page_defaults);

if (!$_GET["action"]) {

switch ($app_type):
/////////////////////////////////////////////////////////////////////////////
// Viewing:
// (viewings are created on viewing_add.php)
// multiple deals and clients are permitted
// single user only (this is becuase a: we need to give the appointment a colour, and b: so we can track user's activity)
// add/remove deals from the appointment (via link_deal_to_appointment link table)
// add/remove clients from the appointment (via cli2app link table)
// change date/time/neg etc
// sql suitable for viewing appointments, selecting property details
// get vendor name(s), group by deal; get property address
/////////////////////////////////////////////////////////////////////////////
case "Viewing":

$sql = "SELECT
dea_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname))  AS cli_name,
d2a_id,d2a_order

FROM appointment

LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_appointment
LEFT JOIN deal ON link_deal_to_appointment.d2a_deal = deal.dea_id

LEFT JOIN property ON deal.dea_prop = property.pro_id

LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id

WHERE
link_deal_to_appointment.d2a_appointment = $app_id
GROUP BY deal.dea_id
ORDER BY link_deal_to_appointment.d2a_order ASC
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
$count = 1;
while ($row = $q->fetchRow()) {

	$duration = $duration+$default_appointment_duration;

	$deal_table .= '
<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" valign="top">
<td width="10">'.$row["d2a_order"].':</td>
<td class="bold">'.$row["pro_addr"].'</td>
<td width="200">'.$row["cli_name"].'</td>
<td width="50" nowrap="nowrap">';

// disable first arrow
if ($row["d2a_order"] == 1) {
	$deal_table .= '<img src="/images/sys/admin/icons/arrow_up_sm_grey.gif" border="0" alt="Move Up" height="16" width="16">';
	} else {
	$deal_table .= '<a href="?action=reorder&app_id='.$app_id.'&d2a_id='.$row["d2a_id"].'&cur='.$row["d2a_order"].'&new='.($row["d2a_order"]-1).'"><img src="/images/sys/admin/icons/arrow_up_sm.gif" border="0" alt="Move Up" height="16" width="16"></a>';
	}
// disable last arrow
if ($count == $numRows) {
	$deal_table .= '<img src="/images/sys/admin/icons/arrow_down_sm_grey.gif" border="0" alt="Move Down" height="16" width="16">';
	} else {
	$deal_table .= '<a href="?action=reorder&app_id='.$app_id.'&d2a_id='.$row["d2a_id"].'&cur='.$row["d2a_order"].'&new='.($row["d2a_order"]+1).'"><img src="/images/sys/admin/icons/arrow_down_sm.gif" border="0" alt="Move Down" height="16" width="16"></a>';
	}
// prevent last deal from being deleted
if ($numRows == 1) {
	$deal_table .= '<img src="/images/sys/admin/icons/cross_sm_grey.gif" border="0" width="16" height="16" hspace="1" alt="Remove from Viewing" />';
	} else {
	$deal_table .= '<a href="?action=delete&app_id='.$app_id.'&d2a_id='.$row["d2a_id"].'"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" width="16" height="16" hspace="1" alt="Remove from Viewing" /></a>';
	}
$deal_table .= '</td>
</tr>
';
	$count++;
	}

$deal_table = '
<div id="results_table">
<table>
<tr>
<th colspan="2">Property</th><th>Vendor(s)</th><th>&nbsp;</th></tr>
'.$deal_table.'</table>
</div>';



// get list of negs (currently all staff)
#$negotiators[] = '-- select --'; // blank option
$sql = "SELECT use_id,CONCAT(use_fname,' ',use_sname) AS use_name FROM user
WHERE use_status = 'Active'
ORDER BY CONCAT(use_fname,' ',use_sname) ASC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$negotiators[$row["use_id"]] = $row["use_name"];
	}

$formData1 = array(
	'app_title'=>array(
		'type'=>'text',
		'label'=>'Title',
		'value'=>$app_type.': '.$cli_name,
		'attributes'=>array('style'=>'width:250px'),
		'group'=>'Title'
		),
	'cli_tel'=>array(
		'type'=>'select',
		'label'=>'Tel',
		'options'=>$tels,
		'group'=>'Title',
		'last_in_group'=>1
		),
	'app_neg'=>array(
		'type'=>'select',
		'label'=>'Negotiator',
		'value'=>$app_neg,
		'default'=>$_SESSION["auth"]["use_id"],
		'options'=>$negotiators,
		'attributes'=>array('class'=>'medium')
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app_date,
		'attributes'=>array('class'=>'medium')
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app_time
		),
	'app_duration'=>array(
		'type'=>'select',
		'label'=>'Estimated Duration',
		'value'=>$duration,
		'options'=>array(
			'5'=>'5 minutes',
			'10'=>'10 minutes',
			'15'=>'15 minutes',
			'20'=>'20 minutes',
			'25'=>'25 minutes',
			'30'=>'30 minutes',
			'45'=>'45 minutes',
			'60'=>'1 hour',
			'75'=>'1 hour, 15 minutes',
			'90'=>'1 hour, 30 minutes'
			),
		'attributes'=>array('class'=>'medium')
		)
	);


$form = new Form();

$form->addForm("app_form","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));


$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app_type . '</div>');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'))));
$form->addHtml($deal_table);
$form->addHtml($form->addDiv($form->makeField("button",$formName,"","Add Properties",array('class'=>'submit','onClick'=>'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&cli_id='.$cli_id.'&app_id='.$app_id.'\';'))));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


$page->setTitle("Appointment");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addScript('js/CalendarPopup.js');
$page->addScriptDeclaration('document.write(getCalendarStyles());var popcal = new CalendarPopup("popCalDiv");popcal.showYearNavigation(); ');
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;


break;
/////////////////////////////////////////////////////////////////////////////
// Valuation:
// (valuations are created on valuation_add.php page)
// multiple clients are permitted
// single user only (this is becuase a: we need to give the appointment a colour, and b: so we can track user's activity)
// single deal only (multiple valuations means multiple appointments)
// valuer name and company, from contacts (or directory?)
/////////////////////////////////////////////////////////////////////////////
case "Valuation":


break;
endswitch;


} else {
// if form is submitted



print_r($_GET);

$date_parts = explode("/",$_GET["app_date"]);
$day = $date_parts[0];
$month = $date_parts[1];
$year = $date_parts[2];

$app_date = $year.'-'.$month.'-'.$day;
echo $app_date."<p>";
$app_start = $app_date.' '.$app_time_hour.':'.$app_time_min.':00';
echo $app_start."<p>";

$app_start = strtotime($app_start);
$app_end = $app_start + ($_GET["app_duration"] * 60);
echo $app_start."<p>";

echo date('Y-m-d G:i:s',$app_start);
echo "<p>";
echo date('Y-m-d G:i:s',$app_end);


// returns mysql start and end dates from start date (uk format) and duration (minutes)
function date_app_length($date,$time,$duration) {
	// dates are supplied in uk format, so first we split it into parts and re-arrange to mysql format
	$date_parts = explode("/",$start);
	$day = $date_parts[0];
	$month = $date_parts[1];
	$year = $date_parts[2];
	$start = $month.'/'.$day.'/'.$year;

	}

}

?>