<?php
require_once("inx/global.inc.php");

if (!$_GET["stage"]) {
	$stage = 1;
} else {
	$stage = $_GET["stage"];
}

# go to client add form if client is NOT found
$goto_notfound = "client_add.php";
# go to client add form if client is found
$goto_found = "client_edit.php";

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - detailed search
###########################################################
	case 1:

# advanced client search
# detailed search of client database

		if (!$_GET["action"]) {

			if (!$_GET["scope"]) {
				$_GET["scope"] = $_SESSION["auth"]["default_scope"];
			}
// disable term (pw/pcm) unless term == let
			if ($_GET["scope"] == "Lettings") {
				$term_attributes = array();
			} else {
				$term_attributes = array('disabled' => 'disabled');
			}

// date ranges
			$today     = strtotime($date_mysql);
			$week      = (7 * 24 * 60 * 60);
			$past_week = date(MYSQL_DATE_FORMAT, ($today - $week));
			$past_week = explode(" ", $past_week);
			$past_week = $past_week[0];

			$month      = (4 * 7 * 24 * 60 * 60);
			$past_month = date(MYSQL_DATE_FORMAT, ($today - $month));
			$past_month = explode(" ", $past_month);
			$past_month = $past_month[0];

			$month6      = (6 * 4 * 7 * 24 * 60 * 60);
			$past_month6 = date(MYSQL_DATE_FORMAT, ($today - $month6));
			$past_month6 = explode(" ", $past_month6);
			$past_month6 = $past_month6[0];

			$year      = (12 * 4 * 7 * 24 * 60 * 60);
			$past_year = date(MYSQL_DATE_FORMAT, ($today - $year));
			$past_year = explode(" ", $past_year);
			$past_year = $past_year[0];

			if (!$_GET["cli_branch"]) {
				$_GET["cli_branch"] = 999;
			}

			$salestatuses[0]    = '';
			$lettingstatuses[0] = '';

			$sql = "SELECT * FROM cstatus";
			$q   = $db->query($sql);
			while ($row = $q->fetchRow()) {
				if ($row["cst_scope"] == 'Sales') {
					$salestatuses[$row["cst_id"]] = $row["cst_title"];
				} else {
					$lettingstatuses[$row["cst_id"]] = $row["cst_title"];
				}
			}

			$formData1 = array(
				'scope'          => array(
					'type'       => 'radio',
					'label'      => 'Sales or Lettings',
					'value'      => $_GET["scope"],
					'default'    => 'Sales',
					'options'    => array('Sales' => 'sale', 'Lettings' => 'let'),
					'attributes' => array('onClick' => 'javascript:disableTermField(\'scope\',\'term\');showHideStatusRow(\'scope\');')
				),
				'keyword'        => array(
					'type'       => 'text',
					'label'      => 'Name or Email',
					'value'      => $_GET["keyword"],
					'attributes' => array('class' => 'addr')
				),
				'feature'        => array(
					'type'       => 'text',
					'label'      => 'Keywords(s)',
					'value'      => $_GET["feature"],
					'attributes' => array('class' => 'addr'),
					'tooltip'    => 'Areas, postcodes, and any specific property requirements. e.g. Garden'
				),
				'price'          => array(
					'type'       => 'text',
					'label'      => 'Price',
					'value'      => $_GET["price"],
					'group'      => 'Price',
					'attributes' => array('style' => 'width:120px')
				),
				'term'           => array(
					'type'          => 'select',
					'label'         => 'Term',
					'value'         => $_GET["term"],
					'group'         => 'Price',
					'last_in_group' => '1',
					'attributes'    => $term_attributes,
					'options'       => array('pw' => 'per week', 'pcm' => 'per month')
				), /*
	'type'=>array(
		'type'=>'select',
		'label'=>'Property Type',
		'value'=>$_GET["type"],
		'options'=>array(''=>'Any','1'=>'House','2'=>'Apartment','3'=>'Other'),
		'attributes'=>array('style'=>'width:160px')
		),*/
				'bedmin'         => array(
					'type'  => 'select_number',
					'label' => 'Bedrooms',
					'value' => $_GET["bedmin"],
					'group' => 'Bedrooms'
				),
				'bedmax'         => array(
					'type'          => 'select_number',
					'label'         => 'Bedrooms',
					'value'         => $_GET["bedmax"],
					'group'         => 'Bedrooms',
					'last_in_group' => 1
				),
				'date_range'     => array(
					'type'       => 'select',
					'label'      => 'Registered',
					'value'      => $_GET["date_range"],
					'attributes' => array('style' => 'width:200px'),
					'options'    => array(
						''           => 'Anytime',
						$past_week   => 'in the past week',
						$past_month  => 'in the past month',
						$past_month6 => 'in the past 6 months',
						$past_year   => 'in the past 12 months'
					)
				),
				'cli_neg'        => array(
					'type'       => 'select_neg',
					'label'      => 'Assigned Negotiator',
					'value'      => $_GET["cli_neg"],
					'options'    => array('' => ''),
					'attributes' => array('style' => 'width:200px')
				),
				'cli_branch'     => array(
					'type'       => 'select_branch',
					'label'      => 'Assigned Branch',
					'value'      => $_GET["cli_branch"],
					'options'    => array('' => ''),
					'attributes' => array('style' => 'width:200px')
				),
				'cli_method'     => array(
					'type'       => 'select',
					'label'      => 'Initial Contact Method',
					'value'      => $_GET["cli_method"],
					'options'    => array(
						''        => '', 'Website' => 'Self-registration on our site', 'Telephone' => 'Telephone', 'Email' => 'Email', 'Internet' => 'Internet (portal)',
						'Walk-in' => 'Walk-in'
					),

					//db_enum("client","cli_method","array"),
					'attributes' => array('style' => 'width:200px')
				),
				'cli_salestatus' => array(
					'type'       => 'select',
					'label'      => 'Current Status (Sales)',
					'value'      => $_GET["cli_salestatus"],
					'options'    => $salestatuses,
					'attributes' => array('style' => 'width:200px')
				),
				'cli_letstatus'  => array(
					'type'       => 'select',
					'label'      => 'Current Status (Lettings)',
					'value'      => $_GET["cli_letstatus"],
					'options'    => $lettingstatuses,
					'attributes' => array('style' => 'width:200px')
				),
				'has_email'      => array(
					'type'    => 'radio',
					'label'   => 'Has email address?',
					'value'   => $_GET["has_email"],
					'options' => array('Yes' => 'Yes', 'No' => 'No')
				)
			);

			$form = new Form();

			$form->addForm("client_form", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "stage", "", "1");
			$form->addField("hidden", "action", "", "advanced_search");

			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Search Applicants</div>');
			$form->addData($formData1, $_GET);

			$form->addSeperator();
			$ptype_data = ptype("sale", explode("|", $_GET["cli_saleptype"]));
			$form->addHtml('<div id="sale" style="display:' . $sale_display . '">');
//			$form->addHtml($form->addLabel('type', 'Houses', $ptype_data['house'], 'javascript:checkAll(document.forms[0], \'sale1\');'));
//			$form->addHtml($form->addLabel('type', 'Apartments', $ptype_data['apartment'], 'javascript:checkAll(document.forms[0], \'sale2\');'));
//			$form->addHtml($form->addLabel('type', 'Others', $ptype_data['other'], 'javascript:checkAll(document.forms[0], \'sale3\');'));

			$buttons = $form->makeField("submit", "", "", "Search", array('class' => 'submit'));
			$buttons .= $form->makeField("button", "", "", "Reset", array('class' => 'button', 'onClick' => 'javascript:location.href=\'' . $PHP_SELF . '\';'));
			$form->addHtml($form->addDiv($buttons));
			$form->addHtml("</fieldset>\n");

			if (!$_GET["viewForm"]) {
				$viewForm = 1;
			}
			$additional_js = '
if (!previousID) {
	var previousID = "form' . $viewForm . '";
	}
';
			if ($_GET["scope"] == 'Lettings') {
				$additional_js .= '
$(document).ready(function(){
	$("#cli_salestatus").parent().hide();
	});
	';
			} else {
				$additional_js .= '
$(document).ready(function(){
	$("#cli_letstatus").parent().hide();
	});
	';
			}

			$navbar_array = array(
				'back'     => array('title' => 'Back', 'label' => 'Back', 'link' => $searchLink),
				'search'   => array('title' => 'Client Search', 'label' => 'Client Search', 'link' => 'client_search.php'),
				'headline' => array('label' => $_SESSION["auth"]["use_fname"] . ', tell me what options you want to see here')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Client > Search");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addScript('js/jquery.js');
			$page->addScriptDeclaration($additional_js);
			$page->setBodyAttributes(array('onLoad' => $onLoad));
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
				$q[]             = "cli_sales = 'Yes' AND";
				$scope           = "sale";
				$return["scope"] = 'Sales';
			} elseif ($_GET["scope"] == "let") {
				$q[]             = "cli_lettings = 'Yes' AND";
				$scope           = "let";
				$return["scope"] = 'Lettings';
			}

			if ($_GET["keyword"]) {
				$return["keyword"] = $_GET["keyword"];
				$string            = trim(str_replace(",", " ", $_GET["keyword"]));
				// if more than one name is entered, split
				if (strpos($string, " ")) {
					$string = explode(" ", $string);
				} else {
					$string = array($string);
				}
				$keyword_count = count($string);
			}

			if ($_GET["feature"]) {
				$return["feature"] = $_GET["feature"];
				$feature           = trim(str_replace(",", " ", $_GET["feature"]));
				// if more than one name is entered, split
				if (strpos($feature, " ")) {
					$feature = explode(" ", $feature);
				} else {
					$feature = array($feature);
				}
			}
			/*
			if ($_GET["keyword"]) {
				$return["keyword"] = $_GET["keyword"];
				$keyword = str_replace(" ",",",$_GET["keyword"]);
				$keywords = explode(",",$keyword);
				foreach ($keywords AS $keyword) {
					$keyword = trim($keyword);
					$q[] = "(cli_fname LIKE '%$keyword%' OR cli_sname LIKE '%$keyword%' OR cli_email LIKE '%$keyword%') OR cli_id = '$keyword' AND ";
					}
				}
			*/

			if ($_GET["price"]) {
				$price           = numbers_only($_GET["price"]);
				$return["price"] = $price;
				$return["term"]  = $_GET["term"];
				if ($_GET["term"] == "pcm" && $scope == 'let') { // only use term for lettings, per week by default
					$price = pcm2pw($price);
				}
				$q[] = " (cli_" . $scope . "min <= $price AND cli_" . $scope . "max >= $price) AND ";
			}

// type from simple select box - this is very messy, property types MUST be moved to link table
			if ($_GET["type"]) {
				$return["type"] = $_GET["type"];

				// get property types in array
				$sql_ptype = "SELECT * FROM ptype";
				$q_ptype   = $db->query($sql_ptype);
				if (DB::isError($q_ptype)) {
					die("db error: " . $q_ptype->getMessage() . $sql_ptype);
				}
				while ($row = $q_ptype->fetchRow()) {
					if ($row["pty_type"] == 1) {
						$house[] = $row["pty_id"];
					} elseif ($row["pty_type"] == 2) {
						$apartment[] = $row["pty_id"];
					} elseif ($row["pty_type"] == 3) {
						$other[] = $row["pty_id"];
					}
				}

				if ($_GET["type"] == 1) {
					foreach ($house as $val) {
						$sql_ptype2 .= " CONCAT('|',cli_" . $scope . "ptype,'|') LIKE '%|$val|%' OR ";
					}
				} elseif ($_GET["type"] == 2) {
					foreach ($apartment as $val) {
						$sql_ptype2 .= " CONCAT('|',cli_" . $scope . "ptype,'|') LIKE '%|$val|%' OR ";
					}
				} elseif ($_GET["type"] == 3) {
					foreach ($other as $val) {
						$sql_ptype2 .= " CONCAT('|',cli_" . $scope . "ptype,'|') LIKE '%|$val|%' OR ";
					}
				}

				$q[] = " (" . remove_lastchar($sql_ptype2, "OR") . ") AND";

			}

// ptype using checkboxes
			if ($_GET["cli_saleptype"]) {
				$return["cli_saleptype"] = array2string($_GET["cli_saleptype"]);

				foreach ($_GET["cli_saleptype"] as $val) {
					$sql_ptype2 .= " CONCAT('|',cli_" . $scope . "ptype,'|') LIKE '%|$val|%' OR ";
				}

				$q[] = " (" . remove_lastchar($sql_ptype2, "OR") . ") AND";

			}

			if ($_GET["cli_area"]) {
				#$area = array2string($_GET["cli_area"],",");
				$return["cli_area"] = array2string($_GET["cli_area"], "|");
				foreach ($_GET["cli_area"] as $areaid) {
					#$q[] = " cli_area LIKE '%|".$areaid."|%' OR";
					#$area_sql .= " cli_area IN (".$areaid.") OR";
				}
				$area_sql = remove_lastchar($area_sql, "OR");
				#$q[] = "( ".$area_sql." ) AND";
			}
			if ($_GET["bedmin"]) {
				$return["bedmin"] = $_GET["bedmin"];
				$q[]              = " cli_" . $scope . "bed >= " . $_GET["bedmin"] . "  AND ";
			}
			if ($_GET["bedmax"]) {
				$return["bedmax"] = $_GET["bedmax"];
				$q[]              = " cli_" . $scope . "bed <= " . $_GET["bedmax"] . "  AND ";
			}
			if ($_GET["date_range"]) {
				$return["date_range"] = $_GET["date_range"];
				$q[]                  = " cli_created > '" . $_GET["date_range"] . " 00:00:00'  AND ";
			}

// registered by current user
			if ($_GET["cli_neg"]) {
				$return["cli_neg"] = $_GET["cli_neg"];
				$q[]               = " cli_neg = " . $_GET["cli_neg"] . " AND ";
			}

			if ($_GET["cli_branch"]) {
				$return["cli_branch"] = $_GET["cli_branch"];
				$q[]                  = " cli_branch = " . $_GET["cli_branch"] . " AND ";
			}

			if ($_GET["cli_method"]) {
				$return["cli_method"] = $_GET["cli_method"];
				$q[]                  = " cli_method = '" . $_GET["cli_method"] . "' AND ";
			}

			if ($_GET["has_email"] == 'Yes') {
				//$return["has_email"] = $_GET["has_email"];
				$q[] = " cli_email != '' AND ";
			} elseif ($_GET["has_email"] == 'No') {
				//$return["has_email"] = $_GET["has_email"];
				$q[] = " cli_email = '' AND ";
			}

			if ($_GET["cli_salestatus"] && $_GET["scope"] == "sale") {
				$return["cli_salestatus"] = $_GET["cli_salestatus"];
				$q[]                      = " cli_salestatus = '" . $_GET["cli_salestatus"] . "' AND ";
			}
			if ($_GET["cli_letstatus"] && $_GET["scope"] == "let") {
				$return["cli_letstatus"] = $_GET["cli_letstatus"];
				$q[]                     = " cli_letstatus = '" . $_GET["cli_letstatus"] . "' AND ";
			}

			if ($_GET["orderby"]) {
				$orderby           = $_GET["orderby"];
				$return["orderby"] = $orderby;
			} else {
				$orderby = 'cli_name';
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
			$returnLink = '?' . http_build_query($return);
			$searchLink = $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']);
			foreach ($q AS $statement) {
				$sql .= $statement;
			}
			$sql = remove_lastchar($sql, "AND");
			$sql = remove_lastchar($sql, "OR");

			$sql = "SELECT
cli_id,cli_fname,cli_sname,CONCAT(cli_fname,' ',cli_sname) AS cli_name,cli_email,cli_saleptype,cli_letptype,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr,
CONCAT(line1,' ',line2,' ',line3,' ',postcode) AS pro_addr,
DATE_FORMAT(cli_created, '%D %b %Y') AS date,
CONCAT(LEFT(user.use_fname,1),LEFT(user.use_sname,1)) as use_initial,user.use_colour,CONCAT(user.use_fname,' ',user.use_sname) AS use_name,
CONCAT(LEFT(regd.use_fname,1),LEFT(regd.use_sname,1)) as regd_initial,regd.use_colour AS regd_colour,CONCAT(regd.use_fname,' ',regd.use_sname) AS regd_name,
branch.bra_colour AS branchTitle
FROM client
LEFT JOIN property ON property.pro_id = client.cli_pro


LEFT JOIN note ON note.not_row = client.cli_id AND note.not_type = 'client_req' AND note.not_status = 'Active'

LEFT JOIN user ON client.cli_neg = user.use_id
LEFT JOIN user regd ON client.cli_regd = regd.use_id
LEFT JOIN address ON client.addressID = address.id

LEFT JOIN branch ON client.cli_branch = branch.bra_id
WHERE $sql
";
			/*

			*/
			if ($string) {
				$sql .= "AND (";
				foreach ($string AS $str) {
					if ($str) {
						if (is_int($str)) {
							$sql .= "
				client.cli_id = '$str' OR ";
						}
						$sql .= "
			client.cli_fname LIKE '$str%' OR
			client.cli_sname LIKE '$str%' OR
			client.cli_email LIKE '$str%' OR ";
					}
				}
				$sql = remove_lastchar($sql, "OR");
				$sql .= ")";
			}

//$operator = 'OR';
//if ($feature) {
//	$sql .= "AND (";
//	foreach ($feature AS $fea) {
//		if ($fea) {
//			$sql .= "
//			feature.fea_title LIKE '%$fea%' $operator
//			note.not_blurb LIKE '%$fea%' $operator
//			(area.are_title LIKE '%$fea%' OR area.are_postcode LIKE '$fea%') AND ";
//			/*
//			*/
//			}
//		}
//	$sql = remove_lastchar($sql,'AND');
//	$sql .= ")";
//	}
			$sql .= "
GROUP BY client.cli_id
ORDER BY $orderby $direction";

//echo $sql;
//exit;

			$q = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql);
			}
			$numRows = $q->numRows();
			if ($numRows !== 0) {
				while ($row = $q->fetchRow()) {
					// onClick="trClick(\'client_edit.php?cli_id='.$row["cli_id"].'&searchLink='.$searchLink.'\');"

					if ($row["use_initial"]) {
						$neg = '<span class="use_col" style="background-color: #' . $row["use_colour"] . ';" title="' . $row["use_name"] . '"><img src="/images/sys/admin/blank.gif" width="10" height="10" alt="' . $row["use_name"] . '"></span><span style="font-size:10px;margin-left:5px;">' . $row["use_initial"] . '</span>';
					} else {
						unset($neg);
					}

					if ($row["regd_initial"]) {
						$regd = '<span class="use_col" style="background-color: #' . $row["regd_colour"] . ';" title="' . $row["regd_name"] . '"><img src="/images/sys/admin/blank.gif" width="10" height="10" alt="' . $row["regd_name"] . '"></span><span style="font-size:10px;margin-left:5px;">' . $row["regd_initial"] . '</span>';
					} else {
						unset($regd);
					}

					$all_clients[] = array(
						'cli_id'     => $row["cli_id"],
						'cli_fname'  => $row["cli_fname"],
						'cli_sname'  => $row["cli_sname"],
						'cli_name'   => $row["cli_name"],
						'cli_email'  => $row["cli_email"],
						'cli_addr'   => $row["pro_addr"],
						'cli_neg'    => $neg,
						'regd'       => $regd,
						'cli_branch' => $row["branchTitle"],
						'date'       => $row["date"]
					);

				}
			}

//print_r($all_clients);
//exit;

			if (is_array($all_clients)) {
// loop through the results, and compare to $string array to assign score
				foreach ($all_clients as $key => $val) {

					// assume $string[0] is fname and $string[($keyword_count-1)] is sname
					// cli_fname could be more than one name, compare only the length of the $str.
					// in essence, ignoring any middle names
					// this salso means that part names will match as i am only comparing to first part of string
					// if $str is mar, it will match mark and margaret
					// to overcome this, add a space to the end of str and cli_fname field - NO
					$fname_length = strlen($string[0]);
					$sname_length = strlen($string[($keyword_count - 1)]);
					// exact match, fname AND sname
					if (
						strtolower($string[0] . ' ') == strtolower(substr($val["cli_fname"] . ' ', 0, ($fname_length + 1))) &&
						strtolower($string[($keyword_count - 1)]) == strtolower(substr($val["cli_sname"], 0, $sname_length))
					) {
						$results[0][] = $val;
					} // match to first part of fname AND sname
					elseif (
						strtolower($string[0]) == strtolower(substr($val["cli_fname"], 0, $fname_length)) &&
						strtolower($string[($keyword_count - 1)]) == strtolower(substr($val["cli_sname"], 0, $sname_length))
					) {
						$results[1][] = $val;
					} // match soundex of fname AND exact sname
					elseif (
						strtolower(soundex($string[0])) == strtolower(soundex(substr($val["cli_fname"], 0, $fname_length))) &&
						strtolower($string[($keyword_count - 1)]) == strtolower(substr($val["cli_sname"], 0, $sname_length))
					) {
						$results[2][] = $val;
					} // match exact fname AND soundex of sname
					elseif (
						strtolower($string[0]) == strtolower(substr($val["cli_fname"], 0, $fname_length)) &&
						strtolower(soundex($string[($keyword_count - 1)])) == strtolower(soundex(substr($val["cli_sname"], 0, $sname_length)))
					) {
						$results[3][] = $val;
					} // match exact fname OR exact sname
					elseif (
						strtolower($string[0]) == strtolower(substr($val["cli_fname"], 0, ($fname_length + 1))) ||
						strtolower($string[($keyword_count - 1)]) == strtolower($val["cli_sname"])
					) {
						$results[4][] = $val;
					} // match to soundex of fname AND soundex of sname
					elseif (
						strtolower(soundex($string[0])) == strtolower(soundex(substr($val["cli_fname"], 0, ($fname_length + 1)))) &&
						strtolower(soundex($string[($keyword_count - 1)])) == strtolower(soundex(substr($val["cli_sname"], 0, $sname_length)))
					) {
						$results[5][] = $val;
					} // match to first part of fname OR to first part of sname
					elseif (
						strtolower($string[0] . ' ') == strtolower(substr($val["cli_fname"] . ' ', 0, ($fname_length + 1))) ||
						strtolower($string[($keyword_count - 1)] . ' ') == strtolower(substr($val["cli_sname"] . ' ', 0, $sname_length))
					) {
						$results[6][] = $val;
					} // match to soundex of fname OR soundex of sname
					elseif (
						strtolower(soundex($string[0])) == strtolower(soundex(substr($val["cli_fname"], 0, ($fname_length + 1)))) ||
						strtolower(soundex($string[($keyword_count - 1)])) == strtolower(soundex(substr($val["cli_sname"], 0, $sname_length)))
					) {
						$results[7][] = $val;
					} // all the rest
					else {
						$results[8][] = $val;
					}
				}
			}

			if (!$results) {

// no results
				$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="' . urldecode($returnLink) . '">try again</a></strong></td>
  </tr>
</table>';

			} else {

				ksort($results);

//print_r($results);
				foreach ($results as $key => $val) {
					$result_count = $result_count + count($val);

					foreach ($val as $subarray => $values) {
						$options[$values['cli_id']] = $values['cli_name'] . $values['cli_addr'];

						if ($values["cli_email"]) {
							// not using email form until further testing and logging is done
							//$email = '<a href="client_contact.php?cli_id='.$values["cli_id"].'&return='.$_SERVER['SCRIPT_NAME'].'?'.urlencode($_SERVER['QUERY_STRING']).'"><img src="/images/sys/admin/icons/mail-icon.png" border="0" width="16" height="16" hspace="1" alt="Email this client" /></a>';
							$email = '<a href="mailto:' . $values['cli_email'] . '"><img src="/images/sys/admin/icons/mail-icon.png" border="0" width="16" height="16" hspace="1" alt="Email this client" /></a>';
						} else {
							$email = '<a href="javascript:alert(\'No email address\');"><img src="/images/sys/admin/icons/mail-icon.png" border="0" width="16" height="16" hspace="1" alt="Email this client" /></a>';
						}

						if ($values["cli_branch"]) {
							$class = ' style="background-color:#' . $values["cli_branch"] . '"';
							//$class = ' class="'.strtolower(str_replace(" ","-",$values["cli_branch"])).'"';
						} else {
							unset($class);
						}
						#echo $branch_class;
						$data[] = '
		<tr ' . $class . '>
		<td width="10"><label><input type="checkbox" name="cli_id[]" id="check_client_' . $values["cli_id"] . '" value="' . $values["cli_id"] . '"></label></td>
		<td class="bold" width="180" onmousedown="document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked = (document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked ? false : true);">' . $values["cli_name"] . '</td>
		<td onmousedown="document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked = (document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked ? false : true);">' . $values["cli_email"] . '</td>
		<td onmousedown="document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked = (document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked ? false : true);">
		<div style="height:16px;overflow:hidden;">
		' . $values["cli_addr"] . '
		</div>
		</td>
		<td width="100" nowrap="nowrap" onmousedown="document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked = (document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked ? false : true);">' . $values["date"] . '</td>
		<td width="55" onmousedown="document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked = (document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked ? false : true);">' . $values["regd"] . '</td>
		<td width="45" onmousedown="document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked = (document.getElementById(\'check_client_' . $values["cli_id"] . '\').checked ? false : true);">' . $values["cli_neg"] . '</td>
		<td width="70" nowrap="nowrap">
		<a href="client_edit.php?cli_id=' . $values["cli_id"] . '&searchLink=' . $searchLink . '"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this client" /></a>
		' . $email . '
		<!--<a href="client_contact.php?contact_method=telephone&cli_id=' . $values["cli_id"] . '"><img src="/images/sys/admin/icons/telephone.gif" border="0" width="16" height="16" hspace="1" alt="Telephone this client" />--></a>
		</td>
		</tr>';

					}
				}

				require_once 'Pager/Pager.php';
				$params = array(
					'mode'     => 'Sliding',
					'perPage'  => 21,
					'delta'    => 3,
					'itemData' => $data
				);
				$pager  = & Pager::factory($params);
				$data   = $pager->getPageData();
				$links  = $pager->getLinks();

// convert the querystring into hidden fields
				$qs = parse_str($_SERVER['QUERY_STRING'], $output);
				foreach ($output AS $key => $val) {
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

					$header = '
<div id="header">
<table>
  <tr>
    <td>' . $pager->numItems() . ' records found';
					if ($pager->numPages() > 1) {
						$header .= ' - Page: ' . $back . ' ' . str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;", $links['pages']) . ' ' . $next . '';
					}
					$header .= '</td>
	<td align="right"><a href="' . urldecode($returnLink) . '">Modify Search</a> / <a href="client_search.php">New Search</a></td>
  </tr>
</table>
</div>
';

					$results = '
<table class="resTbl">
  <tr>
    ' . columnHeader(array(
						  array('title' => 'Name', 'column' => 'cli_name', 'colspan' => '2'),
						  array('title' => 'Email', 'column' => 'cli_email'),
						  array('title' => 'Address', 'column' => 'pro_addr'),
						  array('title' => 'Date', 'column' => 'cli_created'),
						  array('title' => 'Reg\'d', 'column' => 'regd_initial'),
						  array('title' => 'Neg', 'column' => 'use_initial'),
						  array('title' => '&nbsp;')
					 ), $_SERVER["QUERY_STRING"]) . '
  </tr>';
					foreach ($data AS $output) {
						$results .= $output;
					}
					$results .= '</table>
';

// not using email form until further testing and logging is done
					/*
					$footer = '
					<div id="footer">
					<table>
					  <tr>
						<td>With selected:
						<input type="hidden" name="return" value="'.$_SERVER['SCRIPT_NAME'].'?'.urlencode($_SERVER['QUERY_STRING']).'">
						<input type="submit" name="contact_method" value="Email" class="button"></td>
					  </tr>
					</table>
					</div>
					';
					*/
				}
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

			$form->addForm("", "get", "client_contact.php");
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Search Results</div>');
			$form->addHtml('<div id="results_table">');
			$form->addHtml($header);
			$form->addHtml($results);
			$form->addHtml($header);
			$form->addHtml('</div>');
			$form->addHtml("</fieldset>\n");

			$navbar_array = array(
				'back'   => array('title' => 'Back', 'label' => 'Back', 'link' => $returnLink),
				'search' => array('title' => 'Client Search', 'label' => 'Client Search', 'link' => 'client_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Client > Search Results");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addScript('js/jquery.js');


			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content_wide">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div><!--' . $sql . '-->');
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