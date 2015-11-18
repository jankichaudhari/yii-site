<?php
require_once("inx/global.inc.php");
/*

cancel appointment

Meeting / Lunch / Note:
Set to cancelled and return to calendar

Viewing:
Reason for deletion - user cancel, vendor cancel, viewer cancel, re-arrange
Show all persons involved with the viewing (vendor(s), viewer(s), attendees) with contact options
and advise all need to be informed of cancellation

Valuation:
Reason for deletion - user cancel, vendor cancel, re-arrange
Show all persons involved with the viewing (vendor(s), viewer(s), attendees) with contact options
and advise all need to be informed of cancellation

Appointments in the past cannot be cancelled


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
appointment.*,ity_title,DATE_FORMAT(appointment.app_created, '%W %D %M %Y %T') AS app_created,
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
// if appointment occours over (default_appointment_delay) hours in the past, divert to appointment_view.php
//////////////////////////////////////////////////////////////////
/*
$app_comparison_today = strtotime($date_mysql);
$app_comparison_start = strtotime($app["start"]);
$app_comparison_start = ($app_comparison_start + $default_appointment_delay);

if ($app_comparison_start < $app_comparison_today) {
	header("Location:appointment_view.php?app_id=$app_id");
	exit;
	}
*/

// if appointment has ended, create in_past var so feedback form is shown instead of cv form
if ($_SESSION["auth"]["default_scope"] != 'Lettings') {
	if (strtotime($app["end"]) < strtotime($date_mysql)-$default_appointment_expiry) {
		echo "You cannot cancel appointments that have expired";
		exit;
		}
	}

/*
if ($app["type"] == "Lunch" || $app["type"] == "Note" || $app["type"] == "Meeting") {
	$db_data["app_status"] = 'Cancelled';
	db_query($db_data,"UPDATE","appointment","app_id",$app["id"]);
	header("Location:calendar.php?branch=".$app["branch"]."&y=$y&m=$m&d=$d");
	exit;

	}
*/



$formData1 = array(
	'notes'=>array(
		'type'=>'textarea',
		'label'=>'Reason for Cancelling',
		'value'=>'',
		'attributes'=>array('class'=>'noteInput')
		)
	);


$form = new Form();

$form->addForm("app_form","POST",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","app_id","",$app_id);
$form->addField("hidden","searchLink","",urlencode($return));

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Cancel '.$app["type"] . '</div>');
$form->addHtml('<p class="appInfo">Please remember to inform all vendors/landlords and/or viewers that this appointment has been cancelled</p>');
$form->addData($formData1,$_POST);
$form->addHtml(renderNotes('appointment_cancel',$app_id,array('label'=>'Cancellation Notes')));
$buttons = $form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit'));
$form->addHtml($form->addDiv($buttons));
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');




if (!$_POST["action"]) {

/*
if ($_GET["searchLink"]) {
	$searchLink = str_replace("%3F","?",replaceQueryStringArray($_GET["searchLink"],array('app_id'))).'&jumpto='.$hour;
	}
*/

if ($_GET["msg"]) {
	$onLoad .= 'javascript:hideMsg();';
	$msg = '
	<script type="text/javascript" language="javascript">
	<!--
	function hideMsg(){
		setTimeout("hideMsgDiv()",1500);
		}
	function hideMsgDiv() {
		new Effect.Fade("floating_message");
		}
	-->
	</script><div id="notify"><div id="floating_message">'.urldecode($_GET["msg"]).'</div></div>';
	}

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
$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());

$page->addBodyContent('</div>');
if ($msg) {
	$page->addBodyContent($msg);
	}
$page->display();

exit;


// if form is submitted
} elseif ($_POST["action"] == "update") {



$app_id = $_POST["app_id"];

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


// extract notes from db_data and store in notes table
if ($db_data["notes"]) {
	$notes = $db_data["notes"];
	unset($db_data["notes"]);
	$db_data2 = array(
		'not_blurb'=>$notes,
		'not_row'=>$app_id,
		'not_type'=>'appointment_cancel',
		'not_user'=>$_SESSION["auth"]["use_id"],
		'not_date'=>$date_mysql
		);
	db_query($db_data2,"INSERT","note","not_id");
	}
unset($db_data["notes"]);

$db_data["app_status"] = 'Cancelled';

$db_response = db_query($db_data,"UPDATE","appointment","app_id",$app_id,true);

$db_response["array"]["appointment_cancelled"] = array('new'=>"Appointment Cancelled");
notify($db_response,'edit');

header("Location:appointment_edit.php?app_id=$app_id&searchLink=".$_POST["searchLink"]."&msg=Update+Successful");

exit;
}

?>