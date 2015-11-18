<?php
include_once dirname(__FILE__) . "/../application/views/system/leftMenu.php";

function renderNotes($not_type, $not_row, $options = array())
{

	if ($options["label"]) {
		$label = $options["label"];
	} else {
		$label = 'Notes';
	}
	if ($options["order"] == "ASC") {
		$order = $options["order"];
	} else {
		$order = 'DESC';
	}
	$querystring = $_SERVER['QUERY_STRING'];
	if ($options["viewform"]) {
		$querystring = replaceQueryString($querystring, 'viewForm');
		$querystring .= '&viewForm=' . $options["viewform"];
	}
	$return = $_SERVER['PHP_SELF'] . urlencode('?' . $querystring);

	$_sql    = "SELECT note.*,
	DATE_FORMAT(note.not_edited,'%d/%m/%y') AS date,
	CONCAT(use_fname,' ',use_sname) AS use_name
	FROM note,user
	WHERE not_type = '$not_type' AND not_row = $not_row AND not_user = user.use_id
	ORDER BY not_status, not_edited $order ";
	$_result = mysql_query($_sql);
	$numRows = mysql_num_rows($_result);

	if (!$_result) {
		die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $_sql . "</pre>");
	}
	while ($row = mysql_fetch_array($_result)) {

		if ($row["not_status"] == 'Deleted') {
			$class = 'note_deleted';
		} else {
			$class = 'note';
		}
		// wrap each note in a div for mouseover highlight
		if ($options["layout"] == "readonly") {
			$notes .= '<div class="noteOff" onMouseOver="this.className=\'noteOn\';" onMouseOut="this.className=\'noteOff\';">';
			$notes .= '
			<div class="noteInfo"><img src="/images/sys/admin/icons/note.gif" />' . $row["use_name"] . ' ' . $row["date"] . '</div>
			<div class="' . $class . '">' . nl2br($row["not_blurb"]) . '</div>
			';
		} elseif ($options["layout"] == "simple") {
			$notes .= '<div class="noteOff" onMouseOver="this.className=\'noteOn\';" onMouseOut="this.className=\'noteOff\';">';
			$notes .= '
			<div class="' . $class . '">' . nl2br($row["not_blurb"]) . '</div>
			';
		} else {
			$notes .= '<div class="noteOff" onMouseOver="this.className=\'noteOn\';" onMouseOut="this.className=\'noteOff\';" onClick="document.location.href = \'note.php?not_id=' . $row["not_id"] . '&amp;return=' . $return . '\'">';
			$notes .= '
			<div class="noteInfo"><a href="note.php?not_id=' . $row["not_id"] . '&amp;return=' . $return . '"><img src="/images/sys/admin/icons/note.gif" />' . $row["use_name"] . ' &nbsp;edited: ' . $row["date"] . '</a></div>
			<div class="' . $class . '">' . nl2br($row["not_blurb"]) . '</div>
			';

		}
		$notes .= '</div>';

	}
	if ($notes) {
		if ($numRows > 0) {
			$numRows = ' (' . $numRows . ')';
		}
		$notes = '
		<div class="noteLabel">' . $label . $numRows . '</div>
		<div class="noteWrapper">' . $notes . '</div>
		';
	}
	return $notes;

}

// html header and footer
// email footer

function html_header($_title = "Wooster & Stock Administration", $_body = null, $_js = null)
{

	$_render = '
<html>
<head>
<title>' . $_title . '</title>
<link href="css/styles.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="js/global.js"></script>
<script type="text/javascript" language="javascript" src="js/googlefix.js"></script>
<script type="text/javascript" language="javascript" src="js/gen_validatorv2.js"></script>
<script type="text/javascript" language="javascript" src="js/scriptaculous/prototype.js"></script>
<script type="text/javascript" language="javascript" src="js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" language="javascript">
<!--
// Updates the title of the frameset if possible (ns4 does not allow this)
if (typeof(parent.document) != \'undefined\' && typeof(parent.document) != \'unknown\'
	&& typeof(parent.document.title) == \'string\') {
	parent.document.title = \'window.document.title\';
}

// Multiple drop-down function from http://www.quirksmode.org/js/options.html
' . $_js . '

function init(dd1) 	{
	optionTest = true;
	lgth = dd1.options.length;
	dd1.options[lgth] = null;
	if (dd1.options[lgth]) optionTest = false;
	}

// dd1 is the first drop down, dd2 is the second
function populate(dd1,dd2) 	{
	if (!optionTest) return;
	var box = dd1;
	var number = box.options[box.selectedIndex].value;
	if (!number) return;
	var list = thelist[number];
	var box2 = dd2;
	box2.options.length = 0;
	for(i=0;i<list.length;i+=2) {
		box2.options[i/2] = new Option(list[i],list[i+1]);
		}
	dd2.focus();
	}
-->
</script>
</head>
<body ' . $_body . ' bgcolor="#FFFFFF">
<div id="content">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td valign="top">
  <table cellpadding="5" width="100%" border="0">
  <tr>
  <td>

';

	return $_render;
}

$html_footer = '
  </td>
  </tr>
  </table>
</td>
</tr>
</table>
</div>
</body>
</html>
';

// create deal table, pre viewing shows CV status, post viewing shows feedback
function renderDealTable($_data, $app_id, $_feedback = null)
{

	if ($_feedback) {

		if (is_array($_data)) {
			$numRows = (count($_data));
			foreach ($_data AS $key => $val) {

				if (!$val["d2a_ord"]) {
					$val["d2a_ord"] = 1;
				}

				if ($val["d2a_feedback"] == "None") {
					$bgcol               = '#FFFFFF'; //'#FD7777';
					$val["d2a_feedback"] = 'Leave Feedback';
				} elseif ($val["d2a_feedback"] == "Negative") {
					$bgcol = '#FC9B9B'; //'#93AFF5';
				} elseif ($val["d2a_feedback"] == "Indifferent") {
					$bgcol = '#FFC750'; //'#93AFF5';
				} elseif ($val["d2a_feedback"] == "Positive") {
					$bgcol = '#8BE277'; //'#5EC346';
				}

				$_render .= '
<tr class="trOff ' . ($val['DIY'] !== 'None' ? $val['DIY'] . '-property' : '') . '" onMouseOver="trOver(this)" onMouseOut="trOut(this)" valign="top">
<td width="10">' . $val["d2a_ord"] . ':</td>
<td class="bold">' . $val["addr"] . '</td>
<td width="120" id="cvLinkTd"><div id="cvLink" style="background-color:' . $bgcol . '"><a href="appointment_feedback.php?d2a_id=' . $val["d2a_id"] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">' . $val["d2a_feedback"] . '</a></span></td>
<td width="70" nowrap="nowrap"><a href="/admin4/instruction/summary/id/' . $val["dea_id"] . '"><img src="/images/sys/admin/icons/edit-icon.png" border="0" alt="Edit/View this property" height="16" width="16"></a></td>
</tr>';
			}
		}
		$_render = '
<div id="results_table">
<table>
<tr>
<th colspan="2">Property</th><th colspan="2">Feedback</th></tr>
' . $_render . '</table>
</div>';

	} else {

		if (is_array($_data)) {
			$numRows = (count($_data));
			foreach ($_data AS $key => $val) {

				if (!$val["d2a_ord"]) {
					$val["d2a_ord"] = 1;
				}

				if ($val["d2a_cv"] == "Not Confirmed") {
					$bgcol = '#FC9B9B'; //'#FD7777';
				} elseif ($val["d2a_cv"] == "Message Left") {
					$bgcol = '#FFC750'; //'#93AFF5';
				} elseif ($val["d2a_cv"] == "Confirmed") {
					$bgcol = '#8BE277'; //'#5EC346';
				}

				$_render .= '
<tr class="trOff ' . ($val['DIY'] !== 'None' ? 'DIY-property' : '') . '" onMouseOver="trOver(this)" onMouseOut="trOut(this)" valign="top">
<td width="10">' . $val["d2a_ord"] . ':</td>
<td class="bold">' . $val["addr"] . '</td>
<td width="120" id="cvLinkTd"><div id="cvLink" style="background-color:' . $bgcol . '"><a href="appointment_confirm.php?d2a_id=' . $val["d2a_id"] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">' . $val["d2a_cv"] . '</a></span></td>
<td width="90" nowrap="nowrap">
<a href="/admin4/instruction/summary/id/' . $val["dea_id"] . '"><img src="/images/sys/admin/icons/edit-icon.png" border="0" alt="Edit/View this property" height="16" width="16"></a>
<a href="javascript:dealPrint(' . $val["dea_id"] . ');"><img src="/images/sys/admin/icons/print-icon.png" border="0" alt="Print this property" height="16" width="16"></a>
';

// disable first arrow
				if ($val["d2a_ord"] == 1) {
					$_render .= '<img src="/images/sys/admin/icons/arrow_up_sm_grey.gif" border="0" alt="Move Up" height="16" width="16">';
				} else {
					$_render .= '<a href="?do=reorder&app_id=' . $app_id . '&d2a_id=' . $val["d2a_id"] . '&cur=' . $val["d2a_ord"] . '&new=' . ($val["d2a_ord"] - 1) . '"><img src="/images/sys/admin/icons/arrow_up_sm.gif" border="0" alt="Move Up" height="16" width="16"></a>';
				}
// disable last arrow
				if ($count == ($numRows - 1)) {
					$_render .= '<img src="/images/sys/admin/icons/arrow_down_sm_grey.gif" border="0" alt="Move Down" height="16" width="16">';
				} else {
					$_render .= '<a href="?do=reorder&app_id=' . $app_id . '&d2a_id=' . $val["d2a_id"] . '&cur=' . $val["d2a_ord"] . '&new=' . ($val["d2a_ord"] + 1) . '"><img src="/images/sys/admin/icons/arrow_down_sm.gif" border="0" alt="Move Down" height="16" width="16"></a>';
				}
// prevent last deal from being deleted
				if ($numRows == 1) {
					$_render .= '<img src="/images/sys/admin/icons/cross_sm_grey.gif" border="0" width="16" height="16" hspace="1" alt="Remove from appointment" />';
				} else {
					$_render .= '<a href="?do=remove&app_id=' . $app_id . '&d2a_id=' . $val["d2a_id"] . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" width="16" height="16" hspace="1" alt="Remove from appointment" /></a>';
				}
				$_render .= '</td>
</tr>
';
				$count++;
			}

			$_render = '
<div id="results_table">
<table>
<tr>
<th colspan="2">Property</th><th>Status</th><th>&nbsp;</th></tr>
' . $_render . '</table>
</div>';
		}

	}
	return $_render;
}

// build table of linked clients (viewers, or vendors/landlords/tenants)
function renderViewerTable($_data, $app_id, $_readonly = null)
{

	if (is_array($_data)) {
		foreach ($_data AS $cli_id => $cli_name) {
			$_render .= '<tr><td height="20"><a href="/admin4/client/update/id/' . $cli_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">' . $cli_name . '</a></td><td align="right">';
			if (count($_data) == 1 || $_readonly) {
				$_render .= '';
			} else {
				$_render .= '<a href="?do=remove_client&app_id=' . $app_id . '&cli_id=' . $cli_id . '&return=' . urlencode('?' . $_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove ' . $cli_name . ' from this appointment"></a>';
			}
			$_render .= '</td></tr>';
		}
	}
	$_render = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Viewer(s)</td>
	<td>
	  <table width="200" cellpadding="0" cellspacing="0" border="0">
	  ' . $_render;

	if (!$_readonly) {
		$_render .= '
	    <tr>
		  <td height="20"><input type="button" value="Add Viewer" onClick="document.location.href = \'client_lookup.php?dest=add_client_to_appointment&app_id=' . $app_id . '&return=' . urlencode($_GET["searchLink"]) . '\';" class="button"></td>
	    </tr>';
	}
	$_render .= ' </table>
	</td>
  </tr>
</table>';
	return $_render;
}

// build table of linked clients (vendors/landlords/tenants)
function renderVendorTable($_data, $app_id, $_readonly = null)
{

	if (is_array($_data)) {
		foreach ($_data AS $cli_id => $cli_name) {
			$_render .= '<tr><td height="20"><a href="client_edit.php?cli_id=' . $cli_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">' . $cli_name . '</a></td><td align="right">';
			if (count($_data) == 1 || $_readonly) {
				$_render .= '';
			} else {
				$_render .= '<a href="?do=remove_client&app_id=' . $app_id . '&cli_id=' . $cli_id . '&return=' . urlencode('?' . $_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove ' . $cli_name . ' from this appointment"></a>';
			}
			$_render .= '</td></tr>';
		}
	}
	$_render = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Vendor/Contact</td>
	<td>
	  <table width="200" cellpadding="0" cellspacing="0" border="0">
	  ' . $_render;

	if (!$_readonly) {
		$_render .= '
	    <tr>
		  <td height="20"><input type="button" value="Add Vendor/Contact" onClick="document.location.href = \'client_lookup.php?dest=add_client_to_appointment&app_id=' . $app_id . '&return=' . urlencode($_GET["searchLink"]) . '\';" class="button"></td>
	    </tr>';
	}
	$_render .= '</table>
	</td>
  </tr>
</table>';
	return $_render;
}

// build table of linked clients (vendors/landlords/tenants)
function renderContactTable($_data, $app_id, $_readonly = null)
{

	if (is_array($_data)) {
		foreach ($_data AS $con_id => $con_name) {
			$_render .= '<tr><td height="20"><a href="contact_edit.php?con_id=' . $con_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">' . $con_name . '</a></td><td align="right">';
			if (count($_data) == 1 || $_readonly) {
				$_render .= '';
			} else {
				$_render .= '<a href="?do=remove_contact&app_id=' . $app_id . '&con_id=' . $con_id . '&return=' . urlencode('?' . $_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove ' . $con_name . ' from this appointment"></a>';
			}
			$_render .= '</td></tr>';
		}
	}
	$_render = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Inspector(s)</td>
	<td>
	  <table width="300" cellpadding="0" cellspacing="0" border="0">
	  ' . $_render;

	if (!$_readonly) {
		$_render .= '
	    <tr>
		  <td height="20"><input type="button" value="Add Inspector" onClick="document.location.href = \'inspection_add.php?stage=inspector&app_id=' . $app_id . '&return=' . urlencode($_GET["searchLink"]) . '\';" class="button"></td>
	    </tr>';
	}
	$_render .= '</table>
	</td>
  </tr>
</table>';
	return $_render;
}

function renderAttendeeTable($_data, $app_id, $_readonly = null)
{

	if ($_readonly) {
		if ($_data) {
			foreach ($_data AS $use_id => $use_name) {
				$_render .= '<tr><td height="20">' . $use_name . '</td></tr>';
			}

			$_render = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Attendee(s)</td>
	<td>
	  <table width="200" cellpadding="0" cellspacing="0" border="0">
	  ' . $_render . '
	  </table>
	</td>
  </tr>
</table>';
		}

	} else {

		// get a list of possible attendees
		$attendees = '<option value="">Add Attendee</option>' . "\n";
		$sql       = "SELECT use_id,CONCAT(use_fname,' ',use_sname) AS use_name FROM user WHERE use_status = 'Active'
	ORDER BY CONCAT(use_fname,' ',use_sname) ASC";
		$_result   = mysql_query($sql);
		if (!$_result) {
			die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $sql . "</pre>");
		}
		while ($row = mysql_fetch_array($_result)) {
			$attendees .= '<option value="' . $row["use_id"] . '">' . $row["use_name"] . '</option>' . "\n";
		}

		if ($_data) {
			asort($_data);
			foreach ($_data AS $use_id => $use_name) {
				$_render .= '<tr><td height="20">' . $use_name . '</td><td align="right">';
				$_render .= '<a href="?do=remove_user&app_id=' . $app_id . '&use_id=' . $use_id . '&return=' . urlencode('?' . $_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove ' . $use_name . ' from this appointment"></a>';
				$_render .= '</td></tr>';

			}
		}

		$_render = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Attendee(s)</td>
	<td>
	  <table width="200" cellpadding="0" cellspacing="0" border="0">
	  ' . $_render;
		if (!$_readonly) {
			$_render .= '
	    <tr>
		  <td height="20"><select name="attendee" onChange="javascript:addUserToAppointment(' . $app_id . ',document.forms[0].attendee.options[document.forms[0].attendee.selectedIndex].value,\'' . urlencode($_GET['searchLink']) . '\')">' . $attendees . '</select><!--<input type="button" value="Add Neg" onClick="javascript:addUserToAppointment(' . $app_id . ',document.forms[0].neg.options[document.forms[0].neg.selectedIndex].value)" class="button">--></td>
	    </tr>';
		}
		$_render .= '
	  </table>
	</td>
  </tr>
</table>';
	}

	return $_render;
}

// clients linked to an offer
// build table of linked clients (viewers, or vendors/landlords/tenants)
function renderClientOfferTable($_data, $off_id, $_readonly = null)
{

	if (is_array($_data)) {
		foreach ($_data AS $cli_id => $cli_name) {
			$_render .= '<tr><td height="20"><a href="client_edit.php?cli_id=' . $cli_id . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">' . $cli_name . '</a></td><td align="right">';
			if (count($_data) == 1 || $_readonly) {
				$_render .= '';
			} else {
				$_render .= '<a href="?do=remove_client&off_id=' . $off_id . '&cli_id=' . $cli_id . '&return=' . urlencode('?' . $_SERVER['QUERY_STRING']) . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Remove ' . $cli_name . ' from this appointment"></a>';
			}
			$_render .= '</td></tr>';
		}
	}
	$_render = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
    <td class="label" valign="top" width="158">Client(s)</td>
	<td>
	  <table width="200" cellpadding="0" cellspacing="0" border="0">
	  ' . $_render;

	if (!$_readonly) {
		$_render .= '
	    <tr>
		  <td height="20"><input type="button" value="Add Client" onClick="document.location.href = \'client_lookup.php?dest=add_client_to_offer&off_id=' . $off_id . '&return=' . urlencode($_GET["searchLink"]) . '\';" class="button"></td>
	    </tr>';
	}
	$_render .= ' </table>
	</td>
  </tr>
</table>';
	return $_render;
}

$photograph_titles = array(
		''              => '',
		'Exterior'      => 'Exterior',
		'Reception 1'   => 'Reception 1',
		'Reception 2'   => 'Reception 2',
		'Reception 3'   => 'Reception 3',
		'Reception 4'   => 'Reception 4',
		'Dining Room'   => 'Dining Room',
		'Dining Area'   => 'Dining Area',
		'Kitchen'       => 'Kitchen',
		'Hall'          => 'Hall',
		'Stairs'        => 'Stairs',
		'Landing'       => 'Landing',
		'Bedroom 1'     => 'Bedroom 1',
		'Bedroom 2'     => 'Bedroom 2',
		'Bedroom 3'     => 'Bedroom 3',
		'Bedroom 4'     => 'Bedroom 4',
		'Bedroom 5'     => 'Bedroom 5',
		'Bedroom 6'     => 'Bedroom 6',
		'Bedroom 7'     => 'Bedroom 7',
		'Bedroom 8'     => 'Bedroom 8',
		'Bathroom 1'    => 'Bathroom 1',
		'Bathroom 2'    => 'Bathroom 2',
		'Bathroom 3'    => 'Bathroom 3',
		'Bathroom 4'    => 'Bathroom 4',
		'Wet Room'      => 'Wet Room',
		'Shower Room'   => 'Shower Room',
		'En suite'      => 'En suite',
		'W.C.'          => 'W.C.',
		'Mezzanine'     => 'Mezzanine',
		'Utility Room'  => 'Utility Room',
		'Dressing Room' => 'Dressing Room',
		'Play Room'     => 'Play Room',
		'Study'         => 'Study',
		'Conservatory'  => 'Conservatory',
		'Garden'        => 'Garden',
		'Grounds'       => 'Grounds',
		'Rear'          => 'Rear',
		'Balcony'       => 'Balcony',
		'Roof Terrace'  => 'Roof Terrace',
		'View'          => 'View',
		'Basement'      => 'Basement',
		'Cellar'        => 'Cellar',
		'Store'         => 'Store',
		'Loft'          => 'Loft',
		'Attic'         => 'Attic',
		'Gym'           => 'Gym',
		'Studio'        => 'Studio',
		'Shop'          => 'Shop',
		'Garage'        => 'Garage',
		'Room'          => 'Room',
		'Feature'       => 'Feature'
);
$floorplan_titles  = array(
		''                   => '',
		'Lower Ground Floor' => 'Lower Ground Floor',
		'Ground Floor'       => 'Ground Floor',
		'Upper Ground Floor' => 'Upper Ground Floor',
		'First Floor'        => 'First Floor',
		'Second Floor'       => 'Second Floor',
		'Third Floor'        => 'Third Floor',
		'Fourth Floor'       => 'Fourth Floor',
		'Fifth Floor'        => 'Fifth Floor',
		'Sixth Floor'        => 'Sixth Floor',
		'Seventh Floor'      => 'Seventh Floor',
		'Eigth Floor'        => 'Eigth Floor',
		'Ninth Floor'        => 'Ninth Floor',
		'Tenth Floor'        => 'Tenth Floor',
		'Evelenth Floor'     => 'Evelenth Floor',
		'Twelfth Floor'      => 'Twelfth Floor',
		'Thirteenth Floor'   => 'Thirteenth Floor',
		'Fouteenth Floor'    => 'Fouteenth Floor',
		'Fifteenth Floor'    => 'Fifteenth Floor',
		'Sixteenth Floor'    => 'Sixteenth Floor',
		'Seventeenth Floor'  => 'Seventeenth Floor',
		'Eighteenth Floor'   => 'Eighteenth Floor',
		'Nineteenth Floor'   => 'Nineteenth Floor',
		'Twentieth Floor'    => 'Twentieth Floor',
		'Mezzanine'          => 'Mezzanine',
		'Attic'              => 'Attic',
		'Garage'             => 'Garage',
		'Out Building'       => 'Out Building',
		'Cellar/Basement'    => 'Cellar/Basement',
		'Garden'             => 'Garden',
		'Land'               => 'Land',
		'Entire Plot'        => 'Entire Plot',
		'Roof Terrace'       => 'Roof Terrace'
);
?>