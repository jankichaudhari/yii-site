<?php
require_once("inx/global.inc.php");
/*
confirm appointment with vendor / landlord / tenant
each link_deal_to_appointment has to be confirmed, but this "could" be done on a single page (risky)

first run of new style notes system used here 30/03/07
note table:
	type -  (appointment,confirmation,feedback,deal(for viewing times, vendor instructions etc),applicant - more?)
	row - the row which the note relates to,
		e.g. if it is an appoitment note, it will relate to an app_id
			 if it is a confirmation note, it will relate to a link_deal_to_appointment.d2a_id
	note - the note content
	date
	user
	flag - different coloured flags for different purposes, including "complete"
	status - active and deleted (nothing ever gets deleted!)

grabbing phone numbers from tel table
*/

$d2a_id = $_GET["d2a_id"];
//GROUP_CONCAT(DISTINCT CONCAT('<a href=client_edit.php?cli_id=',cli_id,'>',cli_fname,' ',cli_sname,'</a><br/>Tel: ',cli_tel1,' (',cli_tel1type,')') ORDER BY client.cli_id ASC SEPARATOR '<br />') AS cli_name,

$sql = "SELECT
appointment.app_id,appointment.app_type,
link_deal_to_appointment.d2a_cv,link_deal_to_appointment.d2a_cvnotes,
GROUP_CONCAT(DISTINCT CONCAT(vendor.cli_id,'|',vendor.cli_fname,' ',vendor.cli_sname,'|',vendor.cli_email,'|',vendor.cli_preferred) ORDER BY vendor.cli_id ASC SEPARATOR '~') AS vendor_name,
GROUP_CONCAT(DISTINCT CONCAT(tenant.cli_id,'|',tenant.cli_fname,' ',tenant.cli_sname,'|',tenant.cli_email,'|',tenant.cli_preferred) ORDER BY tenant.cli_id ASC SEPARATOR '~') AS tenant_name,

deal.dea_id,deal.dea_type,dea_key,
GROUP_CONCAT(DISTINCT CONCAT(vendor_tel.tel_number,'|',vendor_tel.tel_type,'|',vendor_tel.tel_cli,'|',vendor_tel.tel_id) ORDER BY vendor_tel.tel_ord ASC SEPARATOR '~') AS ven_tel,
GROUP_CONCAT(DISTINCT CONCAT(tenant_tel.tel_number,'|',tenant_tel.tel_type,'|',tenant_tel.tel_cli,'|',tenant_tel.tel_id) ORDER BY tenant_tel.tel_ord ASC SEPARATOR '~') AS ten_tel,
keybook.*,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr
FROM link_deal_to_appointment

LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN appointment ON link_deal_to_appointment.d2a_app = appointment.app_id
LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
LEFT JOIN client AS vendor ON link_client_to_instruction.clientId = vendor.cli_id AND link_client_to_instruction.capacity = 'Owner'

LEFT JOIN link_client_to_instruction AS ten2dea ON ten2dea.dealId = deal.dea_id AND ten2dea.capacity = 'Tenant'
LEFT JOIN client AS tenant ON ten2dea.clientId = tenant.cli_id

LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN tel AS vendor_tel ON vendor.cli_id = vendor_tel.tel_cli
LEFT JOIN tel AS tenant_tel ON tenant.cli_id = tenant_tel.tel_cli
LEFT JOIN keybook ON deal.dea_id = keybook.key_deal
WHERE
link_deal_to_appointment.d2a_dea = deal.dea_id AND
link_deal_to_appointment.d2a_app = appointment.app_id AND
link_deal_to_appointment.d2a_id = $d2a_id
GROUP BY deal.dea_id";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$app_id      = $row["app_id"];
	$app_type    = $row["app_type"];
	$d2a_cv      = $row["d2a_cv"];
	$d2a_cvnotes = $row["d2a_cvnotes"];
	$dea_id      = $row["dea_id"];
	$dea_type    = $row["dea_type"];
	$pro_addr    = $row["pro_addr"];

	if ($row["dea_key"]) {
		$key_info = $row["dea_key"];
	} else {
		$key_info = 'No key registered for this property';
	}

	$vendors = explode("~", $row["vendor_name"]);
	foreach ($vendors as $vendor) {
		$vendor_details = explode("|", $vendor);
		$id             = $vendor_details[0];
		$name           = $vendor_details[1];
		$email          = $vendor_details[2];
		$pref           = $vendor_details[3];

		// email message
		$email_subject = 'Confirmation of Appointment - Wooster %26 Stock';
		$email_message = "Dear $name,";
		// client_edit.php?cli_id='.$id.'&searchLink='.$_SERVER['SCRIPT_NAME'].'?'.urlencode($_SERVER['QUERY_STRING']).'
		$vendor_name .= '<p><a href="javascript:showHideDiv(\'vendor_' . $id . '\');">' . $name . '</a></p>';
		$vendor_name .= '<div id="vendor_' . $id . '" style="margin-bottom:10px;padding-left:7px;">';
		if ($pref == "Email" && $email) {
			$vendor_name .= '<p><a href="mailto:' . $email . '?subject=' . $email_subject . '&amp;body=' . $email_message . '">' . $email . '</a></p>';
		}

		$tels = explode("~", $row["ven_tel"]);
		foreach ($tels as $tel) {
			$telephone_numbers = explode("|", $tel);
			if ($telephone_numbers[2] == $id) {
				$tel_render .= '<p>' . $telephone_numbers[0] . ' ' . $telephone_numbers[1] . '</p>';
			}
		}

		$vendor_name .= $tel_render;
		unset($tel_render);

		if ($pref <> "Email" && $email) {
			$vendor_name .= '<p><a href="mailto:' . $email . '?subject=' . $email_subject . '&amp;body=' . $email_message . '">' . $email . '</a></p>';
		}
		$vendor_name .= '</div>';
	}

	unset($tel_render);

	if (trim($row["tenant_name"])) {
		$tenants = explode("~", $row["tenant_name"]);
		foreach ($tenants as $vendor) {
			$vendor_details = explode("|", $vendor);
			$id             = $vendor_details[0];
			$name           = $vendor_details[1];
			$email          = $vendor_details[2];
			$pref           = $vendor_details[3];

			// email message
			$email_subject = 'Confirmation of Appointment - Wooster %26 Stock';
			$email_message = "Dear $name,";
			$href          = 'client_edit.php?cli_id=' . $id . '&searchLink=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '';
			$tenant_name .= '<p><a href="' . $href . '">' . $name . '</a></p>';
			$tenant_name .= '<div id="tenant_' . $id . '" style="margin-bottom:10px;padding-left:7px;">';

			if ($pref == "Email" && $email) {
				$tenant_name .= '<p><a href="mailto:' . $email . '?subject=' . $email_subject . '&amp;body=' . $email_message . '">' . $email . '</a></p>';
			}

			$tenant_tels = explode("~", $row["ten_tel"]);
			$used        = array();
			foreach ($tenant_tels as $tel) {
				$telephone_numbers = explode("|", $tel);
				if ($telephone_numbers[2] == $id && !in_array($telephone_numbers[2], $used)) {
					$tel_render .= '<p>' . $telephone_numbers[0] . ' ' . $telephone_numbers[1] . '</p>';
					$used[] = $telephone_numbers[2]; // bit of a cheeky fix for duplicate numbers coming up
				}
			}

			$tenant_name .= $tel_render;
			unset($tel_render);

			if ($pref <> "Email" && $email) {
				$tenant_name .= '<p><a href="mailto:' . $email . '?subject=' . $email_subject . '&amp;body=' . $email_message . '">' . $email . '</a></p>';
			}
			$tenant_name .= '</div>';
		}
	}
}

if ($dea_type == 'Sales') {
	$owner = 'Vendor';
} elseif ($dea_type == 'Lettings') {
	$owner = 'Landlord';
}

$render = '<table cellpadding="2" cellspacing="2" border="0">
  <tr>
	<td class="label" valign="top">Property</td>
	<td><a href="/admin4/instruction/summary/id/' . $dea_id . '">' . $pro_addr . '</a></td>
  </tr>
  <tr>
	<td class="label" valign="top">' . $owner . '(s)</td>
	<td class="spaced">' . remove_lastchar($vendor_name, '<br />') . '</td>
  </tr>';
if ($tenant_name) {
	$render .= '
  <tr>
	<td class="label" valign="top">Tenant(s)</td>
	<td class="spaced">' . remove_lastchar($tenant_name, '<br />') . '</td>
  </tr>';
}
$render .= '
  <tr>
	<td class="label" valign="top">Key</td>
	<td>' . $key_info . '</td>
  </tr>
</table>
' . renderNotes('viewing_arrangements', $dea_id, array('layout' => 'readonly', 'label' => 'View Times and Info'));

$formData1 = array(
	'd2a_cv' => array(
		'type'    => 'radio',
		'label'   => 'Confirm Status',
		'value'   => $d2a_cv,
		'options' => db_enum("link_deal_to_appointment", "d2a_cv", "array")
	),
	'notes'  => array(
		'type'       => 'textarea',
		'label'      => 'Add Confirmation Note',
		'attributes' => array('class' => 'noteInput'),
		'tooltip'    => 'Notes relating to the confirmation of this property only'
	)
);

if (!$_GET["action"]) {

	$form = new Form();

	$form->addForm("app_form", "GET", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "app_id", "", $app_id);
	$form->addField("hidden", "d2a_id", "", $d2a_id);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));

	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Confirm ' . $app_type . '</div>');
	$form->addHtml($render);

	$form->addData($formData1, $_GET);
	$form->addHtml(renderNotes('confirm', $d2a_id, array('label' => 'Confirmation Notes')));
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'))));
	$form->addHtml("</fieldset>\n");
	$form->addHtml('</div>');

	$navbar_array = array(
		'back'   => array('title' => 'Back', 'label' => 'Back', 'link' => $_GET["searchLink"]),
		'search' => array('title' => 'Appointment Search', 'label' => 'Appointment Search', 'link' => 'appointment_search.php')
	);
	$navbar       = navbar2($navbar_array);

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Confirm Appointment");
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
	$return = $_SERVER['SCRIPT_NAME'] . '?stage=valuation_address&';
	if ($cli_id) {
		$results['Results']['cli_id'] = $cli_id;
	}
	if (is_array($results['Results'])) {
		$return .= http_build_query($results['Results']);
	}
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
			'not_type'  => 'confirm',
			'not_user'  => $_SESSION["auth"]["use_id"],
			'not_date'  => $date_mysql
		);
		db_query($db_data2, "INSERT", "note", "not_id");
	}
	unset($db_data["notes"]);
	db_query($db_data, "UPDATE", "link_deal_to_appointment", "d2a_id", $d2a_id);
	if ($_GET["searchLink"]) {
		header("Location:" . urldecode($_GET["searchLink"]));
	} else {
		header("Location:appointment_edit.php?app_id=" . $_GET["app_id"] . '&searchLink=' . $_GET["searchLink"]);
	}

	exit;
}
