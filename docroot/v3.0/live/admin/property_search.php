<?php

require_once("inx/global.inc.php");
/*
Property Search, queries the deal table
JS version (23/03/07) gets all results onto a single page, split into layers, so users can check deals spanning
many pages....
alternatives:
	page number links to submit form and add selected properties to GET
	use ajax somehow

the above solution is too slow with all properties in, so reverting back to traditional method.
ticks wont be retained between pages.
*/

// actions resulting from the search results page, redirects
if ($_GET["action"] == "Arrange Viewing") {
	if (is_array($_GET["dea_id"])) {
		$carry_deal = array2string($_GET["dea_id"]);
	} else {
		$carry_deal = $_GET["dea_id"];
	}
	// if there is a cli_id (we have come from Matching Property button on client_edit).
	if ($_GET["cli_id"]) {
		header("Location:viewing_add.php?stage=appointment&cli_id=" . $_GET["cli_id"] . "&dea_id=" . $carry_deal);
	}
	else {
		header("Location:client_lookup.php?dest=viewing&dea_id=" . $carry_deal . "&searchLink=" . $_GET["searchLink"]);
	}
	exit;
}
elseif ($_GET["action"] == "View") {
	if (is_array($_GET["dea_id"])) {
		$deal_counter = count($_GET["dea_id"]);
		$carry_deal   = array2string($_GET["dea_id"]);
	}
	// show deal_summary page if only one deal is selected
	if ($deal_counter == 1) {
		header("Location:deal_summary.php?dea_id=" . $carry_deal);
	}
	// show deal_multiview page if more than one deal selected
	else {
		echo "multi view of property, to follow";
	}
	exit;
}
elseif ($_GET["action"] == "Print") {
	echo "multi print of property, to follow";
	exit;
}
elseif ($_GET["action"] == "Email") {
	$carry_deal = array2string($_GET["dea_id"]);
	header("Location:email_deal_multi.php?dea_id=$carry_deal&searchLink=" . urlencode($_GET["searchLink"]));
	exit;
}

if (!$_GET["stage"]) {
	$stage = 1;
} else {
	$stage = $_GET["stage"];
}

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage) {
###########################################################
# stage 1 - detailed search
###########################################################
	case 1:

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

			$status_special_array = array(
				'available'=> 'Available &nbsp;<span style="color:#666666; font-size: 10px">Available only</span><br />',
				'onsite'   => 'On Website &nbsp;<span style="color:#666666; font-size: 10px">Available, Under Offer and Exchanged</span><br />',
				'pending'  => 'Coming On &nbsp;<span style="color:#666666; font-size: 10px">Instructions and Production</span><br />',
				'won'      => 'Won &nbsp;<span style="color:#666666; font-size: 10px">Completed with us</span><br />',
				'lost'     => 'Lost &nbsp;<span style="color:#666666; font-size: 10px">Withdrawn, Disinstructed and Sold by Other</span><br />',
				'all'      => 'Everything &nbsp;<span style="color:#666666; font-size: 10px">The whole lot</span><br />'
			);

			if (!$_GET["status_special"]) {
				$_GET["status_special"] = 'available';
			}
			$status_special_selected = $status_special_array[$_GET["status_special"]]; // str_replace('<br /><br />','<br />',$status_special_array[$_GET["status_special"]]);
			unset($_GET["status_special"]);

			$status_special_array_flip = array_flip($status_special_array);
			$formData1                 = array(
				'scope'         => array(
					'type'      => 'radio',
					'label'     => 'Sales or Lettings',
					'value'     => $_GET["scope"],
					'default'   => $_SESSION["auth"]["default_scope"],
					'options'   => array('Sales'   => 'Sales',
										 'Lettings'=> 'Lettings'),
					'attributes'=> array('onClick'=> 'javascript:disableTermField(\'scope\',\'term\');javascript:disableTermField(\'scope\',\'dea_term\');')
				),
				'keyword'       => array(
					'type'      => 'text',
					'label'     => 'Keyword(s)',
					'value'     => $_GET["keyword"],
					'attributes'=> array('style'=> 'width:400px'),
					'tooltip'   => 'Any part of property address, or vendor/landlord name(s). Seperate multiple keywords with commas'
				),
				'type'          => array(
					'type'      => 'select',
					'label'     => 'Property Type',
					'value'     => $_GET["type"],
					#'options'=>array('House'=>'1','Apartment'=>'2','Other'=>'3')
					'options'   => array('' => 'Any',
										 '1'=> 'House',
										 '2'=> 'Apartment',
										 '3'=> 'Other'),
					'attributes'=> array('style'=> 'width:150px')
				),
				'status_special'=> array(
					'type'   => 'radio',
					'label'  => 'Status<br><br><br><br><br><br><br><br><br>',
					'value'  => $status_special_selected,
					'options'=> $status_special_array_flip
				),
				'price_min'     => array(
					'type'      => 'text',
					'label'     => 'Price From',
					'value'     => $_GET["price"],
					'group'     => 'Price Range',
					'init'      => '(minimum)',
					'attributes'=> array('style'  => 'width:100px',
										 'onFocus'=> 'javascript:clearField(this,\'(minimum)\')')
				),
				'price_max'     => array(
					'type'      => 'text',
					'label'     => 'Price To',
					'value'     => $_GET["price"],
					'group'     => 'Price Range',
					'init'      => '(maximum)',
					'attributes'=> array('style'  => 'width:100px',
										 'onFocus'=> 'javascript:clearField(this,\'(maximum)\')')
				),
				'term'          => array(
					'type'         => 'select',
					'label'        => 'Term',
					'value'        => $_GET["term"],
					'group'        => 'Price Range',
					'last_in_group'=> '1',
					'attributes'   => $term_attributes,
					'options'      => array('per week' => 'per week',
											'per month'=> 'per month')
				),
				'dea_term'      => array(
					'type'      => 'select',
					'label'     => 'Rental Term',
					'value'     => $_GET["dea_term"],
					'attributes'=> $term_attributes,
					'options'   => join_arrays(array(array(''=> 'Any'), db_enum('deal', 'dea_term', 'array')))
				),
				'bed'           => array(
					'type' => 'select_number',
					'label'=> 'Minimum Beds',
					'value'=> $_GET["bed"]
				),
				'maxBed'           => array(
					'type' => 'select_number',
					'label'=> 'Maximum Beds',
					'value'=> $_GET["maxBed"]
				)
			);

// prevent form class from select current user's branch
			if (!$_GET["branch"]) {
				$_GET["branch"] = "999";
			}
			$formData2 = array(
				'status'  => array(
					'type'   => 'checkbox_table',
					'label'  => 'Status',
					'value'  => $_GET["status"],
					'options'=> array(
						'Available'     => 'Available',
						'Under Offer'   => 'Under Offer',
						'Exchanged'     => 'Exchanged',
						'Completed'     => 'Completed',
						'Valuation'     => 'Valuation',
						'Instructed'    => 'Instructed',
						'Production'    => 'Production',
						'Proofing'      => 'Proofing',
						'U/O with Other'=> 'U/O with Other',
						'Collapsed'     => 'Collapsed',
						'Withdrawn'     => 'Withdrawn',
						'Not Instructed'=> 'Not Instructed',
						'Disinstructed' => 'Disinstructed',
						'Comparable'    => 'Comparable',
						'Chain'         => 'Chain',
						'Unknown'       => 'Unknown'
					)
				),
				'neg'     => array(
					'type'      => 'select_user',
					'label'     => 'Negotiator',
					'value'     => $_GET["neg"],
					'options'   => array(''=> 'Any'),
					'attributes'=> array('class'=> 'wide')
				),
				'branch'  => array(
					'type'      => 'select_branch',
					'label'     => 'Branch',
					'value'     => $_GET["branch"],
					'options'   => array(''=> 'Any'),
					'attributes'=> array('class'=> 'wide')
				),
				/* added 07/09/08 */
				'contract'=> array(
					'type'      => 'select',
					'label'     => 'Contract',
					'value'     => $_GET["contract"],
					'options'   => join_arrays(array(array(''=> 'Any'), db_enum('deal', 'dea_contract', 'array'))),
					'attributes'=> array('class'=> 'wide')
				),
				/* added 07/09/08 */
				'hip'     => array(
					'type'      => 'select',
					'label'     => 'HIP',
					'value'     => $_GET["hip"],
					'options'   => join_arrays(array(array(''=> 'Any'), db_enum('deal', 'dea_hip', 'array'))),
					'attributes'=> array('class'=> 'wide')
				),
				/* added 07/09/08 */
				'board'   => array(
					'type'      => 'select',
					'label'     => 'Board',
					'value'     => $_GET["board"],
					'options'   => join_arrays(array(array(''=> 'Any'), db_enum('deal', 'dea_board', 'array'))),
					'attributes'=> array('class'=> 'wide')
				)
			);

			$form = new Form();

			$form->addForm("", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "stage", "", "1");
			$form->addField("hidden", "action", "", "advanced_search");

			$buttons = $form->makeField("submit", "", "", "Search", array('class'=> 'submit'));
			$buttons .= $form->makeField("button", "", "", "Reset", array('class'  => 'button',
																		  'onClick'=> 'javascript:location.href=\'' . $PHP_SELF . '\';'));

			$formName = 'form1';
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Property Search</div>');
			$form->addHtml('<div id="' . $formName . '">');
			$form->addData($formData1, $_GET);
			$form->addHtml($form->addDiv($buttons));

			$form->addHtml("</div>\n");
			$form->addHtml("</fieldset>\n");

			$formName = 'area';
			$areas    = area($_GET["cli_area"], "cli_", 4); //,$_SESSION["auth"]["default_scope"]
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Areas</div>');
			$form->addHtml('<div id="' . $formName . '" style="display:none" style="margin-left:10px">');
			$form->addHtml('<a href="javascript:checkToggle(document.forms[0], \'branch1\');" style="margin-left:5px;"><strong>Camberwell Branch</strong></a> <!--<span style="font-size:10px;color:#999999">(in brackets: Available &amp; Under Offer ' . $_SESSION["auth"]["default_scope"] . ')</span>-->');
			$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:5px"><tr>' . $areas[1] . '</tr></table>');
			$form->addHtml('<a href="javascript:checkToggle(document.forms[0], \'branch2\');" style="margin-left:5px;"><strong>Sydenham Branch</strong></a> <!--<span style="font-size:10px;color:#999999">(in brackets: Available &amp; Under Offer ' . $_SESSION["auth"]["default_scope"] . ')</span>-->');
			$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:5px"><tr>' . $areas[2] . '</tr></table>');
			$form->addHtml($form->addDiv($buttons));
			$form->addHtml("</div>\n");
			$form->addHtml("</fieldset>\n");

			$formName = 'other';
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Other</div>');
			$form->addHtml('<div id="' . $formName . '">');
			$form->addData($formData2, $_GET);
			$form->addHtml($form->addDiv($buttons));
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
			$onLoad .= ' document.forms[0].keyword.focus();';

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $searchLink),
				'search'=> array('title'=> 'Property Search',
								 'label'=> 'Property Search',
								 'link' => 'property_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$page->setTitle("Property > Search");
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

			if ($_GET["scope"] == "Sales") {
				$q[]             = "dea_type = 'Sales' AND ";
				$return["scope"] = 'Sales';
				$owner           = 'Vendor';
			} elseif ($_GET["scope"] == "Lettings") {
				$q[]             = "dea_type = 'Lettings' AND ";
				$return["scope"] = 'Lettings';
				$owner           = 'Landlord';
			}

// client id is specified, select requirements and make sql
			if ($_GET["cli_id"]) {

				$sql_cli = "SELECT * FROM client WHERE cli_id = " . $_GET["cli_id"];
				$q_cli   = $db->query($sql_cli);
				if (DB::isError($q_cli)) {
					die("db error: " . $q->getMessage() . $sql_cli);
				}
				while ($row = $q_cli->fetchRow()) {

					if ($return["scope"] == "Sales") {

						$ptype_array = explode("|", $row["cli_saleptype"]);
						if (is_array($ptype_array)) {
							$sql = "(";
							foreach ($ptype_array as $val) {
								if (trim($val)) {
									$sql .= "dea_psubtype = $val OR ";
								}
							}
							$sql = remove_lastchar($sql, "OR") . ") AND ";
						}

						$sql .= "dea_type = 'Sales' AND
			(dea_marketprice IS NULL OR (dea_marketprice > '" . $row["cli_salemin"] . "' AND dea_marketprice <= '" . $row["cli_salemax"] . "')) AND
			dea_bedroom >= " . $row["cli_salebed"] . " AND (dea_status = 'Available' OR dea_status = 'Under Offer')";

					}
					elseif ($return["scope"] == "Lettings") {

						$ptype_array = explode("|", $row["cli_letptype"]);
						if (is_array($ptype_array)) {
							$sql = "(";
							foreach ($ptype_array as $val) {
								$sql .= "dea_psubtype = $val OR ";
							}
							$sql = remove_lastchar($sql, "OR") . ") AND ";
						}

						$sql .= "dea_type = 'Lettings' AND
			(dea_marketprice IS NULL OR (dea_marketprice > '" . $row["cli_letmin"] . "' AND dea_marketprice <= '" . $row["cli_letmax"] . "')) AND
			dea_bedroom >= " . $row["cli_letbed"] . " AND (dea_status = 'Available' OR dea_status LIKE 'Under Offer%')";

					}
				}

				$sql = "SELECT
dea_id,dea_prop,dea_status,dea_marketprice,dea_valueprice,dea_type,dea_qualifier,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,
bra_id,bra_title,
cli_id,GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname))  AS cli_name,
(SELECT unix_timestamp(sot_date) FROM sot WHERE sot.sot_deal = deal.dea_id ORDER BY sot.sot_date DESC LIMIT 1)  AS dateUpdated
FROM deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id AND link_client_to_instruction.capacity = 'Owner'
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
WHERE
$sql
GROUP BY deal.dea_id
ORDER BY $orderby $direction
";

// no client id, use search for values
			} else {

				if ($_GET["type"]) {
					$return["type"] = $_GET["type"];
					if (is_array($_GET["type"])) {
						foreach ($_GET["type"] AS $dea_ptype) {
							$ptype_sql .= "dea_ptype = '" . $dea_ptype . "' OR ";
						}
						$ptype_sql = "(" . remove_lastchar($ptype_sql, "OR") . ") AND ";
						$q[]       = $ptype_sql;
					}
					else {
						$q[] = "dea_ptype = '" . $_GET["type"] . "' AND ";
					}
				}

				if ($_GET["keyword"]) {
					$return["keyword"] = $_GET["keyword"];
					$tmpkeywordstr = trim($_GET["keyword"]);
					if (strlen(trim($tmpkeywordstr)) > 1) {

						$keywords = explode(",", $_GET["keyword"]);
						foreach ($keywords AS $keyword) {

							if (strlen(trim($keyword)) > 1) { // ignoring words 2 or less
								$keyword = trim($keyword);

								// get rid of st, temporary solution - why did i do this? dont know, had to add space before st
								$keyword = str_ireplace(" st ", "", $keyword);

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
				}

				if ($_GET["scope"] == "Lettings" && urldecode($_GET["term"]) == 'per month') {
					$return["term"] = $_GET["term"];
					$price_min      = pcm2pw($_GET["price_min"]);
					$price_max      = pcm2pw($_GET["price_max"]);
				} else {
					$price_min = $_GET["price_min"];
					$price_max = $_GET["price_max"];
				}

				if ($price_min && $price_min !== '(minimum)') {
					$return["price_min"] = $_GET["price_min"];
					$q[]                 = "(dea_marketprice IS NULL OR dea_marketprice > '" . $price_min . "') AND ";
				}

				if ($price_max && $price_max !== '(maximum)') {
					$return["price_max"] = $_GET["price_max"];
					$q[]                 = "(dea_marketprice IS NULL OR dea_marketprice < '" . $price_max . "') AND ";
				}

				if ($_GET["status"]) {
					// using individual statuses disbales status_special
					unset($_GET["status_special"]);
					$return["status"] = array2string($_GET["status"]);
					foreach ($_GET["status"] AS $dea_status) {
						if ($dea_status == 'U/O with Other') {
							$dea_status = 'Under Offer with Other';
						}
						$status_sql .= "dea_status = '" . $dea_status . "' OR ";
					}
					$status_sql = "(" . remove_lastchar($status_sql, "OR") . ") AND ";
					$q[]        = $status_sql;
				}

				if ($_GET["status_special"] == 'available') {
					$return["status_special"] = $_GET["status_special"];
					$q[]                      = "(dea_status = 'Available') AND ";
				}
				elseif ($_GET["status_special"] == 'onsite') {
					$return["status_special"] = $_GET["status_special"];
					$q[]                      = "(dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Under Offer with Other' OR dea_status = 'Exchanged') AND ";
				}
				elseif ($_GET["status_special"] == 'pending') {
					$return["status_special"] = $_GET["status_special"];
					$q[]                      = "(dea_status = 'Valuation' OR dea_status = 'Instructed' OR dea_status = 'Production' OR dea_status = 'Proofing') AND ";
				}
				elseif ($_GET["status_special"] == 'won') {
					$return["status_special"] = $_GET["status_special"];
					$q[]                      = "(dea_status = 'Completed') AND ";
				}
				elseif ($_GET["status_special"] == 'lost') {
					$return["status_special"] = $_GET["status_special"];
					$q[]                      = "(dea_status = 'Withdrawn' OR dea_status = 'Disinstructed' OR dea_status = 'Sold by Other' OR dea_status = 'Collapsed' OR dea_status = 'Not Instructed') AND ";
				}

				if ($_GET["bed"]) {
					$return["bed"] = $_GET["bed"];
					$q[]           = "dea_bedroom >= '" . $_GET["bed"] . "' AND ";
				}

				if ($_GET["maxBed"]) {
					$return["maxBed"] = $_GET["maxBed"];
					$q[]           = "dea_bedroom <= '" . $_GET["maxBed"] . "' AND ";
				}

// areas
				if ($_GET["cli_area"]) {
					$return["cli_area"] = $_GET["cli_area"];
					foreach ($_GET["cli_area"] AS $are_id) {
						$area_sql .= "pro_area = '" . $are_id . "' OR ";
					}
					$area_sql = "(" . remove_lastchar($area_sql, "OR") . ") AND ";
					$q[]      = $area_sql;
				}

// rental term
				if ($_GET["dea_term"]) {
					$return["dea_term"] = $_GET["dea_term"];
					$q[]                = "dea_term = '" . $_GET["dea_term"] . "' AND ";
				}

// showing property with vendor set to cli_id = 1 Temporary Vendor
				if ($_GET["novendor"]) {
					$q[] = "link_client_to_instruction.clientId = 1 AND ";
				}

				if ($_GET["neg"]) {
					$return["neg"] = $_GET["neg"];
					$q[]           = "dea_neg = '" . $_GET["neg"] . "' AND ";
				}
				if ($_GET["branch"]) {
					$return["branch"] = $_GET["branch"];
					$q[]              = "dea_branch = '" . $_GET["branch"] . "' AND ";
				}
				/* added 07/09/08 */
				if ($_GET["hip"]) {
					$return["hip"] = $_GET["hip"];
					$q[]           = "dea_hip = '" . $_GET["hip"] . "' AND ";
				}
				/* added 07/09/08 */
				if ($_GET["contract"]) {
					$return["contract"] = $_GET["contract"];
					$q[]                = "dea_contract = '" . $_GET["contract"] . "' AND ";
				}
				/* added 07/09/08 */
				if ($_GET["board"]) {
					$return["board"] = $_GET["board"];
					$q[]             = "dea_board = '" . $_GET["board"] . "' AND ";
				}

				if (!$q) {
					$errors[] = 'Please enter some search criteria';
					echo error_message($errors);
					exit;
				}
				if ($_GET["returnLink"]) {
					$returnLink = urldecode($_GET["returnLink"]);
				} else {
					$returnLink = '?' . http_build_query($return);
				}
				$searchLink = $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']);
				foreach ($q AS $statement) {
					$sql .= $statement . " \n";
				}
				$sql = remove_lastchar($sql, "AND");
				$sql = remove_lastchar($sql, "OR");
				$sql = "SELECT
dea_id,dea_prop,dea_status,dea_marketprice,dea_valueprice,dea_type,dea_qualifier,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,
bra_id,bra_title,
cli_id,GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname))  AS cli_name,
(SELECT unix_timestamp(sot_date) FROM sot WHERE sot.sot_deal = deal.dea_id ORDER BY sot.sot_date DESC LIMIT 1)  AS dateUpdated
FROM deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id AND link_client_to_instruction.capacity = 'Owner'
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN area ON property.pro_area = area.are_id
WHERE
$sql
AND dea_status != 'Archived'
GROUP BY deal.dea_id
ORDER BY $orderby $direction
";

			} // end cli_id if

			$q = $db->query($sql);
			if (DB::isError($q)) {
				echo "<pre style='color:blue' title='" . __FILE__ . "'>" . basename(__FILE__) . ":" . __LINE__ . "<br>";
				echo $q->getMessage() . "<br>";
				print_r($sql);
				echo "</pre>";
			}
			$numRows = $q->numRows();
			if ($numRows !== 0) {
				while ($row = $q->fetchRow()) {

					if ($row["dea_type"] == 'Lettings') {
						$price_suffix = ' p/w';
					} elseif ($row["dea_qualifier"]) {
						//$price_suffix = ' ('.$row["dea_qualifier"].')';
					}

					if ($row["dea_marketprice"]) {
						$price = format_price($row["dea_marketprice"]) . $price_suffix;
					} else {
						$price = 'n/a';
					}

					if ($row["dateUpdated"] > 0) {
						$dateUpdated = date('jS M y', $row["dateUpdated"]);
					} else {
						$dateUpdated = 'Unknown';
					}

					$cli_name = str_replace(",", ", ", $row["cli_name"]);
					$data[]   = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" ondblclick="document.location.href = \'/admin4/instruction/summary/id/' . $row["dea_id"] . '\'">
		<td width="10"><label><input type="checkbox" name="dea_id[]" id="check_deal_' . $row["dea_id"] . '" value="' . $row["dea_id"] . '"></label></td>
		<td class="bold" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $row["pro_addr"] . '</td>
		<td width="230" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $cli_name . '</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . str_replace('Under Offer with Other', 'U/O with Other', $row["dea_status"]) . '</td>
		<td width="80" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $dateUpdated . '</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $price . '</td>
		<!--<td width="100" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $row["bra_title"] . '</td>-->
		<td width="50" nowrap="nowrap">
		<a href="/admin4/instruction/summary/id/' . $row["dea_id"] . '"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this property" /></a>
		<a href="javascript:dealPrint(\'' . $row["dea_id"] . '\');"><img src="/images/sys/admin/icons/print-icon.png" border="0" width="16" height="16" hspace="1" alt="Print this property" /></a>
		</td>
		</tr>';
				}
			}

			require_once 'Pager/Pager.php';
			$params = array(
				'mode'       => 'Sliding',
				'perPage'    => 26,
				'delta'      => 5,
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

				$header = '
<div id="header">
<table>
  <tr>
    <td>' . $pager->numItems() . ' records found';
				if ($pager->numPages() > 1) {
					$header .= ' - Page: ' . $back . ' ' . str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;", $links['pages']) . ' ' . $next . '';
				}
				$header .= '</td>
	<td align="right"><a href="' . urldecode($returnLink) . '">Modify Search</a> / <a href="property_search.php">New Search</a></td>
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
						  array('title' => 'Vendor(s)',
								'column'=> 'cli_name'),
						  array('title' => 'Status',
								'column'=> 'dea_status'),
						  array('title' => 'Date',
								'column'=> 'dateUpdated'),
						  array('title' => 'Price',
								'column'=> 'dea_marketprice'),
						  #array('title'=>'Branch','column'=>'dea_branch'),
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
	<input type="submit" name="action" value="Arrange Viewing" class="button">
	<input type="submit" name="action" value="Email" class="button"></td>
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
			$form->addField("hidden", "cli_id", "", $_GET["cli_id"]);

			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Search Results</div>');
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

// js to control recordset divs
			/*
   $additional_js = '
   var n_pages = '.$n_pages.';
   function showResultPage(n)	{
	   for (var count = 1; count <= n_pages; count++) {
		   document.getElementById("page"+count).style.display = \'none\';
		   }
	   document.getElementById("page"+n).style.display = \'block\';
	   document.getElementById("loading").style.display = \'none\';
	   currentPage = n;
	   }
   ';
   */
			$page->setTitle("Property > Search Results");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addSCriptDeclaration($additional_js);
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content_wide">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
//$page->addBodyContent($showResultsPage);
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
