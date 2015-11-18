<?php
require_once("inx/global.inc.php");

$sql = "SELECT
appointment.*,
DATE_FORMAT(appointment.app_start, '%D %b %y') AS app_date,
DATE_FORMAT(appointment.app_end, '%H:%i') AS app_endtime,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,'',app_id,')') AS pro_addr,
link_deal_to_appointment.d2a_id,dea_type,
CONCAT(contact.con_fname,' ',contact.con_sname) AS con_name,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name
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
AND appointment.app_type = 'Viewing'
AND app_status = 'Active'
GROUP BY pro_addr
ORDER BY app_start ASC";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows) {

	while ($row = $q->fetchRow()) {

		if ($row["dea_type"] == 'Sales') {
			$feedback .= '
			<tr class="trOff">
			<td class="nowrap">' . $row["app_date"] . '</td>
			<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["pro_addr"]) . '</td>
			<td class="nowrap">' . $row["use_name"] . '</td>
			<td><a href="appointment_edit.php?app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View Appointment" /></a></td>
			<td><a href="/admin4/appointment/feedback/id/id=' . $row["d2a_id"] . '"><img src="/images/sys/admin/icons/comment_add.gif" width="16" height="16" border="0" alt="Leave Feedback" /></a></td>
			</tr>
			';
			$countSales++;
		} else {
			$feedback2 .= '
			<tr class="trOff">
			<td class="nowrap">' . $row["app_date"] . '</td>
			<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["pro_addr"]) . '</td>
			<td class="nowrap">' . $row["use_name"] . '</td>
			<td><a href="appointment_edit.php?app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View Appointment" /></a></td>
			<td><a href="/admin4/appointment/feedback/id/id=' . $row["d2a_id"] . '"><img src="/images/sys/admin/icons/comment_add.gif" width="16" height="16" border="0" alt="Leave Feedback" /></a></td>
			</tr>
			';
			$countLettings++;
		}
	}

	$feedback  = '
	<h1>Missing Feedback Sales (' . $countSales . ')</h1>
	<table border="0" cellpadding="5" cellspacing="0">
	' . $feedback . '</table>';
	$feedback2 = '
	<h1>Missing Feedback Lettings (' . $countLettings . ')</h1>
	<table border="0" cellpadding="5" cellspacing="0">
	' . $feedback2 . '</table>';
}
$page = new HTML_Page2($page_defaults);
$page->setTitle("Leave Feedback");
$page->addStyleSheet(getDefaultCss());
$page->addScriptDeclaration($js);
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="home"><table width="100%" cellpadding="10"><tr valign="top"><td width="50%">');
$page->addBodyContent($feedback);
$page->addBodyContent('</td><td width="50%">');
$page->addBodyContent($feedback2);
$page->addBodyContent('</td></tr></table>');
$page->addBodyContent('</div>');
$page->display();
?>