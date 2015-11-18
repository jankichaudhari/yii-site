<?php
require_once(dirname(__FILE__) . "/inx/global.inc.php");

/*
Home page
ALL USERS
welcome note
future appointments
NEGOTIATORS
assigned deals
feedback
past valuations that have not been updated
offers
PHOTOGRAPHERS
instructed
PRODUCTION
pending
EDITORS
proofing
*/
///////////////////////////////////////////////////////////////////////////////////
// ALL USERS
///////////////////////////////////////////////////////////////////////////////////
$hour = date('H');

if ($hour < 12) {
	$greeting = 'Good Morning ';
} elseif ($hour >= 12 && $hour < 17) {
	$greeting = 'Good Afternoon ';
} elseif ($hour >= 17) {
	$greeting = 'Good Evening ';
}

$welcome_text = '<h4>' . $greeting . $_SESSION["auth"]["use_fname"] . '';


// get last login date
$sql = "SELECT *,DATE_FORMAT(log_timestamp, '%D %M %Y %h:%i') AS date
FROM login
WHERE log_use_id = " . $_SESSION["auth"]["use_id"] . "
ORDER BY log_timestamp DESC
LIMIT 1";
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
if ($q->numRows()) {
	while ($row = $q->fetchRow()) {
		$location = $known_ip[$row["log_ip"]];
		if (!$location) {
			$location = $row["log_ip"] . ' (unknown)';
		}
		//$welcome_text .= ' &nbsp; <span class="small">Your last login: '.$row["date"].' &nbsp; Location: '.$location.'</span>';
	}
}
$welcome_text .= '</h4>';


// get future appointments
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');


$sql = "SELECT
appointment.*,
DATE_FORMAT(appointment.app_start, '%H:%i') AS app_starttime,
DATE_FORMAT(appointment.app_start, '%a %D %b %H:%i') AS app_startdate,
DATE_FORMAT(appointment.app_start, '%a %D %b') AS app_startday,
DATE_FORMAT(appointment.app_end, '%H:%i') AS app_endtime,
pro_id,GROUP_CONCAT(DISTINCT CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,')^',link_deal_to_appointment.d2a_cv,',',link_deal_to_appointment.d2a_id,'^',link_deal_to_appointment.d2a_feedback,',',link_deal_to_appointment.d2a_id) ORDER BY link_deal_to_appointment.d2a_ord ASC SEPARATOR '|') AS pro_addr,
bra_id,bra_title,
cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,' (',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR '<br />') AS cli_name,
GROUP_CONCAT(DISTINCT CONCAT(link_deal_to_appointment.d2a_cv) SEPARATOR '|') AS d2a_cv,
user.use_id,CONCAT(user.use_fname,' ',user.use_sname) AS use_name,user.use_colour,
CONCAT(contact.con_fname,' ',contact.con_sname) AS con_name
FROM appointment
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
WHERE
(app_allday = 'No' AND app_end > '" . $date_mysql . "' AND app_user = " . $_SESSION["auth"]["use_id"] . " AND app_status != 'Deleted') OR
(app_allday = 'Yes' AND app_end > '" . $today_start . "' AND app_user = " . $_SESSION["auth"]["use_id"] . " AND app_status != 'Deleted')

GROUP BY appointment.app_id
ORDER BY app_start";
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows) {

	$appointments = '
	<h1>Future Appointments (' . $numRows . ')</h1>
	<div class="scrollDiv" id="appointments" style="height:200px">
	<table>
	';
	while ($row = $q->fetchRow()) {
		$duration = (strtotime($row["app_end"]) - strtotime($row["app_start"])) / 60;
		// get properties into array (pipe separated)
		$props = explode("|", $row["pro_addr"]);
		foreach ($props as $pro) {
			// separate the cv and feedback from the pro (hat separated)
			$pro = explode("^", $pro);
			// separate cv status from d2a_id
			$cv_info       = explode(",", $pro[1]);
			$feedback_info = explode(",", $pro[2]);

			if ($pro[0]) {

				if ($cv_info[0] == "Not Confirmed") {
					$bgcol    = '#FC9B9B'; //'#FD7777';
					$cv_light = 'trafficlight_red.gif';
				} elseif ($cv_info[0] == "Message Left") {
					$bgcol    = '#9FBAFF'; //'#93AFF5';
					$cv_light = 'trafficlight_orange.gif';
				} elseif ($cv_info[0] == "Confirmed") {
					$cv_light = 'trafficlight_green.gif';
					$bgcol    = '#8BE277'; //'#5EC346';
				}
				$pro_addr .= '<a href="appointment_confirm.php?d2a_id=' . $cv_info[1] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '" style="color:' . $bgcol . '"><img src="' . GLOBAL_URL . 'img/' . $cv_light . '" align="absmiddle" border="0" width="10" height="10" alt="' . $cv_info[0] . '" /></a>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $pro[0]) . "<br />\n";

			}
		}

		if ($row["app_type"] == "Meeting" || $row["app_type"] == "Note") {
			$pro_addr = $row["app_subject"];
		}
		if ($row["app_type"] == "Note") {
			$row["app_type"] = $row["app_notetype"];
		}
		if (strtotime($row["app_end"]) > strtotime($date_mysql)) {
			$rowClass = 'trOff';
		} elseif (strtotime($row["app_end"]) < strtotime($date_mysql)) {
			$rowClass = 'trOffGrey';
		}
		if ($row["app_status"] == 'Cancelled') {
			$rowclass = 'trOffCancelled';
			$pro_addr = str_replace(array('trafficlight_red.gif', 'trafficlight_orange.gif', 'trafficlight_green.gif'), 'trafficlight_grey.gif', $pro_addr);
		} else {
			$rowclass = 'trOff';
		}

		$appointments .= '
		<tr class="' . $rowclass . '">
		<td><strong>' . $row["app_type"] . '</strong></td>
		';
		// show todays appointments with just the time, others to show full date
		// show NOW for appointments in progress
		if ($row["app_allday"] == 'Yes') {
			if (date('dm', strtotime($row["app_start"])) == date('dm')) {
				$appointments .= '<td>Today (all day)</td>';
			} else {
				$appointments .= '<td>' . $row["app_startday"] . ' (all day)</td>';
			}
		} elseif (date(MYSQL_DATE_FORMAT) > $row["app_start"] && date(MYSQL_DATE_FORMAT) < $row["app_end"]) {
			$appointments .= '<td width="135">NOW ' . $row["app_starttime"] . ' (' . duration($duration, 'short') . ')</td>';
		} elseif (date('dm', strtotime($row["app_start"])) == date('dm')) {
			$appointments .= '<td>Today ' . $row["app_starttime"] . ' (' . duration($duration, 'short') . ')</td>';
		} else {
			$appointments .= '<td>' . $row["app_startdate"] . '</td>';
		}
		$appointments .= '
		<td>' . $pro_addr . '</td>
		<td align="right"><a href="appointment_edit.php?app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View Appointment" /></a></td>
		</tr>
		';
		unset($pro_addr, $cv, $cv_info, $feedback_info, $pro, $cv_light);
	}
	$appointments .= '</table>
	</div>';
}


// special one so sophie sees colin's appointments
if ($_SESSION["auth"]["use_id"] == 24) {

	$sql = "SELECT
	appointment.*,
	DATE_FORMAT(appointment.app_start, '%H:%i') AS app_starttime,
	DATE_FORMAT(appointment.app_start, '%a %D %b %H:%i') AS app_startdate,
	DATE_FORMAT(appointment.app_start, '%a %D %b') AS app_startday,
	DATE_FORMAT(appointment.app_end, '%H:%i') AS app_endtime,
	pro_id,GROUP_CONCAT(DISTINCT CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,')^',link_deal_to_appointment.d2a_cv,',',link_deal_to_appointment.d2a_id,'^',link_deal_to_appointment.d2a_feedback,',',link_deal_to_appointment.d2a_id) ORDER BY link_deal_to_appointment.d2a_ord ASC SEPARATOR '|') AS pro_addr,
	bra_id,bra_title,
	cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,' (',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR '<br />') AS cli_name,
	GROUP_CONCAT(DISTINCT CONCAT(link_deal_to_appointment.d2a_cv) SEPARATOR '|') AS d2a_cv,
	user.use_id,CONCAT(user.use_fname,' ',user.use_sname) AS use_name,user.use_colour,
	CONCAT(contact.con_fname,' ',contact.con_sname) AS con_name,
	GROUP_CONCAT(DISTINCT CONCAT(attendee.use_fname,' ',attendee.use_sname) SEPARATOR '|') AS attendee_name
	FROM appointment
	LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
	LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
	LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
	LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN branch ON deal.dea_branch = branch.bra_id
	LEFT JOIN user ON appointment.app_user = user.use_id
	LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
	LEFT JOIN contact ON con2app.c2a_con = contact.con_id


	LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
	LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id

	WHERE
	(app_allday = 'No' AND app_end > '" . $date_mysql . "' AND app_user = 10 AND app_status != 'Deleted') OR
	(app_allday = 'Yes' AND app_end > '" . $today_start . "' AND app_user = 10 AND app_status != 'Deleted')

	GROUP BY appointment.app_id
	ORDER BY app_start";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();
	if ($numRows) {

		$appointments = '
		<h1>Colin\'s Appointments (' . $numRows . ')</h1>
		<div class="scrollDiv" id="appointments" style="height:200px">
		<table>
		';
		while ($row = $q->fetchRow()) {
			$duration = (strtotime($row["app_end"]) - strtotime($row["app_start"])) / 60;

			// get properties into array (pipe separated)
			$props = explode("|", $row["pro_addr"]);
			foreach ($props as $pro) {
				// separate the cv and feedback from the pro (hat separated)
				$pro = explode("^", $pro);
				// separate cv status from d2a_id
				$cv_info       = explode(",", $pro[1]);
				$feedback_info = explode(",", $pro[2]);
				if ($pro[0]) {

					if ($cv_info[0] == "Not Confirmed") {
						$bgcol    = '#FC9B9B'; //'#FD7777';
						$cv_light = 'trafficlight_red.gif';
					} elseif ($cv_info[0] == "Message Left") {
						$bgcol    = '#9FBAFF'; //'#93AFF5';
						$cv_light = 'trafficlight_orange.gif';
					} elseif ($cv_info[0] == "Confirmed") {
						$cv_light = 'trafficlight_green.gif';
						$bgcol    = '#8BE277'; //'#5EC346';
					}
					$pro_addr .= '<a href="appointment_confirm.php?d2a_id=' . $cv_info[1] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '" style="color:' . $bgcol . '"><img src="' . GLOBAL_URL . 'img/' . $cv_light . '" align="absmiddle" border="0" width="10" height="10" alt="' . $cv_info[0] . '" /></a>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $pro[0]) . "<br />\n";

				}
			}

			if ($row["app_type"] == "Meeting" || $row["app_type"] == "Note") {
				$pro_addr = $row["app_subject"];
			}
			if (strtotime($row["app_end"]) > strtotime($date_mysql)) {
				$rowClass = 'trOff';
			} elseif (strtotime($row["app_end"]) < strtotime($date_mysql)) {
				$rowClass = 'trOffGrey';
			}
			if ($row["app_status"] == 'Cancelled') {
				$rowclass = 'trOffCancelled';
				$pro_addr = str_replace(array('trafficlight_red.gif', 'trafficlight_orange.gif', 'trafficlight_green.gif'), 'trafficlight_grey.gif', $pro_addr);
			} else {
				$rowclass = 'trOff';
			}

			$appointments .= '
			<tr class="' . $rowclass . '">
			<td><strong>' . $row["app_type"] . '</strong></td>
			';
			// show todays appointments with just the time, others to show full date
			// show NOW for appointments in progress
			if ($row["app_allday"] == 'Yes') {
				if (date('dm', strtotime($row["app_start"])) == date('dm')) {
					$appointments .= '<td>Today (all day)</td>';
				} else {
					$appointments .= '<td>' . $row["app_startday"] . ' (all day)</td>';
				}
			} elseif (date(MYSQL_DATE_FORMAT) > $row["app_start"] && date(MYSQL_DATE_FORMAT) < $row["app_end"]) {
				$appointments .= '<td width="135">NOW ' . $row["app_starttime"] . ' (' . duration($duration, 'short') . ')</td>';
			} elseif (date('dm', strtotime($row["app_start"])) == date('dm')) {
				$appointments .= '<td>Today ' . $row["app_starttime"] . ' (' . duration($duration, 'short') . ')</td>';
			} else {
				$appointments .= '<td>' . $row["app_startdate"] . '</td>';
			}
			$appointments .= '
			<td>' . $pro_addr . '</td>
			<td align="right"><a href="appointment_edit.php?app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View Appointment" /></a></td>
			</tr>
			';
			unset($pro_addr, $cv, $cv_info, $feedback_info, $pro, $cv_light);
		}
		$appointments .= '</table>
		</div>';
	}

}


///////////////////////////////////////////////////////////////////////////////////
// NEGOTIATORS (sales and lettings)
///////////////////////////////////////////////////////////////////////////////////
if (in_array('Negotiator', $_SESSION["auth"]["roles"])) {

	// get all appointments carried out by the current user (past) that have not yet had feedback given
	// adding deal id and app id to the pro_addr for grouping purposes, each must be unique and i is not
	// possible to have two of the same deal in a single appointment
	$sql = "SELECT
appointment.*,
DATE_FORMAT(appointment.app_start, '%D %b') AS app_date,
DATE_FORMAT(appointment.app_end, '%H:%i') AS app_endtime,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,'',app_id,')') AS pro_addr,
link_deal_to_appointment.d2a_id,
CONCAT(contact.con_fname,' ',contact.con_sname) AS con_name
FROM appointment
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
WHERE
(app_end < '" . $date_mysql . "')
AND app_user = " . $_SESSION["auth"]["use_id"] . "
AND link_deal_to_appointment.d2a_feedback = 'None'
AND appointment.app_type = 'Viewing'
AND app_status = 'Active'
GROUP BY pro_addr
ORDER BY app_start DESC";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();
	if ($numRows) {
		$feedback = '
	<h1>Leave Feedback (' . $numRows . ')</h1>
	<div class="scrollDiv" id="feedback" style="height:275px">
	<table>
	';

		while ($row = $q->fetchRow()) {
			$feedback .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'/admin4/appointment/feedback/id/' . $row["d2a_id"] . '\')">
		<td class="nowrap">' . $row["app_date"] . '</td>
		<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["pro_addr"]) . '</td>
		<td><a href="/admin4/appointment/feedback/id/' . $row["d2a_id"] . '"><img src="/images/sys/admin/icons/comment_add.gif" width="16" height="16" border="0" alt="Leave Feedback" /></a></td>
		</tr>
		';
		}
		$feedback .= '</table>
	</div>';
	}

	// get all valuations carried out by the current user (past) that have not changed status (still "Valuation")
	$sql = "SELECT
appointment.*,dea_id,
DATE_FORMAT(appointment.app_start, '%D %b') AS app_date,
DATE_FORMAT(appointment.app_end, '%H:%i') AS app_endtime,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,'',app_id,')') AS pro_addr,
link_deal_to_appointment.d2a_id,
CONCAT(contact.con_fname,' ',contact.con_sname) AS con_name
FROM appointment
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
WHERE
(app_end < '" . $date_mysql . "')
AND app_user = " . $_SESSION["auth"]["use_id"] . "
AND link_deal_to_appointment.d2a_feedback = 'None'
AND appointment.app_type = 'Valuation'
AND deal.dea_status = 'Valuation'
AND app_status = 'Active'
GROUP BY pro_addr
ORDER BY app_start DESC";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();
	if ($numRows) {
		$valuations = '
	<h1>Valuations to follow up (' . $numRows . ')</h1>
	<div class="scrollDiv" id="valuations">
	<table>
	';

		while ($row = $q->fetchRow()) {
			$valuations .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'valuation_followup.php?dea_id=' . $row["dea_id"] . '&app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '\')">
		<td class="nowrap">' . $row["app_date"] . '</td>
		<td height="16">' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["pro_addr"]) . '</td>
		</tr>
		';
		}
		$valuations .= '</table>
	</div>';
	}

	// show assigned deals
	$sql = "SELECT
dea_id,
s.sot_status,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ', ') AS cli_name
FROM
deal
LEFT JOIN property ON deal.dea_prop = property.pro_id


LEFT OUTER JOIN sot AS s
	ON s.sot_deal = deal.dea_id
	AND s.sot_date =
       ( SELECT max(sot_date)
           FROM sot
          WHERE sot_deal = deal.dea_id )


LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id AND link_client_to_instruction.capacity = 'Owner'
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
WHERE
deal.dea_neg = " . $_SESSION["auth"]["use_id"] . " AND
(dea_status = 'Instructed' OR dea_status = 'Production' OR dea_status = 'Proofing' OR
dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Exchanged' OR
dea_status = 'Under Offer with Other')
GROUP BY deal.dea_id
ORDER BY s.sot_date DESC";

	//(deal.dea_status = 'Instructed' OR deal.dea_status = 'Production' OR deal.dea_status = 'Proofing' OR deal.dea_status = 'Available'
	//OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Exchanged') AND
	$q = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();
	if ($numRows) {
		$assigned = '
	<h1>Assigned Property (' . $numRows . ')</h1>
	<div class="scrollDiv" id="deals">
	<table>
	';
		while ($row = $q->fetchRow()) {
			$assigned .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'/admin4/instruction/summary/id/' . $row["dea_id"] . '\')">
		<td height="16">' . $row["pro_addr"] . '</td>
		<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]) . '</td>
		<td>' . $row["sot_status"] . '<!-- ' . $row["sot_date"] . '--></td>
		</tr>
		';
		}
		$assigned .= '</table>
	</div>';
	}

	// show offers
	// only show available, under offer, exchanged
	$sql = "SELECT
offer.*,dea_id,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ', ') AS cli_name
FROM
offer
LEFT JOIN deal ON offer.off_deal = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2off ON cli2off.c2o_off = offer.off_id
LEFT JOIN client ON cli2off.c2o_cli = client.cli_id
WHERE
offer.off_neg = " . $_SESSION["auth"]["use_id"] . " AND
(dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Exchanged') AND
(off_status != 'Withdrawn' AND off_status != 'Rejected')
GROUP BY offer.off_id
ORDER BY off_timestamp DESC";

	$q = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();

	if ($numRows) {
		$offers = '
	<h1>My Offers (' . $numRows . ')</h1>
	<div class="scrollDiv" id="offers">
	<table>
	';
		while ($row = $q->fetchRow()) {
			$offers .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'/admin4/offer/displaySave/id/' . $row["off_id"] .  '\')">
		<td height="16">' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]) . '</td>
		<td>' . $row["pro_addr"] . '</td>
		<td width="100">' . $row["off_status"] . '</td>
		</tr>
		';
		}
		$offers .= '</table>
	</div>';
	}

} //END NEGOTIATORS

///////////////////////////////////////////////////////////////////////////////////
// MANAGER 07/10/08
///////////////////////////////////////////////////////////////////////////////////
// to see valuations done by any user, this will overwrite the negotiator valuations var
/* enable valuations branch (branch id :5) only for Luke Wooster (user id :2) */
if (in_array('Manager', $_SESSION["auth"]["roles"])) {
	$userSpec = "AND (deal.dea_branch = '" . $_SESSION["auth"]["use_branch"] . "')";
	if ($_SESSION["auth"]["use_id"] == '2' || $_SESSION["auth"]["use_id"] == '29') {
		$userSpec = "";
	}
	$sql = "SELECT
appointment.*,dea_id,
DATE_FORMAT(appointment.app_start, '%D %b') AS app_date,
DATE_FORMAT(appointment.app_end, '%H:%i') AS app_endtime,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,'',app_id,')') AS pro_addr,
link_deal_to_appointment.d2a_id,
CONCAT(contact.con_fname,' ',contact.con_sname) AS con_name,

CONCAT('<span class=\"use_col_small\" style=\"background-color: #',user.use_colour,';\"><img src=\"img/blank.gif\" width=\"8\" height=\"8\"></span> ',LEFT(user.use_fname,1),LEFT(user.use_sname,1)) as user

FROM appointment
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id

LEFT JOIN user ON appointment.app_user = user.use_id

WHERE
(app_end < '" . $date_mysql . "')

AND link_deal_to_appointment.d2a_feedback = 'None'
AND appointment.app_type = 'Valuation'
AND deal.dea_status = 'Valuation'
AND deal.dea_type = '" . $_SESSION["auth"]["default_scope"] . "'
" . $userSpec . "
AND app_status = 'Active'
GROUP BY pro_addr
ORDER BY app_start DESC";
//echo $sql; exit;
	//AND deal.dea_branch = '" . $_SESSION["auth"]["use_branch"] . "'
	//AND deal.dea_branch = '" . $_SESSION["auth"]["use_branch"] . "'
	$q = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();
	if ($numRows) {
		$valuations = '
	<h1>Valuations to follow up (' . $numRows . ')</h1>
	<div class="scrollDiv" id="valuations">
	<table>
	';

		while ($row = $q->fetchRow()) {
			$valuations .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'valuation_followup.php?dea_id=' . $row["dea_id"] . '&app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '\')">
		<td class="nowrap">' . $row["user"] . '</td>
		<td class="nowrap">' . $row["app_date"] . '</td>
		<td height="16">' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["pro_addr"]) . '</td>
		</tr>
		';
		}
		$valuations .= '</table>
	</div>';
	}

} // END MANAGER

///////////////////////////////////////////////////////////////////////////////////
// PHOTOGRAPHER
///////////////////////////////////////////////////////////////////////////////////
if (in_array('Photographer', $_SESSION["auth"]["roles"])) {

	// show instructed deals (production)

	$sql = "SELECT
dea_id,dea_type,dea_status,
s.sot_status,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr
FROM
deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT OUTER JOIN sot AS s ON s.sot_deal = deal.dea_id AND s.sot_date = (SELECT max(sot_date) FROM sot WHERE sot_deal = deal.dea_id)
WHERE
(dea_status = 'Instructed')
ORDER BY s.sot_date DESC";

	$q = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();
	if ($numRows) {
		$instructed = '
	<h1>Instructed (' . $numRows . ')</h1>
	<div class="scrollDiv" id="instructed" style="height:250px">
	<table>
	';
		while ($row = $q->fetchRow()) {
			$instructed .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'/admin4/instruction/summary/id/' . $row["dea_id"] . '\')">
		<td height="16">' . $row["pro_addr"] . '</td>
		<td>' . $row["dea_type"] . '</td>
		<td>' . $row["dea_status"] . '</td>
		</tr>
		';
		}
		$instructed .= '</table>
	</div>';
	}

} // END PHOTOGRAPHER

///////////////////////////////////////////////////////////////////////////////////
// PRODUCTION
///////////////////////////////////////////////////////////////////////////////////
if (in_array('Production', $_SESSION["auth"]["roles"])) {

	// show pending deals (production)

	$sql = "SELECT
dea_id,dea_type,dea_status,
s.sot_status,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr
FROM
deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT OUTER JOIN sot AS s ON s.sot_deal = deal.dea_id AND s.sot_date = (SELECT max(s2.sot_date) FROM sot AS s2 WHERE s2.sot_deal = deal.dea_id)
WHERE
(dea_status = 'Production' OR dea_status = 'Proofing')
ORDER BY s.sot_date DESC";

	$q = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$numRows = $q->numRows();
	if ($numRows) {
		$pending = '
	<h1>Pending &amp; Proofing (' . $numRows . ')</h1>
	<div class="scrollDiv" id="pending" style="height:250px">
	<table>
	';
		while ($row = $q->fetchRow()) {
			$pending .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'/admin4/instruction/production/id/' . $row["dea_id"] . '\')">
		<td height="16">' . $row["pro_addr"] . '</td>
		<td>' . $row["dea_type"] . '</td>
		<td>' . $row["dea_status"] . '</td>
		</tr>
		';
		}
		$pending .= '</table>
	</div>';
	}

} // END PRODUCTION

// render the page

$left = array($appointments, $offers, $assigned);
foreach ($left as $content) {
	if ($content) {
		$renderLeft .= '<div>' . $content . '</div>' . "\n";
	}
}

$right = array($feedback, $valuations, $instructed, $pending);
foreach ($right as $content) {
	if ($content) {
		$renderRight .= '<div>' . $content . '</div>' . "\n";
	}
}

if (!$renderLeft) {
	$renderLeft .= '<div>
	<p>&nbsp;</p>
	<p>Welcome to your home page. This page will display various items relating to you, such as
	forthcoming appointments, offers, assigned properties and feedback.
	</p></div>' . "\n";
}


/*
<!--
<p>Negotiators would see:<br>
&nbsp;&nbsp;List of current deals (offers and chains)<br>
&nbsp;&nbsp;List of properties assigned to them<br>
&nbsp;&nbsp;List of hot buyers<br>
&nbsp;&nbsp;Negotiator messages (summary)<br>
&nbsp;&nbsp;Reminders<br>
&nbsp;&nbsp;New properties<br>
&nbsp;&nbsp;Notification of offers</p>
<p>Managers would see:<br>
&nbsp;&nbsp;New take-ons<br>
&nbsp;&nbsp;Exchanges and completions</p>
<p>Office staff would see:<br>
&nbsp;&nbsp;Arrange viewing requests<br>
&nbsp;&nbsp;Valuation requests<br>
&nbsp;&nbsp;Internal staff messages (summary)<br>
&nbsp;&nbsp;New properties</p>
<p>Production would see:<br>
&nbsp;&nbsp;List of pending properties<br>
&nbsp;&nbsp;List of proofing properties<br>
&nbsp;&nbsp;Upcoming advertising deadlines<br>
&nbsp;&nbsp;Production messages (summary)<br>
&nbsp;&nbsp;Errors and mistakes that require attention</p>
-->

*/
$js = '
// IE6 only style sheet
if (document.all&&document.getElementById&&navigator.appVersion.indexOf(\'MSIE 6\')>=0) {
	document.write(\'<link rel="stylesheet" href="css/ie6.css" type="text/css" />\');
	}
';

// start a new page
$page = new HTML_Page2($page_defaults);
$page->setTitle("Home");
$page->addStyleSheet(getDefaultCss());
$page->addScriptDeclaration($js);
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="home">');
$page->addBodyContent($welcome_text);
$page->addBodyContent('<div class="home Right">' . $renderRight . '</div>');
$page->addBodyContent('<div class="home Left">' . $renderLeft . '</div>');
$page->addBodyContent('</div>');
ob_start();
$page->display();
$var = ob_get_clean();

function listIncludedFiles()
{

	echo "<pre>";
	print_r(get_included_files());
	echo "</pre>";
	echo "<pre>";
	print_r(explode(":", get_include_path()));
	echo "</pre>";
};
echo $var;
//listIncludedFiles();

?>
