<?php
extract($_REQUEST);
require_once("inx/global.inc.php");

/*
arrange production:

search property (this is where sales or lettings is chosen)
select one or more properties
show vendors and give option to add as contacts for the appointment

*/

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
}
elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
}
else {
	// default to survey_address
	$stage = "address";
}

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage):

/////////////////////////////////////////////////////////////////////////////
// address
// search deal+property and display any linked properties
// allow mulitple properties
// id app_id is present, add select properties to that appointment, else create new
/////////////////////////////////////////////////////////////////////////////
	case "address":

// if dea_id is present (i.e. pre-selected property to view), skip property lookup
		if ($_GET["dea_id"]) {
			if (is_array($_GET["dea_id"])) {
				foreach ($_GET["dea_id"] as $deal) {
					$carry_deal .= "$deal";
				}
			} else {
				$carry_deal = $_GET["dea_id"];
			}
			header("Location:?stage=appointment&cli_id=" . $_GET["cli_id"] . "&dea_id=" . $carry_deal);
		}

		if (!$_GET["action"]) {

			if (!$_GET["scope"]) {
				$_GET["scope"] = $_SESSION["auth"]["default_scope"];
			}

// disable term (pw/pcm) unless term == let
			if ($_GET["scope"] == "Lettings") {
				$term_attributes = array();
			} else {
				$term_attributes = array('disabled'=> 'disabled');
			}

			$formData1 = array(
				'scope'    => array(
					'type'      => 'radio',
					'label'     => 'Sales or Lettings',
					'value'     => $_GET["scope"],
					'init'      => 'Sales',
					'options'   => array('Sales'   => 'sale',
										 'Lettings'=> 'let'),
					'attributes'=> array('onClick'=> 'javascript:toggleField(\'term\');')
				),
				'keyword'  => array(
					'type'      => 'text',
					'label'     => 'Keyword(s)',
					'value'     => $_GET["keyword"],
					'attributes'=> array('class'=> 'addr'),
					'tooltip'   => 'Seperate multiple keywords with commas'
				),
				'status'   => array(
					'type'   => 'select',
					'label'  => 'Status',
					'value'  => $_GET["status"],
					'default'=> 'Instructed',
					// add "any" to top of status list
					// db_enum("deal","dea_status","array")

					'options'=> join_arrays(array(array(''=> 'Any'), db_enum("deal", "dea_status", "array")))
					/*'options'=>array(
								'Available'=>'Available',
								''=>'Any',
								'Instructed'=>'Instructed',
								'Under Offer'=>'Under Offer',
								'Under Offer with Other'=>'Under Offer with Other'
								) */
				),
				'price_min'=> array(
					'type'      => 'text',
					'label'     => 'Price From',
					'value'     => $_GET["price"],
					'group'     => 'Price Range',
					'init'      => '(minimum)',
					'attributes'=> array('style'  => 'width:100px',
										 'onFocus'=> 'javascript:clearField(this,\'(minimum)\')')
				),
				'price_max'=> array(
					'type'      => 'text',
					'label'     => 'Price To',
					'value'     => $_GET["price"],
					'group'     => 'Price Range',
					'init'      => '(maximum)',
					'attributes'=> array('style'  => 'width:100px',
										 'onFocus'=> 'javascript:clearField(this,\'(maximum)\')')
				),
				'term'     => array(
					'type'         => 'select',
					'label'        => 'Term',
					'value'        => $_GET["term"],
					'group'        => 'Price Range',
					'last_in_group'=> '1',
					'attributes'   => $term_attributes,
					'options'      => array('per week' => 'per week',
											'per month'=> 'per month'),
					'tooltip'      => 'If you enter a price range, properties without a price will not appear'
				),
				'bed'      => array(
					'type' => 'select_number',
					'label'=> 'Minimum Beds'
				)
			);

			$form = new Form();

			$form->addForm("", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "stage", "", "address");
			$form->addField("hidden", "action", "", "advanced_search");
			$form->addField("hidden", "cli_id", "", $cli_id);
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);
			$form->addField("hidden", "date", "", $_GET["date"]);

			$formName = 'form1';
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Property to Visit</div>');
			$form->addHtml('<div id="' . $formName . '">');
			$form->addData($formData1, $_GET);
			$form->addHtml($form->addDiv($form->makeField("submit", "", "", "Search", array('class'=> 'submit'))));
			$form->addHtml("</div>\n");
			$form->addHtml("</fieldset>\n");
			$form->addHtml("</div>\n");

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
								 'link' => urldecode($searchLink)),
				'search'=> array('title'=> 'Property Search',
								 'label'=> 'Property Search',
								 'link' => 'property_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Arrange Production");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addScriptDeclaration($additional_js);
			$page->setBodyAttributes(array('onLoad'=> $onLoad));
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
			$page->display();

			exit;

		} else { // if form is submitted

// construct sql
			if ($_GET["scope"] == "sale") {
				$q[]                  = "dea_type = 'Sales' AND ";
				$return["scope"]      = 'Sales';
				$db_data['cli_sales'] = 'Yes';

			} elseif ($_GET["scope"] == "let") {
				$q[]                     = "dea_type = 'Lettings' AND ";
				$return["scope"]         = 'Lettings';
				$db_data['cli_lettings'] = 'Yes';

			} else {
				echo "You must choose Sales or Lettings";
				exit;
			}
			if ($_GET["keyword"]) {
				$keyword  = $return["keyword"] = $_GET["keyword"];
				$keywords = explode(",", $keyword);
				foreach ($keywords AS $keyword) {

					if (strlen($keyword) > 1) { // ignoring words 2 or less
						$keyword = trim($keyword);
						// get rid of st, temporary solution
						$keyword = str_ireplace("st ", "", $keyword);

						$keyword_sql .= "CONCAT(pro_addr1,' ',pro_addr3) LIKE '%$keyword%' OR ";
						// remove period from street names i.e. st. street
						$keyword_sql .= "REPLACE(pro_addr3, '.', '') LIKE '%$keyword%' OR ";
						// remove period from street names i.e. st. street
						$keyword_sql .= "REPLACE(pro_addr3, '''', '') LIKE '%$keyword%' OR ";

						$keyword_sql .= "pro_addr1 LIKE '%$keyword%' OR pro_addr2 LIKE '%$keyword%' OR pro_addr3 LIKE '%$keyword%' OR ";
						$keyword_sql .= "pro_addr4 LIKE '%$keyword%' OR pro_addr5 LIKE '%$keyword%' OR pro_postcode LIKE '%$keyword%' OR ";
						$keyword_sql .= "dea_keywords LIKE '%$keyword%' OR dea_strapline LIKE '%$keyword%' OR are_title LIKE '%$keyword%' OR ";
						$keyword_sql .= "cli_fname LIKE '%$keyword%' OR cli_sname LIKE '%$keyword%' OR concat_ws(' ',cli_fname, cli_sname)  LIKE '%$keyword%' OR ";
					}

				}
				$keyword_sql = "(" . remove_lastchar($keyword_sql, "OR") . ") AND ";
				$q[]         = $keyword_sql;
			}
			if ($_GET["price_min"] && $_GET["price_min"] !== '(minimum)') {
				$return["price_min"] = $_GET["price_min"];
				$q[]                 = "dea_marketprice > '" . numbers_only($_GET["price_min"]) . "' AND ";
			}

			if ($_GET["price_max"] && $_GET["price_max"] !== '(maximum)') {
				$return["price_max"] = $_GET["price_max"];
				$q[]                 = "dea_marketprice < '" . numbers_only($_GET["price_max"]) . "' AND ";
			}

// status needs to be multi checkboxes
			if ($_GET["status"]) {
				$return["status"] = $_GET["status"];
				$q[]              = "dea_status = '" . $_GET["status"] . "' AND ";
			}

			if ($_GET["bed"]) {
				$return["bed"] = $_GET["bed"];
				$q[]           = "pro_bedroom >= '" . $_GET["bed"] . "' AND ";
			}

			if ($_GET["orderby"]) {
				$orderby           = $_GET["orderby"];
				$return["orderby"] = $orderby;
			} else {
				$orderby = 'pro_addr3';
			}
			if ($_GET['direction']) {
				$direction = $_GET['direction'];
			} else {
				$direction = 'ASC';
			}

			if (!$q) {
				$errors[] = 'Please enter some search criteria';
				echo error_message($errors);-
				exit;
			}
			$returnLink = '?stage=address&cli_id=' . $cli_id . '&app_id=' . $app_id . '&' . http_build_query($return);
			$searchLink = $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']);
			foreach ($q AS $statement) {
				$sql .= $statement . " ";
			}
			$sql = remove_lastchar($sql, "AND");
			$sql = remove_lastchar($sql, "OR");
			$sql = "SELECT
dea_id,dea_prop,dea_status,dea_marketprice,dea_valueprice,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,

cli_id,GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname))  AS cli_name
FROM deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN area ON property.pro_area = area.are_id
WHERE
$sql
GROUP BY deal.dea_id
ORDER BY $orderby $direction";

			$q = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql);
			}
			$numRows = $q->numRows();
			if ($numRows !== 0) {
				while ($row = $q->fetchRow()) {
					// onClick="trClick(\'client_edit.php?cli_id='.$row["cli_id"].'&searchLink='.$searchLink.'\');"
					if ($row["dea_marketprice"]) {
						$price = format_price($row["dea_marketprice"]) . ' (M)';
					} elseif ($row["dea_valueprice"] && !$row["dea_marketprice"]) {
						$price = format_price($row["dea_valueprice"]) . ' (V)';
					} else {
						$price = 'n/a';
					}
					$cli_name = str_replace(",", ", ", $row["cli_name"]);
					$data[]   = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td width="10"><label><input type="checkbox" name="dea_id[]" id="check_deal_' . $row["dea_id"] . '" value="' . $row["dea_id"] . '"></label></td>
		<td class="bold" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $row["pro_addr"] . '</td>
		<td width="200" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $cli_name . '</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $row["dea_status"] . '</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $price . '</td>
		<!--<td width="100" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $row["bra_title"] . '</td>-->
		<td width="50" nowrap="nowrap">
		<a href="deal_summary.php?dea_id=' . $row["dea_id"] . '&searchLink=' . $searchLink . '"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this property" /></a>
		<a href="javascript:dealPrint(\'' . $row["dea_id"] . '\');"><img src="/images/sys/admin/icons/print-icon.png" border="0" width="16" height="16" hspace="1" alt="Print this property" /></a>
		</td>
		</tr>';
				}
			}

			require_once 'Pager/Pager.php';
			$params = array(
				'mode'       => 'Sliding',
				'perPage'    => 20,
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

				$header = '
<div id="header">
<table>
  <tr>
    <td>' . $pager->numItems() . ' records found';
				if ($pager->numPages() > 1) {
					$header .= ' - Page: ' . $back . ' ' . str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;", $links['pages']) . ' ' . $next . '';
				}
				$header .= '</td>
	<td align="right"><a href="' . urldecode($returnLink) . '">Modify Search</a></td>
  </tr>
</table>
</div>
';

				$results = '
<table>
  <tr>
    ' . columnHeader(array(
						  array('title'  => 'Address',
								'column' => 'pro_addr3',
								'colspan'=> '2'),
						  array('title' => 'Vendor',
								'column'=> 'cli_name'),
						  array('title' => 'Status',
								'column'=> 'dea_status'),
						  array('title' => 'Price',
								'column'=> 'dea_marketprice'),
						  array('title'=> '&nbsp;')
						  #array('title'=>'Branch','column'=>'dea_branch')
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
    <td>
	<input type="submit" value="Use Selected Property" class="button"></td>
  </tr>
</table>
</div>
';

			} else {
// no results
				$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="' . urldecode($returnLink) . '">try again</a></strong></td>
  </tr>
</table>';
			}

			$form = new Form();

			$form->addHtml("<div id=\"standard_form\">\n");

			$form->addForm("", "get");
			$form->addField("hidden", "stage", "", "vendor");
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);
			$form->addField("hidden", "searchLink", "", $searchLink);
			$form->addField("hidden", "date", "", $_GET["date"]);
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Property to Visit</div>');
			$form->addHtml('<div id="results_table">');
			$form->addHtml($header);
			$form->addHtml($results);
			$form->addHtml($footer);
			$form->addHtml('</div>');
			$form->addHtml("</fieldset>\n");

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $returnLink),
				'search'=> array('title'=> 'Property Search',
								 'label'=> 'Property Search',
								 'link' => 'property_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Arrange Production");
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

// vendor - list vendors to add as appointment contact
	case "vendor":

#$returnLink = '?stage=viewing_address&cli_id='.$cli_id.'&app_id='.$app_id.'&'.http_build_query($return);
		$searchLink = $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']);

		if (is_array($_GET["dea_id"])) {
			foreach ($_GET["dea_id"] as $deal) {

				$sql = "SELECT
		cli_id,CONCAT(client.cli_fname,' ',client.cli_sname) AS cli_name
		FROM deal
		LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
		LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
		WHERE
		deal.dea_id = " . $deal;
				$q   = $db->query($sql);
				if (DB::isError($q)) {
					die("db error: " . $q->getMessage() . $sql);
				}
				$numRows = $q->numRows();
				if ($numRows !== 0) {
					while ($row = $q->fetchRow()) {
						$vendors[$row["cli_id"]] = $row["cli_name"];
					}
				}
				$dea_id = array2string($_GET["dea_id"]);
			}
		} else {

			$sql = "SELECT
	cli_id,CONCAT(client.cli_fname,' ',client.cli_sname) AS cli_name
	FROM deal
	LEFT JOIN link_client_to_instruction ON deal.dea_id = link_client_to_instruction.dealId
	LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
	WHERE
	deal.dea_id = " . $_GET["dea_id"];
			$q   = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql);
			}
			$numRows = $q->numRows();
			if ($numRows !== 0) {
				while ($row = $q->fetchRow()) {
					$vendors[$row["cli_id"]] = $row["cli_name"];
				}
			}

		}

		$vendor_table = '
<table cellpadding="2" cellspacing="2" border="0">
  <tr>
	<td class="label" valign="top">Associated Vendor(s)</td>
	<td>';
		foreach ($vendors as $key=> $val) {
			$vendor_table .= '<label for="' . $key . '"><input type="checkbox" name="cli_id[]" value="' . $key . '" id="' . $key . '">
		' . $val . '</label><br />';
		}

		$vendor_table .= '</td>
  </tr>
  <tr>
	<td colspan="2"><input type="submit" value="Continue" class="submit"></td>
  </tr>
</table>
';

		$form = new Form();

		$form->addHtml("<div id=\"standard_form\">\n");

		$form->addForm("", "get");
		$form->addField("hidden", "stage", "", "appointment");
		$form->addField("hidden", "dea_id", "", $dea_id);
		$form->addField("hidden", "app_id", "", $_GET["app_id"]);
		$form->addField("hidden", "searchLink", "", $searchLink);
		$form->addField("hidden", "date", "", $_GET["date"]);
		$form->addHtml("<fieldset>\n");
		$form->addHtml('<div class="block-header">Vendor(s)</div>');
		$form->addHtml($vendor_table);
		$form->addHtml("</fieldset>\n");

		$navbar_array = array(
			'back'  => array('title'=> 'Back',
							 'label'=> 'Back',
							 'link' => $returnLink),
			'search'=> array('title'=> 'Property Search',
							 'label'=> 'Property Search',
							 'link' => 'property_search.php')
		);
		$navbar       = navbar2($navbar_array);

		$page->setTitle("Arrange Production");
		$page->addStyleSheet(getDefaultCss());
		$page->addScript('js/global.js');
		$page->addBodyContent($header_and_menu);
		$page->addBodyContent('<div id="content">');
		$page->addBodyContent($navbar);
		$page->addBodyContent($form->renderForm());
		$page->addBodyContent('</div>');
		$page->display();

		exit;

		break;

// appointment
	case "appointment":

		/*

  create appointment and link to deals via link_deal_to_appointment link table...

  maybe here we should skip to separate appointment page, which will be used to view/edit
  all appointments? this would allow user to add/remove deals from the apointment now and
  at any future time.... leave it here for now to develop, move later

  the appointment page (or this stage) would require cli_id, dea_id(array)

  ch-ch-changes (25/10/06)
  dont save anything to appointment table until date and time have been entered, just carry the dea_id array over.....

  */

		if (!$_GET["dea_id"]) {
			/* echo "no dea_id";
				exit; */
			$dea_id = $_POST["dea_id"];
		} else {
			$dea_id = $_GET["dea_id"];
		}

		if ($_GET["cli_id"]) {
			$cli_id = array2string($_GET["cli_id"]);
		}

		if ($_GET["date"]) {
			$app_date = urldecode($_GET["date"]);
		}
		else {
			// default date and time set to now
			$app_date = date('d/m/Y');
			$app_time = date('G:i');
		}

// multiply by number  of properties (deals)
		$numdeals = count(explode("|", $_GET["dea_id"]));

		$duration = ($default_production_duration * $numdeals);

//$sql = "SELECT dea_neg FROM deal WHERE dea_id = ".$_GET["dea_id"];
		$sql = "SELECT dea_neg FROM deal WHERE dea_id = " . $duration;
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage() . $sql);
		}
		while ($row = $q->fetchRow()) {
			$dea_user = $row["dea_neg"];
		}
		if (!$dea_user) {
			$dea_user = $_SESSION["auth"]["use_id"];
		}

// change sydenham, lettins branch to camberwell, as only one calendar in use
		if ($_SESSION["auth"]["use_branch"] == 4) {
			$branch = 3;
		} else {
			$branch = $_SESSION["auth"]["use_branch"];
		}

		$formData1 = array(
			'calendarID'  => array(
				'type'      => 'select_branch_2',
				'label'     => 'Branch',
				'value'     => $branch,
				'attributes'=> array('class'=> 'medium')
			),
			'app_user'    => array(
				'type'      => 'select_user',
				'label'     => 'Negotiator',
				'value'     => $_SESSION["auth"]["use_id"],
				'attributes'=> array('class'=> 'medium'),
				'options'   => $negotiators
			),
			'app_date'    => array(
				'type'      => 'datetime',
				'label'     => 'Date',
				'value'     => $app_date,
				'attributes'=> array('class'   => 'medium',
									 'readonly'=> 'readonly'),
				'tooltip'   => 'Today\'s date is selected by default'
			),
			'app_time'    => array(
				'type' => 'time',
				'label'=> 'Start Time',
				'value'=> $app_time
			),
			'app_duration'=> array(
				'type'      => 'select_duration',
				'label'     => 'Estimated Duration',
				'value'     => $duration,
				'attributes'=> array('class'=> 'medium'),
				'tooltip'   => 'Duration is estimated at ' . $default_production_duration . ' minutes'
			)
		);

		if (!$_GET["action"]) {

			$form = new Form();

			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addForm("", "get");
			$form->addField("hidden", "stage", "", "appointment");
			$form->addField("hidden", "action", "", "update");
			$form->addField("hidden", "cli_id", "", $cli_id);
			$form->addField("hidden", "dea_id", "", $dea_id);
			$form->addField("hidden", "searchLink", "", $searchLink);
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Appointment</div>');
			$form->addData($formData1, $_GET);
			$form->addHtml($form->addDiv($form->makeField("submit", "submit", "", "Save Changes", array('class'=> 'submit'))));
			$form->addHtml("</fieldset>\n");
			$form->addHtml("</div>\n");

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $returnLink),
				'search'=> array('title'=> 'Property Search',
								 'label'=> 'Property Search',
								 'link' => 'property_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Arrange Production");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addScript('js/CalendarPopup.js');
			$page->addScriptDeclaration('document.write(getCalendarStyles());var popcalapp_date = new CalendarPopup("popCalDivapp_date");popcalapp_date.showYearNavigation(); ');
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
			$page->display();

			exit;

		} elseif ($_GET["action"] == "update") { //if form is submitted

			if ($_GET["dea_id"]) {
				$dea_id = explode("|", $_GET["dea_id"]);
			}
			if ($_GET["cli_id"]) {
				$cli_id = explode("|", $_GET["cli_id"]);
			}

// create appointment row
			$db_data["app_type"] = 'Production';

			$date_parts = explode("/", $_GET["app_date"]);
			$day        = $date_parts[0];
			$month      = $date_parts[1];
			$year       = $date_parts[2];

			$app_date = $year . '-' . $month . '-' . $day;

			$app_time_hour = $_GET["app_time_hour"];
			$app_time_min  = $_GET["app_time_min"];

			$app_start = $app_date . ' ' . $app_time_hour . ':' . $app_time_min . ':00';

			$app_start = strtotime($app_start);
			$app_end   = $app_start + ($_GET["app_duration"] * 60);

			$db_data["app_start"]  = date('Y-m-d G:i:s', $app_start);
			$db_data["app_end"]    = date('Y-m-d G:i:s', $app_end);
			$db_data["calendarID"] = $_GET["calendarID"];

			$db_data["app_bookedby"] = $_SESSION["auth"]["use_id"]; // booked by
			$db_data["app_user"]     = $_GET["app_user"]; // lead neg
			$db_data["app_created"]  = $date_mysql;
			$app_id                  = db_query($db_data, "INSERT", "appointment", "app_id");
			unset($db_data);

// add to cli2app table
			if (is_array($cli_id)) {
				foreach ($cli_id as $cli) {
					$db_data["c2a_cli"] = $cli;
					$db_data["c2a_app"] = $app_id;
					db_query($db_data, "INSERT", "cli2app", "c2a_id");
					unset($db_data);
				}
			} elseif ($cli_id) {
				$db_data["c2a_cli"] = $cli_id;
				$db_data["c2a_app"] = $app_id;
				db_query($db_data, "INSERT", "cli2app", "c2a_id");
				unset($db_data);
			}

			$i = 1; // used for d2a_ord
			if (is_array($dea_id)) {
				foreach ($dea_id as $dea) {
					$db_data["d2a_dea"] = $dea;
					$db_data["d2a_app"] = $app_id;
					$db_data["d2a_ord"] = $i;
					db_query($db_data, "INSERT", "link_deal_to_appointment", "d2a_id");
					unset($db_data);
					$i++;
				}
			} elseif ($dea_id) {
				$db_data["d2a_dea"] = $dea_id;
				$db_data["d2a_app"] = $app_id;
				$db_data["d2a_ord"] = $i;
				db_query($db_data, "INSERT", "link_deal_to_appointment", "d2a_id");
				unset($db_data);
			}

// notify - update the app_notify field purely to create the neccesary environment to run the notify function
			unset($db_data);
			$db_data["app_updated"] = date('Y-m-d H:i:s');
			$db_response            = db_query($db_data, "UPDATE", "appointment", "app_id", $app_id, true);
			notify($db_response, 'add');

// forward to calendar
			header("Location:calendar.php?app_id=$app_id");
			exit;

		}

		break;

/////////////////////////////////////////////////////////////////////////////
// if no stage is defined
/////////////////////////////////////////////////////////////////////////////
	default:

endswitch;
?>