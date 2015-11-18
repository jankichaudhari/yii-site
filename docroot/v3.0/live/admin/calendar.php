<?php
require_once(dirname(__FILE__) . "/inx/global.inc.php");
require_once(WS_PATH_COMPONENTS . "/Calendar/Calendar.php");
require_once(WS_PATH_COMPONENTS . "/Calendar/Month/Weekdays.php");
require_once(WS_PATH_COMPONENTS . "/Calendar/Day.php");

// all allowed GET variables to pass to iframe
if ($_GET["y"]) {
	$to_pass["y"] = $_GET["y"];
}
if ($_GET["m"]) {
	$to_pass["m"] = $_GET["m"];
}
if ($_GET["d"]) {
	$to_pass["d"] = $_GET["d"];
}
if ($_GET["branch"]) {
	$to_pass["branch"] = $_GET["branch"];
}
if ($_GET["type"]) {
	$to_pass["type"] = $_GET["type"];
}
if ($_GET["user"]) {
	$to_pass["user"] = $_GET["user"];
}
if ($_GET["highlight"]) {
	$to_pass["highlight"] = $_GET["highlight"];
}
if ($_GET["app_id"]) {
	$to_pass["app_id"] = $_GET["app_id"];
}
if ($to_pass["app_id"]) {

	$sql = "SELECT app_id,app_start,calendarID FROM appointment WHERE app_id = " . $to_pass["app_id"] . " LIMIT 1";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage() . $sql);
	}
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {

			// we need start y,m,d and time 2006-10-25 16:00:00
			$split          = explode(" ", $row["app_start"]);
			$app_start_date = explode("-", $split[0]);

			$to_pass['y'] = $app_start_date[0];
			$to_pass['m'] = $app_start_date[1];
			$to_pass['d'] = $app_start_date[2];

			$app_start_time    = explode(":", $split[1]);
			$to_pass["jumpto"] = $app_start_time[0];

			$to_pass["app_highlight"] = $row["app_id"];

			$to_pass["branch"] = $row["calendarID"];

			unset($to_pass["app_id"]);
		}
	}
}


if ($_GET["do"] == "zoom") {
	$_SESSION["zoom"] = $_GET["zoom"];
}

if (!isset($to_pass['y'])) $to_pass['y'] = date('Y');
if (!isset($to_pass['m'])) $to_pass['m'] = date('n');
if (!isset($to_pass['d'])) $to_pass['d'] = date('j');

$today = $to_pass['y'] . '/' . $to_pass['m'] . '/' . $to_pass['d'];


$Month = new Calendar_Month_Weekdays($to_pass['y'], $to_pass['m']);
$Day = new Calendar_Day($to_pass['y'], $to_pass['m'], $to_pass['d']);

$currentDateShown = strtotime($to_pass['y'] . '-' . $to_pass['m'] . '-' . $to_pass['d']);


$Yesterday = array(
	'd' => date('d', $Day->prevDay($currentDateShown)),
	'm' => date('m', $Day->prevDay($currentDateShown)),
	'y' => date('Y', $Day->prevDay($currentDateShown))
);
$Tomorrow = array(
	'd' => date('d', $Day->nextDay($currentDateShown)),
	'm' => date('m', $Day->nextDay($currentDateShown)),
	'y' => date('Y', $Day->nextDay($currentDateShown))
);


$selection = array($Day);

$ThisMonth = date("F");

// go to next year if current month is dec
if ($Month->thisMonth() == 12) {
	$navNextYear = $Month->nextYear();
} else {
	$navNextYear = $Month->thisYear();
}
if ($Month->thisMonth() == 1) {
	$navPrevYear = $Month->prevYear();
} else {
	$navPrevYear = $Month->thisYear();
}


if (!$to_pass["branch"]) {
	$to_pass["branch"] = $_SESSION["auth"]["use_branch"];
}

foreach ($to_pass AS $key => $val) {
	$query_string .= $key . '=' . $val . '&amp;';
	$hidden_fields .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
}

// url of the calendar frame
$calendar_url = GLOBAL_URL . 'calendar_day.php?' . $query_string;
$today = $to_pass['y'] . '/' . $to_pass['m'] . '/' . $to_pass['d'];


// get list of branches
$sql = "SELECT bra_id,bra_title,bra_colour, COUNT(app_id) AS apps
FROM branch
LEFT JOIN appointment ON bra_id = appointment.calendarID AND (app_start >= '" . $today . " 00:00:00' AND app_start <= '" . $today . " 23:59:59') AND appointment.app_status = 'Active'
WHERE
bra_status = 'Active' AND bra_id != 4
GROUP BY bra_id
ORDER BY bra_id";
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage() . $sql);
}
$numRows = $q->numRows();

if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {

		// counting individual appointment types
		$sql2 = "SELECT app_type
		FROM appointment
		WHERE appointment.calendarID = " . $row["bra_id"] . " AND (app_start >= '" . $today . " 00:00:00' AND app_start <= '" . $today . " 23:59:59') AND appointment.app_status = 'Active'";
		$q2   = $db->query($sql2);
		while ($row2 = $q2->fetchRow()) {
			if ($row2["app_type"] == 'Viewing' || $row2["app_type"] == 'Valuation') {
				$counter1++;
			} else {
				$counter2++;
			}
		}

		$render_branch .= '<option value="' . $row["bra_id"] . '"';
		if ($to_pass["branch"] == $row["bra_id"]) {
			$render_branch .= ' selected';
		}
		$render_branch .= ' style="background-color: #' . $row["bra_colour"] . ';">' . str_replace(
			array("Sydenham Sales"),
			array("Sydenham"),
			$row["bra_title"]
		) . ' (' . intval($counter1) . ', ' . intval($counter2) . ')</option>' . "\n";

		unset($counter1, $counter2);
	}
}

$render_type = '<option value="">All Types</option>' . "\n";
foreach (db_enum("appointment", "app_type", "array") AS $key => $val) {
	$render_type .= '<option value="' . $key . '"';
	if ($to_pass["type"] == $key) {
		$render_type .= ' selected';
	}
	$render_type .= '>' . $val . '</option>' . "\n";
}


$render_user = '<option value="">All Users</option>' . "\n";
$sql = "SELECT use_id,CONCAT(use_fname,' ',use_sname) AS use_name,use_colour,COUNT(app_id) AS apps
FROM user
LEFT JOIN appointment ON use_id = appointment.app_user AND calendarID = " . $to_pass["branch"] . " AND (app_start >= '" . $today . " 00:00:00' AND app_start <= '" . $today . " 23:59:59') AND app_status = 'Active'
WHERE use_status = 'Active'
GROUP BY use_id
ORDER by use_name";
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage() . $sql);
}
$numRows = $q->numRows();
$render_user .= '<option value="' . $_SESSION["auth"]["use_id"] . '">My Appointments</option>' . "\n";
while ($row = $q->fetchRow()) {
	if ($row["apps"] > 0) {
		$render_user .= '<option value="' . $row["use_id"] . '"';
		if ($to_pass["user"] == $row["use_id"]) {
			$render_user .= ' selected';
		}
		$render_user .= '>' . $row["use_name"] . ' (' . $row["apps"] . ')</option>' . "\n";
	}
}
//////////////////////////////////////////////
// ALL DAY APPOINTMENTS
//////////////////////////////////////////////

// get any all day appointment for current date
if ($to_pass["user"]) {
	//$sql_inner = "app_user = ".$to_pass["user"]." AND";
}
$sql = "SELECT
appointment.*,
GROUP_CONCAT(DISTINCT CONCAT(note.not_blurb)  ORDER BY note.not_date DESC SEPARATOR '<br />') AS note,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name,user.use_colour,
GROUP_CONCAT(DISTINCT CONCAT(attendee.use_fname,' ',attendee.use_sname) ORDER BY use2app.u2a_id ASC SEPARATOR '<br />') AS app_attendees

FROM appointment
LEFT JOIN user ON app_user = user.use_id
LEFT JOIN note ON not_row = app_id AND not_type = 'appointment' AND not_status = 'Active'
LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id
WHERE
app_start >= '" . $today . " 00:00:00' AND app_start <= '" . $today . " 23:59:59' AND
calendarID = " . $to_pass["branch"] . " AND
$sql_inner
app_allday = 'Yes'
AND appointment.app_status = 'Active'
GROUP BY app_id
";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage() . $sql);
}
$numRowsAllDay = $q->numRows();
if ($numRowsAllDay !== 0) {

	while ($row = $q->fetchRow()) {
		$alldayarray[] = $row;
	}

}

// $i needs to start above last $alldayarray or we lost some of the apps (sometimes)
$i = count($alldayarray) + 1;


// get any exchange and completion dates assigned to current branch and add to all day array

$sql = "SELECT
dea_id,dea_exchdate,dea_compdate,dea_branch,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',LEFT(pro_postcode,4)) AS pro_addr,
CONCAT(user.use_fname,' ',user.use_sname) AS use_name,user.use_colour,user.use_id
FROM deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN offer ON deal.dea_id = offer.off_deal AND off_status = 'Accepted'
LEFT JOIN user ON offer.off_neg = user.use_id
WHERE dea_branch = " . $to_pass["branch"] . " AND
((dea_exchdate >= '" . $today . " 00:00:00' AND dea_exchdate <= '" . $today . " 23:59:59') OR
(dea_compdate >= '" . $today . " 00:00:00' AND dea_compdate <= '" . $today . " 23:59:59'))
";
//echo $sql;
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage() . $sql);
}
$numRowsExchComp = $q->numRows();
if ($numRowsExchComp) {
	while ($row = $q->fetchRow()) {

		if ($row["dea_exchdate"]) {
			if (strtotime($row["dea_exchdate"]) >= strtotime($today . ' 00:00:00') && strtotime($row["dea_exchdate"]) <= strtotime($today . ' 23:59:59')) {
				$app_type = 'Exchange';
			}
		}
		// if exch and comp are on the same day, comp will display
		if ($row["dea_compdate"]) {
			if (strtotime($row["dea_compdate"]) >= strtotime($today . ' 00:00:00') && strtotime($row["dea_compdate"]) <= strtotime($today . ' 23:59:59')) {
				$app_type = 'Completion';
			}
		}

		$exchcomp[$i] = array(
			'app_id'      => $row["dea_id"], // substitue app_id with dea_id to link to deal_summary
			'app_type'    => $app_type,
			'app_subject' => $row["pro_addr"],
			'app_user'    => $row["use_id"],
			'calendarID'  => $row["dea_branch"],
			'use_name'    => $row["use_name"],
			'use_colour'  => $row["use_colour"]
		);

		$i++;
	}
}
$allDayDivHeight = 18;

$perRow = 4;
$counter = 1;
$width = round(100 / $perRow);

$allDayDivHeight = (ceil(($numRowsAllDay + $numRowsExchComp) / $perRow) * $allDayDivHeight);

//print_r($exchcomp);

$alldayapps = '<table width="100%"><tr>';
if ($exchcomp && $alldayarray) {
	$alldayarray = $exchcomp + $alldayarray;
} elseif ($exchcomp && !$alldayarray) {
	$alldayarray = $exchcomp;
}


if ($alldayarray) {

	foreach ($alldayarray AS $key => $row) {

		if ($row["app_type"] == 'Note' && $row["app_notetype"]) {
			$row["app_type"] = $row["app_notetype"];
		}

		// trim subject
		if (strlen($row["app_subject"]) > 25) {
			$subject = substr(format_overdiv($row["app_subject"]), 0, 25) . "...";
		} else {
			$subject = format_overdiv($row["app_subject"]);
		}

		if ($row["note"]) {
			$note = '<br /><br />' . format_overdiv($row["note"]);
		} else {
			$note = '';
		}
		if ($row["app_attendees"]) {
			$app_attendees = '<br /><br />Attendees:<br />' . trim(preg_replace("/\([a-z0-9\ ]+\)/", "", $row["app_attendees"])) . ''; // this removes the use_id in parenthesis
		} else {
			$app_attendees = '';
		}

		// variable style values from database, rest is in css class
		$alldayapps .= '<td class="allDayEvent" style="border-color:#' . $row["use_colour"] . '; width:' . $width . '%;" ';
		if ($row["app_type"] == 'Exchange' || $row["app_type"] == 'Completion') {
			$alldayapps .= 'onClick="javascript:window.location.href=\'deal_summary.php?dea_id=' . $row["app_id"] . '&amp;searchLink=calendar.php?' . urlencode(replaceQueryString($_SERVER['QUERY_STRING'], 'app_highlight')) . '\'" ';
		} else {
			$alldayapps .= 'onClick="javascript:window.location.href=\'appointment_edit.php?app_id=' . $row["app_id"] . '&amp;searchLink=calendar.php?' . urlencode(replaceQueryString($_SERVER['QUERY_STRING'], 'app_highlight')) . '\'" ';
		}

		// js actions
		$alldayapps .= 'onMouseOver="return overlib(\'' . format_overdiv($row["app_subject"]) . '<br /><br />' . format_overdiv($row["use_name"] . $app_attendees) . '\',CAPTION,\'' . $row["app_type"] . '\');" onMouseOut="nd();" onMouseDown="nd();"';
		// text displayed within appointment
		$alldayapps .= '><strong>' . $row["app_type"] . '</strong> ' . $subject . '</td>' . "\n"; //' - '.$row["use_name"].

		if ($counter % $perRow == 0) {
			$alldayapps .= '
			</tr>
			<tr>';
		}
		$counter++;

	}

	$alldayapps .= '</tr></table>';

	if ($alldayapps) {
		$alldayapps = '
<div id="calAllDayContainer">
  ' . $alldayapps . '
</div>

';
	}
} else {
	$alldayapps = ''; // remove this table, it screws up whole layout
}
//////////////////////////////////////////////
// END ALL DAY APPOINTMENTS
//////////////////////////////////////////////

$render_left = '
<div id="calFilter">
<form method="GET" name="filter">
<select name="branch" onChange="document.filter.submit()">
' . $render_branch . '</select>
<select name="type" onChange="document.filter.submit()">
' . $render_type . '</select>
<select name="user" onChange="document.filter.submit()">
' . $render_user . '</select>';

foreach ($to_pass AS $key => $val) {
	if ($key == 'branch' || $key == 'type' || $key == 'user') {
	} else {
		$render_left .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
	}
}
$render_left .= '
</form>
</div>
<div id="calTitleDate"><a href="' . $calendar_url . '" target="cal_iframe" title="Click to refresh calendar">' . date('l, jS \of F Y', $Day->getTimeStamp()) . '</a></div>
' . $alldayapps . '
<div class="calendar-container" id="calendar-container"><script type="text/javascript">writeCal(\'' . $calendar_url . '\');</script></div>
';


// month browse and navigation
$render_right = '
  <div id="calMonthBrowse">
	<table align="center" cellspacing="0" cellpadding="0">
	<tr>
	<td class="calMonthSelect">
	<a href="?' . replaceQueryStringArray($query_string, array(
															  'd', 'm', 'y', 'app_highlight', 'jumpto'
														 )) . '&amp;y=' . $navPrevYear . '&amp;m=' . $Month->prevMonth() . '&amp;d=' . $Month->thisDay() . '" title="Previous Month">&laquo;</a>
	</td>
	<td colspan="5" class="calMonthSelect">
	  <form method="GET" name="monthBrowse">
		<select name="m" style="width:50px" onChange="document.monthBrowse.submit();">';
foreach ($months_short AS $number => $title) {
	$render_right .= '<option value="' . $number . '"';
	if ($number == $to_pass['m']) {
		$render_right .= ' selected';
	}
	$render_right .= '>' . $title . '</option>' . "\n";
}
$render_right .= '
		</select>
		<select name="y" style="width:50px" onChange="document.monthBrowse.submit();">';
for ($_i = 2006; $_i <= (date('Y') + 10); $_i++) {
	$render_right .= '<option value="' . $_i . '"';
	if ($_i == $to_pass['y']) {
		$render_right .= ' selected';
	}
	$render_right .= '>' . $_i . '</option>' . "\n";
}
$render_right .= '
		</select>
		';
foreach ($to_pass AS $key => $val) {
	if ($key !== 'm' && $key !== 'y') {
		$render_right .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . "\n";
	}
}
$render_right .= '
	  </form>
	  </td>
	<td class="calMonthSelect">
	<a href="?' . replaceQueryStringArray($query_string, array(
															  'd', 'm', 'y', 'app_highlight', 'jumpto'
														 )) . '&amp;y=' . $navNextYear . '&amp;m=' . $Month->nextMonth() . '&amp;d=' . $Month->thisDay() . '" title="Next Month">&raquo;</a>
	</td>
	</tr>
	<tr>
	<th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th>
	</tr>
	';

$Month->build($selection);
while ($Day = $Month->fetch()) {

	// embiggun the current day
	if ($Day->thisDay() == date('j') && $Day->thisMonth() == date('m') && $Day->thisYear() == date('Y')) {
		$display_day = '<strong>' . $Day->thisDay() . '</strong>';
	} else {
		$display_day = $Day->thisDay();
	}

	if ($Day->isFirst()) {
		$render_right .= "<tr>\n";
	}

	if ($Day->isEmpty()) {
		$render_right .= '<td class="calTdEmpty"></td>' . "\n";
	} elseif ($Day->isSelected()) {
		$render_right .= '<td class="calTdSelect"><a href="?' . replaceQueryStringArray($query_string, array(
																											'd', 'm', 'y', 'app_highlight', 'jumpto'
																									   )) . '&amp;y=' . $Day->thisYear() . '&amp;m=' . $Day->thisMonth() . '&amp;d=' . $Day->thisDay() . '">' . $display_day . '</a></td>' . "\n";
	} else {
		$render_right .= '<td class="calTdDay"><a href="' . $_SERVER['PHP_SELF'] . '?' . replaceQueryStringArray($query_string, array(
																																	 'd', 'm', 'y', 'app_highlight', 'jumpto'
																																)) . '&amp;y=' . $Day->thisYear() . '&amp;m=' . $Day->thisMonth() . '&amp;d=' . $Day->thisDay() . '">' . $display_day . '</a></td>' . "\n";
	}
	if ($Day->isLast()) {
		$render_right .= '</tr>' . "\n";
	}
}

$render_right .= '
	<tr>
	<td colspan="7" class="calDaySelect">
		<a href="?' . replaceQueryStringArray($query_string, array(
																  'd', 'm', 'y', 'app_highlight', 'jumpto'
															 )) . '&amp;y=' . $Yesterday['y'] . '&amp;m=' . $Yesterday['m'] . '&amp;d=' . $Yesterday['d'] . '" title="Previous Day">&laquo;</a> &nbsp;
		<a href="?' . replaceQueryStringArray($query_string, array(
																  'd', 'm', 'y', 'app_highlight', 'jumpto'
															 )) . '&amp;y=' . date('Y') . '&amp;m=' . date('m') . '&amp;d=' . date('j') . '">Today</a> &nbsp;
		<a href="?' . replaceQueryStringArray($query_string, array(
																  'd', 'm', 'y', 'app_highlight', 'jumpto'
															 )) . '&amp;y=' . $Tomorrow['y'] . '&amp;m=' . $Tomorrow['m'] . '&amp;d=' . $Tomorrow['d'] . '" title="Next Day">&raquo;</a>
	</td>
	</tr>
	</table>
	</div>
	<br />
	';

// caledar quick add
foreach (db_enum("appointment", "app_type", "array") AS $key => $val) {
	$render_type_q .= '<option value="' . $key . '"';
	$render_type_q .= '>' . $val . '</option>' . "\n";
}
$render_right .= '
	<div id="calQuickAdd">
	<form name="quickAdd" action="appointment_add.php"  method="post">
	<table>
	  <tr>
		<th>Create Appointment</th>
	  </tr>
	  <tr>
		<td>
		<input type="hidden" name="branch" value="' . (isset($_GET['branch']) && $_GET['branch'] ? $_GET['branch'] : '') . '">
		<select name="app_type">
		' . $render_type_q . '
		</select>
		<input type="hidden" id="date" name="date" value="' . padzero($to_pass['d']) . '/' . padzero($to_pass['m']) . '/' . $to_pass['y'] . '">
		<input type="submit" value="Go" class="btn">
		</td>
	  </tr>
	  <tr>
		<td></td>
	  </tr>
	</table>
	</form>
	</div>';

$xrender_right .= '
	<div id="calQuickAdd">
	<form name="quickSearch" action="appointment_search.php">
	<table>
	  <tr>
		<th>Search Appointments</th>
	  </tr>
	  <tr>
		<td>
		<input type="text" name="keyword">
		<input type="submit" value="Go" class="btn">
		<input type="hidden" name="action" value="advanced_search">
		<input type="hidden" name="branch" value="' . $to_pass["branch"] . '">
		<input type="hidden" name="date_from" value="' . date('d/m/Y') . '">
		<input type="hidden" name="date_to" value="' . date('d/m/Y') . '">
		<!--<input type="hidden" name="searchLink" value="' . $_SERVER['PHP_SELF'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '">-->
		</td>
	  </tr>
	  <tr>
		<td class="smallForm"><a href="appointment_search.php">Advanced Search</a></td>
	  </tr>
	</table>
	</form>
	</div>';

// today at a glance
// show number of appointments, number of cancelled/deleted (with link)
$countActive = 0;
$countCancelled = 0;
$countDeleted = 0;
$sql = "SELECT
	app_status
	FROM appointment
	WHERE
	app_start >= '" . $today . " 00:00:00' AND app_start <= '" . $today . " 23:59:59' AND
	calendarID = " . $to_pass["branch"] . "";
$q = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage() . $sql);
}
while ($row = $q->fetchRow()) {
	if ($row["app_status"] == 'Active') {
		$countActive++;
	} elseif ($row["app_status"] == 'Cancelled') {
		$countCancelled++;
	} elseif ($row["app_status"] == 'Deleted') {
		$countDeleted++;
	}
}


$render_right .= '
	<div id="calOverview">
	<table>
	  <tr>
		<th>Day Overview</th>
	  </tr>
	  <tr>
		<td><a href="appointment_search.php?action=advanced_search&status=Active&branch=' . $to_pass["branch"] . '&date_from=' . $to_pass['d'] . '/' . $to_pass['m'] . '/' . $to_pass['y'] . '&date_to=' . $to_pass['d'] . '/' . $to_pass['m'] . '/' . $to_pass['y'] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">Active: ' . $countActive . '</a></td>
	  </tr>
	  <tr>
		<td><a href="appointment_search.php?action=advanced_search&status=Cancelled&branch=' . $to_pass["branch"] . '&date_from=' . $to_pass['d'] . '/' . $to_pass['m'] . '/' . $to_pass['y'] . '&date_to=' . $to_pass['d'] . '/' . $to_pass['m'] . '/' . $to_pass['y'] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">Cancelled: ' . $countCancelled . '</a></td>
	  </tr>
	  <tr>
		<td><a href="appointment_search.php?action=advanced_search&status=Deleted&branch=' . $to_pass["branch"] . '&date_from=' . $to_pass['d'] . '/' . $to_pass['m'] . '/' . $to_pass['y'] . '&date_to=' . $to_pass['d'] . '/' . $to_pass['m'] . '/' . $to_pass['y'] . '&searchLink=' . $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']) . '">Deleted: ' . $countDeleted . '</a></td>
	  </tr>
	</table>
	<p><a href="javascript:calendarPrint(\'' . str_replace("_day", "_print", $calendar_url) . '\');"><img src="/images/sys/admin/icons/print-icon.png">Print</a></p>
	</div>
	';

$render_right .= '</div>';


$render = '
<div id="calRight">' . $render_right . '</div>
<div id="calLeft">' . $render_left . '</div>
';


$onLoad = $onResize = 'writeCalHeight(\'cal_iframe\',\'' . intval($allDayDivHeight) . '\');';
$js = '
// fix for calendar in IE6 only
if (document.all&&document.getElementById&&navigator.appVersion.indexOf(\'MSIE 6\')>=0) {
	document.write(\'<link rel="stylesheet" href="css/ie6.css" type="text/css" />\');
	}
';

$page = new HTML_Page2($page_defaults);
$page->setTitle("Calendar");
$page->addStyleSheet(getDefaultCss());
$page->addStyleSheet(getCalendarCss());
$page->addScriptDeclaration($js);
$page->addScript('js/global.js');
$page->addScript(GLOBAL_URL . 'js/overlib/overlibmws.js');
$page->setBodyAttributes(array('onResize' => $onResize)); //'onLoad'=>$onLoad,
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content_cal">');
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->addBodyContent('<script type="text/javascript"> window.onload = ' . $onLoad . ' </script>');
//$page->addBodyContent('<!--'.print_r($alldayarray,true));
ob_start();
$page->display();
$v = ob_get_clean();
echo $v;

?>
