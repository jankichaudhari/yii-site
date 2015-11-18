<?php
require_once("inx/global.inc.php");
/*
leave feedback for each deal linked to appointment
*/

$d2a_id = $_GET["d2a_id"];
$app_id = $_GET["app_id"];

$sql = "SELECT
appointment.app_id,DATE_FORMAT(appointment.app_start,'%W %D %M %H:%i') AS app_date,app_type,
link_deal_to_appointment.d2a_feedback,d2a_feedbacknotes,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
dea_id,
GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ' &amp; ') AS cli_name,
GROUP_CONCAT(DISTINCT CONCAT(cli_id) ORDER BY client.cli_id ASC SEPARATOR '|') AS cli_id,
GROUP_CONCAT(DISTINCT CONCAT(con_fname,' ',con_sname,'(',con_id,')') ORDER BY contact.con_id ASC SEPARATOR ' &amp; ') AS con_name
FROM link_deal_to_appointment
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN appointment ON link_deal_to_appointment.d2a_app = appointment.app_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
WHERE
link_deal_to_appointment.d2a_dea = deal.dea_id AND
link_deal_to_appointment.d2a_app = appointment.app_id AND
link_deal_to_appointment.d2a_id = $d2a_id
GROUP BY link_deal_to_appointment.d2a_id";

$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$app_id            = $row["app_id"];
	$app_date          = $row["app_date"];
	$app_type          = $row["app_type"];
	$d2a_feedback      = $row["d2a_feedback"];
	$d2a_feedbacknotes = $row["d2a_feedbacknotes"];
	$pro_addr          = $row["pro_addr"];
	$dea_id            = $row["dea_id"];
	$cli_id            = $row["cli_id"];
	$cli_name          = $row["cli_name"];
	$con_name          = $row["con_name"];
}

$render = '<table cellpadding="2" cellspacing="2" border="0">
  <tr>
	<td class="label" valign="top">Property</td>
	<td><a href="/admin4/instruction/summary/id/'.$dea_id.'">'.$pro_addr.'</a></td>
  </tr>';
if ($app_type == "Valuation") {
	$render .= '
  <tr>
	<td class="label" valign="top">Vendor(s)</td>
	<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $cli_name) . '</td>
  </tr>';
} elseif ($app_type == "Inspection") {
	$render .= '
  <tr>
	<td class="label" valign="top">Inspector(s)</td>
	<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $con_name) . '</td>
  </tr>';
} else {
	$render .= '
  <tr>
	<td class="label" valign="top">Viewer(s)</td>
	<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $cli_name) . '</td>
  </tr>';
}
$render .= '
  <tr>
	<td class="label" valign="top">Date</td>
	<td>' . $app_date . '</td>
  </tr>
</table>';

$formData1 = array(
	'd2a_feedback' => array(
		'type'     => 'radio',
		'label'    => 'Outcome',
		'value'    => $d2a_feedback,
		'options'  => array_slice(db_enum("link_deal_to_appointment", "d2a_feedback", "array"), 1), // remove first element 'None' from array
		'required' => 2
	),
	'notes'        => array(
		'type'       => 'textarea',
		'label'      => 'Add Note',
		'attributes' => array('class' => 'noteInput'),
		'tooltip'    => 'These notes will be visible to vendors'
	)
);

if (!$_GET["action"]) {

	$form = new Form();

	$form->addForm("app_form", "GET", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "app_id", "", $app_id);
	$form->addField("hidden", "d2a_id", "", $d2a_id);
	$form->addField("hidden", "dea_id", "", $dea_id);
	$form->addField("hidden", "cli_id", "", $cli_id);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));

	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Feedback</div>');
	$form->addHtml($render);
	$form->addData($formData1, $_GET);
	$form->addHtml(renderNotes('feedback', $d2a_id));
	if ($app_type == 'Viewing') {
		$form->addHtml($form->addRow('radio', 'makeoffer', 'Submit Offer?', 'No', '', array('Yes' => 'Yes', 'No' => 'No')));
	}
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'))));
	$form->addHtml("</fieldset>\n");
	$form->addHtml('</div>');

	$navbar_array = array(
		'back'   => array('title' => 'Back', 'label' => 'Back', 'link' => $_GET["searchLink"]),
		'search' => array('title' => 'Appointment Search', 'label' => 'Appointment Search', 'link' => 'appointment_search.php')
	);
	$navbar       = navbar2($navbar_array);

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Feedback");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addScript('js/scriptaculous/prototype.js');
	$page->addScript('js/scriptaculous/scriptaculous.js');
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent('</div>');
	$page->display();

} else {

	$result  = new Validate();
	$results = $result->process($formData1, $_GET);
	$db_data = $results['Results'];

// build return link
	$return = $_SERVER['SCRIPT_NAME'] . '?';
	if ($d2a_id) {
		$results['Results']['d2a_id'] = $d2a_id;
	}
	if (is_array($results['Results'])) {
		$return .= http_build_query($results['Results']);
	}
	$return .= '&searchLink=' . $_GET["searchLink"];
	if ($results['Errors']) {
		echo error_message($results['Errors'], urlencode($return));
		exit;
	}

// extract notes from db_data and store in notes table
	if ($db_data["notes"]) {
		$notes = $db_data["notes"];
		unset($db_data["notes"]);
		$db_data2 = array(
			'not_blurb' => $notes,
			'not_row'   => $d2a_id,
			'not_type'  => 'feedback',
			'not_user'  => $_SESSION["auth"]["use_id"],
			'not_date'  => $date_mysql
		);
		db_query($db_data2, "INSERT", "note", "not_id");
	}
	unset($db_data["notes"]);

	db_query($db_data, "UPDATE", "link_deal_to_appointment", "d2a_id", $d2a_id);

// auto forward to offer submit page with client id(s) and deal id
	if ($_GET["makeoffer"] == 'Yes') {
		header("Location:offer_submit.php?dea_id=$dea_id&cli_id=$cli_id&app_id=$app_id&return=" . $_GET["searchLink"]);
		exit;
	}
	if ($_GET["searchLink"]) {
		header("Location:" . urldecode($_GET["searchLink"]));
	} else {
		header("Location:appointment_edit.php?app_id=" . $_GET["app_id"] . '&searchLink=' . $_GET["searchLink"]);
	}
	exit;
}
?>