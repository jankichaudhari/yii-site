<?php
require_once("inx/global.inc.php");
/*
appointments that occour in the past (say over 24 hours old, to allow for any "next-day" changes) are
read only, and need a different layout to fit more info on the page. It will be possible to leave feedback
on completed appointments, and re-book (create new appointment) cancelled appointments

*/


if ($_GET["app_id"]) {
	$app_id = $_GET["app_id"];
	} elseif ($_POST["app_id"]) {
	$app_id = $_POST["app_id"];
	} else {
	echo "no app_id";
	exit;
	}


// appointment fields plus: lead user, booked by user and attendees
// store in array named "app"
$sql = "SELECT
appointment.*,DATE_FORMAT(appointment.app_created, '%W %D %M %Y %T') AS app_created,
user.use_id, CONCAT(user.use_fname,' ',user.use_sname) AS use_name,
attendee.use_id AS attendee_id,CONCAT(attendee.use_fname,' ',attendee.use_sname) AS attendee_name,attendee.use_colour,
bookedby.use_id, CONCAT(bookedby.use_fname,' ',bookedby.use_sname) AS app_bookedbyname
FROM appointment
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN user AS bookedby ON appointment.app_bookedby = bookedby.use_id
LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id
WHERE app_id = $app_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$app["id"] = $row["app_id"];
	$app["type"] = $row["app_type"];
	$app["user"] = $row["app_user"];
	$app["branch"] = $row["calendarID"];
	$app["notes"] = $row["app_notes"];
	$app["subject"] = $row["app_subject"];
	$app["allday"] = $row["app_allday"];
	$app["private"] = $row["app_private"];
	$app["user_name"] = $row["use_name"];
	$app["bookedbyname"] = $row["app_bookedbyname"];
	$app["created"] = $row["app_created"];
	$app["start"] = $row["app_start"];
	$split = explode(" ",$row["app_start"]);
	// date
	if ($split[0] == '0000-00-00') {
		$app["date"] = date('d/m/Y');
		} else {
		$parts = explode("-",$split[0]);
		$y = $parts[0];
		$m = $parts[1];
		$d = $parts[2];
		$app["date"] = $d.'/'.$m.'/'.$y;
		}
	// time
	if ($split[1] == '00:00:00') {
		$app["time"] = date('G:i');
		} else {
		$app["time"] = $split[1];
		}
	// duration
	$duration = (strtotime($row["app_end"]) - strtotime($row["app_start"]))/60;

	// array of attendees
	if ($row["attendee_id"]) {
		$attendees[$row["attendee_id"]] = $row["attendee_name"];
		}
	// appointment info
	$appointment_info = '<pre>
  Type:       '.$app["type"].' (ID: '.$app["id"].')
  Booked by:  '.$app["bookedbyname"].'
  Created on: '.$app["created"].'</pre>';
	}

for ($i = 5; $i <= 300;) { // what is that 300?
	$app_duration_data[$i] = duration($i);
	$i = $i+5;
	}



//////////////////////////////////////////////////////////////////
// if appointment occours under (default_appointment_delay) hours in the past, divert to appointment_edit.php
//////////////////////////////////////////////////////////////////

$app_comparison_today = strtotime($date_mysql);
$app_comparison_start = strtotime($app["start"]);
$app_comparison_start = ($app_comparison_start + $default_appointment_delay);

if ($app_comparison_start > $app_comparison_today) {
	header("Location:appointment_edit.php?app_id=$app_id");
	exit;
	}




//////////////////////////////////////////////////////////////////
// now page splits into app_type switch
// anything above is common, anything below is specific
//////////////////////////////////////////////////////////////////
switch ($app["type"]):

/*
Viewing
many deals(link_deal_to_appointment), many clients(viewer - cli2app), many attendees(use2app)
property (link_deal_to_appointment)
vendors (link_client_to_instruction)
viewers (cli2app)
attendees (use2app)
*/

case "Viewing":

$sql = "SELECT
dea_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
viewer.cli_id AS viewer_id,
CONCAT(viewer.cli_salutation,' ',viewer.cli_fname,' ',viewer.cli_sname) AS viewer_name,
d2a_id,d2a_ord,d2a_cv,d2a_feedback
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id
WHERE
appointment.app_id = $app_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
$count = 1;
while ($row = $q->fetchRow()) {

	// array of properties (deals)
	$deals[$row["d2a_ord"]] = array(
		'dea_id'=>$row["dea_id"],
		'd2a_id'=>$row["d2a_id"],
		'd2a_ord'=>$row["d2a_ord"],
		'd2a_cv'=>$row["d2a_cv"],
		'd2a_feedback'=>$row["d2a_feedback"],
		'addr'=>$row["pro_addr"]
		);
	// array of viewers (clients)
	if ($row["viewer_id"]) {
		$viewers[$row["viewer_id"]] = $row["viewer_name"];
		}
	}

$formData1 = array(
	'fee_outcome'=>array(
		'type'=>'radio',
		'label'=>'Outcome',
		'value'=>$fee_outcome,
		'required'=>2,
		'options'=>db_enum("feedback","fee_outcome","array")
		),
	'fee_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$fee_notes,
		'attributes'=>array('class'=>'wide','style'=>'height:50px')
		)
	);

// build table
$render = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Date/Time</td>
	<td>'.date('l jS F Y G:i',strtotime($app["start"])).'</td>
  </tr>
  <tr>
    <td class="label" valign="top" width="158">Negotiator</td>
	<td>'.$app["user_name"].'</td>
  </tr>
</table>
';
$render .= renderViewerTable($viewers,$app_id,'readonly');
if ($app["notes"]) {
$render .= '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Notes</td>
	<td>'.$app["notes"].'</td>
  </tr>
</table>
';
}
$render .= renderAttendeeTable($attendees,$app_id,'readonly');
$render .= renderDealTable($deals,$app_id,'feedback');



$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app["type"] . '</div>');
$form->addHtml($render);

#$form->addData($formData1,$_POST);
#$buttons = $form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'));
#$buttons .= $form->makeField("button","","","View in Calendar",array('class'=>'submit','onClick'=>'javascript:document.location.href=\'calendar.php?app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'));
#$form->addHtml($form->addDiv($buttons));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


break;

/*
Valuation
many deals(link_deal_to_appointment), many clients(viewer - cli2app), many attendees(use2app)
property (link_deal_to_appointment)
vendors (link_client_to_instruction)
viewers (cli2app)
attendees (use2app)
*/
case "Valuation":

$sql = "SELECT
dea_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
vendor.cli_id AS vendor_id,CONCAT(vendor.cli_salutation,' ',vendor.cli_fname,' ',vendor.cli_sname) AS vendor_name,
d2a_id,d2a_ord,d2a_cv
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS vendor ON cli2app.c2a_cli = vendor.cli_id
WHERE
appointment.app_id = $app_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
$count = 1;
while ($row = $q->fetchRow()) {

	// array of properties (deals)
	$deals[$row["d2a_ord"]] = array(
		'dea_id'=>$row["dea_id"],
		'd2a_id'=>$row["d2a_id"],
		'd2a_ord'=>$row["d2a_ord"],
		'd2a_cv'=>$row["d2a_cv"],
		'addr'=>$row["pro_addr"]
		);
	// array of vendors/landlords/tenants (clients)
	if ($row["vendor_id"]) {
		$vendors[$row["vendor_id"]] = $row["vendor_name"];
		}
	}

$formData1 = array(
	'calendarID'=>array(
		'type'=>'select_branch',
		'label'=>'Branch',
		'value'=>$app["branch"],
		'attributes'=>array('class'=>'medium')
		),
	'app_user'=>array(
		'type'=>'select_user',
		'label'=>'Negotiator',
		'value'=>$app["user"],
		'attributes'=>array('class'=>'medium')
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app["date"],
		'attributes'=>array('class'=>'medium','readonly'=>'readonly')
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app["time"]
		),
	'app_duration'=>array(
		'type'=>'select_duration',
		'label'=>'Duration',
		'value'=>$duration,
		'attributes'=>array('class'=>'medium')
		),
	'app_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$app["notes"],
		'attributes'=>array('class'=>'wide','style'=>'height:50px')
		)
	);


$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app["type"] . '</div>');
$form->addData($formData1,$_POST);
$buttons = $form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","View in Calendar",array('class'=>'button','onClick'=>'javascript:document.location.href=\'calendar.php?app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'));
$form->addHtml($form->addDiv($buttons));
$form->addSeperator();
$form->addHtml(renderVendorTable($vendors,$app_id));
$form->addHtml(renderAttendeeTable($attendees,$app_id));
$form->addHtml(renderDealTable($deals,$app_id));
$form->addHtml($form->addDiv($form->makeField("button",$formName,"","Add Properties",array('class'=>'submit','onClick'=>'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'))));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');



break;

case "Production":

// similar to valuation with contacts (vendor, landlord, tenant)
// muliple properties allowed

$sql = "SELECT
dea_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
vendor.cli_id AS vendor_id,CONCAT(vendor.cli_salutation,' ',vendor.cli_fname,' ',vendor.cli_sname) AS vendor_name,
d2a_id,d2a_ord,d2a_cv
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS vendor ON cli2app.c2a_cli = vendor.cli_id
WHERE
appointment.app_id = $app_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
$count = 1;
while ($row = $q->fetchRow()) {

	// array of properties (deals)
	$deals[$row["d2a_ord"]] = array(
		'dea_id'=>$row["dea_id"],
		'd2a_id'=>$row["d2a_id"],
		'd2a_ord'=>$row["d2a_ord"],
		'd2a_cv'=>$row["d2a_cv"],
		'addr'=>$row["pro_addr"]
		);
	// array of vendors/landlords/tenants (clients)
	if ($row["vendor_id"]) {
		$vendors[$row["vendor_id"]] = $row["vendor_name"];
		}
	}


$formData1 = array(
	'calendarID'=>array(
		'type'=>'select_branch',
		'label'=>'Branch',
		'value'=>$app["branch"],
		'attributes'=>array('class'=>'medium')
		),
	'app_user'=>array(
		'type'=>'select_user',
		'label'=>'User',
		'value'=>$app["user"],
		'attributes'=>array('class'=>'medium')
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app["date"],
		'attributes'=>array('class'=>'medium','readonly'=>'readonly')
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app["time"]
		),
	'app_duration'=>array(
		'type'=>'select_duration',
		'label'=>'Duration',
		'value'=>$duration,
		'attributes'=>array('class'=>'medium')
		),
	'app_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$app["notes"],
		'attributes'=>array('class'=>'wide','style'=>'height:50px')
		)
	);


$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app["type"] . '</div>');
$form->addData($formData1,$_POST);
$buttons = $form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","View in Calendar",array('class'=>'button','onClick'=>'javascript:document.location.href=\'calendar.php?app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'));
$form->addHtml($form->addDiv($buttons));
$form->addSeperator();
$form->addHtml(renderVendorTable($vendors,$app_id));
$form->addHtml(renderAttendeeTable($attendees,$app_id));
$form->addHtml(renderDealTable($deals,$app_id));
$form->addHtml($form->addDiv($form->makeField("button",$formName,"","Add Properties",array('class'=>'submit','onClick'=>'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'))));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


break;

case "Survey":

$sql = "SELECT
dea_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
cli_id,GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname) SEPARATOR ', ')  AS cli_name,
con_id,CONCAT(con_fname,' ',con_sname) AS con_name,
d2a_id,d2a_ord,d2a_cv
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
WHERE
link_deal_to_appointment.d2a_app = $app_id
GROUP BY deal.dea_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";


$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
$count = 1;
while ($row = $q->fetchRow()) {

	// array of properties (deals)
	$deals[$row["d2a_ord"]] = array(
		'dea_id'=>$row["dea_id"],
		'd2a_id'=>$row["d2a_id"],
		'd2a_ord'=>$row["d2a_ord"],
		'd2a_cv'=>$row["d2a_cv"],
		'addr'=>$row["pro_addr"]
		);
	// array of vendors/landlords/tenants (clients)
	if ($row["vendor_id"]) {
		$vendors[$row["vendor_id"]] = $row["vendor_name"];
		}
	}

$formData1 = array(
	'calendarID'=>array(
		'type'=>'select_branch',
		'label'=>'Branch',
		'value'=>$app["branch"],
		'attributes'=>array('class'=>'medium')
		),
	'app_user'=>array(
		'type'=>'select_user',
		'label'=>'Negotiator',
		'value'=>$app["user"],
		'attributes'=>array('class'=>'medium')
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app["date"],
		'attributes'=>array('class'=>'medium','readonly'=>'readonly')
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app["time"]
		),
	'app_duration'=>array(
		'type'=>'select_duration',
		'label'=>'Duration',
		'value'=>$duration,
		'attributes'=>array('class'=>'medium')
		),
	'app_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$app["notes"],
		'attributes'=>array('class'=>'wide','style'=>'height:50px')
		)
	);


$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app["type"] . '</div>');
$form->addData($formData1,$_POST);
$buttons = $form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","View in Calendar",array('class'=>'button','onClick'=>'javascript:document.location.href=\'calendar.php?app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'));
$form->addHtml($form->addDiv($buttons));
$form->addSeperator();
$form->addHtml(renderVendorTable($vendors,$app_id));
$form->addHtml(renderAttendeeTable($attendees,$app_id));
$form->addHtml(renderDealTable($deals,$app_id));
$form->addHtml($form->addDiv($form->makeField("button",$formName,"","Add Properties",array('class'=>'submit','onClick'=>'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'))));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');

break;




case "Meeting":

$formData1 = array(
	'app_subject'=>array(
		'type'=>'text',
		'label'=>'Subject',
		'value'=>$app["subject"],
		'attributes'=>array('class'=>'wide'),
		'required'=>2
		),
	'app_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$app["notes"],
		'attributes'=>array('class'=>'wide','style'=>'height:50px')
		),
	'app_user'=>array(
		'type'=>'select_user',
		'label'=>'User',
		'value'=>$app["user"],
		'attributes'=>array('class'=>'medium')
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app["date"],
		'attributes'=>array('class'=>'medium','readonly'=>'readonly')
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app["time"]
		),
	'app_duration'=>array(
		'type'=>'select_duration',
		'label'=>'Duration',
		'value'=>$duration,
		'attributes'=>array('class'=>'medium')
		)
	);


$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app["type"] . '</div>');
$form->addData($formData1,$_GET);
$buttons = $form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","View in Calendar",array('class'=>'button','onClick'=>'javascript:document.location.href=\'calendar.php?app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'));
$form->addHtml($form->addDiv($buttons));
$form->addSeperator();
$form->addHtml(renderAttendeeTable($attendees,$app_id));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


break;

case "Lunch":

$formData1 = array(
	'app_user'=>array(
		'type'=>'select_user',
		'label'=>'User',
		'value'=>$app["user"],
		'attributes'=>array('class'=>'medium')
		),
	'app_date'=>array(
		'type'=>'datetime',
		'label'=>'Date',
		'value'=>$app["date"],
		'attributes'=>array('class'=>'medium','readonly'=>'readonly')
		),
	'app_time'=>array(
		'type'=>'time',
		'label'=>'Start Time',
		'value'=>$app["time"]
		),
	'app_duration'=>array(
		'type'=>'select_duration',
		'label'=>'Duration',
		'value'=>$duration,
		'attributes'=>array('class'=>'medium')
		),
	'app_notes'=>array(
		'type'=>'textarea',
		'label'=>'Notes',
		'value'=>$app["notes"],
		'attributes'=>array('class'=>'wide','style'=>'height:50px')
		)
	);


$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($searchLink));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app["type"] . '</div>');
$form->addData($formData1,$_GET);
$buttons = $form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","View in Calendar",array('class'=>'button','onClick'=>'javascript:document.location.href=\'calendar.php?app_id='.$app_id.'&return='.urlencode($_GET["searchLink"]).'\';'));
$form->addHtml($form->addDiv($buttons));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');

break;

case "Holiday":
break;

endswitch;




if (!$_POST["action"]) {

/*
if ($_GET["searchLink"]) {
	$searchLink = str_replace("%3F","?",replaceQueryStringArray($_GET["searchLink"],array('app_id'))).'&jumpto='.$hour;
	}
*/
$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["searchLink"]),
	'search'=>array('title'=>'Appointment Search','label'=>'Appointment Search','link'=>'appointment_search.php')
	);
$navbar = navbar2($navbar_array); //replaceQueryStringArray($_GET["searchLink"],array('app_id'))

$page = new HTML_Page2($page_defaults);
$page->setTitle("Appointment");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addScript('js/scriptaculous/scriptaculous.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent($appointment_info);
$page->addBodyContent('</div>');
$page->display();

exit;


// if form is submitted
} elseif ($_POST["action"] == "update") {


// remove the date, time and duration form the array before processing
unset(
	$formData1["app_date"],
	$formData1["app_time"],
	$formData1["app_duration"]
	);
// deal with the dates manually
$date_parts = explode("/",$_POST["app_date"]);

$app_date = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];

$app_start = $app_date.' '.$_POST["app_time_hour"].':'.$_POST["app_time_min"].':00';


$app_start = strtotime($app_start);
$app_end = $app_start + ($_POST["app_duration"] * 60);


$result = new Validate();
$results = $result->process($formData1,$_POST);
$db_data = $results['Results'];

// build return link
$return = $_SERVER['SCRIPT_NAME'].'?';
if ($_POST["app_id"]) {
	$results['Results']['app_id'] = $_POST["app_id"];
	}
if (is_array($results['Results'])) {
	$return .= http_build_query($results['Results']);
	}
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}

// now add the date, time and duration to db_data array
$db_data["app_start"] = date('Y-m-d G:i:s',$app_start);
$db_data["app_end"] = date('Y-m-d G:i:s',$app_end);

db_query($db_data,"UPDATE","appointment","app_id",$app_id);

// return to calendar, correct day and time
if ($_POST["searchLink"]) {
	header("Location:calendar.php?app_id=$app_id&searchLink=".$_POST["searchLink"]);
	} else {
	header("Location:?app_id=$app_id&searchLink=".$_POST["searchLink"]);
	}

exit;
}









/*




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
*/
?>