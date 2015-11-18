<?php
require_once("inx/global.inc.php");

// same as calendar_day, but changed for printing (different style sheet, no links, no autoreload etc)


// calendar stuff
require_once 'Calendar/Calendar.php';
require_once 'Calendar/Month/Weekdays.php';
require_once 'Calendar/Day.php';

if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('n');
if (!isset($_GET['d'])) $_GET['d'] = date('j');

$Month = & new Calendar_Month_Weekdays($_GET['y'],$_GET['m']);
$Day = & new Calendar_Day($_GET['y'],$_GET['m'],$_GET['d']);
$selection = array($Day);



// get first second of the day
$daybegin = strtotime($_GET["y"].'/'.$_GET["m"].'/'.$_GET["d"].' 00:00:00');
// add $default_workingday_start hours
$daybegin = ($daybegin+($default_workingday_start*60*60));
$endhour = ($default_workingday_end-$default_workingday_start) + 1;
$dayend = $daybegin+($endhour*60*60);

// zoom out enough to fit whole day on single page
$zoom = '1.2';


// construct sql
if ($_GET["type"]) {
	$q[] = "app_type = '".$_GET["type"]."' AND ";
	$return["type"] = $_GET["type"];
	}
if ($_GET["user"]) {
	$q[] = "app_user = '".$_GET["user"]."' AND ";
	$return["user"] = $_GET["user"];
	}
if ($_GET["branch"]) {
	$q[] = "calendarID = '".$_GET["branch"]."' AND ";
	$return["branch"] = $_GET["branch"];
	// get branch colour for background
	$sql_colour = "SELECT bra_colour FROM branch WHERE bra_id = ".$_GET["branch"];
	$result_colour = $db->query($sql_colour);
	if (DB::isError($result_colour)) {  die("db error: ".$result_colour->getMessage().$sql_colour); }
	while ($row = $result_colour->fetchRow()) {
		$calendar_bg_colour = $row["bra_colour"];
		}
	}
if ($_GET["status"]) {
	$q[] = "app_status = '".$_GET["status"]."' AND ";
	$return["status"] = $_GET["status"];
	}

if ($_GET["keyword"]) {
	$return["keyword"] = $_GET["keyword"];
	#$keyword = str_replace(" ",",",$_GET["keyword"]);
	$keywords = explode(",",$keyword);
	foreach ($keywords AS $keyword) {
		$keyword = trim($keyword);
		$keyword_sql .= "property.pro_addr1 LIKE '%$keyword%' OR property.pro_addr2 LIKE '%$keyword%' OR property.pro_addr3 LIKE '%$keyword%' OR
		property.pro_addr4 LIKE '%$keyword%' OR property.pro_addr5 LIKE '%$keyword%' OR property.pro_postcode LIKE '%$keyword%' OR
		client.cli_fname LIKE '%$keyword%' OR client.cli_sname LIKE '%$keyword%' OR
		CONCAT(client.cli_fname,' ',client.cli_sname)  LIKE '%$keyword%' OR ";
		}
	$keyword_sql = "(".remove_lastchar($keyword_sql,"OR").") AND ";
	$q[] = $keyword_sql;
	}

if ($_GET["date_from"]) {
	$return["date_from"] = $_GET["date_from"];
	// split up the dates, and re-format to mysql friendly 0000-00-00
	$split = explode("/",$_GET["date_from"]);
	#$q[] = "app_start >= '2006-11-09 00:00:00' AND ";
	$q[] = "app_start >= '".$split[2]."-".$split[1]."-".$split[0]." 00:00:00' AND ";
	}

if ($_GET["date_to"]) {
	$return["date_to"] = $_GET["date_to"];
	$split = explode("/",$_GET["date_to"]);
	#$q[] = "app_start <= '2006-11-09 23:59:59' AND ";
	$q[] = "app_start <= '".$split[2]."-".$split[1]."-".$split[0]." 00:00:00' AND ";
	}

// single day only, using calendar arguments
$today = $_GET['y'].'/'.$_GET['m'].'/'.$_GET['d'];

$q[] = "app_start >= '".$today." 00:00:00' AND app_start <= '".$today." 23:59:59' AND ";


if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
	$return["orderby"] = $orderby;
	} else {
	$orderby = 'app_start';
	}
if ($_GET['direction']) {
	$direction = $_GET['direction'];
	} else {
	$direction = 'ASC';
	}

if (!$q) {
	$errors[] = 'Please enter some search criteria';
	echo error_message($errors);
	exit;
	}
#$returnLink = '?'.http_build_query($return);
$searchLink = $_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']);
foreach ($q AS $statement){
	$sql .= $statement." ";
	}
$sql = remove_lastchar($sql,"AND");
$sql = remove_lastchar($sql,"OR");


/*

GROUP_CONCAT(DISTINCT CONCAT('<span class=\"use_col_small\" style=\"background-color: #',attendee.use_colour,';\"><img src=\"img/blank.gif\"></span> ',attendee.use_fname,' ',attendee.use_sname,' (',attendee.use_id,')') ORDER BY use2app.u2a_id ASC SEPARATOR '<br>') AS app_attendees,

*/
// select client name and property address with unique id number in parenthesis to ensure all are displayed
// i.e. if there are two clients with the same name, or two properties with the same display address, they will both show

#$sql2 = "SET GLOBAL group_concat_max_len = 2048";
#$q2 = $db->query($sql2);
#if (DB::isError($q2)) {  die("db error: ".$q2->getMessage().$sql2); }

$sql = "SELECT
appointment.*,ity_title,DATE_FORMAT(appointment.app_start, '%a %D %b<br>%H:%i') AS app_date,
pro_id,GROUP_CONCAT(DISTINCT CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,')') ORDER BY link_deal_to_appointment.d2a_ord ASC SEPARATOR '\n') AS pro_addr,
cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,' (',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR '<br>') AS cli_name,
attendee.use_id,
GROUP_CONCAT(DISTINCT CONCAT(attendee.use_fname,' ',attendee.use_sname) ORDER BY use2app.u2a_id ASC SEPARATOR '\n') AS app_attendees,
GROUP_CONCAT(DISTINCT CONCAT(link_deal_to_appointment.d2a_cv) SEPARATOR '|') AS d2a_cv,

GROUP_CONCAT(DISTINCT CONCAT(note.not_blurb)  ORDER BY note.not_date DESC SEPARATOR '<br />') AS note,


user.use_id,CONCAT(user.use_fname,' ',user.use_sname) AS use_name,user.use_colour,

GROUP_CONCAT(DISTINCT CONCAT(contact.con_fname,' ',contact.con_sname)  ORDER BY contact.con_fname DESC SEPARATOR '<br />') AS con_name

FROM appointment
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN itype ON appointment.app_subtype = ity_id

LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id

LEFT JOIN note ON not_row = app_id AND not_type = 'appointment' AND not_status = 'Active'

WHERE
$sql
AND appointment.app_status = 'Active'
AND appointment.app_allday = 'No'
GROUP BY appointment.app_id
ORDER BY $orderby $direction";

#echo $sql;
#exit;


$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {

		// make start time to nearest 15 minutes
		$start_time = date('i',strtotime($row["app_start"]));

		// calculate duration
		$duration = (strtotime($row["app_end"]) - strtotime($row["app_start"]))/60;



		if ($row["dea_marketprice"]) {
			$price = format_price($row["dea_marketprice"]).' (M)';
			} elseif ($row["dea_valueprice"] && !$row["dea_marketprice"]) {
			$price = format_price($row["dea_valueprice"]).' (V)';
			} else {
			$price = 'n/a';
			}
		$pro_addr = preg_replace("/\([a-z0-9\ ]+\)/", "", $row["pro_addr"]); // this removes the dea_id in parenthesis
		$cli_name = preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]); // this removes the cli_id in parenthesis
		if ($row["app_attendees"]) {
			$app_attendees = '<br><font size="1">'.trim(preg_replace("/\([a-z0-9\ ]+\)/", "", $row["app_attendees"])).'</font>'; // this removes the use_id in parenthesis
			}
		if ($row["use_colour"]) {
			$use_colour = '<span class="use_col" style="background-color: '.$row["use_colour"].';"><img src="/images/sys/admin/blank.gif"></span>&nbsp;';
			}


		$start = explode(" ",$row["app_start"]);
		$start = $start[1];

		$end = explode(" ",$row["app_end"]);
		$end = $end[1];


		$first_app = (strtotime($row["app_start"]) - $daybegin);
		$start_pixel = (($first_app/60)*$zoom);

		$divs[] = array(
			'id'=>$row["app_id"],
			'start_stamp'=>strtotime($row["app_start"]),
			'end_stamp'=>strtotime($row["app_end"]),
			'start_offset'=>(round_to_nearest($start_time,15 )*$zoom), // not used
			//'start'=>substr($start, 0, -3),
			//'start'=>date('g:i',strtotime(substr($start, 0, -3))),
			'start'=>date('H:i',strtotime(substr($start, 0, -3))),
			//'end'=>substr($end, 0, -3),
			//'end'=>date('g:ia',strtotime(substr($end, 0, -3))),
			'end'=>date('H:i',strtotime(substr($end, 0, -3))),
			'duration'=>$duration,
			'start_pixel'=>$start_pixel,
			'end_pixel'=>$start_pixel+($duration*$zoom), // multiply by $zoom becuase each minute = $zoom pixels
			'app_height'=>($duration*$zoom),
			'colour'=>$row["use_colour"],
			'type'=>$row["app_type"],
			'client'=>$cli_name,
			'addr'=>str_replace("  "," ",$pro_addr),
			'user'=>$row["use_name"],
			'contact'=>$row["con_name"],
			'attendees'=>$row["app_attendees"],
			'cv'=>$row["d2a_cv"],
			'subject'=>format_overdiv($row["app_subject"]),
			'notes'=>format_overdiv($row["note"]),
			'subtype'=>$row["ity_title"],
			'private'=>$row["app_private"],
			'bookedby'=>$row["app_bookedby"]
			);



		unset($app_attendees,$use_colour,$pro_addr,$cli_name,$price);
		}
	}

//print_r($divs);
// end sql






// need to loop every 15 minutes, consider not using Calendar class for this and going bespoke
$Day->build();
while ( $Hour = & $Day->fetch() ) {

	// show working hours only
	if (date('H',$Hour->getTimeStamp()) >= $default_workingday_start && date('H',$Hour->getTimeStamp()) <= $default_workingday_end) {

		// highlight current hour slot but only if viewing current day
		if (date('l, jS of F, Y',$Day->getTimeStamp()) == date('l, jS of F, Y')) {
			if (date('H',$Hour->getTimeStamp()) == date('H')) {
				$class = "calHourNow";
				} else {
				$class = "calHour";
				}
			} else {
			$class = "calHour";
			}

		// make the hour bar
		$hourBar .= '<div class="'.$class.'" style="height: '.((60*$zoom)-1).'px"><a name="'.date('H',$Hour->getTimeStamp()).'"></a>'.date('H:',$Hour->getTimeStamp()).''.date('i',$Hour->getTimeStamp()).'</div>'."\n";
		}
	}




/*
how do we match the divs:
if end if larger than start
if start of current is equal or larger than comparison start
current deal id is not equal to comparison id

this works except when an appointment that has ended, but caused a subsequent to be offset, then the current
div only see's one conflcting div therefore only offsets once

if more than two apps have the same start time, the offset dosent work properly

if an offset event is longer than a following event, the following event needs to NOT be offset as the
conflicting	appointment has already been offset

new idea (while sleeping 12/11/06)
Loop through the working day at 5 minutes intervals (which is absolutel minimum app length)
Do this with timestamps, adding (5*60) each time.
Build an array of the time slots, containing each app that starts in that time slot. If an app is already present (i.e.
the app hasnt reached end_pixel yet), offest new app to column 2, or 3, and so on.
This might just work mark!

maintain 2 arrays, startpoints and endpoints.
div ids stay in startpoints until the appointment has reached its end

*/

// default width of appointments
$width = $default_calendar_appointment_width;

$active = array();

$counter = 0;

// loop through whole day at 5 minute intervals, the current timestap is $interval
for ($interval = $daybegin; $interval <= $dayend; $interval+=$default_calendar_interval) {

	// loop through array to get appointments who's start time is in current interval
	if (is_array($divs)) {

		foreach($divs AS $div_id=>$div) {

			// create an array of active appointments: add appointment to array when it starts, remove when it ends
			if ( ($div["start_stamp"] >= $interval) && ($div["start_stamp"] < ($interval+$default_calendar_interval)) ){
				// add appointment to active array
				$active[] = $div_id;
				}
			// the current appointment ends in this interval
			if ( ($div["end_stamp"] >= $interval) && ($div["end_stamp"] < ($interval+$default_calendar_interval)) ){
				// remove appointment from active array
				unset($active[$div_id]);
				}

			// if there is an appointment in this time slot, process...
			if ( ($div["start_stamp"] >= $interval) && ($div["start_stamp"] < ($interval+$default_calendar_interval)) && in_array($div_id,$active) ) {

				// make sure column isnt in use, if it is find first free column
				for ($i=0; $i < count($divs); $i++) {
					if ( $divs[$i]['column'] == $column ) {
						// column is in use, need to find free column
						// get all currently used columns
						$used_columns = array();
						foreach($divs AS $column_check) {
							// if column key is in array, add to used_columns array
							if ($column_check["column"]) {
								$used_columns[] = $column_check["column"];
								}
							}
						// find first available free column by looping through numbers until one isnt matched with used_columns array
						for ($i2=1; $i2 < 50; $i2++) {
							if (!in_array($i2,$used_columns)) {
								$column = $i2;
								break;
								}
							}
						}
					}

				// bit of fiddling to get the position right
				$left = (($width*$column) - $width) + 42;



				// set current appointment's column value, this is used in subsequent apps to determine positioning
				$divs[$div_id]["column"] = $column;

				if ($column > $max_column) {
					$max_column = $column;
					}

				// highlight the app you just edited, and set scrolling properties
				if ($_GET["app_highlight"] == $div["id"]) {
					$class = "calEntryDivHL";
					if ($left > 700) { // don't jump right if appointment is probably already visible
						$jumpRight = ($left-$default_appointment_width);
						}
					$jumpDown = ($div["start_pixel"]-120);
					} else {
					$class = "calEntryDiv";
					}
				// echo "end: ".$div["end_stamp"]."<br>now: ".strtotime($date_mysql)."<br><br>";
				// check each of the d2a's in current appointment for cv status (appointments that have not yet ended only)
				if ($div["end_stamp"] >= strtotime($date_mysql)) {
					$cv_status = explode("|",$div["cv"]);
					if (in_array("Not Confirmed",$cv_status) || in_array("Message Left",$cv_status)) {
						$cv_alert = '<img src="/images/sys/admin/icons/error_sm.gif" align="absmiddle" width="7" height="8" alt="Not Confirmed" />';
						} else {
						$cv_alert = '';
						}
					}


				// need to handle content of different appointment types in different ways
				switch ($div["type"]):
				case "Viewing":
				$title = $cv_alert.'<strong>'.$div["type"].'</strong> '.$div["start"].'<br />'.$div["client"];
				$caption = $div["type"].' '.$div["start"].' to '.$div["end"].'<br />('.duration($div["duration"],'long').')';
				$overdiv = "Client:\n".str_replace("<br>","\n",$div["client"])."\n\nProperty:\n".$div["addr"]."\n\nNegotiator:\n".$div["user"];

				break;
				case "Valuation":
				$title = $cv_alert.'<strong>'.$div["type"].'</strong> '.$div["start"].'<br />'.$div["client"];
				$caption = $div["type"].' '.$div["start"].' to '.$div["end"].'<br />('.duration($div["duration"],'long').')';
				$overdiv = "Client:\n".str_replace("<br>","\n",$div["client"])."\n\nProperty:\n".$div["addr"]."\n\nValuer:\n".$div["user"];

				break;
				case "Production":
				$title = $cv_alert.'<strong>'.$div["type"].'</strong> '.$div["start"].'<br />'.$div["client"];
				$caption = $div["type"].' '.$div["start"].' to '.$div["end"].'<br />('.duration($div["duration"],'long').')';
				$overdiv = "Client:\n".str_replace("<br>","\n",$div["client"])."\n\nProperty:\n".$div["addr"]."\n\nUser:\n".$div["user"];

				break;
				case "Inspection":
				$title = $cv_alert.'<strong>'.$div["type"].'</strong> '.$div["start"].'<br />'.$div["addr"];
				$caption = $div["type"].' '.$div["start"].' to '.$div["end"].'<br />('.duration($div["duration"],'long').')';
				$overdiv = "Type:\n".$div["subtype"]."\n\nProperty:\n".$div["addr"]."\n\nContact:\n".$div["contact"]."\n\nNegotiator:\n".$div["user"];

				break;
				case "Meeting":
				$title = '<strong>'.$div["type"].'</strong> '.$div["start"].'<br />'.$div["subject"];
				$caption = $div["type"].' '.$div["start"].' to '.$div["end"].'<br />('.duration($div["duration"],'long').')';
				$overdiv = $div["subject"];
				if ($div["notes"]) {
					$overdiv .= "\n\n".$div["notes"];
					}

				break;
				case "Note":
				$title = '<strong>'.$div["type"].'</strong> '.$div["start"].'<br />'.$div["subject"];
				$caption = $div["type"].' '.$div["start"].' to '.$div["end"].'<br />('.duration($div["duration"],'long').')';
				$overdiv = $div["subject"]."\n\n".$div["notes"];

				break;
				case "Lunch":
				$title = '<strong>'.$div["type"].'</strong> '.$div["start"].'<br />'.$div["user"];
				$caption = $div["type"].' '.$div["start"].' to '.$div["end"].'<br />('.duration($div["duration"],'long').')';
				$overdiv = "User:\n".$div["user"];


				break;
				endswitch;


				if ($div["attendees"]) {
					$overdiv .= "\n\nAttendees:\n".$div["attendees"];
					}
				$overdiv = str_replace("\n","<br />",$overdiv);
				$overdiv = str_replace("'","\\'",$overdiv);
				$div_mouseover = 'return overlib(\''.$overdiv.'\',CAPTION,\''.$caption.'\');'; //calEventOver(this,'.($div["app_height"]-6).');
				$div_mouseout = 'nd();'; //calEventOut(this,'.($div["app_height"]-6).');

				// write the div
				if ($div["app_height"] < 24) {
					$app_height = 24;
					} else {
					$app_height = ($div["app_height"]-6);
					}

				// if neg is unnassigned, show special background image
				if (!$div["user"]) {
					$special_background = '  background-image:url(img/anibg.gif)';
					$div["colour"] = 'FFFFFF';
					} else {
					$special_background = '';
					}

				// private appointments are only visible to bookedby
				//if ($div["private"] == 'Yes' && $_SESSION["auth"]["use_id"] <> $div["bookedby"]) {



				//	} else {
					// variable style values from database, rest is in css class
					$render .= '  <div id="app'.$div["id"].'" class="'.$class.'" style="position: absolute; height: '.($app_height).'px; left: '.$left.'px; top:'.($div["start_pixel"]).'px; width:'.($width-18).'px;  border: 1px solid #'.$div["colour"].'; border-left: 10px solid #'.$div["colour"].'; z-index:1; overflow: hidden;'.$special_background.'" ';

					// href targetted at parent of parent. remove hightlight var from querystring
					// parent.parent.frames[\'mainFrame\'].window.location.href=
					// $render .= 'onClick="javascript:parent.window.location.href=\'appointment_edit.php?app_id='.$div["id"].'&amp;searchLink=calendar.php?'.urlencode(replaceQueryString($_SERVER['QUERY_STRING'],'app_highlight')).'\'" ';
					// js actions
					$render .= 'onMouseOver="'.$div_mouseover.'" onMouseOut="'.$div_mouseout.'" onMouseDown="nd();">'."\n";
					// text displayed within appointment
					$render .= $title."\n".'  </div>'."\n";
				//	}
				}

			// the current appointment ends in this interval
			if ( ($div["end_stamp"] >= $interval) && ($div["end_stamp"] < ($interval+$default_calendar_interval)) ){
				// remove column reference from master array
				unset($divs[$div_id]["column"]);
				}
			}
		}
		unset($left,$column);
		$counter++;
	}





$js = "

var theDiv = null;
var theHeight = null;

function calEventOver(lnk,newHeight) {
	theDiv = lnk;
	theHeight = newHeight;
	window.setTimeout(\"calEventOver2()\", 1000);
	}
function calEventOver2() {
	theDiv.style.overflow = 'visible';
	theDiv.style.zIndex = '1000';
	theDiv.style.height = theHeight;
	}


	";

/*
// no longer using anchor as it causes and extra "back" in history
if ($_GET["anchor"]) {
	$onLoad .= 'location.hash=\''.padzero(($_GET["anchor"]-1)).'\';'."\n";
	}
*/

// today, and no jumpdown (i.e. no highlighted app) jumps to current hour
if (!$jumpDown && ($_GET["d"] == date('d'))) {
	$jumpDown = (((date('H')-$default_workingday_start)*120)-120);
	}
if (!$jumpDown) {
	$jumpDown = '0';
	}
if (($jumpRight-$width) < 0) {
	$jumpRight = '0';
	} else {
	$jumpRight = ($jumpRight-$width);
	}

// scroll to active appointment (or current hour) minus 120 to show previous hour
$onLoad .= '
	window.print();
	';
//window.focus();


$js_footer = '<script type="text/javascript">
// <!--

window.onLoad = '.$onLoad.'

// -->
</script>';



$page = new HTML_Page2($page_defaults);
$page->setTitle('Calendar');
$page->addStyleSheet(GLOBAL_URL.'css/styles_print.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addScript(GLOBAL_URL.'js/overlib/overlibmws.js');
//$page->setBodyAttributes(array('style'=>'background-color: #'.$calendar_bg_colour.'; background-image: url(\'/images/sys/admin/calendar_bg.gif\');background-repeat: repeat')); //'onLoad'=>$onLoad,
$page->addBodyContent('<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>');
#$page->addBodyContent('<div id="loading" style="width:100%; text-align:center; padding-top:200px"><h1><img src="/images/sys/admin/ajax-loader.gif" /> Loading</h1></div>');
$page->addBodyContent('<div id="calendar">'); //style="display: none;"
$page->addBodyContent('<div style="height:'.((($default_workingday_end-$default_workingday_start)+1)*(60*$zoom)).'px; width:50px; position: absolute">'."\n".$hourBar.'</div>'."\n");
$page->addBodyContent('<div style="height:'.((($default_workingday_end-$default_workingday_start)+1)*(60*$zoom)).'px;">'."\n".$render.'</div>'."\n");
$page->addBodyContent('</div>');
$page->addBodyContent($js_footer);


$page->display();


?>