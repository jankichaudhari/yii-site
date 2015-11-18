<?php
/*
notify - 27/09/09
*/
function notify($array, $action)
{

	global $db;

	$id    = $array["row"];
	$array = $array["array"];
	$sending = array();

	if (count($array) == 0) {
		return;
	}

	// in add mode, we have no array
	if ($action == 'add') {
		$sql = "SELECT * FROM appointment WHERE app_id = $id";
		$q = $db->query($sql);
		while ($row = $q->fetchRow()) {
			foreach ($row as $key => $val) {
				$array[$key] = $val;
			}
		}
	}

	// other data, text labels for id number: branch and users
	$sql = "SELECT * FROM user";
	$q     = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$users[$row["use_id"]] = array(
				'name'   => $row["use_fname"] . ' ' . $row["use_sname"],
				'email'  => $row["use_email"],
				'notify' => $row["use_notify"]
		);
	}
	$sql = "SELECT * FROM branch";
	$q     = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$branches[$row["bra_id"]] = $row["bra_title"];
	}

	// email addresses we may need are: neg (new and old if app_user is present in array), client(s), vendor(s), attendees

	// this query fetches all users
	$sql = "SELECT appointment.app_user,appointment.app_bookedby,attendee.use_id,appointment.app_type
	FROM appointment
	LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
	LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id
	WHERE app_id = $id";
	$q     = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$user     = $users[$row["app_user"]];
		$bookedby = $users[$row["app_bookedby"]];
		$attendees[] = $users[$row["use_id"]];

		$appointmentType = $row["app_type"];
	}

	// getting all viewers
	$sql = "SELECT viewer.cli_id,viewer.cli_fname,viewer.cli_sname,viewer.cli_email
	FROM appointment
	LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
	LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id
	WHERE appointment.app_id = $id";
	$q     = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$viewers[$row["cli_id"]] = array(
				'name'  => $row["cli_fname"] . ' ' . $row["cli_sname"],
				'email' => $row["cli_email"]
		);
	}

	// getting all vendors / landlords
	$sql = "SELECT vendor.cli_id,vendor.cli_fname,vendor.cli_sname,vendor.cli_email
	FROM appointment
	LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
	LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
	LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
	LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id
	WHERE appointment.app_id = $id";
	$q     = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$vendors[$row["cli_id"]] = array(
				'name'  => $row["cli_fname"] . ' ' . $row["cli_sname"],
				'email' => $row["cli_email"]
		);
	}

	$debug         = "
Action:       $action
User:         " . print_r($user, true) . "
Booked By:    " . print_r($bookedby, true) . "
Atendees:     " . print_r($attendees, true) . "
Viewers:      " . print_r($viewers, true) . "
Vendors:      " . print_r($vendors, true) . "

Array:        " . print_r($array, true) . "
";

	if ($action == 'edit') {

		// branch
		if (array_key_exists('calendarID', $array)) {
			$changes .= "Branch changed from " . $branches[$array["calendarID"]["old"]] . " to " . $branches[$array["calendarID"]["new"]] . "\n";
		}
		// negotiator
		if (array_key_exists('app_user', $array)) {
			$changes .= "Negotiator changed from " . $users[$array["app_user"]["old"]]["name"] . " to " . $users[$array["app_user"]["new"]]["name"] . "\n";
		}
		// start
		if (array_key_exists('app_start', $array)) {
			$changes .= "Start changed from " . date('j M Y g:ia', strtotime($array["app_start"]["old"])) . " to " . date('j M Y g:ia', strtotime($array["app_start"]["new"])) . "\n";
		}
		// duration
		if (array_key_exists('app_end', $array)) {
			$changes .= "End changed from " . date('j M Y g:ia', strtotime($array["app_end"]["old"])) . " to " . date('j M Y g:ia', strtotime($array["app_end"]["new"])) . "\n";
		}

		// status change notifications
		// new properties added
		if (array_key_exists('add_property', $array)) {
			$changes .= "Additional property added to appointment";
		}
		// new client added
		if (array_key_exists('add_client', $array)) {
			$changes .= "Additional client added to appointment";
		}
		// new attendee added
		if (array_key_exists('add_attendee', $array)) {
			$changes .= "Attendee added to appointment";
		}
		// property removed
		if (array_key_exists('remove_property', $array)) {
			$changes .= "Property removed from appointment";
		}
		// client removed
		if (array_key_exists('remove_client', $array)) {
			$changes .= "Client removed from appointment";
		}
		// attendee removed
		if (array_key_exists('remove_attendee', $array)) {
			$changes .= "Attendee removed from appointment";
		}
		// appointment deleted
		if (array_key_exists('appointment_deleted', $array)) {
			$changes .= "Appointment Deleted";
		}
		// appointment undeleted
		if (array_key_exists('appointment_undeleted', $array)) {
			$changes .= "Appointment Un-Deleted or Un-Cancelled";
		}

		// appointment cancelled
		if (array_key_exists('appointment_cancelled', $array)) {
			$changes .= "Appointment Cancelled";
		}

		$emailSubject = "NOTIFY: An appointment associated with you has been updated";
		$emailBody = "On " . date('j M Y g:ia') . ", " . $users[$_SESSION["auth"]["use_id"]]["name"] . " updated an appointment with the following changes:\n";
		$emailBody .= $changes . "\n";

		// build list of recipients

		// assigned neg
		$recipients[$user["email"]] = $user["email"];

		// send to both old and new assigned neg
		if ($array["app_user"]["old"]) {
			$recipients[$users[$array["app_user"]["old"]]["email"]] = $users[$array["app_user"]["old"]]["email"];
		}
		if ($array["app_user"]["new"]) {
			$recipients[$users[$array["app_user"]["new"]]["email"]] = $users[$array["app_user"]["new"]]["email"];
		}
		// viewers
		foreach ($viewers as $key => $val) {
			//$recipients[$val["email"]] = $val["email"];
		}
		// vendors
		foreach ($vendors as $key => $val) {
			//$recipients[$val["email"]] = $val["email"];
		}
		// atendees
		foreach ($attendees as $key => $val) {
			$recipients[$val["email"]] = $val["email"];
		}

		// don't send email to the current user
		unset($recipients[$_SESSION["auth"]["use_email"]]);

		foreach ($recipients as $recip) {
			if (trim($recip)) {
				$sending[] = $recip;
			}
		}

		$emailBody .= appointmentEmailContent($id);
		//$emailBody .= "\n\n\n\n\n\n\nSending email to the following recipients: ".print_r($sending,true);
		//$emailBody .= "\n\n".$debug;

	} elseif ($action == 'add') {

		$emailSubject = "NOTIFY: An appointment has been created";
		$emailBody = "On " . date('j M Y g:ia') . ", " . $users[$_SESSION["auth"]["use_id"]]["name"] . " created an appointment with the following details:\n";
		$emailBody .= appointmentEmailContent($id);

		// build list of recipients

		// assigned neg
		$recipients[$users[$array["app_user"]]["email"]] = $users[$array["app_user"]]["email"];

		// don't send email to the current user
		unset($recipients[$_SESSION["auth"]["use_email"]]);

		foreach ($recipients as $recip) {
			if (trim($recip)) {
				$sending[] = $recip;
			}
		}

		//$emailBody .= "\n\n\n\n\n\n\nSending email to the following recipients: ".print_r($sending,true);
		//$emailBody .= "\n\n".$debug;

	} elseif ($action == 'cancel') {
	} elseif ($action == 'delete') {
	}

	// only send for some app types
	if ($appointmentType == 'Note' || $appointmentType == 'Meeting' || $appointmentType == 'Lunch') {
		return false;
	}

	$emailHeaders = "From: Admin <post@woosterstock.co.uk>\r\n";
	foreach ($sending as $to) {
		mail($to, $emailSubject, $emailBody, $emailHeaders);
		//mail('mail@markdw.com',$to.' = '.$emailSubject,$emailBody,$emailHeaders);
	}

	$emailBody .= "\n\n\n\n\n\n\nSending email to the following recipients: " . print_r($sending, true);
	//$emailBody .= "\n\n".$debug;
	//mail('mail@markdw.com','DEBUG - '.$emailSubject,$emailBody);

}

// create email body content for given appointment
function appointmentEmailContent($appid)
{
	global $db;

	$sql = "SELECT appointment.*,
	CONCAT(user.use_fname,' ',user.use_sname) AS negotiator,
	CONCAT(bookedby.use_fname,' ',bookedby.use_sname) AS bookedby,
	GROUP_CONCAT(CONCAT(attendee.use_fname,' ',attendee.use_sname) SEPARATOR ', ') AS attendees
	FROM appointment
	LEFT JOIN user ON appointment.app_user = user.use_id
	LEFT JOIN user AS bookedby ON appointment.app_bookedby = bookedby.use_id
	LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
	LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id

	WHERE app_id = $appid
	GROUP BY appointment.app_id";
	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {
		foreach ($row as $key => $val) {
			$array[$key] = $val;
		}
	}

	$sql = "SELECT
	CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr
	FROM appointment
	LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
	LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	WHERE appointment.app_id = $appid
	ORDER BY link_deal_to_appointment.d2a_ord ASC";
	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$locations .= $row["pro_addr"] . "\n";
	}

	$sql = "SELECT
	CONCAT(viewer.cli_salutation,' ',viewer.cli_fname,' ',viewer.cli_sname) AS client_name
	FROM appointment
	LEFT JOIN cli2app ON cli2app.c2a_app = appointment.app_id
	LEFT JOIN client AS viewer ON cli2app.c2a_cli = viewer.cli_id
	WHERE appointment.app_id = $appid";
	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$clients .= $row["client_name"] . "\n";
	}

	$sql = "SELECT
	CONCAT(contact.con_fname,' ',contact.con_sname,' - ',company.com_title) AS contact_name
	FROM appointment
	LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
	LEFT JOIN contact ON con2app.c2a_con = contact.con_id
	LEFT JOIN company ON contact.con_company = company.com_id
	WHERE appointment.app_id = $appid";
	$q = $db->query($sql);
	while ($row = $q->fetchRow()) {
		$contacts .= $row["contact_name"] . "\n";
	}

	$output = "
Appointment Details
--------------------
Link: https://www.woosterstock.co.uk/v3.0/live/admin/appointment_edit.php?app_id=$appid
Appointment Type:  " . $array["app_type"] . "
Assigned User:     " . $array["negotiator"] . "
Start:             " . date('d/m/y H:i', strtotime($array["app_start"])) . "
End:               " . date('d/m/y H:i', strtotime($array["app_end"])) . "
Attendees:         " . $array["attendees"] . "
Booked By:         " . $array["bookedby"] . "

";
	if (trim($clients)) {
		$output .= "Client(s):\n" . trim($clients) . "\n\n";
	}
	if (trim($contacts)) {
		$output .= "Contacts(s):\n" . trim($contacts) . "\n\n";
	}
	if (trim($locations)) {
		$output .= "Location(s):\n" . trim($locations) . "\n\n";
	}

	return $output;
}

?>