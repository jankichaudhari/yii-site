<?php
require_once("inx/global.inc.php");
/*
Appointment Search, queries the appointment table and links to client, deal, etc
Some appointment types have different search methods, e.g. meetings are not related to properties (and so on)

Organise into parts that relate to any appointment, and those that are specific, to minimise code

improve date range search, add a specific "daterange" type to form.class
add time element to form class (validator), joiing parts together before validating

*/

if (!$_GET["stage"]) {
	$stage = 1;
} else {
	$stage = $_GET["stage"];
}

// start a new page
$page = new HTML_Page2($page_defaults);
switch ($stage) {
###########################################################
# stage 1 - search
###########################################################
	case 1:

		if (!$_GET["action"]) {
			/*
   if (!$_GET["date_from"]) {
	   $_GET["date_from"] = date('d/m/Y'); //23/11/2006
	   }
   if (!$_GET["date_to"]) {
	   $_GET["date_to"] = date('d/m/Y');
	   }
   */

			if ($_GET["type"] == 'Note') {
				$notetype_display = "";
			} else {
				$notetype_display = "none";
			}

			$formData1 = array(
				'keyword'  => array(
					'type'      => 'text',
					'label'     => 'Keyword(s)',
					'value'     => $_GET["keyword"],
					'attributes'=> array('class'=> 'addr'),
					'tooltip'   => 'Seperate multiple keywords with commas'
				),
				'branch'   => array(
					'type'      => 'select_branch_2',
					'label'     => 'Branch',
					'value'     => $_GET["branch"],
					'attributes'=> array('class'=> 'medium')
				),
				'user'     => array(
					'type'      => 'select_user',
					'label'     => 'User',
					'value'     => $_GET["user"],
					'attributes'=> array('class'=> 'medium'),
					'tooltip'   => 'Lead user only (does not include attendees)',
					'options'   => array(''=> 'Any')
				),
				'type'     => array(
					'type'      => 'select',
					'label'     => 'Appointment Type',
					'group'     => 'Appointment Type',
					'value'     => $_GET["type"],
					//'default'=>'Viewing',
					'options'   => join_arrays(array(array(''=> 'Any'), db_enum("appointment", "app_type", "array"))),
					//'options'=>join_arrays(array(array(''=>'Any'),db_enum("appointment","app_type","array"))),
					'attributes'=> array('class'   => 'medium',
										 'onChange'=> 'controlAppointmentType(this,\'type\');')
				),
				'notetype' => array(
					'type'         => 'select',
					'label'        => 'Sub-Type',
					'group'        => 'Appointment Type',
					'last_in_group'=> 1,
					'value'        => $_GET["notetype"],
					//'default'=>'Viewing',
					'options'      => db_enum("appointment", "app_notetype", "array"),
					//'options'=>join_arrays(array(array(''=>'Any'),db_enum("appointment","app_type","array"))),
					'attributes'   => array('style'=> 'display:' . $notetype_display)
				),
				'date_from'=> array(
					'type'      => 'datetime',
					'label'     => 'Date (from)',
					'value'     => $_GET["date_from"],
					'attributes'=> array('class'=> 'medium')
				),
				'date_to'  => array(
					'type'      => 'datetime',
					'label'     => 'Date (to)',
					'value'     => $_GET["date_to"],
					'attributes'=> array('class'=> 'medium')
				),
				'status'   => array(
					'type'      => 'select',
					'label'     => 'Status',
					'value'     => $_GET["status"],
					'options'   => db_enum("appointment", "app_status", "array"),
					'attributes'=> array('class'=> 'medium')
				)
			);

			$form = new Form();

			$form->addForm("", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "stage", "", "1");
			$form->addField("hidden", "action", "", "advanced_search");

			$formName = 'form1';
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Search Calendar</div>');
			$form->addHtml('<div id="' . $formName . '">');
			$form->addData($formData1, $_GET);
			$form->addHtml($form->addDiv($form->makeField("submit", "", "", "Search", array('class'=> 'submit'))));
			$form->addHtml("</div>\n");
			$form->addHtml("</fieldset>\n");

			if (!$_GET["viewForm"]) {
				$viewForm = 1;
			}
			$additional_js = '
if (!previousID) {
	var previousID = "form' . $viewForm . '";
	}
';

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => urldecode($_GET["searchLink"])),
				'search'=> array('title'=> 'Search Calendar',
								 'label'=> 'Search Calendar',
								 'link' => 'appointment_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Search Calendar");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addScript('js/CalendarPopup.js');
			$page->addScriptDeclaration($additional_js);
			$page->setBodyAttributes(array('onLoad'=> $onLoad));
			$page->addScriptDeclaration('document.write(getCalendarStyles());var popcaldate_from = new CalendarPopup("popCalDivdate_from");popcaldate_from.showYearNavigation();var popcaldate_to = new CalendarPopup("popCalDivdate_to");popcaldate_to.showYearNavigation(); ');
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
			$page->display();

			exit;

		} else { // if form is submitted

// construct sql
			if ($_GET["type"]) {
				$q[]            = "app_type = '" . $_GET["type"] . "' AND ";
				$return["type"] = $_GET["type"];
			}
// only include in query when note is selected as type
			if ($_GET["notetype"] && $_GET["type"] == 'Note') {
				$q[]                = "app_notetype = '" . $_GET["notetype"] . "' AND ";
				$return["notetype"] = $_GET["notetype"];
			}
			if ($_GET["status"]) {
				$q[]              = "app_status = '" . $_GET["status"] . "' AND ";
				$return["status"] = $_GET["status"];
			}
			if ($_GET["keyword"]) {
				$return["keyword"] = $_GET["keyword"];
				// special deal id search is prefixed with hat symbol
				if ($_GET["keyword"]{0} == "^") {
					$keyword_sql .= "deal.dea_id = '" . substr($_GET["keyword"], 1) . "' OR ";
					// special client id search is prefixed with tilde symbol
				} elseif ($_GET["keyword"]{0} == "~") {
					$keyword_sql .= "client.cli_id = '" . substr($_GET["keyword"], 1) . "' OR ";

				} else {
					$keywords = explode(",", $_GET['keyword']);
					foreach ($keywords AS $keyword) {
						$keyword = trim($keyword);
						$keyword_sql .= "
			appointment.app_subject LIKE '%$keyword%' OR appointment.app_type LIKE '%$keyword%' OR appointment.app_subtype LIKE '%$keyword%' OR
			deal.dea_id = 'keyword' OR
			property.pro_addr1 LIKE '%$keyword%' OR property.pro_addr2 LIKE '%$keyword%' OR property.pro_addr3 LIKE '%$keyword%' OR
			property.pro_addr4 LIKE '%$keyword%' OR property.pro_addr5 LIKE '%$keyword%' OR property.pro_postcode LIKE '%$keyword%' OR
			client.cli_fname LIKE '%$keyword%' OR client.cli_sname LIKE '%$keyword%' OR
			CONCAT(client.cli_fname,' ',client.cli_sname)  LIKE '%$keyword%' OR
			contact.con_fname LIKE '%$keyword%' OR contact.con_sname LIKE '%$keyword%' OR
			CONCAT(contact.con_fname,' ',contact.con_sname)  LIKE '%$keyword%' OR
			company.com_title LIKE '%$keyword%' OR ";
					}
				}
				$keyword_sql = "(" . remove_lastchar($keyword_sql, "OR") . ") AND ";
				$q[]         = $keyword_sql;
			}

			if ($_GET["date_from"]) {
				$return["date_from"] = $_GET["date_from"];
				// split up the dates, and re-format to mysql friendly 0000-00-00
				$split = explode("/", $_GET["date_from"]);
				$q[]   = "app_start >= '" . $split[2] . "-" . $split[1] . "-" . $split[0] . " 00:00:00' AND ";
			}

			if ($_GET["date_to"]) {
				$return["date_to"] = $_GET["date_to"];
				$split             = explode("/", $_GET["date_to"]);
				$q[]               = "app_start <= '" . $split[2] . "-" . $split[1] . "-" . $split[0] . " 23:59:59' AND ";
			}

			if ($_GET["branch"]) {
				$q[]              = "calendarID = '" . $_GET["branch"] . "' AND ";
				$return["branch"] = $_GET["branch"];
			}
			if ($_GET["user"]) {
				$q[]            = "app_user = '" . $_GET["user"] . "' AND ";
				$return["user"] = $_GET["user"];
			}

			if ($_GET["orderby"]) {
				$orderby           = $_GET["orderby"];
				$return["orderby"] = $orderby;
			} else {
				$orderby = 'app_start';
			}
			if ($_GET['direction']) {
				$direction = $_GET['direction'];
			} else {
				$direction = 'DESC';
			}

			if (!$q) {
				$errors[] = 'Please enter some search criteria';
				echo error_message($errors);
				exit;
			}
			if ($_GET["searchLink"]) {
				$returnLink = $_GET["searchLink"];
			} else {
				$returnLink = '?' . http_build_query($return);
			}
			$searchLink = $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']);
			foreach ($q AS $statement) {
				$sql .= $statement . " ";
			}
			$sql = remove_lastchar($sql, "AND");
			$sql = remove_lastchar($sql, "OR");

// select client name and property address with unique id number in parenthesis to ensure all are displayed
// i.e. if there are two clients with the same name, or two properties with the same display address, they will both show

//$sql2 = "SET GLOBAL group_concat_max_len = 2048";
//$q2 = $db->query($sql2);
//if (DB::isError($q2)) {  die("db error: ".$q2->getMessage().$sql2); }

			$sql = "SELECT
appointment.*,ity_title,DATE_FORMAT(appointment.app_start, '%a %D %b %y<br>%H:%i') AS app_date,
pro_id,GROUP_CONCAT(DISTINCT CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4),' (',dea_id,')') ORDER BY link_deal_to_appointment.d2a_ord ASC SEPARATOR '<br>') AS pro_addr,
bra_id,bra_title,
cli_id,GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,' (',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR '<br>') AS cli_name,
con_id,GROUP_CONCAT(DISTINCT CONCAT(con_fname,' ',con_sname,' (',con_id,')') ORDER BY contact.con_id ASC SEPARATOR '<br>') AS con_name,
not_blurb,
attendee.use_id,
GROUP_CONCAT(DISTINCT CONCAT('<span class=\"use_col_small\" style=\"background-color: #',attendee.use_colour,';\"><img src=\"img/blank.gif\" width=\"8\" height=\"8\"></span> ',attendee.use_fname,' ',attendee.use_sname,' (',attendee.use_id,')') ORDER BY use2app.u2a_id ASC SEPARATOR '<br>') AS app_attendees,

user.use_id,CONCAT(user.use_fname,' ',user.use_sname) AS use_name,user.use_colour

FROM appointment
LEFT JOIN cli2app ON appointment.app_id = cli2app.c2a_app
LEFT JOIN client ON cli2app.c2a_cli = client.cli_id
LEFT JOIN link_deal_to_appointment ON appointment.app_id = link_deal_to_appointment.d2a_app
LEFT JOIN deal ON link_deal_to_appointment.d2a_dea = deal.dea_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN calendar ON appointment.calendarID = calendar.bra_id
LEFT JOIN use2app ON appointment.app_id = use2app.u2a_app
LEFT JOIN user AS attendee ON use2app.u2a_use = attendee.use_id
LEFT JOIN user ON appointment.app_user = user.use_id
LEFT JOIN con2app ON appointment.app_id = con2app.c2a_app
LEFT JOIN contact ON con2app.c2a_con = contact.con_id
LEFT JOIN company ON contact.con_company = company.com_id
LEFT JOIN itype ON appointment.app_subtype = itype.ity_id
LEFT JOIN note ON note.not_row = appointment.app_id AND note.not_type = 'appointment' AND not_status = 'Active'
WHERE
$sql
GROUP BY appointment.app_id
ORDER BY $orderby $direction";
//Bra2Calendar
//echo $sql;
//exit;

			$q = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql);
			}
			$numRows = $q->numRows();
			if ($numRows !== 0) {
				while ($row = $q->fetchRow()) {
					if ($row["dea_marketprice"]) {
						$price = format_price($row["dea_marketprice"]) . ' (M)';
					} elseif ($row["dea_valueprice"] && !$row["dea_marketprice"]) {
						$price = format_price($row["dea_valueprice"]) . ' (V)';
					} else {
						$price = 'n/a';
					}
					$pro_addr = preg_replace("/\([a-z0-9\ ]+\)/", "", $row["pro_addr"]); // this removes the dea_id in parenthesis
					$cli_name = preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]); // this removes the cli_id in parenthesis
					if ($row["app_attendees"]) {
						$app_attendees = '<br><font size="1">' . trim(preg_replace("/\([a-z0-9\ ]+\)/", "", $row["app_attendees"])) . '</font>'; // this removes the use_id in parenthesis
					}
					if ($row["use_colour"]) {
						$use_colour = '<span class="use_col" style="background-color: #' . $row["use_colour"] . ';"><img src="/images/sys/admin/blank.gif" width="10" height="10"></span>&nbsp;';
					}

					if ($row["app_status"] == "Active") {
						$cal_link = '<a href="calendar.php?app_id=' . $row["app_id"] . '&searchLink=' . $searchLink . '"><img src="/images/sys/admin/icons/calendar.gif" border="0" width="16" height="16" hspace="1" alt="View in ' . $row["bra_title"] . ' calendar" /></a>';
					} else {
						$cal_link = '<a href="javascript:alert(\'This appointment has been ' . $row["app_status"] . ' so it will not show up in the calendar\');"><img src="/images/sys/admin/icons/calendar_grey.gif" border="0" width="16" height="16" hspace="1" alt="View in ' . $row["bra_title"] . ' calendar" /></a>';
						$row["app_type"] .= '<br /><span class="app' . $row["app_status"] . '">' . $row["app_status"] . '</span>';
					}

					if ($row["app_type"] == 'Inspection') {
						$app_type = $row["ity_title"];
						if ($row["con_name"]) {
							$cli_name = preg_replace("/\([a-z0-9\ ]+\)/", "", $row["con_name"]);
						}
					} elseif ($row["app_type"] == 'Note') {
						$app_type = $row["app_notetype"];
					} else {
						$app_type = $row["app_type"];
					}

					if ($row["app_allday"] == 'Yes') {
						$date            = explode("<br>", $row["app_date"]);
						$row["app_date"] = $date[0] . '<br />(All day)';
					}
					// use subject, or notes, if no pro_addr
					if (!$pro_addr) {
						if ($row["app_subject"]) {
							$pro_addr = $row["app_subject"];
						} else {
							$pro_addr = $row["not_blurb"];
						}
					}

					$data[] = '
	  <tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" valign="top">
		<td width="10"><label><input type="checkbox" name="dea_id[]" id="check_deal_' . $row["app_id"] . '" value="' . $row["app_id"] . '"></label></td>
		<td width="120" style="padding-top:5px;padding-bottom:5px" onmousedown="document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked ? false : true);">' . $row["app_date"] . '</td>
		<td class="bold" style="padding-top:5px;padding-bottom:5px" onmousedown="document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked ? false : true);">' . $app_type . '</td>
		<td class="bold" style="padding-top:5px;padding-bottom:5px" onmousedown="document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked ? false : true);">' . $pro_addr . '</td>
		<td width="130" style="padding-top:5px;padding-bottom:5px" onmousedown="document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked ? false : true);">' . $cli_name . '</td>
		<td width="130" style="padding-top:5px;padding-bottom:5px" onmousedown="document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["app_id"] . '\').checked ? false : true);">' . $use_colour . '<strong>' . $row["use_name"] . '</strong>' . $app_attendees . '</td>
		<td width="70" style="padding-top:5px;padding-bottom:5px" nowrap="nowrap">
		' . $cal_link . '
		<a href="appointment_edit.php?app_id=' . $row["app_id"] . '&searchLink=' . $searchLink . '"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this appointment" /></a>
		<a href="javascript:appointmentPrint(\'' . $row["app_id"] . '\',\'' . $_SESSION["auth"]["use_id"] . '\');"><img src="/images/sys/admin/icons/print-icon.png" border="0" width="16" height="16" hspace="1" alt="Print this appointment" /></a>
		</td>
	  </tr>
	  ';

					unset($app_attendees, $use_colour, $pro_addr, $cli_name, $price);
				}
			}
#print_r($vendor);
#print_r($deal);
#exit;
			require_once 'Pager/Pager.php';
			$params = array(
				'mode'       => 'Sliding',
				'perPage'    => 14,
				'delta'      => 2,
				'itemData'   => $data
			);
			$pager  = & Pager::factory($params);
			$data   = $pager->getPageData();
			$links  = $pager->getLinks();

// convert the querystring into hidden fields
			$qs = parse_str($_SERVER['QUERY_STRING'], $output);
			foreach ($output AS $key=> $val) {
				if ($key !== "setPerPage") {
					$hidden_fields .= '<input type="hidden" name="' . $key . '" value="' . $val . '">';
				}
			}

			/*
   $perpage = '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="GET">
   '.$pager->getperpageselectbox().'
   '.$hidden_fields.'
   <input type="submit" value="Go" class="button" />
   </form>';
   */
//$links is an ordered+associative array with 'back'/'pages'/'next'/'first'/'last'/'all' links.
//(page '.$pager->getCurrentPageID().' of '.$pager->numPages().')
			if (!$links['back']) {
				$back = "&laquo;";
			} else {
				$back = $links['back'];
			}
			if (!$links['next']) {
				$next = "&raquo;";
			} else {
				$next = $links['next'];
			}

			if ($pager->numItems()) {

				$return = 'appointment_search.php?' . replaceQueryString($_SERVER['QUERY_STRING'], 'action');

				$header = '
<div id="header">
<table>
  <tr>
    <td>' . $pager->numItems() . ' records found';
				if ($pager->numPages() > 1) {
					$header .= ' - Page: ' . $back . ' ' . str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;", $links['pages']) . ' ' . $next . '';
				}
				$header .= '</td>
	<td align="right"><a href="' . $return . '">Modify Search</a> / <a href="appointment_search.php">New Search</a></td>
  </tr>
</table>
</div>
';

				$results = '
<table>
  <tr>
    ' . columnHeader(array(
						  array('title'  => 'Date',
								'column' => 'app_start',
								'colspan'=> '2'),
						  array('title' => 'Type',
								'column'=> 'app_type'),
						  array('title'=> 'Location(s)'),
						  array('title'=> 'Client/Contact'),
						  array('title' => 'User(s)',
								'column'=> 'use_name'),
						  array('title'=> '&nbsp;')
					 ), $_SERVER["QUERY_STRING"]) . '
  </tr>';
				foreach ($data AS $output) {
					$results .= $output;
				}
				$results .= '</table>
';

				$footer = '
<div id="footer">
<table>
  <tr>
    <td>With selected:
	<input type="button" name="action" value="View" class="button">
	<input type="button" name="action" value="Print" class="button"></td>
  </tr>
</table>
</div>
';

			} else { // no results

				$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="' . urldecode($returnLink) . '">try again</a></strong></td>
  </tr>
</table>';
			}
			/*
   //Results from methods:
   echo 'getCurrentPageID()...: '; var_dump($pager->getCurrentPageID());
   echo 'getNextPageID()......: '; var_dump($pager->getNextPageID());
   echo 'getPreviousPageID()..: '; var_dump($pager->getPreviousPageID());
   echo 'numItems()...........: '; var_dump($pager->numItems());
   echo 'numPages()...........: '; var_dump($pager->numPages());
   echo 'isFirstPage()........: '; var_dump($pager->isFirstPage());
   echo 'isLastPage().........: '; var_dump($pager->isLastPage());
   echo 'isLastPageComplete().: '; var_dump($pager->isLastPageComplete());
   echo '$pager->range........: '; var_dump($pager->range);
   */

			$form = new Form();

			$form->addHtml("<div id=\"standard_form\">\n");

			$form->addForm("", "get", $_SERVER['PHP_SELF']);
			$form->addField("hidden", "searchLink", "", $searchLink);
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Search Results</div>');
			$form->addHtml('<div id="results_table">');
			$form->addHtml($header);
			$form->addHtml($results);
//$form->addHtml($footer);
			$form->addHtml('</div>');
			$form->addHtml("</fieldset>\n");

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $returnLink),
				'search'=> array('title'=> 'Search Calendar',
								 'label'=> 'Search Calendar',
								 'link' => 'appointment_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle('Search Results');
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content_wide">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
			$page->display();

			exit;
		}

		break;
###########################################################
# default
###########################################################
	default:

}
?>