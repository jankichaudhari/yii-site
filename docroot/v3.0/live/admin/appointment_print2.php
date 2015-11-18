<?php
require_once("inx/global.inc.php");
// printer friendly appointment details

if (!$_GET["app_id"]) {
	die("no app_id");
	}

// display the person who printed this doc
if ($_GET["use_id"]) {
	$sql = "SELECT CONCAT(use_fname,' ',use_sname) AS use_name FROM user WHERE use_id = ".$_GET["use_id"];
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	while ($row = $q->fetchRow()) {
		$printed_by = $row["use_name"];
		}
	}

// start a new page
$page = new HTML_Page2($page_defaults);


$sql = "SELECT
appointment.*,ity_title, DATE_FORMAT(appointment.app_created, '%W %D %M %Y %T') AS app_created,
user.use_id, CONCAT(user.use_fname,' ',user.use_sname) AS use_name,
attendee.use_id AS attendee_id,CONCAT(attendee.use_fname,' ',attendee.use_sname) AS attendee_name,attendee.use_colour,
bookedby.use_id, CONCAT(bookedby.use_fname,' ',bookedby.use_sname) AS app_bookedbyname
FROM appointment
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN user AS bookedby ON appointment.app_bookedby = bookedby.use_id
LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id
LEFT JOIN itype ON appointment.app_subtype = itype.ity_id
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
	$app["end"] = $row["app_end"];
	$app["subtype"] = $row["ity_title"];
	$app["allday"] = $row["app_allday"];
	$app["status"] = $row["app_status"];
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

GROUP_CONCAT(DISTINCT CONCAT(' ',tel.tel_number,' (',tel.tel_type,')') ORDER BY tel.tel_ord  SEPARATOR ', ') AS viewer_tel,

d2a_id,d2a_ord,d2a_cv,d2a_feedback,
GROUP_CONCAT(DISTINCT CONCAT(vendor.cli_salutation,' ',vendor.cli_fname,' ',vendor.cli_sname,' (',vendor_tel.tel_number,' - ',vendor_tel.tel_type,')')  SEPARATOR '<br />') AS vendor_name
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id

LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id

LEFT JOIN tel ON viewer.cli_id = tel.tel_cli
LEFT JOIN tel AS vendor_tel ON vendor.cli_id = vendor_tel.tel_cli
WHERE
appointment.app_id = $app_id
GROUP BY viewer.cli_id,deal.dea_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
$count = 1;
while ($row = $q->fetchRow()) {

//print_r($row);
	// array of properties (deals)
	$deals[$row["d2a_ord"]] = array(
		'dea_id'=>$row["dea_id"],
		'd2a_id'=>$row["d2a_id"],
		'd2a_ord'=>$row["d2a_ord"],
		'd2a_cv'=>$row["d2a_cv"],
		'd2a_feedback'=>$row["d2a_feedback"],
		'addr'=>$row["pro_addr"],
		'vendor'=>$row["vendor_name"]
		);
	// array of viewers (clients)
	if ($row["viewer_id"]) {
		$viewers[$row["viewer_id"]] = $row["viewer_name"].'<br />'.$row["viewer_tel"];
		}
	}


$render .= renderNotes('appointment',$app_id,array('label'=>'Viewing Notes:','layout'=>'simple'));


$render .= '<hr />
<h2>Viewers:</h2>
';
foreach ($viewers as $viewer) {
	$render .= '<p>'.$viewer.'</p>'."\n";
	}


$render .= '<hr />
<h2>Properties:</h2>
<ol>
';

foreach ($deals as $deal) {
	$render .= '<li>'.$deal["addr"].' - '.$deal["d2a_cv"]."\n";
	//$render .= '<p class="vendor">'.$deal["vendor"].'</p>'."\n";
	$render .= renderNotes('confirm',$deal["d2a_id"],array('label'=>'Confirmation Notes:','layout'=>'simple','order'=>'ASC'));
	$render .= '</li>'."\n";
	}
$render .= '</ol>'."\n";


if ($attendees) {
	$render2 .= '<hr>'."\n";
	$render2 .= '<h2>Attendees:</h2>
<p>';
	foreach ($attendees as $attendee) {
		$render2 .= $attendee.', ';
		}
	$render .= remove_lastchar($render2,",").'</p>'."\n";
	}
$render .= '<hr />';





break;
case "Valuation":

$sql = "SELECT
dea_id,dea_bedroom,dea_reception,dea_bedroom,dea_floor,
T.pty_title AS ptype, ST.pty_title AS psubtype,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,pro_east,pro_north,
viewer.cli_id AS viewer_id,
CONCAT(viewer.cli_salutation,' ',viewer.cli_fname,' ',viewer.cli_sname,' (',tel.tel_number,' - ',tel.tel_type,')') AS viewer_name,
d2a_id,d2a_ord,d2a_cv,d2a_feedback,
GROUP_CONCAT(DISTINCT CONCAT(vendor.cli_salutation,' ',vendor.cli_fname,' ',vendor.cli_sname,' (',vendor_tel.tel_number,' - ',vendor_tel.tel_type,')')  SEPARATOR '<br />') AS vendor_name
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id

LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id

LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id

LEFT JOIN tel ON viewer.cli_id = tel.tel_cli
LEFT JOIN tel AS vendor_tel ON vendor.cli_id = vendor_tel.tel_cli
WHERE
appointment.app_id = $app_id
GROUP BY viewer.cli_id,deal.dea_id
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
		'addr'=>$row["pro_addr"],
		'vendor'=>$row["vendor_name"],
		'bedroom'=>$row["dea_bedroom"],
		'reception'=>$row["dea_reception"],
		'bathroom'=>$row["dea_bathroom"],
		'type'=>$row["ptype"],
		'subtype'=>$row["psubtype"]
		);
	// array of viewers (clients)
	if ($row["viewer_id"]) {
		$viewers[$row["viewer_id"]] = $row["viewer_name"];
		}
	$x = $row["pro_east"];
	$y = $row["pro_north"];
	}


$render .= renderNotes('appointment',$app_id,array('label'=>'Viewing Notes:','layout'=>'simple'));
$debug = 'renderNotes(\'appointment\','.$app_id.',array(\'label\'=>\'Viewing Notes:\',\'layout\'=>\'simple\'))';

$render .= '<hr />
<h2>Vendor or Contact(s):</h2>
';
foreach ($viewers as $viewer) {
	$render .= '<p>'.$viewer.'</p>'."\n";
	}


$render .= '<hr />
<h2>Properties:</h2>
<ol>
';
print_r($deals);
foreach ($deals as $deal) {

	if (!$deal["type"]) {
		$deal["type"] = '(unknown)';
		} else {
		if ($deal["subtype"]) {
			$deal["type"] .= ' / '.$deal["subtype"];
			}
		}
	if (!$deal["bedroom"]) {
		$deal["bedroom"] = '(none or unknown)';
		}
	if (!$deal["reception"]) {
		$deal["reception"] = '(none or unknown)';
		}
	if (!$deal["bathroom"]) {
		$deal["bathroom"] = '(none or unknown)';
		}

	$render .= '<li>'.$deal["addr"].' - '.$deal["d2a_cv"]."\n";
	//$render .= '<p class="vendor">'.$deal["vendor"].'</p>'."\n";
	$render .= '<p class="vendor">Type: '.$deal["type"].'<br />Bedrooms: '.$deal["bedroom"].'<br />Receptions: '.$deal["reception"].'<br />Bathrooms: '.$deal["bathroom"].'</p>'."\n";
	$render .= renderNotes('confirm',$deal["d2a_id"],array('label'=>'Confirmation Notes:','layout'=>'simple','order'=>'ASC'));
	$render .= '</li>'."\n";
	}
$render .= '</ol>'."\n";


if ($attendees) {
	$render2 .= '<hr>'."\n";
	$render2 .= '<h2>Attendees:</h2>
<p>';
	foreach ($attendees as $attendee) {
		$render2 .= $attendee.', ';
		}
	$render .= remove_lastchar($render2,",").'</p>'."\n";
	}
$render .= '<hr />';

// add map if there is only one property
if ($numRows == 1 && strlen($x) == 6 && strlen($y) == 6) {
	$map = new Map();
	$map->drawMap($x,$y);
	$map->addLocator($x,$y);
	$render .= $map->renderMap();
	}




break;
case "Production":

$sql = "SELECT
dea_id,dea_bedroom,dea_reception,dea_bedroom,dea_floor,
T.pty_title AS ptype, ST.pty_title AS psubtype,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,pro_east,pro_north,
viewer.cli_id AS viewer_id,
CONCAT(viewer.cli_salutation,' ',viewer.cli_fname,' ',viewer.cli_sname,' (',tel.tel_number,' - ',tel.tel_type,')') AS viewer_name,
d2a_id,d2a_ord,d2a_cv,d2a_feedback,
GROUP_CONCAT(DISTINCT CONCAT(vendor.cli_salutation,' ',vendor.cli_fname,' ',vendor.cli_sname,' (',vendor_tel.tel_number,' - ',vendor_tel.tel_type,')')  SEPARATOR '<br />') AS vendor_name
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id

LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id

LEFT JOIN tel ON viewer.cli_id = tel.tel_cli
LEFT JOIN tel AS vendor_tel ON vendor.cli_id = vendor_tel.tel_cli

LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id
WHERE
appointment.app_id = $app_id
GROUP BY viewer.cli_id,deal.dea_id
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
		'addr'=>$row["pro_addr"],
		'vendor'=>$row["vendor_name"],
		'bedroom'=>$row["dea_bedroom"],
		'reception'=>$row["dea_reception"],
		'bathroom'=>$row["dea_bathroom"],
		'type'=>$row["ptype"],
		'subtype'=>$row["psubtype"]
		);
	// array of viewers (clients)
	if ($row["viewer_id"]) {
		$viewers[$row["viewer_id"]] = $row["viewer_name"];
		}

	$x = $row["pro_east"];
	$y = $row["pro_north"];
	}


$render .= renderNotes('appointment',$app_id,array('label'=>'Viewing Notes:','layout'=>'simple'));


$render .= '<hr />
<h2>Vendor or Contact(s):</h2>
';
foreach ($viewers as $viewer) {
	$render .= '<p>'.$viewer.'</p>'."\n";
	}


$render .= '<hr />
<h2>Properties:</h2>
<ol>
';

foreach ($deals as $deal) {


	if (!$deal["type"]) {
		$deal["type"] = '(unknown)';
		} else {
		if ($deal["subtype"]) {
			$deal["type"] .= ' / '.$deal["subtype"];
			}
		}
	if (!$deal["bedroom"]) {
		$deal["bedroom"] = '(none or unknown)';
		}
	if (!$deal["reception"]) {
		$deal["reception"] = '(none or unknown)';
		}
	if (!$deal["bathroom"]) {
		$deal["bathroom"] = '(none or unknown)';
		}


	$render .= '<li>'.$deal["addr"].' - '.$deal["d2a_cv"]."\n";
	//$render .= '<p class="vendor">'.$deal["vendor"].'</p>'."\n";
	$render .= '<p class="vendor">Type: '.$deal["type"].'<br />Bedrooms: '.$deal["bedroom"].'<br />Receptions: '.$deal["reception"].'<br />Bathrooms: '.$deal["bathroom"].'</p>'."\n";
	$render .= renderNotes('confirm',$deal["d2a_id"],array('label'=>'Confirmation Notes:','layout'=>'simple','order'=>'ASC'));
	$render .= '</li>'."\n";


	}
$render .= '</ol>'."\n";


if ($attendees) {
	$render2 .= '<hr>'."\n";
	$render2 .= '<h2>Attendees:</h2>
<p>';
	foreach ($attendees as $attendee) {
		$render2 .= $attendee.', ';
		}
	$render .= remove_lastchar($render2,",").'</p>'."\n";
	}
$render .= '<hr />';

// add map if there is only one property
if ($numRows == 1 && strlen($x) == 6 && strlen($y) == 6) {
	$map = new Map();
	$map->drawMap($x,$y);
	$map->addLocator($x,$y);
	$render .= $map->renderMap();
	}




break;
case "Inspection":

$app["type"] = $app["subtype"].' '.$app["type"];

$sql = "SELECT
dea_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
d2a_id,d2a_ord,d2a_cv,d2a_feedback,
con_id,CONCAT(con_fname,' ',con_sname) AS con_name,com_title,
GROUP_CONCAT(DISTINCT CONCAT(vendor.cli_salutation,' ',vendor.cli_fname,' ',vendor.cli_sname,' (',vendor_tel.tel_number,' - ',vendor_tel.tel_type,')')  SEPARATOR '<br />') AS vendor_name
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id

LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id

LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
LEFT JOIN company ON contact.con_company = company.com_id

LEFT JOIN tel AS vendor_tel ON vendor.cli_id = vendor_tel.tel_cli
WHERE
appointment.app_id = $app_id
GROUP BY deal.dea_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
//echo $sql;
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
		'addr'=>$row["pro_addr"],
		'vendor'=>$row["vendor_name"]
		);
	// array of viewers (clients)
	if ($row["viewer_id"]) {
		$viewers[$row["viewer_id"]] = $row["viewer_name"];
		}
	if ($row["con_id"]) {
		if ($row["com_title"]) {
			$contact = $row["con_name"].' - '.$row["com_title"];
			} else {
			$contact = $row["con_name"];
			}
		$contacts[$row["con_id"]] = $contact;
		}
	}


$render .= renderNotes('appointment',$app_id,array('label'=>'Appointment Notes:','layout'=>'simple'));

$render .= '<hr />
<h2>Inspector(s):</h2>
';
foreach ($contacts as $contact) {
	$render .= '<p>'.$contact.'</p>'."\n";
	}



$render .= '<hr />
<h2>Properties:</h2>
<ol>
';

foreach ($deals as $deal) {
	$render .= '<li>'.$deal["addr"].' - '.$deal["d2a_cv"]."\n";
	//$render .= '<p class="vendor">'.$deal["vendor"].'</p>'."\n";
	$render .= renderNotes('confirm',$deal["d2a_id"],array('label'=>'Confirmation Notes:','layout'=>'simple','order'=>'ASC'));
	$render .= '</li>'."\n";
	}
$render .= '</ol>'."\n";


if ($attendees) {
	$render2 .= '<hr>'."\n";
	$render2 .= '<h2>Attendees:</h2>
<p>';
	foreach ($attendees as $attendee) {
		$render2 .= $attendee.', ';
		}
	$render .= remove_lastchar($render2,",").'</p>'."\n";
	}
$render .= '<hr />';




break;
case "Meeting":

$app["type"] = $app["type"];


$render .= '<p>'.$app["subject"].'</p>';

$render .= renderNotes('appointment',$app_id,array('label'=>'Appointment Notes:','layout'=>'simple'));





if ($attendees) {
	$render2 .= '<hr>'."\n";
	$render2 .= '<h2>Attendees:</h2>
<p>';
	foreach ($attendees as $attendee) {
		$render2 .= $attendee.', ';
		}
	$render .= remove_lastchar($render2,",").'</p>'."\n";
	}
$render .= '<hr />';



break;
case "Note":

$app["type"] = $app["subtype"].' '.$app["type"];


$render .= '<p>'.$app["subject"].'</p>';

$render .= renderNotes('appointment',$app_id,array('label'=>'Appointment Notes:','layout'=>'simple'));





if ($attendees) {
	$render2 .= '<hr>'."\n";
	$render2 .= '<h2>Attendees:</h2>
<p>';
	foreach ($attendees as $attendee) {
		$render2 .= $attendee.', ';
		}
	$render .= remove_lastchar($render2,",").'</p>'."\n";
	}
$render .= '<hr />';




break;
default:

//$render = "<p>No further printable information available</p>";

endswitch;


// heading
$heading = '
<h1>'.$app["user_name"].' - '.$app["type"].'</h1>
<hr class="title" />
';
if ($app["allday"] == 'Yes') {
	$heading .= '<p>All day appointment</p>';
	} else {
	$heading .= '<p>'.date('D jS F g:ia',strtotime($app["start"])).' ('.duration($duration,'long').')</p>';
	}

// confidentiality statement
$render .= '
<div id="footer">
<h1>Confidentiality notice</h1>
<p>This document contains privileged information which must remain confidential. Under no circumstances should
this document be given to a client, left in a property or your car, or otherwise fall into anyone\'s hands other than a member
of the company. This document should be destroyed after use.</p>';
if ($printed_by) {
	$render .= '<p>Document printed on '.date('d/m/Y h:ia').' by '.$printed_by.'</p>';
	}
$render .= '</div>';



$js_footer = '<script type="text/javascript">
// <!--
window.onLoad = window.focus(); window.print();
// -->
</script>';

$page->setTitle($app["user_name"].' - '.$app["type"]);
$page->addStyleSheet('css/print.css');
$page->addBodyContent('<div id="appointment">');
$page->addBodyContent($heading);
$page->addBodyContent($render);
$page->addBodyContent('</div>'.$debug);
$page->addBodyContent($js_footer);
$page->display();
?>