<?php

// 19/06/06 updated page to use POST instead of GET due to huge number of checkboxes preventing form submission
// 22/09/06 started working on moving client addresses to property table

/* 25/09/06
ADDRESSES
now i am going to store multiple addresses for each client. addresses re stored in the property table, a
reference joining property to clients will be in the pro2cli (link) table, and client will have default address
specified in client table.
select client info, join pro2cli on client.cli_id, and on property.pro_id

TELEPHONE
remove telephones from client table, and enter into new table which is linked to the client table
tel2cli - > client
*/

require_once("inx/global.inc.php");

// get existing values
if ($_GET["cli_id"]) {
	$cli_id = $_GET["cli_id"];
} elseif ($_POST["cli_id"]) {
	$cli_id = $_POST["cli_id"];
} else {
	exit;
}

// block changes to id=1
if ($cli_id == 1) {
	$errors[] = "You cannot edit the TEMPORARY VENDOR record";
	echo error_message($errors);
	exit;
}
if ($cli_id == 2) {
	$errors[] = "You cannot edit the TEMPORARY LANDLORD record";
	echo error_message($errors);
	exit;
}

if ($_GET["searchLink"]) {
	$searchLink = $_GET["searchLink"];
} elseif ($_POST["searchLink"]) {
	$searchLink = $_POST["searchLink"];
}

if ($_POST["viewForm"]) {
	$viewForm = $_POST["viewForm"];
} elseif ($_GET["viewForm"]) {
	$viewForm = $_GET["viewForm"];
} else {
	$viewForm = 1;
}

#register and unregsiter client for sale and/or lettings
if ($_GET["do"] == "register") {
	if ($_GET["scope"] == "sales") {
		$db_data["cli_sales"] = "Yes";
	} elseif ($_GET["scope"] == "lettings") {
		$db_data["cli_lettings"] = "Yes";
	}
	db_query($db_data, "UPDATE", "client", "cli_id", $cli_id);
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
} elseif ($_GET["do"] == "deregister") {
	if ($_GET["scope"] == "sales") {
		$db_data["cli_sales"] = "No";
	} elseif ($_GET["scope"] == "lettings") {
		$db_data["cli_lettings"] = "No";
	}
	db_query($db_data, "UPDATE", "client", "cli_id", $cli_id);
	$_GET["return"] = replaceQueryString($_GET["return"], 'cli_lettings');
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
} elseif ($_GET["do"] == "addr_default") {
	$db_data["cli_pro"] = $_GET["pro_id"];
	db_query($db_data, "UPDATE", "client", "cli_id", $cli_id);
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
} elseif ($_GET["do"] == "addr_delete") {
	$sql = "DELETE FROM pro2cli WHERE p2c_cli = '" . $cli_id . "' AND p2c_pro = '" . $_GET["pro_id"] . "' LIMIT 1";
	$q   = $db->query($sql);
	unset($sql);
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
} // deleting last address, for superadmin only to get rid of false addresses
elseif ($_GET["do"] == "addr_delete_default") {

	$sql = "UPDATE client SET cli_pro = '0' WHERE cli_id = '" . $cli_id . "'";
	$q   = $db->query($sql);
	unset($sql, $q);
	$sql = "DELETE FROM pro2cli WHERE p2c_cli = '" . $cli_id . "' AND p2c_pro = '" . $_GET["pro_id"] . "' LIMIT 1";
	$q   = $db->query($sql);
	unset($sql);
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
} // adding a feature. must check if feature is already added and update as necesary
elseif ($_GET["do"] == "feature_add") {
	$cli_id    = $_GET["cli_id"];
	$status    = $_GET["status"];
	$featureId = $_GET["featureId"];
	$sql       = "DELETE FROM link_client_to_feature WHERE clientId = $cli_id AND featureId = $featureId";
	$q         = $db->query($sql);

	$db_data["clientId"]  = $cli_id;
	$db_data["status"]    = $status;
	$db_data["featureId"] = $featureId;

	db_query($db_data, "INSERT", "link_client_to_feature", "id");
	$_GET["return"] = replaceQueryString($_GET["return"], "viewForm");
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
	exit;
} // adding a feature. must check if feature is already added and update as necesary
elseif ($_GET["do"] == "feature_remove") {
	$cli_id    = $_GET["cli_id"];
	$status    = $_GET["status"];
	$featureId = $_GET["featureId"];
	$sql       = "DELETE FROM link_client_to_feature WHERE clientId = $cli_id AND featureId = $featureId";
	$q         = $db->query($sql);
	/*
	$db_data["clientId"] = $cli_id;
	$db_data["status"] = $status;
	$db_data["featureId"] = $featureId;
	db_query($db_data,"INSERT","link_client_to_feature","id");
	*/
	header("Location:" . $_GET["return"] . "&viewForm=" . str_replace('form', '', $_GET["viewForm"]));
	exit;
}

$properties       = array();
$default_property = array();

// get property types into array for comparison
$sql = "SELECT * FROM ptype";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
while ($row = $q->fetchRow()) {

	if ($row["pty_type"] == 1) {
		$proptype1[] = $row["pty_id"];
	} elseif ($row["pty_type"] == 2) {
		$proptype2[] = $row["pty_id"];
	} elseif ($row["pty_type"] == 3) {
		$proptype3[] = $row["pty_id"];
	}
}

$sql = "SELECT
client.*,DATE_FORMAT(cli_created, '%D %M %Y') AS cli_created,
pro_id,pro_addr1,pro_addr2,pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_pcid,
p2c_id, p2c_type, p2c_pro,
GROUP_CONCAT(DISTINCT CONCAT(tel_id,'~',tel_number,'~',tel_type,'~',tel_ord) ORDER BY tel_ord ASC SEPARATOR '|') AS tel,
GROUP_CONCAT(DISTINCT CONCAT(feature.fea_id,'~',feature.fea_title,'~',status) ORDER BY fea_title ASC SEPARATOR '|') AS features,
source.sou_title,
CONCAT(con_fname,' ',con_sname) AS con_name,com_title,
COUNT(app_id) AS app_count
FROM client
LEFT JOIN pro2cli ON pro2cli.p2c_cli = client.cli_id
LEFT JOIN property ON pro2cli.p2c_pro = property.pro_id
LEFT JOIN tel ON client.cli_id = tel.tel_cli
LEFT JOIN link_client_to_feature ON client.cli_id = link_client_to_feature.clientId
LEFT JOIN feature ON link_client_to_feature.featureId = feature.fea_id
LEFT JOIN source ON client.cli_source = source.sou_id
LEFT JOIN cli2app ON client.cli_id = cli2app.c2a_cli
LEFT JOIN appointment ON cli2app.c2a_app = appointment.app_id AND appointment.app_type = 'Viewing' AND app_status = 'Active'
LEFT JOIN contact ON client.cli_solicitor = contact.con_id
LEFT JOIN company ON contact.con_company = company.com_id
WHERE cli_id = '" . $cli_id . "'
GROUP BY pro_id";

$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();

if ($numRows == 0) {
	header("Location:client_lookup.php?msg=No+matches+found.+Please+try+again");
	exit;
} else {
	while ($row = $q->fetchRow()) {

		foreach ($row as $key => $val) {
			$$key = $val;
		}
		if ($com_title) {
			$con_name .= ' - ' . $com_title;
		}
		if (!$cli_method) {
			$cli_method = '(unknown)';
		}
		if ($cli_source == 0) {
			$sou_title = '(unknown)';
		}
		$referer = $sou_title;

		$cli_saleptype_array = explode("|", $cli_saleptype);
		if (array_intersect($cli_saleptype_array, $proptype1)) {
			$saleptype_array[] = 'House';
		}
		if (array_intersect($cli_saleptype_array, $proptype2)) {
			$saleptype_array[] = 'Apartment';
		}
		if (array_intersect($cli_saleptype_array, $proptype3)) {
			$saleptype_array[] = 'Other';
		}
		if (is_array($saleptype_array)) {
			foreach ($saleptype_array as $val) {
				$saleptype .= $val . ' or ';
			}
			$saleptype = remove_lastchar(trim($saleptype), 'or');
		}

		$cli_letptype_array = explode("|", $cli_letptype);
		if (array_intersect($cli_letptype_array, $proptype1)) {
			$letptype_array[] = 'House';
		}
		if (array_intersect($cli_letptype_array, $proptype2)) {
			$letptype_array[] = 'Apartment';
		}
		if (array_intersect($cli_letptype_array, $proptype3)) {
			$letptype_array[] = 'Other';
		}
		if (is_array($letptype_array)) {
			foreach ($letptype_array as $val) {
				$letptype .= $val . ' or ';
			}
		}
		$letptype = remove_lastchar(trim($letptype), 'or');

		// put all associated properties into an array, with default at the top
		if ($row["pro_id"] == $row["cli_pro"]) {

			$default_property = array(
				'pro_addr1'    => $row["pro_addr1"],
				'pro_addr2'    => $row["pro_addr2"],
				'pro_addr3'    => $row["pro_addr3"],
				'pro_addr4'    => $row["pro_addr4"],
				'pro_addr5'    => $row["pro_addr5"],
				'pro_postcode' => $row["pro_postcode"],
				'pro_pcid'     => $row["pro_pcid"],
				'p2c_type'     => $row["p2c_type"],
				'p2c_id'       => $row["p2c_id"],
				'p2c_pro'      => $row["p2c_pro"]
			);

		} else {

			$properties[$row["p2c_id"]] = array(
				'pro_addr1'    => $row["pro_addr1"],
				'pro_addr2'    => $row["pro_addr2"],
				'pro_addr3'    => $row["pro_addr3"],
				'pro_addr4'    => $row["pro_addr4"],
				'pro_addr5'    => $row["pro_addr5"],
				'pro_postcode' => $row["pro_postcode"],
				'pro_pcid'     => $row["pro_pcid"],
				'p2c_type'     => $row["p2c_type"],
				'p2c_id'       => $row["p2c_id"],
				'p2c_pro'      => $row["p2c_pro"]
			);
		}
	}
}

// put the default address (as defined in the cli_pro row) on top of the array of properties
array_unshift($properties, $default_property);

// get the tels into an array ready for the form
if ($tel) {
	$tel_numbers = explode("|", $tel);
	foreach ($tel_numbers as $tels) {
		$tel_detail  = explode("~", $tels);
		$telephone[] = array(
			'id'     => $tel_detail[0],
			'number' => $tel_detail[1],
			'type'   => $tel_detail[2],
			'order'  => $tel_detail[3]
		);
	}
}

// make properties table

foreach ($properties AS $property_id => $property_addr) {
	if ($property_addr["p2c_pro"]) {
		// the default property
		if ($cli_pro == $property_addr["p2c_pro"]) {
			$render_addresses .= '<tr>
		<td><strong>' . $property_addr["pro_addr1"] . ' ' . $property_addr["pro_addr2"] . ' ' . $property_addr["pro_addr3"] . ' ' . $property_addr["pro_addr4"] . ' ' . $property_addr["pro_addr5"] . ' ' . $property_addr["pro_postcode"] . '</strong> (' . $property_addr["p2c_type"] . ')</td>
		<td colspan="2" width="32">(default)';
			if (in_array('SuperAdmin', $_SESSION["auth"]["roles"])) {
				$render_addresses .= '<a href="javascript:confirmDelete(\'Are you sure you want to delete this address?\',\'?do=addr_delete_default&cli_id=' . $cli_id . '&pro_id=' . $property_addr["p2c_pro"] . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=2\');"><img src="/images/sys/admin/icons/cross-icon.png" width="16" height="16" border="0" alt="Delete" /></a>';
			}
			$render_addresses .= '</td>
		</tr>';
		} else {
			$render_addresses .= '<tr>
		<td>' . $property_addr["pro_addr1"] . ' ' . $property_addr["pro_addr2"] . ' ' . $property_addr["pro_addr3"] . ' ' . $property_addr["pro_addr4"] . ' ' . $property_addr["pro_addr5"] . ' ' . $property_addr["pro_postcode"] . ' (' . $property_addr["p2c_type"] . ')</td>
		<td width="16"><a href="?do=addr_default&cli_id=' . $cli_id . '&pro_id=' . $property_addr["p2c_pro"] . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=2"><img src="/images/sys/admin/icons/tick.gif" width="16" height="16" border="0" alt="Make default" /></a></td>
		<td width="16"><a href="javascript:confirmDelete(\'Are you sure you want to delete this address?\',\'?do=addr_delete&cli_id=' . $cli_id . '&pro_id=' . $property_addr["p2c_pro"] . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=2\');"><img src="/images/sys/admin/icons/cross-icon.png" width="16" height="16" border="0" alt="Delete" /></a></td>
		</tr>';
		}
	}
}
if ($render_addresses) {
	$render_addresses = '<table width="95%" cellpadding="3" cellspacing="2" align="center">' . $render_addresses . '<tr><td colspan="3"><hr></td></tr></table>';
}

// make features table
if ($features) {
	// pipe seperated
	$features = explode("|", $features);
	foreach ($features as $feature) {

		// id~title~status
		$feat = explode("~", $feature);

		if ($feat[2] == "Would like") {
			$render_would_like .= '<a href="?do=feature_remove&cli_id=' . $cli_id . '&featureId=' . $feat[0] . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=7">' . $feat[1] . '</a><br />';
		} elseif ($feat[2] == "Must have") {
			$render_must_have .= '<a href="?do=feature_remove&cli_id=' . $cli_id . '&featureId=' . $feat[0] . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=7">' . $feat[1] . '</a><br />';
		} elseif ($feat[2] == "Must not have") {
			$render_must_not_have .= '<a href="?do=feature_remove&cli_id=' . $cli_id . '&featureId=' . $feat[0] . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=7">' . $feat[1] . '</a><br />';
		}
	}

	$fea_table = '
	<table id="detailTable" width="97%" cellpadding="2" cellspacing="2" align="center">
	<tr>
	<td colspan="3">These selections will directly affect the email alerts received by this client (click to delete)</td>
	</tr>
	<tr>
	<th width="33%">Would like</th>
	<th width="33%">Must have</th>
	<th width="33%">Must not have</th>
	</tr>
	<tr>
	<td valign="top">' . $render_would_like . '</td>
	<td valign="top">' . $render_must_have . '</td>
	<td valign="top">' . $render_must_not_have . '</td>
	</tr>
	</table>
	';

}

// get deals (using linked link_client_to_instruction table)
$sql = "SELECT
dea_id,dea_status,dea_type,DATE_FORMAT(dea_created, '%d/%m/%y') AS date,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',LEFT(pro_postcode,4)) AS pro_addr

FROM deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id AND link_client_to_instruction.capacity = 'Owner'
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
WHERE
link_client_to_instruction.clientId = $cli_id AND deal.dea_status != 'Archived'
ORDER BY dea_created DESC
";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows  = $q->numRows();
$numDeals = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$deal_table .= '<tr>
		<td><a href="deal_summary.php?dea_id=' . $row["dea_id"] . '&searchLink=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '&viewForm=3') . '">' . $row["pro_addr"] . '</a></td>
		<td>' . $row["dea_status"] . '</td>
		<td>' . $row["dea_type"] . '</td>
		<td>' . $row["date"] . '</td>
		</tr>';
	}
	$render_deal = '<table width="95%" cellpadding="3" cellspacing="2" align="center">' . $deal_table . '</table>';
}

// get all appointments and build table
$countViewing    = 0;
$countValuation  = 0;
$countProduction = 0;
$countInspection = 0;
$sql             = "SELECT
app_id,app_type,app_start,app_end,app_status,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name,CONCAT(LEFT(user.use_fname,1),LEFT(user.use_sname,1)) AS use_initial,use_colour,
cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ', ') AS cli_name,
GROUP_CONCAT(DISTINCT CONCAT(cli_id) ORDER BY client.cli_id ASC SEPARATOR '|') AS cli,
DATE_FORMAT(appointment.app_start, '%d/%m/%y') AS app_date,
d2a_id,d2a_feedback,
CONCAT(property.pro_addr1,' ',property.pro_addr2,' ',property.pro_addr3,' ',LEFT(property.pro_postcode,4)) AS pro_addr
FROM link_deal_to_appointment
LEFT JOIN appointment ON link_deal_to_appointment.d2a_app = appointment.app_id
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
WHERE
client.cli_id = $cli_id AND appointment.app_status != 'Deleted'
GROUP BY d2a_id
ORDER BY app_start DESC";
$q               = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows) {

	while ($row = $q->fetchRow()) {

		if ($row["app_type"] == 'Viewing') {

			// count the feedbacks to supply percentage stats
			if ($row["d2a_feedback"] == "Positive") {
				$countPositive++;
			} elseif ($row["d2a_feedback"] == "Indifferent") {
				$countIndifferent++;
			} elseif ($row["d2a_feedback"] == "Negative") {
				$countNegative++;
			} else {
				$countEmpty++;
			}

			// only show feedback for appointments in the past
			if (strtotime($row["app_end"]) < strtotime($date_mysql)) {
				if (!$row["d2a_feedback"]) {
					$feedback = '<a href="appointment_feedback.php?d2a_id=' . $row["d2a_id"] . '&cli_id=' . $row["cli"] . '&dea_id=' . $dea_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=3.1') . '">(not entered)</a>';
				} else {
					$feedback = '<a href="appointment_feedback.php?d2a_id=' . $row["d2a_id"] . '&cli_id=' . $row["cli"] . '&dea_id=' . $dea_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=3.1') . '">' . $row["d2a_feedback"] . '</a>';
				}
			} else {
				$feedback = '(in future)';
			}

			// cancelled overwrites above feedback text
			if ($row["app_status"] == 'Cancelled') {
				$feedback = '(cancelled)';
			}

			if ($row["use_colour"]) {
				$use_colour = '<span class="use_col" style="background-color: #' . $row["use_colour"] . ';"><img src="/images/sys/admin/blank.gif" width="10" height="10" alt="' . $row["use_name"] . '"></span>&nbsp;';
			}
			$use_name = $use_colour . $row["use_initial"];

			$viewings_table .= '
  <tr>
	<td width="13%" valign="top">' . $row["app_date"] . '</td>
	<td width="10%" valign="top">' . $use_name . '</td>
	<td width="57%" valign="top">' . $row["pro_addr"] . '</td>
	<td width="15%" valign="top">' . $feedback . '</td>
	<td width="5%" align="right" valign="top">
	  <a href="appointment_edit.php?app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=3.1') . '"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View/Edit Appointment"/></a>
	  </td>
  </tr>';
			$countViewing++;
		}

	}
}

if ($countViewing) {
	$centPositive    = @round(($countPositive / $countViewing) * 100);
	$centIndifferent = @round(($countIndifferent / $countViewing) * 100);
	$centNegative    = @round(($countNegative / $countViewing) * 100);
	$centEmpty       = @round(($countEmpty / $countViewing) * 100);
	$viewingStats    = '&nbsp; <span class="small">(Total: ' . $countViewing . ' | Postitive: ' . $centPositive . '% | Indifferent: ' . $centIndifferent . '% | Negative: ' . $centNegative . '% | Empty: ' . $centEmpty . '%)</span>';
}

$appointments_table = '
<table id="detailTable" width="97%" cellpadding="2" cellspacing="2" align="center">
  <tr>
    <td colspan="4"><strong>Viewings</strong> ' . $viewingStats . '</td>
	<td align="right"><a href="viewing_add.php?cli_id=' . $cli_id . '">[ New ]</a></td>
  </tr>
</table>
<div style="width:97%;height:210px;overflow:auto;margin-left:10px">
<table id="detailTable" width="97%" cellpadding="2" cellspacing="2" align="center">
  <tr>
    <th>Date</th>
    <th>Neg</th>
    <th>Property</th>
    <th colspan="2">Feedback</th>
  </tr>
' . $viewings_table . '
</table>';

#overwrite database values with POST values (probably empty)
foreach ($_POST AS $key => $val) {
	$$key = $val;
}
#overwrite database values with GET values when returning from error message
foreach ($_GET AS $key => $val) {
	$$key = $val;
}

$summary_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label">Database ID number</td>
	<td>' . $cli_id . '</td>
  </tr>
  <tr>
    <td class="label">Registered since</td>
	<td>' . $cli_created . '</td>
  </tr>
  <tr>
    <td class="label">Initial Contact Method</td>
	<td>' . $cli_method . '</td>
  </tr>
</table>
';

$form1 = array(
	'cli_salutation' => array(
		'type'       => 'select',
		'group'      => 'Full Name',
		'label'      => 'Salutation',
		'value'      => $cli_salutation,
		'required'   => 2,
		'options'    => join_arrays(array(array('' => ''), db_enum("client", "cli_salutation", "array"))),
		'attributes' => array('style' => 'width:60px')
	),
	'cli_fname'      => array(
		'type'       => 'text',
		'group'      => 'Full Name',
		'label'      => 'Forename',
		'value'      => $cli_fname,
		'init'       => 'Forename(s)',
		'required'   => 2,
		'attributes' => array('style' => 'width:100px', 'onFocus' => 'javascript:clearField(this,\'Forename(s)\')'),
		'function'   => 'format_name'
	),
	'cli_sname'      => array(
		'type'          => 'text',
		'group'         => 'Full Name',
		'last_in_group' => 1,
		'label'         => 'Surname',
		'value'         => $cli_sname,
		'init'          => 'Surname',
		'required'      => 2,
		'attributes'    => array('style' => 'width:152px', 'onFocus' => 'javascript:clearField(this,\'Surname\')'),
		'function'      => 'format_name'
	),
	'cli_tel'        => array(
		'type'  => 'tel',
		'label' => 'Telephone',
		'value' => $telephone
	),
	'cli_email'      => array(
		'type'       => 'text',
		'label'      => 'Email',
		'value'      => $cli_email,
		'required'   => 3,
		'attributes' => array('style' => 'width:320px', 'maxlength' => 255),
		'tooltip'    => 'Must be a valid email address'
	),
	'cli_web'        => array(
		'type'       => 'text',
		'label'      => 'Website',
		'value'      => $cli_web,
		'init'       => 'http://',
		'required'   => 1,
		'attributes' => array('style' => 'width:320px', 'maxlength' => 255)
	),
	'cli_preferred'  => array(
		'type'     => 'radio',
		'label'    => 'Preferred Contact',
		'value'    => $cli_preferred,
		'required' => 2,
		'options'  => db_enum("client", "cli_preferred", "array"),
		'tooltip'  => 'How the client would prefer to be contacted, not including property alerts'
	),
	'cli_notes'      => array(
		'type'       => 'textarea',
		'label'      => 'Add General Note',
		'attributes' => array('class' => 'noteInput')
	)
);

// address, this is only used for manual input resulting from ajax input (validation only)
$form2 = array(
	'pro_addr1'    => array(
		'type'       => 'text',
		'label'      => 'House Number',
		'value'      => $pro_addr1,
		'required'   => 2,
		'attributes' => array('class' => 'addr'),
		'function'   => 'format_street'
	),
	'pro_addr2'    => array(
		'type'       => 'text',
		'label'      => 'Building Name',
		'value'      => $pro_addr2,
		'required'   => 1,
		'attributes' => array('class' => 'addr'),
		'function'   => 'format_street'
	),
	'pro_addr3'    => array(
		'type'       => 'text',
		'label'      => 'Street',
		'value'      => $pro_addr3,
		'required'   => 2,
		'attributes' => array('class' => 'addr'),
		'function'   => 'format_street'
	),
	'pro_addr4'    => array(
		'type'       => 'text',
		'label'      => 'Town or Area',
		'value'      => $pro_addr4,
		'required'   => 3,
		'attributes' => array('class' => 'addr'),
		'function'   => 'format_street'
	),
	'pro_addr5'    => array(
		'type'       => 'text',
		'label'      => 'City or County',
		'value'      => $pro_addr5,
		'required'   => 2,
		'attributes' => array('class' => 'addr'),
		'function'   => 'format_street'
	),
	'pro_postcode' => array(
		'type'       => 'text',
		'label'      => 'Postcode',
		'value'      => $pro_postcode,
		'required'   => 2,
		'attributes' => array('class' => 'pc', 'maxlength' => 9),
		'function'   => 'format_postcode'
	)
);

// form 3, deals
$form3 = array();

$form4      = array(
	'cli_salemin'   => array(
		'type'       => 'select_price',
		'value'      => $cli_salemin,
		'label'      => 'Minimum Price',
		'group'      => 'Price Range',
		'required'   => 2,
		'options'    => array('scope' => 'sales', 'default' => 'Minimum'),
		'attributes' => array('style' => 'width:120px')
	),
	'cli_salemax'   => array(
		'type'          => 'select_price',
		'value'         => $cli_salemax,
		'label'         => 'Maximum Price',
		'group'         => 'Price Range',
		'last_in_group' => 1,
		'required'      => 2,
		'options'       => array('scope' => 'sales', 'default' => 'Maximum'),
		'attributes'    => array('style' => 'width:120px')
	),
	'cli_salebed'   => array(
		'type'     => 'select_number',
		'value'    => $cli_salebed,
		'label'    => 'Minimum Beds',
		'required' => 2
	),
	'cli_saleemail' => array(
		'type'     => 'radio',
		'value'    => $cli_saleemail,
		'label'    => 'Email Updates',
		'required' => 2,
		'options'  => db_enum("client", "cli_saleemail", "array")
	)
	/*,
		'would_like'=>array(
			'type'=>'select',
			'value'=>$cli_saleemail,
			'label'=>'Would like',
			'required'=>2,
			'options'=>$fea_would_like
			),
		'must_have'=>array(
			'type'=>'select',
			'value'=>$cli_saleemail,
			'label'=>'Must have',
			'required'=>2,
			'options'=>$fea_must_have
			),
		'must_not_have'=>array(
			'type'=>'select',
			'value'=>$cli_saleemail,
			'label'=>'Must not have',
			'required'=>2,
			'options'=>$fea_must_not_have
			)*/
);
$ptype_sale = ptype("sale", explode("|", $cli_saleptype));

$form5     = array(
	'cli_letmin'   => array(
		'type'       => 'select_price',
		'value'      => $cli_letmin,
		'label'      => 'Minimum Price',
		'group'      => 'Price Range',
		'required'   => 2,
		'options'    => array('scope' => 'lettings', 'default' => 'Minimum'),
		'attributes' => array('style' => 'width:120px')
	),
	'cli_letmax'   => array(
		'type'          => 'select_price',
		'value'         => $cli_letmax,
		'label'         => 'Maximum Price',
		'group'         => 'Price Range',
		'last_in_group' => 1,
		'required'      => 2,
		'options'       => array('scope' => 'lettings', 'default' => 'Maximum'),
		'attributes'    => array('style' => 'width:120px')
	),
	'cli_letbed'   => array(
		'type'     => 'select_number',
		'value'    => $cli_letbed,
		'label'    => 'Minimum Beds',
		'required' => 2
	),
	'cli_letemail' => array(
		'type'     => 'radio',
		'value'    => $cli_letemail,
		'label'    => 'Email Updates',
		'required' => 2,
		'options'  => db_enum("client", "cli_letemail", "array")
	)
);
$ptype_let = ptype("let", explode("|", $cli_letptype));

$form7 = array(
	'cli_req' => array(
		'type'       => 'textarea',
		'label'      => 'Add Requirement',
		'attributes' => array('class' => 'noteInput', 'viewForm' => 7)
	)
);

$source = source($cli_source, $_SERVER['QUERY_STRING'], 'readonly');
if (!$cli_salestatus) {
	$salestatus_array[] = '';
}
if (!$cli_letstatus) {
	$letstatus_array[] = '';
}
$sql = "SELECT * FROM cstatus";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	if ($row["cst_scope"] == 'Sales') {
		$salestatus_array[$row["cst_id"]] = $row["cst_title"];
	} elseif ($row["cst_scope"] == 'Lettings') {
		$letstatus_array[$row["cst_id"]] = $row["cst_title"];
	}
}

$form8 = array(
	'cli_source'       => array(
		'type'    => 'select_multi',
		'label'   => 'Referer',
		'value'   => $cli_source,
		'options' => $source
	),
	'cli_neg'          => array(
		'type'       => 'select_neg',
		'label'      => 'Assigned Negotiator',
		'value'      => $cli_neg,
		'attributes' => array('class' => 'wide'),
		'options'    => array('' => '(unassigned)')
	),
	'cli_salestatus'   => array(
		'type'       => 'select',
		'label'      => 'Current Status (Sales)',
		'value'      => $cli_salestatus,
		'options'    => $salestatus_array,
		'attributes' => array('class' => 'wide')
	),
	'cli_letstatus'    => array(
		'type'       => 'select',
		'label'      => 'Current Status (Lettings)',
		'value'      => $cli_letstatus,
		'options'    => $letstatus_array,
		'attributes' => array('class' => 'wide')
	),
	'cli_solicitor'    => array(
		'type'       => 'text',
		'label'      => 'Solicitor',
		'value'      => $con_name,
		'attributes' => array('class' => 'wide')
	),
	'cli_solicitor_id' => array(
		'type'  => 'hidden',
		'value' => $cli_solicitor_id
	)
);

// form is not submitted, show the form
if (!$_POST["action"]) {

// start new form object
	$form = new Form();

	$form->addForm("testForm", "post", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "cli_id", "", $cli_id);
	$form->addField("hidden", "searchLink", "", $searchLink);
//$form->addHtml('<input type="hidden" name="action" value="update">');

	$form->addHtml('<h1>' . $cli_fname . ' ' . $cli_sname . '</h1>');

/////////////////////////////////////////////////////////////////////////////////

	$formName = 'form1';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Contact</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addData($$formName, $_POST);
	$form->addHtml(renderNotes('client_general', $cli_id, array('viewform' => '1', 'label' => 'General Notes')));
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'))));
	$form->addHtml("</div>\n");
	$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////

	$formName = 'form2';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Address</div>');
	$form->addHtml('<div id="' . $formName . '">');

// show old address from import
	if ($cli_oldaddr && !$render_addresses) {
		$form->addHtml('<p class="appInfo">Please re-enter this address into the form below</p>');
		$form->addHtml($form->addRow('textarea', 'cli_oldaddr', 'Old Address', $cli_oldaddr, array('style' => 'width:400px', 'readonly' => 'readonly'), '', ''));

	}
// add address table
	$form->addHtml($render_addresses);

// add new address
	$form->addRow('radio', 'p2c_type', 'Type', 'Home', '', db_enum("pro2cli", "p2c_type", "array"));
	$form->ajaxPostcode("by_freetext", "pro");

	$form->addHtml("</div>\n");
	$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////
	if ($deal_table) {
		$formName = 'form3';
		$form->addHtml("<fieldset>\n");
//		$form->addLegend('Deals (' . $numDeals . ')', array('style' => 'cursor:pointer', 'onClick' => 'javascript:showHide(\'' . $formName . '\');'));
		$form->addHtml('<div class="block-header">Deals (' . $numDeals . ')</div>');
		$form->addHtml('<div id="' . $formName . '">');
		$form->addHtml($render_deal);
		$form->addHtml($form->addDiv($form->addField("button", $formName, "", "Create New Deal", array(
																									  'class'   => 'submit',
																									  'onClick' => 'javascript:location.href=\'valuation_add.php?stage=valuation_address&cli_id=' . $cli_id . '&searchLink=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '\';'
																								 ))));
		$form->addHtml("</div>\n");
		$form->addHtml("</fieldset>\n");
	}
/////////////////////////////////////////////////////////////////////////////////

	if ($countViewing > 0) {
		$formName = 'form3.1';
		$form->addHtml("<fieldset>\n");
//		$form->addLegend('Viewings (' . $countViewing . ')', array('style' => 'cursor:pointer', 'onClick' => 'javascript:showHide(\'' . $formName . '\');'));
		$form->addHtml('<div class="block-header">Viewings (' . $countViewing . ')</div>');
		$form->addHtml('<div id="' . $formName . '">');
		$form->addHtml($appointments_table);
		$form->addHtml('</div>');
		$form->addHtml("</fieldset>\n");
	}

	$formName = 'form4';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Sales Requirements</div>');
	$form->addHtml('<div id="' . $formName . '">');

	if ($cli_sales !== 'Yes') {
		$sale_display = 'none';
	}

	$form->addHtml('<div id="sale" style="display:' . $sale_display . '">');
	$form->addHtml($form->addLabel('cli_saleptype', 'Houses', $ptype_sale['house'], 'javascript:checkAll(document.forms[0], \'sale1\');'));
	$form->addHtml($form->addLabel('cli_saleptype', 'Apartments', $ptype_sale['apartment'], 'javascript:checkAll(document.forms[0], \'sale2\');'));
	$form->addHtml($form->addLabel('cli_saleptype', 'Others', $ptype_sale['other'], 'javascript:checkAll(document.forms[0], \'sale3\');'));
	$form->addData($$formName, $_POST);

	$buttons = $form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'));
	$buttons .= $form->makeField("button", $formName, "", "Matching Property", array(
																					'class'   => 'button',
																					'onClick' => 'javascript:location.href=\'property_search.php?action=advanced_search&scope=Sales&cli_id=' . $cli_id . '&returnLink=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING'] . '&viewForm=4') . '\''
																			   )); //type=&price_min='.$cli_salemin.'&price_max='.$cli_salemax.'&bed='.$cli_salebed.'&status[]=Available&status[]=Under Offer
	$buttons .= $form->makeField("button", $formName, "", "Deregister", array(
																			 'class'   => 'button',
																			 'onClick' => 'javascript:location.href=\'?do=deregister&scope=sales&cli_id=' . $cli_id . '&viewForm=' . $formName . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '\';'
																		));
	$form->addHtml($form->addDiv($buttons));

	$form->addField("hidden", "cli_sales", "", 'Yes');
	$form->addHtml('</div>');

	if ($cli_sales !== 'Yes') {
		$form->addHtml('<div id="sale2">');
		$form->addHtml($form->addDiv($form->makeField("button", $formName, "", "Register", array('class' => 'submit', 'onClick' => 'javascript:swapDiv(\'sale\',\'sale2\');'))));
		$form->addHtml('</div>');
	}

	$form->addHtml("</div>\n");
	$form->addHtml("</fieldset>\n");
/////////////////////////////////////////////////////////////////////////////////

	$formName = 'form5';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Lettings Requirements</div>');
	$form->addHtml('<div id="' . $formName . '">');

	if ($cli_lettings !== 'Yes') {
		$let_display = 'none';
	}

	$form->addHtml('<div id="let" style="display:' . $let_display . '">');
	$form->addHtml($form->addLabel('cli_letptype', 'Houses', $ptype_let['house'], 'javascript:checkAll(document.forms[0], \'let1\');'));
	$form->addHtml($form->addLabel('cli_letptype', 'Apartments', $ptype_let['apartment'], 'javascript:checkAll(document.forms[0], \'let2\');'));
	$form->addHtml($form->addLabel('cli_letptype', 'Others', $ptype_let['other'], 'javascript:checkAll(document.forms[0], \'let3\');'));
	$form->addData($$formName, $_POST);
	$buttons = $form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'));
	$buttons .= $form->makeField("button", $formName, "", "Matching Property", array(
																					'class'   => 'button',
																					'onClick' => 'javascript:location.href=\'property_search.php?action=advanced_search&scope=Lettings&cli_id=' . $cli_id . '&returnLink=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING'] . '&viewForm=5') . '\''
																			   ));
	$buttons .= $form->makeField("button", $formName, "", "Deregister", array(
																			 'class'   => 'button',
																			 'onClick' => 'javascript:location.href=\'?do=deregister&scope=lettings&cli_id=' . $cli_id . '&viewForm=' . $formName . '&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '\';'
																		));
	$form->addHtml($form->addDiv($buttons));

	$form->addField("hidden", "cli_lettings", "", 'Yes');
	$form->addHtml('</div>');

	if ($cli_lettings !== 'Yes') {
		$form->addHtml('<div id="let2">');
		$form->addHtml($form->addDiv($form->makeField("button", $formName, "", "Register", array('class' => 'submit', 'onClick' => 'javascript:swapDiv(\'let\',\'let2\');'))));
		$form->addHtml('</div>');
	}

	$form->addHtml("</div>\n");
	$form->addHtml("</fieldset>\n");

/////////////////////////////////////////////////////////////////////////////////
// only show if client is registered for sales or lettings
	if ($cli_sales == "Yes" || $cli_lettings == "Yes") {

		/* hiding for now until perfected
		$formName = 'form7';
		$form->addHtml("<fieldset>\n");
		$form->addHtml('<div class="block-header">Specifics</div>');
		$form->addHtml('<div id="' . $formName . '">');
		$feature_form = $form->makeField('select','status','Status','','',db_enum("link_client_to_feature","status","array"));
		$feature_form .= $form->makeField('select','featureId','Feature','','',db_lookup("feature","feature","array","","fea_title"));
		$feature_form .= $form->makeField("button",$formName,"","Add",array('class'=>'button','onClick'=>'javascript:addFeature();'));

		$form->addHtml($fea_table);
		$form->addHtml($form->addLabel('Add Feature','Add Feature',$feature_form));
		$form->addData($$formName,$_POST);
		$form->addHtml(renderNotes('client_req',$cli_id,array('viewform'=>7,'label'=>'Special Requirements')));
		$form->addHtml($form->makeField("submit",$formName,"","Save Changes",array('class'=>'submit')));
		$form->addHtml("</div>\n");
		$form->addHtml("</fieldset>\n");
		*/

		$areas    = area(explode("|", $cli_area));
		$formName = 'form6';
		$form->addHtml("<fieldset>\n");
		$form->addHtml('<div class="block-header">Areas</div>');
		$form->addHtml('<div id="' . $formName . '" style="display:none" style="margin-left:10px">');
		$form->addHtml('<a href="javascript:checkToggle(document.forms[0], \'branch1\');" style="margin-left:5px;"><strong>Camberwell Branch</strong></a>');
		$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:5px"><tr>' . $areas[1] . '</tr></table>');
		$form->addHtml('<a href="javascript:checkToggle(document.forms[0], \'branch2\');" style="margin-left:5px;"><strong>Sydenham Branch</strong></a>');
		$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:5px"><tr>' . $areas[2] . '</tr></table>');
		$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'))));
		$form->addHtml("</div>\n");
		$form->addHtml("</fieldset>\n");
	}
/////////////////////////////////////////////////////////////////////////////////

	$formName = 'form8';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Other Info</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addHtml($form->addHtml($summary_table));
	$form->addData($$formName, $_POST);
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'))));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$form->addHtml("</div>\n");

	$onLoad .= 'showForm(' . $viewForm . ');self.focus;' . $source['onload'] . ';';

// start a new page
	$page = new HTML_Page2($page_defaults);

	$additional_js = '
if (!previousID) {
	var previousID = "form' . $viewForm . '";
	}


function addFeature() {
	var status = document.testForm.status.options[document.testForm.status.options.selectedIndex].value;
	var featureId = document.testForm.featureId.options[document.testForm.featureId.options.selectedIndex].value;
	document.location.href = \'?do=feature_add&cli_id=' . $cli_id . '&status=\'+status+\'&featureId=\'+featureId+\'&return=' . urlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']) . '&viewForm=7\';
	}




';

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
	</script><div id="notify"><div id="floating_message">' . urldecode($_GET["msg"]) . '</div></div>';
	}

	$navbar_array = array(
		'back'   => array('title' => 'Back', 'label' => 'Back', 'link' => $searchLink),
		'search' => array('title' => 'Client Search', 'label' => 'Client Search', 'link' => 'client_search.php')
	);
	$navbar       = navbar2($navbar_array);

	$page->setTitle("Client > $cli_fname $cli_sname");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addScript('js/scriptaculous/prototype.js');
	$page->addScript('js/scriptaculous/scriptaculous.js');
	$page->addScriptDeclaration($additional_js);
	$page->addScriptDeclaration($source['js']);
	$page->setBodyAttributes(array('onLoad' => $onLoad)); //,'onKeyPress'=>'keyPressShowDiv(event.keyCode)'
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent('</div>');
	$page->addBodyContent('<div id="hint"></div><script type="text/javascript">
new Ajax.Autocompleter("cli_solicitor","hint","ajax_solicitor.php",{afterUpdateElement : getSelectionId});
function getSelectionId(text, li) {
	document.getElementById(\'cli_solicitor_id\').value = li.id;
	}
</script>');
	if ($msg) {
		$page->addBodyContent($msg);
	}
	$page->display();

	/*
	echo '
	<script language="JavaScript" type="text/javascript">
	//You should create the validator only after the definition of the HTML form

	  var frmvalidator  = new Validator("testForm");
	  frmvalidator.addValidation("cli_fname","req","Forename(s) is required");

	  frmvalidator.addValidation("cli_sname","req","Surname is required");

	  frmvalidator.addValidation("cli_email","req","Email is required");
	  frmvalidator.addValidation("cli_email","email","Email is invalid");

	</script>';
	*/

} else { // if form is submitted

	$result = new Validate();

// validate the appropriate data array+form combintation, except for form2 (addresses) which is dealt with separately

	if ($_POST["form1"]) {

		if ($_POST["cli_notes"]) {
			$notes        = $_POST["cli_notes"];
			$db_data_note = array(
				'not_blurb' => $notes,
				'not_row'   => $cli_id,
				'not_type'  => 'client_general',
				'not_user'  => $_SESSION["auth"]["use_id"],
				'not_date'  => $date_mysql
			);
			db_query($db_data_note, "INSERT", "note", "not_id");
		}
		unset($form1["cli_notes"]);

		$fields   = $form1;
		$viewForm = 1;

		// check if existing phone numbers have been changed and update, do not allow blanks
		if ($telephone) {
			foreach ($telephone as $key => $val) {
				$tel_count = ($key + 1);
				if (($_POST["tel" . $tel_count] !== $val["number"] || $_POST["tel" . $tel_count . "type"] !== $val["type"]) && trim($_POST["tel" . $tel_count])) {
					$db_data['tel_number'] = phone_format($_POST["tel" . $tel_count]);
					$db_data['tel_type']   = $_POST["tel" . $tel_count . "type"];
					$db_data['tel_cli']    = $cli_id;
					db_query($db_data, "UPDATE", "tel", "tel_id", $val['id']);
				}
			}
		}

		// check if new phone has been entrered
		// new phones perhaps should go at the top, reordering the rest down by 1 ? for the time being, they are put at the bottom
		if ($telephone) {
			$ord = (count($telephone) + 1);
		} else {
			$ord = 1;
		}
		if ($_POST["telnew"]) {
			if (phone_validate($_POST["telnew"])) {
				$db_data = array(
					'tel_number' => phone_format($_POST["telnew"]),
					'tel_type'   => $_POST["telnewtype"],
					'tel_cli'    => $cli_id,
					'tel_ord'    => $ord
				);
				db_query($db_data, "INSERT", "tel", "tel_id");
				unset($db_data);
			} else {
				$errors[] = 'Please enter a valid phone number';
			}
		}

	} elseif ($_POST["form2"]) {
		$viewForm = 2;
		// addresses from postcode lookup will already be stored in table, and will provide pro_pro_id
		// this needs to be stored in the link table pro2cli
		if ($_POST["pro_pro_id"]) {
			$pro_id              = $_POST["pro_pro_id"];
			$db_data["p2c_pro"]  = $pro_id;
			$db_data["p2c_cli"]  = $_POST["cli_id"];
			$db_data["p2c_type"] = $_POST["p2c_type"];
			// check to prevent duplicates
			$sql = "SELECT p2c_pro,p2c_cli,p2c_type
		FROM pro2cli
		WHERE p2c_pro = '$pro_id' AND p2c_cli = '" . $_POST["cli_id"] . "' AND p2c_type = '" . $_POST["p2c_type"] . "'";
			$q   = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage());
			}
			if (!$q->numRows()) {
				db_query($db_data, "INSERT", "pro2cli", "p2c_id");
			}

			// if client has not default address, make the above property it
			$sql = "SELECT cli_pro FROM client WHERE cli_id = '" . $_POST["cli_id"] . "'";
			$q   = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage());
			}
			while ($row = $q->fetchRow()) {
				if ($row["cli_pro"] == 0) {
					$db_dataD["cli_pro"] = $pro_id;
					db_query($db_dataD, "UPDATE", "client", "cli_id", $_POST["cli_id"]);
				}
			}

		} else {
			// if the manual input form is used, put values into array and insert into property table
			// all manual entries are inserted with -1 as pcid, and should be checked by admin until a script does it automatically
			$results = $result->process($form2, $_POST);
			$db_data = $results['Results'];

			// build return link
			$redirect = $_SERVER['SCRIPT_NAME'] . '?';
			if ($cli_id) {
				$redirect .= 'cli_id=' . $cli_id;
			}
			if ($viewForm) {
				$redirect .= '&viewForm=' . $viewForm;
			}
			if ($searchLink) {
				$redirect .= '&searchLink=' . urlencode($searchLink);
			}
			if ($results['Errors']) {
				if (is_array($results['Results'])) {
					$redirect .= '&' . http_build_query($results['Results']);
				}
				echo error_message($results['Errors'], urlencode($redirect));
				exit;
			}

			// here, in fuure, we should check table for existing properties to prevent duplicates
			$db_data["pro_pcid"] = '-1';
			$pro_id              = db_query($db_data, "INSERT", "property", "pro_id");

			// insert into pro2cli table linkage
			$db_data2["p2c_cli"]  = $_POST["cli_id"];
			$db_data2["p2c_pro"]  = $pro_id;
			$db_data2["p2c_type"] = $_POST["p2c_type"];
			db_query($db_data2, "INSERT", "pro2cli", "p2c_id");

			// if client has not default address, make the above property it
			$sql = "SELECT cli_pro FROM client WHERE cli_id = '" . $_POST["cli_id"] . "'";
			$q   = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage());
			}
			while ($row = $q->fetchRow()) {
				if ($row["cli_pro"] == 0) {
					$db_dataD["cli_pro"] = $pro_id;
					db_query($db_dataD, "UPDATE", "client", "cli_id", $_POST["cli_id"]);
				}
			}

			$msg = urlencode('Update Successful');
			header("Location:$redirect&msg=$msg");
			exit;
		}
	} elseif ($_POST["form4"]) {
		$addFormData4 = array(
			'cli_sales'     => array(
				'label'    => 'Sales',
				'required' => 2,
				'value'    => 'Yes'
			),
			'cli_saleptype' => array(
				'label'    => 'Property Type',
				'required' => 2,
				'value'    => array2string($_POST["cli_saleptype"], "|")
			)
		);
		$fields       = join_arrays(array($form4, $addFormData4));
		$viewForm     = 4;
	} elseif ($_POST["form5"]) {
		$addFormData5 = array(
			'cli_lettings' => array(
				'label'    => 'Lettings',
				'required' => 2,
				'value'    => 'Yes'
			),
			'cli_letptype' => array(
				'label'    => 'Property Type',
				'required' => 2,
				'value'    => array2string($_POST["cli_letptype"], "|")
			)
		);
		$fields       = join_arrays(array($form5, $addFormData5));
		$viewForm     = 5;
	} elseif ($_POST["form6"]) {
		$addFormData6 = array(
			'cli_area' => array(
				'label'    => 'Areas',
				'required' => 2,
				'value'    => array2string($_POST["cli_area"], "|")
			)
		);
		$fields       = join_arrays(array($form6, $addFormData6));
		$viewForm     = 6;
	} elseif ($_POST["form7"]) {
		// extract notes from db_data and store in notes table
		if ($_POST["cli_req"]) {
			$notes        = $_POST["cli_req"];
			$db_data_note = array(
				'not_blurb' => $notes,
				'not_row'   => $cli_id,
				'not_type'  => 'client_req',
				'not_user'  => $_SESSION["auth"]["use_id"],
				'not_date'  => $date_mysql
			);
			db_query($db_data_note, "INSERT", "note", "not_id");
		}
		//print_r($db_data_note);
		unset($db_data["dea_notes_sot"]);
		$viewForm = 7;
	} elseif ($_POST["form8"]) {

		// new source
		if ($_POST["cli_source"] == "x") {
			if (!$_POST["sourceNew"]) {
				$errors[] = "Please enter a referer title or choose existing from the list";
				echo error_message($errors);
				exit;
			} else {
				// check if it already exists... (not fail-safe, but worth a try)
				// lower case all, and remove space from both new and existing for comparison
				$sql_source_check    = "SELECT sou_id FROM source
			WHERE sou_type = " . $_POST["cli_source1"] . " AND REPLACE(LOWER(sou_title),' ','') = '" . trim(strtolower(str_replace(" ", "", $_POST["sourceNew"]))) . "'";
				$result_source_check = mysql_query($sql_source_check);
				if (mysql_num_rows($result_source_check)) {
					while ($row_source_check = mysql_fetch_array($result_source_check)) {
						$_POST["cli_source"] = $row_source_check["sou_id"];
					}
				} else {
					$db_data_source["sou_type"]  = $_POST["cli_source1"];
					$db_data_source["sou_title"] = trim($_POST["sourceNew"]);
					db_query($db_data_source, "INSERT", "source", "sou_id");
					// get the id
					$sql_source    = "SELECT sou_id FROM source WHERE sou_type = " . $_POST["cli_source1"] . " AND sou_title = '" . trim($_POST["sourceNew"]) . "'";
					$result_source = mysql_query($sql_source);
					while ($row_source = mysql_fetch_array($result_source)) {
						$_POST["cli_source"] = $row_source["sou_id"];
					}
				}
			}
		}

		// cli_solicitor from ajax is in hidden field
		if ($_POST["cli_solicitor_id"]) {
			$_POST["cli_solicitor"] = $_POST["cli_solicitor_id"];
		} elseif ($_POST["cli_solicitor"]) {
			// new solicitor entered, forward to add comapny page AFTER update
			$split     = explode(" ", $_POST["cli_solicitor"]);
			$forwardto = 'contact_add.php?cli_id=' . $cli_id . '&con_fname=' . $split[0] . '&con_sname=' . $split[1] . '&con_type=2';
		}

		unset($_POST["cli_solicitor_id"]);
		$fields   = $form8;
		$viewForm = 8;

	}

	if ($viewForm !== 2 && $viewForm !== 7) {
		$results = $result->process($fields, $_POST);
		$db_data = $results['Results'];
	}

	if ($viewForm == 4 || $viewForm == 5) {
		$db_data["cli_reviewed"] = $date_mysql;
	}

// build return link
	$redirect = $_SERVER['SCRIPT_NAME'] . '?';
	if ($cli_id) {
		$redirect .= 'cli_id=' . $cli_id;
	}
	if ($viewForm) {
		$redirect .= '&viewForm=' . $viewForm;
	}
	if ($searchLink) {
		$redirect .= '&searchLink=' . urlencode($searchLink);
	}
	if ($results['Errors'] || $errors) {
		if (is_array($results['Results'])) {
			$redirect .= '&' . http_build_query($results['Results']);
		}
		echo error_message(join_arrays(array($results['Errors'], $errors)), urlencode($redirect));
		exit;
	}

	if ($viewForm !== 2 && $viewForm !== 7) {
		db_query($db_data, "UPDATE", "client", "cli_id", $cli_id);
	}

	if ($forwardto) {
		header("Location:$forwardto");
	} else {
		$msg = urlencode('Update Successful');
		header("Location:$redirect&msg=$msg");
	}
	exit;

}


?>