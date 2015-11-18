<?php
require_once("inx/global.inc.php");

/*
Deal Summary, shows deal and property info
Offer table only visible if property is Instructed (other statuses may hide this table too)
Summary page:
Address, Summary (n Bed psubtype ptype)
Bedrooms, Receptions, Barthrooms, Features (parking, garden etc)
Price, Qualifier and Tenure


NOTE: property particulars are stored in the property table as it is assumed they will never change, or if they do
it will be a permanent change that will effect all deals. Possible problem arises if the property is changed (e.g.
and extra bedroom is added) it will effect all deals on that property (historically), so previous deals will be
rendered inacurate. One possible solution is to move the particaulr into the deal table, but in most cases it is more
useful to have them in the property table as they will rarely change. As a workaround, we could make the particulars
readonly if there is another deal associated with it. Or maybe store an array of particualrs in the deal table to
backward compatibility? Undecided (17/10/06)
MOVE tenure from property to deal table (17/10/06)
Property details have been movd to deal table. When creating a new deal on a property that has a past deal on it, the
property details will be populated from teh most recent past deal.

SALES AND LETTINGS
sales only fields: tenure, chain free
lettins only fields: furnished, managed


*/

if ($_GET["do"] == "remove_vendor") {
	// remove vendor from deal, delete row from link_client_to_instruction table
	$sql = "DELETE FROM link_client_to_instruction WHERE
	link_client_to_instruction.clientId = " . $_GET["cli_id"] . " AND
	link_client_to_instruction.dealId = " . $_GET["dea_id"];
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	header("Location:" . $_GET["return"] . "&viewForm=1&msg=Update+Successful");
}

if (!$_GET["stage"]) {
	$stage = 1;
} else {
	$stage = $_GET["stage"];
}

if (!$_GET["dea_id"]) {
	echo "error, no dea_id";
} else {
	$dea_id = $_GET["dea_id"];
}

if ($_POST["viewForm"]) {
	$viewForm = $_POST["viewForm"];
} elseif ($_GET["viewForm"]) {
	$viewForm = $_GET["viewForm"];
} else {
	$viewForm = "0";
}

// start a new page
$page = new HTML_Page2($page_defaults);

// improvement 06/10/06 - link table for multiple vendors on a single deal
$sql = "SELECT
	deal.*,
	property.*,
	CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,
	cli_id,CONCAT(cli_salutation,' ',cli_fname,' ',cli_sname) AS cli_name,
	CONCAT(use_fname,' ',use_sname) AS use_name,
	link_client_to_instruction.*,
	branch.bra_id,branch.bra_title,
	T.pty_title AS ptype,
	ST.pty_title AS psubtype,
	keybook.*
FROM
	deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id

LEFT JOIN property ON deal.dea_prop = property.pro_id

LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN user ON deal.dea_neg = user.use_id

LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id

LEFT JOIN keybook ON deal.dea_id = keybook.key_deal

WHERE
deal.dea_id = $dea_id
";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows == 0) {
	echo "select error";
	exit;
} else {
	while ($row = $q->fetchRow()) {
		$vendors[$row["cli_id"]] = $row["cli_name"];
		foreach ($row as $key => $val) {
			$$key = $val;
		}
		if ($row["dea_available"] && $row["dea_available"] != '0000-00-00 00:00:00') {
			$dea_available = date('d/m/Y', strtotime($row["dea_available"]));
		} else {
			$dea_available = '';
		}
		if ($row["dea_exchdate"] && $row["dea_exchdate"] != '0000-00-00 00:00:00') {
			$dea_exchdate = date('d/m/Y', strtotime($row["dea_exchdate"]));
		} else {
			$dea_exchdate = '';
		}
		if ($row["dea_compdate"] && $row["dea_compdate"] != '0000-00-00 00:00:00') {
			$dea_compdate = date('d/m/Y', strtotime($row["dea_compdate"]));
		} else {
			$dea_compdate = '';
		}
	}
}

if ($dea_type == 'Sales') {
	$owner = 'Vendor';
} elseif ($dea_type == 'Lettings') {
	$owner = 'Landlord';
}

foreach ($vendors AS $ven_id => $ven_name) {
	$vendor_table .= '<tr><td height="20"><a href="client_edit.php?cli_id=' . $ven_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">' . $ven_name . '</a></td><td>';
	if (count($vendors) == 1) {
		$vendor_table .= '';
	} else {
		$vendor_table .= '<a href="?do=remove_vendor&dea_id=' . $dea_id . '&cli_id=' . $ven_id . '&return=' . urlencode('?' . $_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove ' . $ven_name . ' from this Deal"></a>';
	}
	$vendor_table .= '</td></tr>';
}
$vendor_table = '<table width="300" cellpadding="0" cellspacing="0" border="0">
  ' . $vendor_table . '
  <tr>
    <td height="20"><input type="button" value="Add ' . $owner . '" onClick="document.location.href = \'client_lookup.php?dest=add_vendor&dea_id=' . $dea_id . '&return=' . urlencode($_GET["searchLink"]) . '\';" class="button"></td>
  </tr>
</table>';

// calculate gross internal area from media table
$sql = "SELECT med_dims FROM media WHERE
med_table = 'deal' AND
med_row = $dea_id AND
med_type = 'Floorplan'";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows) {
	while ($row = $q->fetchRow()) {
		$internal_area = $internal_area + $row["med_dims"];
	}
}
if ($internal_area == 0) {
	$internal_area = '(tbc)';
} else {
	$internal_area .= ' m&sup2; / ' . sqmtr2sqft($internal_area) . ' ft&sup2; - these are approximate figures';
}

// summary screen
$nw = new Numbers_Words();

if ($dea_bedroom == '') {
	$dea_bedroom_word = "(tbc)";
} else {
	$dea_bedroom_word = ucwords($nw->toWords($dea_bedroom));
}
if ($dea_reception == '') {
	$dea_reception_word = "(tbc)";
} else {
	$dea_reception_word = ucwords($nw->toWords($dea_reception));
}
if ($dea_bathroom == '') {
	$dea_bathroom_word = "(tbc)";
} else {
	$dea_bathroom_word = ucwords($nw->toWords($dea_bathroom));
}

if (!$dea_marketprice) {
	$price = format_price($dea_valueprice) . ' (Valuation)';
} else {
	if ($dea_qualifier !== 'None') {
		$qual = ' (' . $dea_qualifier . ')';
	}

	if ($dea_type == 'Sales') {
		$price = format_price($dea_marketprice) . ' ' . $dea_tenure;
	} elseif ($dea_type == 'Lettings') {
		$price .= format_price($dea_marketprice, 'GBP', true) . ' per week / ' . format_price(pw2pcm($dea_marketprice), 'GBP', true) . ' per month';
	}
	$price .= $qual;
}
foreach ($vendors AS $ven_id => $ven_name) {
	$vendor_summary .= $ven_name . ', ';
}
$vendor_summary = remove_lastchar($vendor_summary, ",");

// moved summary table lower down page to catch offer info

// deal table, positioned above formData
$deal_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top">' . $owner . '(s)</td>
	<td>' . $vendor_table . '</td>
  </tr>
</table>
';

// get state of trade, and history
$sql = "SELECT
sot_id,sot_status,sot_nextdate,
CONCAT(use_fname,' ',use_sname) AS use_name,
DATE_FORMAT(sot_date, '%D %M %Y %l:%i%p') AS date
FROM
sot
LEFT JOIN user ON user.use_id = sot.sot_user
WHERE
sot_deal = $dea_id
ORDER BY sot_date DESC";

$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$sot[] = array(
		'sot_id'     => $row["sot_id"],
		'sot_status' => $row["sot_status"],
		'sot_date'   => $row["date"]
	);

	$sot_table .= '<tr class="' . $class . '">
	<td>' . $row["sot_status"] . '</td>
	<td>' . $row["date"] . '</td>
	<td>' . $row["use_name"] . '</td>
	</tr>';
}
$sot_table = '
<div style="height:110px;overflow:auto;">
<table width="95%">
' . $sot_table . '
</table>
</div>
';

// get all existing offers and build table
$sql = "SELECT
offer.off_id,offer.off_deal,offer.off_price,offer.off_conditions,offer.off_status,offer.off_neg,
DATE_FORMAT(off_date, '%D %M %Y') AS date,
cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ' &amp; ') AS cli_name,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name,CONCAT(LEFT(user.use_fname,1),LEFT(user.use_sname,1)) AS use_initial,use_colour
FROM
offer
LEFT JOIN cli2off ON cli2off.c2o_off = offer.off_id
LEFT JOIN client ON cli2off.c2o_cli = client.cli_id
LEFT JOIN user ON offer.off_neg = user.use_id
WHERE
offer.off_deal = $dea_id
GROUP BY offer.off_id
ORDER BY offer.off_date DESC";

$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numOffers = $q->numRows();
if ($numOffers) {
	while ($row = $q->fetchRow()) {

		// adding info to summary table when property is under offer or exchanged
		if (($dea_status == 'Under Offer' || $dea_status == 'Exchanged' || $dea_status == 'Completed') && $row["off_status"] == 'Accepted') {
			$offer_info = ' - ' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]) . ' at ' . format_price($row["off_price"]);
		}

		if ($row["use_colour"]) {
			$use_colour = '<span class="use_col" style="background-color: #' . $row["use_colour"] . ';"><img src="/images/sys/admin/blank.gif" width="10" height="10" alt="' . $row["use_name"] . '"></span>&nbsp;';
		}
		$use_name = $use_colour . $row["use_initial"];

		// only show owner neg and manager the proce and edit link, unless accepted
		if ($row["off_neg"] == $_SESSION["auth"]["use_id"] || in_array('Manager', $_SESSION["auth"]["roles"]) || ($row["off_status"] == 'Accepted' || $row["off_status"] == 'Rejected')) {
			$offer     = format_price($row["off_price"]);
			$edit_link = '<a href="offer_edit.php?off_id=' . $row["off_id"] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=4') . '"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View/Edit this offer"/></a>';
		} else {
			$offer     = 'hidden';
			$edit_link = '';
		}

		$offer_table .= '
  <tr class="' . $row["off_status"] . '">
	<td>' . $use_name . '</td>
	<td>' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]) . '</td>
	<td>' . $offer . '</td>
	<td>' . $row["date"] . '</td>
	<td>' . $row["off_status"] . '</td>
	<td align="right">' . $edit_link . '</td>
  </tr>';

	}
	$offer_table = '
<table id="detailTable" width="95%" cellpadding="2" cellspacing="2" align="center">
  <tr>
    <th>Neg</th>
    <th>Client</th>
    <th>Offer</th>
    <th>Date</th>
    <th colspan="2">Status</th>
  </tr>' . $offer_table . '
</table>';
}

$summary_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label">Address</td>
	<td>' . $pro_addr . '</td>
  </tr>
  <tr>
    <td class="label">Current Status</td>
	<td><strong>' . $dea_status . '</strong>' . $offer_info . '</td>
  </tr>
  <tr>
    <td class="label">Price</td>
	<td>' . $price . '</td>
  </tr>
  <tr>
    <td class="label">' . $owner . '(s)</td>
	<td>' . $vendor_summary . '</td>
  </tr>
  <tr>
    <td class="label">Property Type</td>
	<td>' . $ptype . ' / ' . $psubtype . '</td>
  </tr>
  <tr>
    <td class="label">Rooms</td>
	<td>' . $dea_bedroom_word . ' Bedroom, ' . $dea_reception_word . ' Reception, ' . $dea_bathroom_word . ' Bathroom</td>
  </tr>
  <tr>
    <td class="label">Internal Area</td>
	<td>' . $internal_area . '</td>
  </tr>
</table>
';

// get all appointments and build table
$countViewing    = 0;
$countValuation  = 0;
$countProduction = 0;
$countInspection = 0;
$sql             = "SELECT
app_id,app_type,app_start,app_end,app_status,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name,use_colour,
cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ', ') AS cli_name,
GROUP_CONCAT(DISTINCT CONCAT(cli_id) ORDER BY client.cli_id ASC SEPARATOR '|') AS cli,
DATE_FORMAT(appointment.app_start, '%d/%m/%y %H:%i') AS app_date,
d2a_id,d2a_feedback
FROM link_deal_to_appointment
LEFT JOIN appointment ON link_deal_to_appointment.d2a_app = appointment.app_id
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
WHERE
link_deal_to_appointment.d2a_dea = $dea_id  AND appointment.app_status != 'Deleted'
GROUP BY appointment.app_id
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
			if ($row["app_status"] == "Cancelled") {
				$countCancelled++;
			} elseif ($row["d2a_feedback"] == "Positive") {
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
					$feedback = '<a href="appointment_feedback.php?d2a_id=' . $row["d2a_id"] . '&cli_id=' . $row["cli"] . '&dea_id=' . $dea_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=5') . '">(not entered)</a>';
				} else {
					$feedback = '<a href="appointment_feedback.php?d2a_id=' . $row["d2a_id"] . '&cli_id=' . $row["cli"] . '&dea_id=' . $dea_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=5') . '">' . $row["d2a_feedback"] . '</a>';
				}
			} else {
				$feedback = '(in future)';
			}
			// cancelled overwrites above feedback text
			if ($row["app_status"] == 'Cancelled') {
				$feedback = '(cancelled)';
			}

			if ($row["use_colour"]) {
				$use_colour = '<span class="use_col" style="background-color: #' . $row["use_colour"] . ';"><img src="/images/sys/admin/blank.gif" width="10" height="10"></span>&nbsp;';
			} else { // unnassigned
				$use_colour = '<span class="use_col" style="background-color: #FFFFFF;"><img src="/images/sys/admin/blank.gif" width="10" height="10"></span>&nbsp;';
				;
			}
			if (!$row["use_name"]) {
				$row["use_name"] = '(unassigned)';
			}
			$use_name = $use_colour . $row["use_name"];

			$viewings_table .= '
  <tr>
	<td width="20%" valign="top">' . $row["app_date"] . '</td>
	<td width="25%" valign="top">' . $use_name . '</td>
	<td width="35%" valign="top">' . preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]) . '</td>
	<td width="15%" valign="top">' . $feedback . '</td>
	<td width="5%" align="right" valign="top">
	  <a href="appointment_edit.php?app_id=' . $row["app_id"] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=5') . '"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View/Edit Appointment"/></a>
	  </td>
  </tr>';
			$countViewing++;
		} elseif ($row["app_type"] == 'Valuation') {
			$countValuation++;
		} elseif ($row["app_type"] == 'Production') {
			$countProduction++;
		} elseif ($row["app_type"] == 'Inspection') {
			$countInspection++;
		}

	}
}

if ($countViewing) {
	$centPositive    = @round(($countPositive / $countViewing) * 100);
	$centIndifferent = @round(($countIndifferent / $countViewing) * 100);
	$centNegative    = @round(($countNegative / $countViewing) * 100);
	$centCancelled   = @round(($countCancelled / $countViewing) * 100);
	$centEmpty       = @round(($countEmpty / $countViewing) * 100);
	$viewingStats    = '&nbsp; <span class="small">(Total: ' . $countViewing . ' | Postitive: ' . $centPositive . '% | Indifferent: ' . $centIndifferent . '% | Negative: ' . $centNegative . '% | Cancelled: ' . $centCancelled . '% | Empty: ' . $centEmpty . '%)</span>';
}
/*
include("inx/libchart/libchart.php");
$chart = new PieChart(200, 200);
$chart->addPoint(new Point("Positive", $centPositive));
$chart->addPoint(new Point("Indifferent", $centIndifferent));
$chart->addPoint(new Point("Negative", $centNegative));
$chart->addPoint(new Point("Empty", $centEmpty));
$chart->render("img/generated/$PHPSESSID.png");
<img src="img/generated/'.$PHPSESSID.'.png">
*/

$appointments_table = '
<table id="detailTable" width="97%" cellpadding="2" cellspacing="2" align="center">
  <tr>
    <td colspan="4"><strong>Viewings</strong> ' . $viewingStats . '</td>
	<td align="right"><a href="client_lookup.php?dest=viewing&dea_id=' . $dea_id . '">[ New ]</a></td>
  </tr>
</table>
<div id="viewingContainer">
<table id="detailTable" width="95%">
  <tr>
    <th>Date</th>
    <th>Neg</th>
    <th>Client</th>
    <th colspan="2">Feedback</th>
  </tr>
' . $viewings_table . '
</table>
</div>
<br />
<table id="detailTable" width="97%" style="margin-left:10px">
  <tr>
    <td colspan="3"><strong>Other Appointments</strong></td>
  </tr>
  <tr>
	<td width="120">Valuations:</td>
	<td>';
if ($countValuation > 0) {
	$appointments_table .= '<a href="appointment_search.php?stage=1&action=advanced_search&keyword=^' . $dea_id . '&type=Valuation&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=5') . '">' . $countValuation . '</a>';
} else {
	$appointments_table .= "0";
}
$appointments_table .= '</td>
	<td><a href="client_lookup.php?dest=valuation&dea_id=' . $dea_id . '">[ New ]</a></td>
  </tr>
  <tr>
	<td>Production:</td>
	<td>';
if ($countProduction > 0) {
	$appointments_table .= '<a href="appointment_search.php?stage=1&action=advanced_search&keyword=^' . $dea_id . '&type=Production&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=5') . '">' . $countProduction . '</a>';
} else {
	$appointments_table .= "0";
}
$appointments_table .= '</td>
	<td><a href="production_add.php?stage=vendor&dea_id=' . $dea_id . '">[ New ]</a></td>
  </tr>
  <tr>
	<td>Inspections:</td>
	<td>';
if ($countInspection > 0) {
	$appointments_table .= '<a href="appointment_search.php?stage=1&action=advanced_search&keyword=^' . $dea_id . '&type=Inspection&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . replaceQueryString($_SERVER['QUERY_STRING'], 'viewForm') . '&viewForm=5') . '">' . $countInspection . '</a>';
} else {
	$appointments_table .= "0";
}
$appointments_table .= '</td>
	<td><a href="inspection_add.php?dea_id=' . $dea_id . '">[ New ]</a></td>
  </tr>
</table>';

// build data arrays for editable tabs

if ($dea_type == 'Sales') {

	if ($dea_commissiontype == 'fixed') {
		$dea_commission = format_price($dea_commission);
	}
	$formData1 = array(
		'dea_valueprice'     => array(
			'type'       => 'text',
			'label'      => 'Valuation Price',
			'value'      => format_price($dea_valueprice),
			'required'   => 2,
			'attributes' => array('style' => 'width: 100px', 'maxlength' => 12),
			'function'   => 'numbers_only'
		),
		'dea_marketprice'    => array(
			'type'       => 'text',
			'label'      => 'Market Price',
			'value'      => format_price($dea_marketprice),
			'required'   => 2,
			'attributes' => array('style' => 'width: 100px', 'maxlength' => 12),
			'group'      => 'Market Price',
			'function'   => 'numbers_only'
		),
		'dea_qualifier'      => array(
			'type'          => 'select',
			'label'         => 'Market Price',
			'value'         => $dea_qualifier,
			'required'      => 2,
			'options'       => db_enum('deal', 'dea_qualifier', 'array'),
			'group'         => 'Market Price',
			'last_in_group' => 1,
			'attributes'    => array('style' => 'width: 61px')
		),
		'dea_commission'     => array(
			'type'       => 'text',
			'label'      => 'Commission',
			'group'      => 'Commission',
			'value'      => $dea_commission,
			'required'   => 3,
			'attributes' => array('style' => 'width: 55px', 'maxlength' => 9),
			'function'   => 'numbers_only'
		),
		'dea_commissiontype' => array(
			'type'          => 'select',
			'label'         => 'Commission',
			'group'         => 'Commission',
			'last_in_group' => 1,
			'value'         => $dea_commissiontype,
			'required'      => 3,
			'options'       => db_enum('deal', 'dea_commissiontype', 'array')
		),
		'dea_share'          => array(
			'type'       => 'select',
			'label'      => 'Deal Share',
			'value'      => $dea_share,
			'required'   => 2,
			'attributes' => array('style' => 'width: 70px'),
			'options'    => db_enum('deal', 'dea_share', 'array')
		),
		'dea_chainfree'      => array(
			'type'     => 'radio',
			'label'    => 'Chain Free',
			'value'    => $dea_chainfree,
			'required' => 2,
			'options'  => db_enum('deal', 'dea_chainfree', 'array')
		),
		'dea_tenure'         => array(
			'type'       => 'select',
			'label'      => 'Tenure',
			'value'      => $dea_tenure,
			'required'   => 2,
			'options'    => db_enum("deal", "dea_tenure", "array"),
			'attributes' => array('style' => 'width: 165px')
		),
		'dea_servicecharge'  => array(
			'type'       => 'text',
			'label'      => 'Service Charge',
			'value'      => $dea_servicecharge,
			'attributes' => array('style' => 'width: 165px')
		),
		'dea_groundrent'     => array(
			'type'       => 'text',
			'label'      => 'Ground Rent',
			'value'      => $dea_groundrent,
			'attributes' => array('style' => 'width: 165px')
		),
		'dea_board'          => array(
			'type'       => 'select',
			'label'      => 'Board',
			'value'      => $dea_board,
			'required'   => 2,
			'options'    => db_enum("deal", "dea_board", "array"),
			'attributes' => array('style' => 'width: 99px'),
			'group'      => 'Board'
		),
		'dea_board_log'      => array(
			'type'          => 'button',
			'label'         => 'History',
			'value'         => 'History',
			'attributes'    => array(
				'class' => 'button', 'onClick' => 'window.open(\'changeLog.php?scope=board&dea_id=' . $dea_id . '\',\'logWin\',\'width=550,height=400,scrollbars=1\');'
			),
			'group'         => 'Board',
			'last_in_group' => 1
		),
		'dea_branch'         => array(
			'type'       => 'select_branch',
			'label'      => 'Branch',
			'value'      => $dea_branch,
			'required'   => 2,
			'attributes' => array('style' => 'width: 165px')
		),
		'dea_neg'            => array(
			'type'       => 'select_user',
			'label'      => 'Negotiator',
			'value'      => $dea_neg,
			'required'   => 3,
			'attributes' => array('style' => 'width: 165px'),
			'options'    => array('' => '(unassigned)')
		)
	);

} elseif ($dea_type == 'Lettings') {

	$formData1 = array(
		'dea_marketprice'    => array(
			'type'       => 'text',
			'label'      => 'Market Price (pw)',
			'value'      => format_price($dea_marketprice, 'GBP', true),
			'required'   => 2,
			'attributes' => array('style' => 'width: 100px', 'maxlength' => 15),
			//'group'=>'Market Price (pw)',
			'function'   => 'numbers_only'
		),
		'dea_commission'     => array(
			'type'       => 'text',
			'label'      => 'Commission',
			'group'      => 'Commission',
			'value'      => $dea_commission,
			'required'   => 3,
			'attributes' => array('style' => 'width: 70px', 'maxlength' => 7),
			'function'   => 'numbers_only'
		),
		'dea_commissiontype' => array(
			'type'          => 'select',
			'label'         => 'Commission',
			'group'         => 'Commission',
			'last_in_group' => 1,
			'value'         => $dea_commissiontype,
			'required'      => 3,
			'options'       => db_enum('deal', 'dea_commissiontype', 'array')
		),
		'dea_share'          => array(
			'type'       => 'select',
			'label'      => 'Deal Share',
			'value'      => $dea_share,
			'required'   => 2,
			'attributes' => array('style' => 'width: 70px'),
			'options'    => db_enum('deal', 'dea_share', 'array')
		),
		'dea_available'      => array(
			'type'  => 'datetime',
			'label' => 'Available Date',
			'value' => $dea_available
		),
		'dea_managed'        => array(
			'type'    => 'radio',
			'label'   => 'Managed by W&amp;S',
			'value'   => $dea_managed,
			'options' => db_enum('deal', 'dea_managed', 'array')
		),
		'dea_board'          => array(
			'type'       => 'select',
			'label'      => 'Board',
			'value'      => $dea_board,
			'required'   => 2,
			'options'    => db_enum("deal", "dea_board", "array"),
			'attributes' => array('style' => 'width: 99px'),
			'group'      => 'Board'
		),
		'dea_board_log'      => array(
			'type'          => 'button',
			'label'         => 'History',
			'value'         => 'History',
			'attributes'    => array(
				'class' => 'button', 'onClick' => 'window.open(\'changeLog.php?scope=board&dea_id=' . $dea_id . '\',\'logWin\',\'width=550,height=400,scrollbars=1\');'
			),
			'group'         => 'Board',
			'last_in_group' => 1
		),
		'dea_branch'         => array(
			'type'       => 'select_branch',
			'label'      => 'Branch',
			'value'      => $dea_branch,
			'required'   => 2,
			'attributes' => array('style' => 'width: 165px')
		),
		'dea_neg'            => array(
			'type'       => 'select_user',
			'label'      => 'Negotiator',
			'value'      => $dea_neg,
			'required'   => 3,
			'attributes' => array('style' => 'width: 165px'),
			'options'    => array('' => '(unassigned)')
		)
	);

}

# build select of possible new statuses, and create any additional fields relating to the possible new statuses
switch ($dea_status) {

	case "Valuation":
		$statuses          = array(
			'Valuation'      => 'Valuation',
			'Instructed'     => 'Instructed',
			'Not Instructed' => 'Not Instructed'
		);
		$additional_fields = array();
		break;

	case "Instructed":
		$statuses          = array(
			'Instructed'    => 'Instructed',
			'Production'    => 'Production',
			'Withdrawn'     => 'Withdrawn',
			'Disinstructed' => 'Disinstructed'
		);
		$additional_fields = array();
		break;
	case "Production":
		$statuses          = array(
			'Production'    => 'Production',
			'Proofing'      => 'Proofing',
			'Withdrawn'     => 'Withdrawn',
			'Disinstructed' => 'Disinstructed'
		);
		$additional_fields = array();
		break;
	case "Proofing":
		$statuses          = array(
			'Proofing'      => 'Proofing',
			'Available'     => 'Available',
			'Withdrawn'     => 'Withdrawn',
			'Disinstructed' => 'Disinstructed'
		);
		$additional_fields = array();
		break;
	case "Available":
		// need to prevent people setting this to under offer, as it has to be done via offer submit
		$statuses          = array(
			'Available'              => 'Available',
			'Under Offer'            => 'Under Offer',
			'Under Offer with Other' => 'Under Offer with Other',
			'Withdrawn'              => 'Withdrawn',
			'Disinstructed'          => 'Disinstructed'
		);
		$additional_fields = array();
		break;
	case "Under Offer":
		$statuses          = array(
			'Under Offer'   => 'Under Offer',
			'Exchanged'     => 'Exchanged',
			'Collapsed'     => 'Collapsed',
			'Withdrawn'     => 'Withdrawn',
			'Disinstructed' => 'Disinstructed'
		);
		$additional_fields = array();
		break;
	case "Exchanged":
		$statuses = array(
			'Exchanged'     => 'Exchanged',
			'Completed'     => 'Completed',
			'Collapsed'     => 'Collapsed',
			'Withdrawn'     => 'Withdrawn',
			'Disinstructed' => 'Disinstructed'
		);
		break;
	case "Completed":
		$statuses          = array(
			'Completed' => 'Completed',
			'Collapsed' => 'Collapsed'
		);
		$additional_fields = array();
		break;
	case "Collapsed":
		$statuses          = array(
			'Collapsed'  => 'Collapsed',
			'Production' => 'Production',
			'Available'  => 'Available'
		);
		$additional_fields = array();
		break;
	case "Not Instructed":
		$statuses          = array(
			'Not Instructed' => 'Not Instructed',
			'Instructed'     => 'Instructed'
		);
		$additional_fields = array();
		break;
	case "Withdrawn":
		$statuses          = array(
			'Withdrawn'  => 'Withdrawn',
			'Production' => 'Production',
			'Proofing'   => 'Proofing'
		);
		$additional_fields = array();
		break;
	case "Disinstructed":
		$statuses          = array(
			'Disinstructed' => 'Disinstructed',
			'Production'    => 'Production',
			'Proofing'      => 'Proofing'
		);
		$additional_fields = array();
		break;
	case "Under Offer with Other":
		$statuses           = array(
			'Under Offer with Other' => 'Under Offer with Other',
			'Sold by Other'          => 'Sold by Other',
			'Available'              => 'Available'
		);
		$xadditional_fields = array(
			'dea_otheragent' => array(
				'type'       => 'text',
				'label'      => 'Other Agent',
				'value'      => $dea_otheragent,
				'required'   => 3,
				'attributes' => array('class' => 'wide')
			)
		);
		break;
	case "Sold by Other":
		$statuses          = array(
			'Sold by Other' => 'Sold by Other'
		);
		$additional_fields = array();
		break;
	case "Archived":
		$statuses          = array(
			'Archived' => 'Archived'
		);
		$additional_fields = array();
		break;
	case "Comparable":
		$statuses          = array(
			'Comparable' => 'Comparable'
		);
		$additional_fields = array();
		break;
	case "Chain":
		$statuses          = array(
			'Chain' => 'Chain'
		);
		$additional_fields = array();
		break;
}

if ($dea_type == 'Sales' && ($dea_status == 'Under Offer' || $dea_status == 'Exchanged')) {

	$formData2 = array(
		'dea_status'    => array(
			'type'       => 'select',
			'label'      => 'Status',
			'value'      => $dea_status,
			'options'    => $statuses,
			'attributes' => array('readonly' => 'readonly', 'style' => 'width:300px')
		),
		'dea_exchdate'  => array(
			'type'  => 'datetime',
			'label' => 'Exchange Date',
			'value' => $dea_exchdate
		),
		'dea_compdate'  => array(
			'type'  => 'datetime',
			'label' => 'Completion Date',
			'value' => $dea_compdate
		),
		'dea_notes_sot' => array(
			'type'       => 'textarea',
			'label'      => 'Add Status Note',
			'attributes' => array('class' => 'noteInput', 'viewform' => 3)
		)
	);

} else {
	if ($dea_status == 'Available') {
		$status_attributes = array('style' => 'width:300px', 'onChange' => 'controlUnderOffer(this,\'dea_status\',\'' . $dea_status . '\');');
	} else {
		$status_attributes = array('style' => 'width:300px');
	}
	$formData2 = array(
		'dea_status'    => array(
			'type'       => 'select',
			'label'      => 'Status',
			'value'      => $dea_status,
			'options'    => $statuses,
			'attributes' => $status_attributes
		),
		'dea_notes_sot' => array(
			'type'       => 'textarea',
			'label'      => 'Add Status Note',
			'attributes' => array('class' => 'noteInput', 'viewform' => 3)
		)
	);
}

// viewing arrangements
$formData3 = array(
	'dea_key'       => array(
		'type'       => 'text',
		'label'      => 'Key Number',
		'value'      => $dea_key,
		'attributes' => array('class' => 'wide')
	),
	'dea_notes_arr' => array(
		'type'       => 'textarea',
		'label'      => 'Add Viewing Info',
		'attributes' => array('class' => 'noteInput')
	)
);

if (!$_GET["action"]) {

	$form = new Form();

	$form->addForm("", "GET", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\">\n");
	$form->addField("hidden", "stage", "", "1");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "dea_id", "", $dea_id);
	$form->addField("hidden", "pro_id", "", $pro_id);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));

	$form->addHtml('<h1>' . $pro_addr . ' (' . $dea_type . ')</h1>');

	$formName = 'form0';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Summary</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addHtml($form->addHtml($summary_table));
//$form->addHtml($form->addDiv($form->makeField("textarea",$formName,"General Notes","",array('class'=>'noteInput'))));
	$form->addHtml($form->addRow('textarea', 'dea_notes', 'Add General Note', '', array('class' => 'noteInput'), '', ''));
	$form->addHtml(renderNotes('deal_general', $dea_id, array('viewform' => '0', 'label' => 'General Notes')));
	$buttons = $form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'));
	if (in_array('Production', $_SESSION["auth"]["roles"]) || in_array('Editor', $_SESSION["auth"]["roles"])) {
		$buttons .= $form->makeField("button", $formName, "", "Production", array(
																				 'class'   => 'button',
																				 'onClick' => 'javascript:location.href=\'deal_production.php?dea_id=' . $dea_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '\''
																			));
	}

	$form->addHtml($buttons);
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form1';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Marketing Details</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addHtml($form->addHtml($deal_table));
	$form->addData($formData1, $_GET);
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'))));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form2';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">State of Trade</div>');
	$form->addHtml('<div id="' . $formName . '">');

	if ($dea_status == 'Proofing' && !in_array('Editor', $_SESSION["auth"]["roles"])) {

		$form->addHtml('<p class="appInfo">Submitted to the proofing list. Only Editors can change the status</p>');
		unset($formData2["dea_status"]);
		$form->addData($formData2, $_GET);

		$form->addHtml(renderNotes('sot', $dea_id, array('viewform' => 2, 'label' => 'Status Notes')));
		$buttons = $form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'));

	} else {

		if (is_array($additional_fields)) {
			$form->addData($additional_fields, $_GET);
		}
		$form->addData($formData2, $_GET);
		$form->addHtml(renderNotes('sot', $dea_id, array('viewform' => 2, 'label' => 'Status Notes')));
		$buttons = $form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'));

	}

	if ($_GET["prompt"] == "app_production") {
		$buttons .= $form->makeField("button", "", "", "Book Production Appointment", array(
																						   'class'   => 'button',
																						   'onClick' => 'document.location.href=\'production_add.php?stage=vendor&dea+id=' . $dea_id . '\''
																					  ));
	}
	$form->addHtml($buttons);
	$form->addSeperator();
	$form->addHtml($form->addLabel('history', 'Status history', $sot_table));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form3';
	$form->addHtml("<fieldset>\n");
	$form->addHtml('<div class="block-header">Keys & Viewing Times</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addHtml($key_table);
	$form->addData($formData3, $_GET);
	$form->addHtml(renderNotes('viewing_arrangements', $dea_id, array('viewform' => 3, 'label' => 'Viewing Times and Info')));

	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class' => 'submit'))));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form5';
	$form->addHtml("<fieldset>\n");
//$form->addLegend('Viewings ('.$countViewing.')',array('style'=>'cursor:pointer','onClick'=>'javascript:showHide(\''.$formName.'\');'));
	$form->addHtml('<div class="block-header">Viewings (' . $countViewing . ')</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addHtml($appointments_table);
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$formName = 'form4';
	$form->addHtml("<fieldset>\n");
//	$form->addLegend('Offers (' . $numOffers . ')', array('style' => 'cursor:pointer', 'onClick' => 'javascript:showHide(\'' . $formName . '\');'));
	$form->addHtml('<div class="block-header">Offers (' . $numOffers . ')</div>');
	$form->addHtml('<div id="' . $formName . '">');
	$form->addHtml($offer_table);
	$buttons = $form->makeField("button", $formName, "", "Submit Offer", array(
																			  'onClick' => 'javascript:document.location.href=\'client_lookup.php?dest=offer&dea_id=' . $dea_id . '&return=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '\';',
																			  'class'   => 'submit'
																		 ));
	$form->addHtml($form->addDiv($buttons));
	$form->addHtml('</div>');
	$form->addHtml("</fieldset>\n");

	$navbar_array = array(
		'back'   => array('title' => 'Back', 'label' => 'Back', 'link' => $searchLink),
		'search' => array('title' => 'Property Search', 'label' => 'Property Search', 'link' => 'property_search.php'),
		'print'  => array('title' => 'Open printable details', 'label' => 'Print', 'link' => 'javascript:dealPrint(\'' . $dea_id . '\');')
		//,'email'=>array('title'=>'Send details to a client','label'=>'Send','link'=>'javascript:dealPrint(\''.$dea_id.'\');')
	);
	$navbar       = navbar2($navbar_array);

	$onLoad .= 'showForm(' . $viewForm . ');'; # '.$ptype['onload'];

	$additional_js = '
if (!previousID) {
	var previousID = "form' . $viewForm . '";
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

	$page->setTitle("$pro_addr");
	$page->addStyleSheet(getDefaultCss());
	$page->addScript('js/global.js');
	$page->addScript('js/scriptaculous/prototype.js');
	$page->addScript('js/scriptaculous/scriptaculous.js');
	$page->addScript('js/CalendarPopup.js');

	if ($dea_type == 'Sales' && ($dea_status == 'Under Offer' || $dea_status == 'Exchanged')) {
		$page->addScriptDeclaration('
document.write(getCalendarStyles());
var popcaldea_exchdate = new CalendarPopup("popCalDivdea_exchdate");
var popcaldea_compdate = new CalendarPopup("popCalDivdea_compdate");
');
	}
	if ($dea_type == 'Lettings') {
		$page->addScriptDeclaration('
document.write(getCalendarStyles());
var popcaldea_available = new CalendarPopup("popCalDivdea_available");
');
	}

	$page->addScriptDeclaration($additional_js);
	$page->setBodyAttributes(array('onLoad' => $onLoad));
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content">');
//$page->addBodyContent($navbar);
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	if ($msg) {
		$page->addBodyContent($msg);
	}
//$page->addBodyContent($render);
	$page->addBodyContent('</div>');
//$page->addBodyContent('<a href="changeLog.php?scope=deal&dea_id='.$dea_id.'">Log</a>');
	$page->display();

	exit;

} elseif ($_GET["action"] == "update") {

	/*
	extra actions required:
	set to production, book production appointment
	sent to proofing, notification to editors
	set to under offer, must tie with an existing offer!
	set as exchanged, enter completition date
	*/

	foreach ($_GET as $key => $val) {
		$_GET[$key] = trim($val);
	}

// form0 is general notes only
	if ($_GET["form0"]) {
		if ($_GET["dea_notes"]) {
			$notes        = $_GET["dea_notes"];
			$db_data_note = array(
				'not_blurb' => $notes,
				'not_row'   => $dea_id,
				'not_type'  => 'deal_general',
				'not_user'  => $_SESSION["auth"]["use_id"],
				'not_date'  => $date_mysql
			);
			db_query($db_data_note, "INSERT", "note", "not_id");
		}
		unset($db_data["dea_notes_sot"]);
		$return = "dea_id=$dea_id&";
	} elseif ($_GET["form1"]) {
		$fields   = $form1;
		$viewForm = 1;

		if ($_GET["dea_neg"] == "0") {
			$_GET["dea_neg"] = "";
		}
		// validate
		$result  = new Validate();
		$results = $result->process($formData1, $_GET);
		$db_data = $results['Results'];

		if ($db_data["dea_available"]) {
			$date_parts               = explode("/", $db_data["dea_available"]);
			$app_date                 = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0] . ' 00:00:00';
			$db_data["dea_available"] = $app_date;
		}

		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&';

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}
		$dea_id = db_query($db_data, "UPDATE", "deal", "dea_id", $dea_id);
	} elseif ($_GET["form2"]) {
		$fields   = $form2;
		$viewForm = 2;

		$formData2 = join_arrays(array($formData2));

		$result  = new Validate();
		$results = $result->process($formData2, $_GET);
		$db_data = $results['Results'];

		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&';

		if ($db_data["dea_exchdate"]) {
			$date_parts              = explode("/", $db_data["dea_exchdate"]);
			$dea_exchdate            = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0] . ' 00:00:00';
			$db_data["dea_exchdate"] = $dea_exchdate;
		}
		if ($db_data["dea_compdate"]) {
			$date_parts              = explode("/", $db_data["dea_compdate"]);
			$dea_compdate            = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0] . ' 00:00:00';
			$db_data["dea_compdate"] = $dea_compdate;
		}

		// extract notes from db_data and store in notes table
		if ($db_data["dea_notes_sot"]) {
			$notes        = $db_data["dea_notes_sot"];
			$db_data_note = array(
				'not_blurb' => $notes,
				'not_row'   => $dea_id,
				'not_type'  => 'sot',
				'not_user'  => $_SESSION["auth"]["use_id"],
				'not_date'  => $date_mysql
			);
			db_query($db_data_note, "INSERT", "note", "not_id");
		}
		unset($db_data["dea_notes_sot"]);

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}
		$dea_id = db_query($db_data, "UPDATE", "deal", "dea_id", $dea_id);

		// insert new row into sot table if the status has been changed
		if ($db_data["dea_status"] && $db_data["dea_status"] !== $dea_status) {
			$db_data2["sot_deal"]   = $dea_id;
			$db_data2["sot_status"] = $db_data["dea_status"];
			$db_data2["sot_date"]   = $date_mysql;
			$db_data2["sot_notes"]  = $_GET["notes"];
			$db_data2["sot_user"]   = $_SESSION["auth"]["use_id"];
			$sot_id                 = db_query($db_data2, "INSERT", "sot", "sot_id");
		}
		// promt: changed to production, ask if appointment should be booked....
		if ($sot_id && $db_data2["sot_status"] == "Production") {
			$prompt = "app_production";
		}

	} // keybook and access details (notes)
	elseif ($_GET["form3"]) {
		$fields   = $form3;
		$viewForm = 3;

		$formData5 = join_arrays(array($formData3));

		$result  = new Validate();
		$results = $result->process($formData3, $_GET);
		$db_data = $results['Results'];

		// build return link
		$return = $_SERVER['SCRIPT_NAME'] . '?stage=1&dea_id=' . $dea_id . '&searchLink=' . $searchLink . '&';

		// extract notes from db_data and store in notes table
		if ($db_data["dea_notes_arr"]) {
			$notes        = $db_data["dea_notes_arr"];
			$db_data_note = array(
				'not_blurb' => $notes,
				'not_row'   => $dea_id,
				'not_type'  => 'viewing_arrangements',
				'not_user'  => $_SESSION["auth"]["use_id"],
				'not_date'  => $date_mysql
			);
			db_query($db_data_note, "INSERT", "note", "not_id");
		}
		unset($db_data["dea_notes_arr"]);

		if ($results['Errors']) {
			if (is_array($results['Results'])) {
				$return .= http_build_query($results['Results']);
			}
			echo error_message($results['Errors'], urlencode($return));
			exit;
		}
		$dea_id = db_query($db_data, "UPDATE", "deal", "dea_id", $dea_id);

	}

	// redirect
	header("Location:?$return&viewForm=$viewForm&msg=Update+Successful&prompt=$prompt");
	exit;

}
?>