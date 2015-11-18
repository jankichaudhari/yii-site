<?php
require_once("inx/global.inc.php");

if ($_GET['app_id']) {
	$app_id = $_GET['app_id'];
} elseif ($_POST['app_id']) {
	$app_id = $_POST['app_id'];
} else {
	echo "no app_id";
	exit;
}

// functions for manipulating this appointment

// remove client from current viewing, delete row from cli2app table
if ($_GET['do'] == "remove_client" && $_GET['cli_id'] && $_GET['app_id']) {
	$sql = "DELETE FROM cli2app WHERE
	cli2app.c2a_cli = " . $_GET['cli_id'] . " AND
	cli2app.c2a_app = " . $_GET['app_id'];
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}

	$db_data['app_updated']                = date('Y-m-d H:i:s');
	$db_response                           = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
	$db_response['array']['remove_client'] = array('new' => "Client removed");
	notify($db_response, 'edit');

	header("Location:?app_id=$app_id");
	exit;
}
// remove client from current viewing, delete row from cli2app table
if ($_GET['do'] == "remove_contact" && $_GET['con_id'] && $_GET['app_id']) {
	$sql = "DELETE FROM con2app WHERE
	con2app.c2a_con = " . $_GET['con_id'] . " AND
	con2app.c2a_app = " . $_GET['app_id'];
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	header("Location:?app_id=$app_id");
	exit;
}
// remove user from current viewing, delete row from use2app table
if ($_GET['do'] == "remove_user" && $_GET['use_id'] && $_GET['app_id']) {
	$sql = "DELETE FROM use2app WHERE
	use2app.u2a_use = " . $_GET['use_id'] . " AND
	use2app.u2a_app = " . $_GET['app_id'];
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}

	$db_data['app_updated']                  = date('Y-m-d H:i:s');
	$db_response                             = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
	$db_response['array']['remove_attendee'] = array('new' => "Attendee removed");
	notify($db_response, 'edit');

	header("Location:?app_id=$app_id");
	exit;
}
// remove deal from current appointment, and reorder
if ($_GET['do'] == "remove" && $_GET['d2a_id']) {
	$d2a_id = $_GET['d2a_id'];

	$sql = "DELETE FROM link_deal_to_appointment WHERE d2a_id = $d2a_id";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}

	// re-number the order of all remaining deals in this appointment
	$sql = "SELECT d2a_id FROM link_deal_to_appointment WHERE d2a_app = $app_id ORDER BY d2a_ord ASC";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$count = 1;
	while ($row = $q->fetchRow()) {
		$sql2 = "UPDATE link_deal_to_appointment SET d2a_ord = $count WHERE d2a_id = " . $row['d2a_id'];
		$q2   = $db->query($sql2);
		$count++;
	}

	$db_data['app_updated']                  = date('Y-m-d H:i:s');
	$db_response                             = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
	$db_response['array']['remove_property'] = array('new' => "Property removed");
	notify($db_response, 'edit');

	header("Location:?app_id=$app_id");
	exit;
}

// reorder deals within appointment
if ($_GET['do'] == "reorder" && $_GET['d2a_id'] && $_GET['cur'] && $_GET['new']) {
	$this_d2a_id = $_GET['d2a_id'];
	$cur         = $_GET['cur']; // current position
	$new         = $_GET['new']; // new position (position to move the deal to, we need to update this position with the postiion it replaces)

	// get id of deal in position we want to move our deal to
	$sql = "SELECT d2a_id,d2a_ord FROM link_deal_to_appointment WHERE d2a_app = $app_id AND d2a_ord = $new";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	while ($row = $q->fetchRow()) {
		$other_d2a_id    = $row['d2a_id'];
		$other_d2a_order = $row['d2a_ord'];
	}

	// update this row with new position
	$db_data['d2a_ord'] = $new;
	db_query($db_data, "UPDATE", "link_deal_to_appointment", "d2a_id", $this_d2a_id);

	$db_data['d2a_ord'] = $cur;
	db_query($db_data, "UPDATE", "link_deal_to_appointment", "d2a_id", $other_d2a_id);

	header("Location:?app_id=$app_id");
	exit;
}

// delete appointment (set status to Deleted)
if ($_GET['do'] == "delete") {

	$db_data['app_status']                       = 'Deleted';
	$db_response                                 = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
	$db_response['array']['appointment_deleted'] = array('new' => "Appointment Deleted");
	notify($db_response, 'edit');
	header("Location:appointment_edit?app_id=$app_id&msg=Deleted&searchLink=" . urlencode($_GET['return']));
	exit;
}

// undelete appointment (set status to Active)
if ($_GET['do'] == "undelete") {
	$db_data['app_status']                         = 'Active';
	$db_response                                   = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
	$db_response['array']['appointment_undeleted'] = array('new' => "Appointment Un-Deleted");
	notify($db_response, 'edit');
	header("Location:appointment_edit?app_id=$app_id&msg=Update+Successful&searchLink=calendar.php?app_id=$app_id");
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
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$app['id']           = $row['app_id'];
	$app['type']         = $row['app_type'];
	$app['subtype']      = $row['app_subtype'];
	$app['notetype']     = $row['app_notetype'];
	$app['user']         = $row['app_user'];
	$app['branch']       = $row['calendarID'];
	$app['notes']        = $row['app_notes'];
	$app['subject']      = $row['app_subject'];
	$app['allday']       = $row['app_allday'];
	$app['private']      = $row['app_private'];
	$app['user_name']    = $row['use_name'];
	$app['bookedbyname'] = $row['app_bookedbyname'];
	$app['created']      = $row['app_created'];
	$app['start']        = $row['app_start'];
	$app['end']          = $row['app_end'];
	$app['allday']       = $row['app_allday'];
	$app['status']       = $row['app_status'];
	$app['DIT'] = $row['DIT'];
	$split               = explode(" ", $row['app_start']);
	// date
	if ($split[0] == '0000-00-00') {
		$app['date'] = date('d/m/Y');
	} else {
		$parts       = explode("-", $split[0]);
		$y           = $parts[0];
		$m           = $parts[1];
		$d           = $parts[2];
		$app['date'] = $d . '/' . $m . '/' . $y;
	}
	// time
	if ($split[1] == '00:00:00') {
		$app['time'] = date('G:i');
	} else {
		$app['time'] = $split[1];
	}
	// duration
	$duration = (strtotime($row['app_end']) - strtotime($row['app_start'])) / 60;

	// array of attendees
	if ($row['attendee_id']) {
		$attendees[$row['attendee_id']] = $row['attendee_name'];
	}
	// appointment info
	$appointment_info = '<pre>
  Type:       ' . $app['type'] . ' (ID: ' . $app['id'] . ')
  Booked by:  ' . $app['bookedbyname'] . '
  Created on: ' . $app['created'] . '</pre>';
}

for ($i = 5; $i <= 300;) { // what is that 300?
	$app_duration_data[$i] = duration($i);
	$i                     = $i + 5;
}

// if appointment has ended, create in_past var so feedback form is shown instead of cv form
if (strtotime($app['end']) < strtotime($date_mysql)) {
	$in_past = 1;
}

if (strtotime($app['end']) < strtotime($date_mysql) - $default_appointment_expiry) {
	$expired = 1;
}
// reset the $expired var is user is calendar administrator
if (in_array('Calendar Administrator', $_SESSION['auth']['roles'])) {
	unset($expired);
}
// reset the $expired var is user LETTINGS
if ($_SESSION['auth']['default_scope'] == 'Lettings') {
	unset($expired);
}

$form = new Form();
$form->addForm("app_form", "POST", $PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden", "action", "", "update");
$form->addField("hidden", "app_id", "", $app_id);
$form->addField("hidden", "searchLink", "", urlencode($searchLink));
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">' . $app['type'] . '</div>');
if ($expired) {
	$form->addHtml('<p class="appInfo">This appointment is read-only</p>');
}
if ($app['status'] == "Deleted") {
	$form->addHtml('<p class="appInfo">This appointment has been deleted</p>');
}
if ($app['status'] == "Cancelled") {
	$form->addHtml('<p class="appInfo">This appointment has been cancelled</p>');
	$form->addHtml(renderNotes('appointment_cancel', $app_id, array(
			'label'  => 'Reason for Cancelling',
			'layout' => 'readonly'
	)));

}

// create action buttons, common for all types
if ($expired == 1) {
	if ($app['status'] == "Cancelled") {
		$expired = 1;
		$buttons .= $form->makeField("button", "", "", "Back to Calendar", array(
				'class'   => 'submit',
				'onClick' => 'javascript:document.location.href=\'calendar.php?app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
	} elseif ($app['status'] == "Deleted") {
		$buttons .= $form->makeField("button", "", "", "UnDelete", array(
				'class'   => 'submit',
				'onClick' => 'javascript:document.location.href=\'?do=undelete&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
	} else {
		$buttons .= $form->makeField("button", "", "", "View in Calendar", array(
				'class'   => 'submit',
				'onClick' => 'javascript:document.location.href=\'calendar.php?app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
		//$buttons .= $form->makeField("button","","","Delete",array('class'=>'button','onClick'=>'javascript:document.location.href=\'?do=delete&app_id='.$app_id.'&return='.urlencode($_GET['searchLink']).'\';'));
	}
} else {
	if ($app['status'] == "Cancelled") {
		$expired = 1;
		$buttons .= $form->makeField("button", "", "", "Back to Calendar", array(
				'class'   => 'submit',
				'onClick' => 'javascript:document.location.href=\'calendar.php?app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
		$buttons .= $form->makeField("button", "", "", "UnCancel", array(
				'class'   => 'button',
				'onClick' => 'javascript:document.location.href=\'?do=undelete&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
	} elseif ($app['status'] == "Deleted") {
		$expired = 1;
		$buttons .= $form->makeField("button", "", "", "Back to Calendar", array(
				'class'   => 'submit',
				'onClick' => 'javascript:document.location.href=\'calendar.php?app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
		$buttons .= $form->makeField("button", "", "", "UnDelete", array(
				'class'   => 'button',
				'onClick' => 'javascript:document.location.href=\'?do=undelete&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
	} else {
		$buttons = $form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'));
		$buttons .= $form->makeField("button", "", "", "Cancel", array(
				'class'   => 'button',
				'onClick' => 'confirmDelete(\'Are you sure you want to Cancel this appointment?\',\'appointment_cancel.php?app_id=' . $app_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '\');'
		));
		$buttons .= $form->makeField("button", "", "", "Delete", array(
				'class'   => 'button',
				'onClick' => 'confirmDelete(\'Are you sure you want to Delete this appointment?\nIf the appointment has been cancelled, please use the cancel button instead.\',\'?do=delete&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\');'
		));
		$buttons .= $form->makeField("button", "", "", "View in Calendar", array(
				'class'   => 'button',
				'onClick' => 'javascript:document.location.href=\'calendar.php?app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
		));
	}
}

if ($app['allday'] == "Yes") {
	$app['allday']  = 'All day event';
	$app['time']    = '08:00';
	$timeAttributes = array('disabled' => 'disabled');
} else {
	$timeAttributes = array();
}

//////////////////////////////////////////////////////////////////
// now page splits into app_type switch
// anything above is common, anything below is specific
//////////////////////////////////////////////////////////////////
switch ($app['type']):
	case "Valuation Follow Up" :
	case "Viewing Follow Up" :
	header("Location: /admin4/Appointment/View/id/" . $app['id']);
		break;
	case "Viewing":

		$sql = "SELECT
dea_id, deal.DIY AS DIY,
CONCAT(a.line1,' ',a.line2,' ',a.line3,' ',a.postcode) AS pro_addr,
viewer.cli_id AS viewer_id,
CONCAT(viewer.cli_fname,' ',viewer.cli_sname) AS viewer_name,
d2a_id,d2a_ord,d2a_cv,d2a_feedback
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN address a ON property.addressId = a.id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id
WHERE
appointment.app_id = $app_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}
		$numRows = $q->numRows();
		$count   = 1;
		while ($row = $q->fetchRow()) {

			// array of properties (deals)
			$deals[$row['d2a_ord']] = array(
					'dea_id'       => $row['dea_id'],
					'd2a_id'       => $row['d2a_id'],
					'd2a_ord'      => $row['d2a_ord'],
					'd2a_cv'       => $row['d2a_cv'],
					'd2a_feedback' => $row['d2a_feedback'],
					'addr'         => $row['pro_addr'],
					'DIY'          => $row['DIY'],
			);
			// array of viewers (clients)
			if ($row['viewer_id']) {
				$viewers[$row['viewer_id']] = $row['viewer_name'];
			}
		}

		$formData1 = array(
				'calendarID'   => array(
						'type'       => 'select_branch_2',
						'label'      => 'Branch',
						'value'      => $app['branch'],
						'attributes' => array('class' => 'medium')
				),
				'app_user'     => array(
						'type'       => 'select_user',
						'label'      => 'Negotiator',
						'value'      => $app['user'],
						'attributes' => array('class' => 'medium'),
						'options'    => array('' => '(unassigned)')
				),
				'app_date'     => array(
						'type'       => 'datetime',
						'label'      => 'Date',
						'value'      => $app['date'],
						'attributes' => array(
								'class'    => 'medium',
								'readonly' => 'readonly'
						)
				),
				'app_time'     => array(
						'type'       => 'time',
						'label'      => 'Start Time',
						'value'      => $app['time'],
						'group'      => 'Start Time',
						'attributes' => $timeAttributes
				),
				'app_allday'   => array(
						'type'          => 'checkbox',
						'label'         => 'All day',
						'value'         => $app['allday'],
						'options'       => array('All day event' => 'Yes'),
						'attributes'    => array('onClick' => 'allDayCheck(\'app_allday[]\')'),
						'group'         => 'Start Time',
						'last_in_group' => 1
				),
				'app_duration' => array(
						'type'       => 'select_duration',
						'label'      => 'Duration',
						'value'      => $duration,
						'attributes' => array('class' => 'medium'),
						'options'    => array('format' => 'long')
				),
				'notes'        => array(
						'type'       => 'textarea',
						'label'      => 'Add ' . $app['type'] . ' Note',
						'value'      => $app['notes'],
						'attributes' => array('class' => 'noteInput'),
						'tooltip'    => 'Only notes relating to the viewer(s). Notes that relate to a specific property should be entered on the confirmation page'
				)
		);

		if ($expired) {
			unset($formData1['notes']); // dont allow new notes to be added
			$renderReadOnlyValue     = 'true';
			$renderNotesArrayOptions = array(
					'label'  => 'Viewing Notes',
					'layout' => 'readonly'
			);
		} else {
			$renderReadOnlyValue     = null;
			$renderNotesArrayOptions = array('label' => 'Viewing Notes');
		}

		if ($app['DIT']) {
			$form->addHtml('<div class="DIT-property dit-notification" style="padding: 10px 183px;"> This is a DIT viewing.</div>');
		}
		$form->addData($formData1, $_POST);
		$form->addHtml(renderNotes('appointment', $app_id, $renderNotesArrayOptions));
		$form->addHtml($form->addDiv($buttons));
		$form->addSeperator();
		$form->addHtml(renderViewerTable($viewers, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderAttendeeTable($attendees, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderDealTable($deals, $app_id, $in_past));

		if (!$expired && !$app['DIT']) {
			$form->addHtml('<div style="float:left; padding-left: 10px; clear: none;">' . $form->makeField("button", $formName, "", "Add Properties", array(
								   'class'   => 'submit',
								   'onClick' => 'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
						   )) . '</div>');
		}
		$canSendTextMessage = false;
		foreach ($deals as $deal) {
			if ($deal['d2a_cv'] == "Confirmed") {
				$canSendTextMessage = true;
				break;
			}
		}

		if ($canSendTextMessage) {
			$form->addHtml('<div style="float:right; padding-right: 10px; clear: none;"><a href="/admin4/sms/confirm/app/' . $app_id . '" class="btn">Send text message</a></div>');
		}

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
CONCAT(a.line1,' ',a.line2,' ',a.line3,' ',a.postcode) AS pro_addr,
vendor.cli_id AS vendor_id,CONCAT(vendor.cli_fname,' ',vendor.cli_sname) AS vendor_name,
d2a_id,d2a_ord,d2a_cv,d2a_feedback
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN address a ON property.addressId = a.id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS vendor ON cli2app.c2a_cli = vendor.cli_id
WHERE
appointment.app_id = $app_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}
		$numRows = $q->numRows();
		$count   = 1;
		while ($row = $q->fetchRow()) {

			// array of properties (deals)
			$deals[$row['d2a_ord']] = array(
					'dea_id'       => $row['dea_id'],
					'd2a_id'       => $row['d2a_id'],
					'd2a_ord'      => $row['d2a_ord'],
					'd2a_cv'       => $row['d2a_cv'],
					'd2a_feedback' => $row['d2a_feedback'],
					'addr'         => $row['pro_addr']
			);
			// array of vendors/landlords/tenants (clients)
			if ($row['vendor_id']) {
				$vendors[$row['vendor_id']] = $row['vendor_name'];
			}
		}

		$formData1 = array(
				'calendarID'   => array(
						'type'       => 'select_branch_2',
						'label'      => 'Branch',
						'value'      => $app['branch'],
						'attributes' => array('class' => 'medium')
				),
				'app_user'     => array(
						'type'       => 'select_user',
						'label'      => 'Negotiator',
						'value'      => $app['user'],
						'attributes' => array('class' => 'medium'),
						'options'    => array('' => '(unassigned)')
				),
				'app_date'     => array(
						'type'       => 'datetime',
						'label'      => 'Date',
						'value'      => $app['date'],
						'attributes' => array(
								'class'    => 'medium',
								'readonly' => 'readonly'
						)
				),
				'app_time'     => array(
						'type'  => 'time',
						'label' => 'Start Time',
						'value' => $app['time']
				),
				'app_duration' => array(
						'type'       => 'select_duration',
						'label'      => 'Duration',
						'value'      => $duration,
						'attributes' => array('class' => 'medium')
				),
				'notes'        => array(
						'type'       => 'textarea',
						'label'      => 'Add ' . $app['type'] . ' Note',
						'value'      => $app['notes'],
						'attributes' => array('class' => 'noteInput')
				)
		);

		if ($expired) {
			unset($formData1['notes']); // dont allow new notes to be added
			$renderReadOnlyValue     = 'true';
			$renderNotesArrayOptions = array(
					'label'  => 'Valuation Notes',
					'layout' => 'readonly'
			);
		} else {
			$renderReadOnlyValue     = null;
			$renderNotesArrayOptions = array('label' => 'Valuation Notes');
		}

		$form->addData($formData1, $_POST);
		$form->addHtml(renderNotes('appointment', $app_id, $renderNotesArrayOptions));
		$form->addHtml($form->addDiv($buttons));
		$form->addSeperator();

		$form->addHtml(renderVendorTable($vendors, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderAttendeeTable($attendees, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderDealTable($deals, $app_id, $in_past));
		if (!$expired && !$app['DIT']) {
			$form->addHtml('<div style="float:left; padding-left: 10px; clear: none;">' . $form->makeField("button", $formName, "", "Add Properties", array(
								   'class'   => 'submit',
								   'onClick' => 'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
						   )) . '</div>');
		}
		$canSendTextMessage = false;
		foreach ($deals as $deal) {
			if ($deal['d2a_cv'] == "Confirmed") {
				$canSendTextMessage = true;
				break;
			}
		}

		if ($canSendTextMessage) {
			$form->addHtml('<div style="float:right; padding-right: 10px; clear: none;"><a href="/admin4/sms/confirm/app/' . $app_id . '" class="btn">Send text message</a></div>');
		}

		$form->addHtml("</fieldset>\n");
		$form->addHtml('</div>');

		break;

	case "Production":

// similar to valuation with contacts (vendor, landlord, tenant)
// muliple properties allowed

		$sql = "SELECT
dea_id,
CONCAT(a.line1,' ',a.line2,' ',a.line3,' ',a.postcode) AS pro_addr,
vendor.cli_id AS vendor_id,CONCAT(vendor.cli_fname,' ',vendor.cli_sname) AS vendor_name,
d2a_id,d2a_ord,d2a_cv,d2a_feedback
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN address a ON property.addressId = a.id
LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
LEFT JOIN client AS vendor ON cli2app.c2a_cli = vendor.cli_id
WHERE
appointment.app_id = $app_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}
		$numRows = $q->numRows();
		$count   = 1;
		while ($row = $q->fetchRow()) {

			// array of properties (deals)
			$deals[$row['d2a_ord']] = array(
					'dea_id'       => $row['dea_id'],
					'd2a_id'       => $row['d2a_id'],
					'd2a_ord'      => $row['d2a_ord'],
					'd2a_cv'       => $row['d2a_cv'],
					'd2a_feedback' => $row['d2a_feedback'],
					'addr'         => $row['pro_addr']
			);
			// array of vendors/landlords/tenants (clients)
			if ($row['vendor_id']) {
				$vendors[$row['vendor_id']] = $row['vendor_name'];
			}
		}

		$formData1 = array(
				'calendarID'   => array(
						'type'       => 'select_branch_2',
						'label'      => 'Branch',
						'value'      => $app['branch'],
						'attributes' => array('class' => 'medium')
				),
				'app_user'     => array(
						'type'       => 'select_user',
						'label'      => 'User',
						'value'      => $app['user'],
						'attributes' => array('class' => 'medium'),
						'options'    => array('' => '(unassigned)')
				),
				'app_date'     => array(
						'type'       => 'datetime',
						'label'      => 'Date',
						'value'      => $app['date'],
						'attributes' => array(
								'class'    => 'medium',
								'readonly' => 'readonly'
						)
				),
				'app_time'     => array(
						'type'  => 'time',
						'label' => 'Start Time',
						'value' => $app['time']
				),
				'app_duration' => array(
						'type'       => 'select_duration',
						'label'      => 'Duration',
						'value'      => $duration,
						'attributes' => array('class' => 'medium')
				),
				'notes'        => array(
						'type'       => 'textarea',
						'label'      => 'Add ' . $app['type'] . ' Note',
						'value'      => $app['notes'],
						'attributes' => array('class' => 'noteInput')
				)
		);

		if ($expired) {
			unset($formData1['notes']); // dont allow new notes to be added
			$renderReadOnlyValue     = 'true';
			$renderNotesArrayOptions = array(
					'label'  => 'Production Notes',
					'layout' => 'readonly'
			);
		} else {
			$renderReadOnlyValue     = null;
			$renderNotesArrayOptions = array('label' => 'Production Notes');
		}

		$form->addData($formData1, $_POST);
		$form->addHtml(renderNotes('appointment', $app_id, $renderNotesArrayOptions));

		$form->addHtml($form->addDiv($buttons));
		$form->addSeperator();
		$form->addHtml(renderVendorTable($vendors, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderAttendeeTable($attendees, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderDealTable($deals, $app_id, $in_past));
		if (!$expired) {
			$form->addHtml($form->addDiv($form->makeField("button", $formName, "", "Add Properties", array(
					'class'   => 'submit',
					'onClick' => 'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
			))));
		}
		$form->addHtml("</fieldset>\n");
		$form->addHtml('</div>');

		break;

	case "Inspection":

		$sql = "SELECT
dea_id,
CONCAT(a.line1,' ',a.line2,' ',a.line3,' ',a.postcode) AS pro_addr,
cli_id,GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname) SEPARATOR ', ')  AS cli_name,
con_id,CONCAT(con_fname,' ',con_sname) AS con_name,com_title,
d2a_id,d2a_ord,d2a_cv,d2a_feedback
FROM appointment
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN address a ON property.addressId = a.id
LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
LEFT JOIN company ON contact.con_company = company.com_id
WHERE
link_deal_to_appointment.d2a_app = $app_id
GROUP BY property.pro_id
ORDER BY link_deal_to_appointment.d2a_ord ASC
";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage());
		}
		$numRows = $q->numRows();
		$count   = 1;
		while ($row = $q->fetchRow()) {

			// array of properties (deals)
			$deals[$row['d2a_ord']] = array(
					'dea_id'       => $row['dea_id'],
					'd2a_id'       => $row['d2a_id'],
					'd2a_ord'      => $row['d2a_ord'],
					'd2a_cv'       => $row['d2a_cv'],
					'd2a_feedback' => $row['d2a_feedback'],
					'addr'         => $row['pro_addr']
			);

			// array of contacts
			if ($row['con_id']) {
				if ($row['com_title']) {
					$contact = $row['con_name'] . ' - ' . $row['com_title'];
				} else {
					$contact = $row['con_name'];
				}
				$contacts[$row['con_id']] = $contact;
			}
		}

		$sql           = "SELECT * FROM itype WHERE ity_scope = '" . $_SESSION['auth']['default_scope'] . "' AND ity_id = " . $app['subtype'] . " ORDER BY ity_title";
		$q             = $db->query($sql);
		$numRows_itype = $q->numRows();
		if ($numRows_itype) {

			$sql2 = "SELECT * FROM itype WHERE ity_scope = '" . $_SESSION['auth']['default_scope'] . "' ORDER BY ity_title";
			$q2   = $db->query($sql2);
			while ($row = $q2->fetchRow()) {
				$itype[$row['ity_id']] = $row['ity_title']; //.' ('.$row['ity_scope'].')';
			}

		} else {

			$sql2 = "SELECT * FROM itype ORDER BY ity_title";
			$q2   = $db->query($sql2);
			while ($row = $q2->fetchRow()) {
				$itype[$row['ity_id']] = $row['ity_title']; //.' ('.$row['ity_scope'].')';
			}

		}

		$formData1 = array(
				'app_subtype'  => array(
						'type'       => 'select',
						'label'      => 'Inspection Type',
						'value'      => $app['subtype'],
						'attributes' => array('class' => 'wide'),
						'options'    => $itype
				),
				'calendarID'   => array(
						'type'       => 'select_branch_2',
						'label'      => 'Branch',
						'value'      => $app['branch'],
						'attributes' => array('class' => 'medium')
				),
				'app_user'     => array(
						'type'       => 'select_user',
						'label'      => 'Negotiator',
						'value'      => $app['user'],
						'attributes' => array('class' => 'medium'),
						'options'    => array('' => '(unassigned)')
				),
				'app_date'     => array(
						'type'       => 'datetime',
						'label'      => 'Date',
						'value'      => $app['date'],
						'attributes' => array(
								'class'    => 'medium',
								'readonly' => 'readonly'
						)
				),
				'app_time'     => array(
						'type'  => 'time',
						'label' => 'Start Time',
						'value' => $app['time']
				),
				'app_duration' => array(
						'type'       => 'select_duration',
						'label'      => 'Duration',
						'value'      => $duration,
						'attributes' => array('class' => 'medium')
				),
				'notes'        => array(
						'type'       => 'textarea',
						'label'      => 'Add ' . $app['type'] . ' Note',
						'value'      => $app['notes'],
						'attributes' => array('class' => 'noteInput')
				)
		);

		if ($expired) {
			unset($formData1['notes']); // dont allow new notes to be added
			$renderReadOnlyValue     = 'true';
			$renderNotesArrayOptions = array(
					'label'  => 'Inspection Notes',
					'layout' => 'readonly'
			);
		} else {
			$renderReadOnlyValue     = null;
			$renderNotesArrayOptions = array('label' => 'Inspection Notes');
		}

		$form->addData($formData1, $_POST);
		$form->addHtml(renderNotes('appointment', $app_id, $renderNotesArrayOptions));
		$form->addHtml($form->addDiv($buttons));
		$form->addSeperator();
		$form->addHtml(renderContactTable($contacts, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderAttendeeTable($attendees, $app_id, $renderReadOnlyValue));
		$form->addHtml(renderDealTable($deals, $app_id, $in_past));
		if (!$expired) {
			$form->addHtml($form->addDiv($form->makeField("button", $formName, "", "Add Properties", array(
					'class'   => 'submit',
					'onClick' => 'javascript:document.location.href=\'viewing_add.php?stage=viewing_address&app_id=' . $app_id . '&return=' . urlencode($_GET['searchLink']) . '\';'
			))));
		}
		$form->addHtml("</fieldset>\n");
		$form->addHtml('</div>');

		break;

	case "Meeting":

		$formData1 = array(
				'app_subject'  => array(
						'type'       => 'text',
						'label'      => 'Subject',
						'value'      => $app['subject'],
						'attributes' => array('style' => 'width:400px'),
						'required'   => 2
				),
				'calendarID'   => array(
						'type'       => 'select_branch_2',
						'label'      => 'Branch',
						'value'      => $app['branch'],
						'attributes' => array('class' => 'medium')
				),
				'app_user'     => array(
						'type'       => 'select_user',
						'label'      => 'User',
						'value'      => $app['user'],
						'attributes' => array('class' => 'medium'),
						'options'    => array('' => '(unassigned)')
				),
				'app_date'     => array(
						'type'       => 'datetime',
						'label'      => 'Date',
						'value'      => $app['date'],
						'attributes' => array(
								'class'    => 'medium',
								'readonly' => 'readonly'
						)
				),
				'app_time'     => array(
						'type'       => 'time',
						'label'      => 'Start Time',
						'value'      => $app['time'],
						'group'      => 'Start Time',
						'attributes' => $timeAttributes
				),
				'app_allday'   => array(
						'type'          => 'checkbox',
						'label'         => 'All day',
						'value'         => $app['allday'],
						'options'       => array('All day event' => 'Yes'),
						'attributes'    => array('onClick' => 'allDayCheck(\'app_allday[]\')'),
						'group'         => 'Start Time',
						'last_in_group' => 1
				),
				'app_duration' => array(
						'type'       => 'select_duration',
						'label'      => 'Duration',
						'value'      => $duration,
						'attributes' => array('class' => 'medium')
				),
				'notes'        => array(
						'type'       => 'textarea',
						'label'      => 'Add ' . $app['type'] . ' Note',
						'attributes' => array('class' => 'noteInput')
				)
		);

		if ($expired) {
			unset($formData1['notes']); // dont allow new notes to be added
			$renderReadOnlyValue     = 'true';
			$renderNotesArrayOptions = array(
					'label'  => 'Meeting Notes',
					'layout' => 'readonly'
			);
		} else {
			$renderReadOnlyValue     = null;
			$renderNotesArrayOptions = array('label' => 'Meeting Notes');
		}

		$form->addData($formData1, $_POST);
		$form->addHtml(renderNotes('appointment', $app_id, $renderNotesArrayOptions));
		$form->addHtml($form->addDiv($buttons));
//$form->addSeperator();
		$form->addHtml(renderAttendeeTable($attendees, $app_id, $renderReadOnlyValue));
		$form->addHtml("</fieldset>\n");
		$form->addHtml('</div>');

		break;

	case "Note":

		$formData1 = array(
				'app_notetype' => array(
						'type'       => 'select',
						'label'      => 'Type',
						'value'      => $app['notetype'],
						'attributes' => array('class' => 'wide'),
						'options'    => db_enum("appointment", "app_notetype", "array"),
						'required'   => 1
				),
				'app_subject'  => array(
						'type'       => 'text',
						'label'      => 'Subject',
						'value'      => $app['subject'],
						'attributes' => array('class' => 'wide'),
						'required'   => 1
				),
				'calendarID'   => array(
						'type'       => 'select_branch_2',
						'label'      => 'Branch',
						'value'      => $app['branch'],
						'attributes' => array('class' => 'medium')
				),
				'app_user'     => array(
						'type'       => 'select_user',
						'label'      => 'User',
						'value'      => $app['user'],
						'attributes' => array('class' => 'medium'),
						'options'    => array('' => '(unassigned)')
				),
				'app_date'     => array(
						'type'       => 'datetime',
						'label'      => 'Date',
						'value'      => $app['date'],
						'attributes' => array(
								'class'    => 'medium',
								'readonly' => 'readonly'
						)
				),
				'app_time'     => array(
						'type'       => 'time',
						'label'      => 'Start Time',
						'value'      => $app['time'],
						'group'      => 'Start Time',
						'attributes' => $timeAttributes
				),
				'app_allday'   => array(
						'type'          => 'checkbox',
						'label'         => 'All day',
						'value'         => $app['allday'],
						'options'       => array('All day event' => 'Yes'),
						'attributes'    => array('onClick' => 'allDayCheck(\'app_allday[]\')'),
						'group'         => 'Start Time',
						'last_in_group' => 1
				),
				'app_duration' => array(
						'type'       => 'select_duration',
						'label'      => 'Duration',
						'value'      => $duration,
						'attributes' => array('class' => 'medium')
				),
				'notes'        => array(
						'type'       => 'textarea',
						'label'      => 'Add ' . $app['type'] . ' Note',
						'attributes' => array('class' => 'noteInput')
				)
		);

		if ($expired) {
			unset($formData1['notes']); // dont allow new notes to be added
			$renderReadOnlyValue     = 'true';
			$renderNotesArrayOptions = array(
					'label'  => 'Notes',
					'layout' => 'readonly'
			);
		} else {
			$renderReadOnlyValue     = null;
			$renderNotesArrayOptions = array('label' => 'Notes');
		}

		$form->addData($formData1, $_POST);
		$form->addHtml(renderNotes('appointment', $app_id, $renderNotesArrayOptions));
		$form->addHtml($form->addDiv($buttons));
		$form->addSeperator();
		$form->addHtml(renderAttendeeTable($attendees, $app_id, $renderReadOnlyValue));
		$form->addHtml("</fieldset>\n");
		$form->addHtml('</div>');

		break;

	case "Lunch":

		$formData1 = array(
				'app_user'     => array(
						'type'       => 'select_user',
						'label'      => 'User',
						'value'      => $app['user'],
						'attributes' => array('class' => 'medium')
				),
				'app_date'     => array(
						'type'       => 'datetime',
						'label'      => 'Date',
						'value'      => $app['date'],
						'attributes' => array(
								'class'    => 'medium',
								'readonly' => 'readonly'
						)
				),
				'app_time'     => array(
						'type'  => 'time',
						'label' => 'Start Time',
						'value' => $app['time']
				),
				'app_duration' => array(
						'type'       => 'select_duration',
						'label'      => 'Duration',
						'value'      => $duration,
						'attributes' => array('class' => 'medium')
				),
				'notes'        => array(
						'type'       => 'textarea',
						'label'      => 'Add ' . $app['type'] . ' Note',
						'attributes' => array('class' => 'noteInput')
				)
		);

		if ($expired) {
			unset($formData1['notes']); // dont allow new notes to be added
			$renderReadOnlyValue     = 'true';
			$renderNotesArrayOptions = array(
					'label'  => 'Lunch Notes',
					'layout' => 'readonly'
			);
		} else {
			$renderReadOnlyValue     = null;
			$renderNotesArrayOptions = array('label' => 'Lunch Notes');
		}

		$form->addData($formData1, $_POST);

		$form->addHtml(renderNotes('appointment', $app_id, $renderNotesArrayOptions));
		$form->addHtml($form->addDiv($buttons));
		$form->addHtml("</fieldset>\n");
		$form->addHtml('</div>');

		break;

	case "Holiday":
		break;

endswitch;

if (!$_POST['action']) {

	if ($_GET['msg']) {
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
	</script><div id="notify"><div id="floating_message">' . urldecode($_GET['msg']) . '</div></div>';
	}

	$navbar_array = array(
			'back'   => array(
					'title' => 'Back',
					'label' => 'Back',
					'link'  => $_GET['searchLink']
			),
			'search' => array(
					'title' => 'Appointment Search',
					'label' => 'Appointment Search',
					'link'  => 'appointment_search.php'
			),
			'print'  => array(
					'title' => 'Print',
					'label' => 'Print',
					'link'  => 'javascript:appointmentPrint(\'' . $app_id . '\',\'' . $_SESSION['auth']['use_id'] . '\');'
			)
	);
	$navbar       = navbar2($navbar_array); //replaceQueryStringArray($_GET['searchLink'],array('app_id'))

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Appointment");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addScript('js/scriptaculous/prototype.js');
	$page->addScript('js/scriptaculous/scriptaculous.js');
	$page->addScript('js/CalendarPopup.js');

// this disables dates before today to prevent appointments being made in the past
	$yesterday = (strtotime($date_mysql) - (60 * 60 * 24));
	$y         = date('Y', $yesterday);
	$m         = date('m', $yesterday);
	$m         = ($m - 1); // js date function sees months as minus 1
	$d         = date('d', $yesterday);
	$yesterday = "$y,$m,$d";
	$page->addScriptDeclaration('
document.write(getCalendarStyles());
var now = new Date();
now.setFullYear(' . $yesterday . ');
var popcalapp_date = new CalendarPopup("popCalDivapp_date");
popcalapp_date.showYearNavigation();
//popcalapp_date.addDisabledDates(null,formatDate(now,"yyyy-MM-dd"));
');

	$page->setBodyAttributes(array('onLoad' => $onLoad));
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent($appointment_info);
	$page->addBodyContent('</div>');
	if ($msg) {
		$page->addBodyContent($msg);
	}
	$page->display();

	exit;

// if form is submitted
} elseif ($_POST['action'] == 'update') {

// do not allow any updates if app gas expired

	if (!$expired) {

		if (isset($_POST['app_id']) && !empty($_POST['app_id'])) {
			$appId = (int)$_POST['app_id'];

			$sql           = "SELECT * FROM link_deal_to_appointment WHERE d2a_app = '" . $appId . "'";
			$attachedDeals = array();
			$query         = $db->query($sql);
			while ($arr = $query->fetchRow()) {
				$attachedDeals[$arr['d2a_id']] = $arr['d2a_id'];
			}

			$sql             = "SELECT * FROM appointment WHERE app_id = '" . $appId . "'";
			$query           = $db->query($sql);
			$appointmentData = $query->fetchRow();

			if (!$appointmentData) {
				exit(); // impossible. but who can be sure in this system;
			}
		}

		$resetStatus = function ($deals) use ($db) {

			$sql    = "UPDATE link_deal_to_appointment SET d2a_cv = 'Not Confirmed' WHERE d2a_id IN ('" . implode("', '", $deals) . "')";
			$result = $db->query($sql);
		};

		$appAllday = 'Yes';
		if (!$_POST['app_allday']) {
			$appAllday = $_POST['app_allday'] = 'No';
		} else {
			$_POST['app_time_hour'] = '08';
			$_POST['app_time_min']  = '00';
		}
		unset($formData1['app_date'], $formData1['app_time'], $formData1['app_duration']);
// deal with the dates manually

		$app_date  = date("Y-m-d", strtotime(str_replace("/", ".", $_POST['app_date'])));
		$app_start = $app_date . ' ' . $_POST['app_time_hour'] . ':' . $_POST['app_time_min'] . ':00';
		$app_start = strtotime($app_start);

		$app_end = $app_start + ($_POST['app_duration'] * 60);
		$result  = new Validate();
		$results = $result->process($formData1, $_POST);
		$db_data = $results['Results'];
// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?';
		if ($_POST['app_id']) {
			$results['Results']['app_id'] = $_POST['app_id'];
		}
		if (is_array($results['Results'])) {
			$return .= http_build_query($results['Results']);
		}
		if ($results['Errors']) {
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}
		if ($app_start != strtotime($appointmentData['app_start']) || $appAllday != $appointmentData['app_allday']) {
			$resetStatus($attachedDeals);
		}

// extract notes from db_data and store in notes table
		if ($db_data['notes']) {
			$notes = format_data($db_data['notes']);
			unset($db_data['notes']);
			if ($notes) {
				$db_data2 = array(
						'not_blurb' => $notes,
						'not_row'   => $app_id,
						'not_type'  => 'appointment',
						'not_user'  => $_SESSION['auth']['use_id'],
						'not_date'  => $date_mysql
				);
				db_query($db_data2, "INSERT", "note", "not_id");
			}
		}
		unset($db_data['notes']);

		$db_data['app_start'] = date("Y-m-d H:i:s", $app_start);
		$db_data['app_end']   = date("Y-m-d H:i:s", $app_end);

		$db_response = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
		notify($db_response, 'edit');

	}

	if ($_POST['searchLink']) {
		header("Location:" . urldecode($_POST['searchLink']) . "&app_id=$app_id");
	} else {
		header("Location:?app_id=$app_id&searchLink=" . $_POST['searchLink'] . "&msg=Update+Successful");
	}
	exit;

}
