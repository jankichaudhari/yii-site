<?php
require_once("inx/global.inc.php");
require_once("Calendar/Calendar.php");
require_once("Calendar/Month/Weekdays.php");
require_once("Calendar/Day.php");

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
/*
if ($_GET["jumpto"]) {
	$to_pass["jumpto"] = $_GET["jumpto"];
	}
*/
// jump to and highlight an appointment
if ($to_pass["app_id"]) {

	$sql = "SELECT app_id,app_start,calendarID FROM appointment WHERE app_id = ".$to_pass["app_id"]." LIMIT 1";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	$numRows = $q->numRows();
	if ($numRows !== 0) {
		while ($row = $q->fetchRow()) {

			// we need start y,m,d and time 2006-10-25 16:00:00
			$split = explode(" ",$row["app_start"]);
			$app_start_date = explode("-",$split[0]);

			$to_pass['y'] = $app_start_date[0];
			$to_pass['m'] = $app_start_date[1];
			$to_pass['d'] = $app_start_date[2];

			$app_start_time = explode(":",$split[1]);
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

$today = $to_pass['y'].'/'.$to_pass['m'].'/'.$to_pass['d'];



$Month = & new Calendar_Month_Weekdays($to_pass['y'],$to_pass['m']);
$Day = & new Calendar_Day($to_pass['y'],$to_pass['m'],$to_pass['d']);

$currentDateShown = strtotime($to_pass['y'].'-'.$to_pass['m'].'-'.$to_pass['d']);


$Yesterday = array(
	'd'=>date('d',$Day->prevDay($currentDateShown)),
	'm'=>date('m',$Day->prevDay($currentDateShown)),
	'y'=>date('Y',$Day->prevDay($currentDateShown))
	);
$Tomorrow = array(
	'd'=>date('d',$Day->nextDay($currentDateShown)),
	'm'=>date('m',$Day->nextDay($currentDateShown)),
	'y'=>date('Y',$Day->nextDay($currentDateShown))
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

foreach ($to_pass AS $key=>$val) {
	$query_string .= $key.'='.$val.'&amp;';
	$hidden_fields .= '<input type="hidden" name="'.$key.'" value="'.$val.'" />'."\n";
	}

// url of the calendar frame
$calendar_url = 'calendar_day.php?'.$query_string;

/*
if ($to_pass["jumpto"]) {
	$calendar_url .= 'anchor='.$to_pass["jumpto"];//.'#'.($to_pass["jumpto"]-1);
	} else {
		// only jump to current hour if viewing current day
		if (date('l, jS of F, Y',$Day->getTimeStamp()) == date('l, jS of F, Y')) {
			$calendar_url .= 'anchor='.date('H'); //.'#'.(date('G')-1);
			}
		}
*/
$today = $to_pass['y'].'/'.$to_pass['m'].'/'.$to_pass['d'];



// get list of branches
$sql = "SELECT bra_id,bra_title,bra_colour, COUNT(app_id) AS apps
FROM branch
LEFT JOIN appointment ON bra_id = appointment.calendarID AND (app_start >= '".$today." 00:00:00' AND app_start <= '".$today." 23:59:59') AND appointment.app_status = 'Active'
WHERE
bra_status = 'Active'
GROUP BY bra_id
ORDER BY bra_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$render_branch .= '<option value="'.$row["bra_id"].'"';
		if ($to_pass["branch"] == $row["bra_id"]) {
			$render_branch .= ' selected';
			}
		$render_branch .= ' style="background-color: #'.$row["bra_colour"].';">'.$row["bra_title"].' ('.$row["apps"].')</option>'."\n";
		}
	}

$render_type = '<option value="">All Types</option>'."\n";
foreach(db_enum("appointment","app_type","array") AS $key=>$val) {
	$render_type .= '<option value="'.$key.'"';
	if ($to_pass["type"] == $key) {
		$render_type .= ' selected';
		}
	$render_type .= '>'.$val.'</option>'."\n";
	}



$render_user = '<option value="">All Users</option>'."\n";
$sql = "SELECT use_id,CONCAT(use_fname,' ',use_sname) AS use_name,use_colour,COUNT(app_id) AS apps
FROM user
LEFT JOIN appointment ON use_id = appointment.app_user AND calendarID = ".$to_pass["branch"]." AND (app_start >= '".$today." 00:00:00' AND app_start <= '".$today." 23:59:59') AND app_status = 'Active'
WHERE use_status = 'Active'
GROUP BY use_id
ORDER by use_name";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
$render_user .= '<option value="'.$_SESSION["auth"]["use_id"].'">My Appointments</option>'."\n";
while ($row = $q->fetchRow()) {
	if ($row["apps"] > 0) {
		$render_user .= '<option value="'.$row["use_id"].'"';
		if ($to_pass["user"] == $row["use_id"]) {
			$render_user .= ' selected';
			}
		$render_user .= '>'.$row["use_name"].' ('.$row["apps"].')</option>'."\n";
		}
	}
/*
if (!$_SESSION["zoom"]) {
	$_SESSION["zoom"] = 2;
	}
if ($_SESSION["zoom"] == 1) {
	$next_zoom = 2;
	} elseif ($_SESSION["zoom"] == 2) {
	$next_zoom = 4;
	} elseif ($_SESSION["zoom"] == 4) {
	$next_zoom = 1;
	}
*/
$render = '
<div id="calTop">
<div id="calFilter">
<form method="GET" name="filter">
<select name="branch" onChange="document.filter.submit()" style="width:140px">
'.$render_branch.'
</select>
<select name="type" onChange="document.filter.submit()" style="width:85px">
'.$render_type.'
</select>
<select name="user" onChange="document.filter.submit()" style="width:125px">
'.$render_user.'
</select>';

foreach ($to_pass AS $key=>$val) {
if ($key == 'branch' || $key == 'type' || $key == 'user') {
	} else {
	$render .= '<input type="hidden" name="'.$key.'" value="'.$val.'" />'."\n";
	}
}
$render .= '
</form>
</div>

<div id="calTitleDate"><a href="'.$calendar_url.'" target="cal_iframe" title="Click to refresh calendar">'.date('l, jS of F y',$Day->getTimeStamp()).'</a></div>
</div>
';

//////////////////////////////////////////////
// ALL DAY APPOINTMENTS
//////////////////////////////////////////////

// get any all day appointment for current date

if ($to_pass["user"]) {
	$sql_inner = "app_user = ".$to_pass["user"]." AND";
	}
$sql = "SELECT
appointment.*,
GROUP_CONCAT(DISTINCT CONCAT(note.not_blurb)  ORDER BY note.not_date DESC SEPARATOR '<br />') AS note,
CONCAT(use_fname,' ',use_sname) AS use_name,use_colour
FROM appointment
LEFT JOIN user ON app_user = user.use_id
LEFT JOIN note ON not_row = app_id AND not_type = 'appointment' AND not_status = 'Active'
WHERE
app_start >= '".$today." 00:00:00' AND app_start <= '".$today." 23:59:59' AND
calendarID = ".$to_pass["branch"]." AND
$sql_inner
app_allday = 'Yes'
AND appointment.app_status = 'Active'
GROUP BY app_id
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRowsAllDay = $q->numRows();
if ($numRowsAllDay !== 0) {
	$allDayDivHeight = 19;

	$perRow = 5;
	$counter = 1;
	$width = round(100/$perRow);

	$allDayDivHeight = (ceil($numRowsAllDay/$perRow)*$allDayDivHeight);



	$alldayapps = '<table width="100%"><tr>';
	while ($row = $q->fetchRow()) {

		// trim subject
		if (strlen($row["app_subject"]) > 25) {
			$subject = substr($row["app_subject"],0,25)."...";
			} else {
			$subject = $row["app_subject"];
			}


		// variable style values from database, rest is in css class
		$alldayapps .= '<td style="white-space: nowrap;width:'.$width.'%;border:1px solid #'.$row["use_colour"].';border-left: 10px solid #'.$row["use_colour"].';background-color: #FFFFFF; cursor:pointer; font-size:9px; overflow: hidden;" ';
		//<div style="width:300px;border:1px solid #'.$row["use_colour"].';display:block;" ';  display:inline;  margin: 2px; border: 1px solid #'.$row["use_colour"].';
		// href targetted at parent of parent. remove hightlight var from querystring
		// parent.parent.frames[\'mainFrame\'].window.location.href=
		$alldayapps .= 'onClick="javascript:window.location.href=\'appointment_edit.php?app_id='.$row["app_id"].'&amp;searchLink=calendar.php?'.urlencode(replaceQueryString($_SERVER['QUERY_STRING'],'app_highlight')).'\'" ';
		// js actions
		$alldayapps .= 'onMouseOver="return overlib(\''.$row["app_subject"].'<br />'.format_overdiv($row["note"]).'<br /><br />'.$row["use_name"].'\',CAPTION,\''.$row["app_type"].'\');" onMouseOut="nd();" onMouseDown="nd();"';
		// text displayed within appointment
		$alldayapps .= '>'.$subject.'</td>'."\n"; //' - '.$row["use_name"].


		if($counter % $perRow == 0 ) {
		$alldayapps .=  '
		</tr>
		<tr>';
		}
	$counter++;


		}
	$alldayapps .= '</tr></table>';
	}



if ($alldayapps) {
	$render .= '

<div id="calAllDayContainer" style="height:'.$allDayDivHeight.'px">
  '.$alldayapps.'
</div>

';
	}


//////////////////////////////////////////////
// END ALL DAY APPOINTMENTS
//////////////////////////////////////////////



// month browse and navigation
$render .= '
<div id="calRight">
  <div id="calMonthBrowse">
	<table align="center">
	<tr>
	<th>
	<a href="?'.replaceQueryStringArray($query_string,array('d','m','y','app_highlight','jumpto')).'&amp;y='.$navPrevYear.'&amp;m='.$Month->prevMonth().'&amp;d='.$Month->thisDay().'" title="Previous Month">&laquo;</a>
	</th>
	<th colspan="5">
	  <form method="GET" name="monthBrowse">
		<select name="m" style="width:45px" onChange="document.monthBrowse.submit();">';
		foreach ($months_short AS $number=>$title) {
			$render .= '<option value="'.$number.'"';
			if ($number == $to_pass['m']) {
				$render .= ' selected';
				}
			$render .= '>'.$title.'</option>'."\n";
			}
		$render .= '
		</select>
		<select name="y" style="width:50px" onChange="document.monthBrowse.submit();">';
		for ($_i = 2000; $_i <= (date('Y')+10); $_i++) {
			$render .= '<option value="'.$_i.'"';
			if ($_i == $to_pass['y']) {
				$render .= ' selected';
				}
			$render .= '>'.$_i.'</option>'."\n";
			}
		$render .= '
		</select>
		';
		foreach ($to_pass AS $key=>$val) {
			if ($key !== 'm' && $key !== 'y') {
				$render .= '<input type="hidden" name="'.$key.'" value="'.$val.'" />'."\n";
				}
			}
		$render .= '
	  </form>
	  </th>
	<th>
	<a href="?'.replaceQueryStringArray($query_string,array('d','m','y','app_highlight','jumpto')).'&amp;y='.$navNextYear.'&amp;m='.$Month->nextMonth().'&amp;d='.$Month->thisDay().'" title="Next Month">&raquo;</a>
	</th>
	</tr>
	<tr>
	<th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th>
	</tr>
	';

	$Month->build($selection);
	while ( $Day = $Month->fetch() ) {

		// embiggun the current day
		if ($Day->thisDay() == date('j') && $Day->thisMonth() == date('m') && $Day->thisYear() == date('Y')) {
			$display_day = '<strong>'.$Day->thisDay().'</strong>';
			} else {
			$display_day = $Day->thisDay();
			}

		if ($Day->isFirst()) {
			$render .= "<tr>\n";
			}

		if ($Day->isEmpty()) {
			$render .= '<td class="calTdEmpty"></td>'."\n";
			}
		elseif ($Day->isSelected()) {
			$render .= '<td class="calTdSelect"><a href="?'.replaceQueryStringArray($query_string,array('d','m','y','app_highlight','jumpto')).'&amp;y='.$Day->thisYear().'&amp;m='.$Day->thisMonth().'&amp;d='.$Day->thisDay().'">'.$display_day.'</a></td>'."\n";
			}
		else {
			$render .= '<td><a href="'.$_SERVER['PHP_SELF'].'?'.replaceQueryStringArray($query_string,array('d','m','y','app_highlight','jumpto')).'&amp;y='.$Day->thisYear().'&amp;m='.$Day->thisMonth().'&amp;d='.$Day->thisDay().'">'.$display_day.'</a></td>'."\n";
			}
		if ($Day->isLast()) {
			$render .= '</tr>'."\n";
			}
		}

	$render .= '
	<tr>
	<th colspan="7">
		<a href="?'.replaceQueryStringArray($query_string,array('d','m','y','app_highlight','jumpto')).'&amp;y='.$Yesterday['y'].'&amp;m='.$Yesterday['m'].'&amp;d='.$Yesterday['d'].'" title="Yesterday">&laquo;</a> &nbsp;
		<a href="?'.replaceQueryStringArray($query_string,array('d','m','y','app_highlight','jumpto')).'&amp;y='.date('Y').'&amp;m='.date('m').'&amp;d='.date('j').'">Today</a> &nbsp;
		<a href="?'.replaceQueryStringArray($query_string,array('d','m','y','app_highlight','jumpto')).'&amp;y='.$Tomorrow['y'].'&amp;m='.$Tomorrow['m'].'&amp;d='.$Tomorrow['d'].'" title="Tomorrow">&raquo;</a>
	</th>
	</tr>
	</table>
	</div>
	';







	// caledar quick add
	foreach(db_enum("appointment","app_type","array") AS $key=>$val) {
		$render_type_q .= '<option value="'.$key.'"';
		$render_type_q .= '>'.$val.'</option>'."\n";
		}

	$render .='
	<div id="calQuickAdd">
	<form name="quickAdd" action="appointment_add.php">
	<table>
	  <tr>
		<th>Create Appointment</th>
	  </tr>
	  <tr>
		<td>
		<select name="app_type" style="width: 110px">
		'.$render_type_q.'
		</select>
		<input type="submit" value="Go">
		</td>
	  </tr>
	  <tr>
		<td></td>
	  </tr>
	</table>
	</form>
	</div>';

	$render .='
	<div id="calQuickAdd">
	<form name="quickSearch" action="appointment_search.php">
	<table>
	  <tr>
		<th>Search Appointments</th>
	  </tr>
	  <tr>
		<td>
		<input type="text" name="keyword" style="width: 105px">
		<input type="submit" value="Go">
		<input type="hidden" name="action" value="advanced_search">
		<input type="hidden" name="branch" value="'.$to_pass["branch"].'">
		<input type="hidden" name="date_from" value="'.date('d/m/Y').'">
		<input type="hidden" name="date_to" value="'.date('d/m/Y').'">
		<input type="hidden" name="searchLink" value="'.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'">
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
	app_start >= '".$today." 00:00:00' AND app_start <= '".$today." 23:59:59' AND
	calendarID = ".$to_pass["branch"]."";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
	while ($row = $q->fetchRow()) {
		if ($row["app_status"] == 'Active') {
			$countActive++;
			} elseif ($row["app_status"] == 'Cancelled') {
			$countCancelled++;
			} elseif ($row["app_status"] == 'Deleted') {
			$countDeleted++;
			}
		}



	$render .= '
	<div id="calOverview">
	<table>
	  <tr>
		<th>Today\'s Appointments</th>
	  </tr>
	  <tr>
		<td><a href="appointment_search.php?action=advanced_search&status=Active&branch='.$to_pass["branch"].'&date_from='.date('d/m/Y').'&date_to='.date('d/m/Y').'&searchLink='.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'">Active: '.$countActive.'</a></td>
	  </tr>
	  <tr>
		<td><a href="appointment_search.php?action=advanced_search&status=Cancelled&branch='.$to_pass["branch"].'&date_from='.date('d/m/Y').'&date_to='.date('d/m/Y').'&searchLink='.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'">Cancelled: '.$countCancelled.'</a></td>
	  </tr>
	  <tr>
		<td><a href="appointment_search.php?action=advanced_search&status=Deleted&branch='.$to_pass["branch"].'&date_from='.date('d/m/Y').'&date_to='.date('d/m/Y').'&searchLink='.$_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']).'">Deleted: '.$countDeleted.'</a></td>
	  </tr>
	</table>
	</div>
	';

$render .='</div>';


// write the calendar iframe
$render .= '
<div id="calContainer">
  <script type="text/javascript">writeCal(\''.$calendar_url.'\');</script>
</div>
';






$onLoad = 'writeCalHeight(\'cal_iframe\',\''.$allDayDivHeight.'\');';

$onResize = 'writeCalHeight(\'cal_iframe\',\''.$allDayDivHeight.'\');';



$page = new HTML_Page2($page_defaults);
$page->setTitle("Calendar");
$page->addStyleSheet('css/cal.css');
$page->addScript('js/global.js');
$page->addScript(GLOBAL_URL.'js/overlib/overlibmws.js');
$page->setBodyAttributes(array('onResize'=>$onResize));//'onLoad'=>$onLoad,
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content_cal">');
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->addBodyContent('<script type="text/javascript"> window.onLoad = '.$onLoad.' </script>');

$page->display();
?>
