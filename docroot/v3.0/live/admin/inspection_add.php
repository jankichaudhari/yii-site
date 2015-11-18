<?php
extract($_REQUEST);
require_once(dirname(__FILE__) . "/inx/global.inc.php");
/*
arrange survey:

search property (this is where sales or lettings is chosen)
select one property to survey
preview (add, remove properties from viewing, change order, obtain key details, viewing times etc)
create appointment

automatically add the vendor? maybe...?

*/

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
}
elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
}
else {
	// default to survey_address
	$stage = "inspection_type";
}
if (!$_GET["con_id"]) {
	/* echo "no con_id";
	exit; */
	$con_id = $_POST["con_id"];
} else {
	$con_id = $_GET["con_id"];
}

if (!$_GET["dea_id"]) {
	/* echo "no dea_id";
	exit; */
	$dea_id = $_POST["dea_id"];
} else {
	$dea_id = $_GET["dea_id"];
}

/* include 'property_search_mod.php';

exit; */

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

// start a new page
$page = new HTML_Page2($page_defaults);

switch ($stage):

	case "inspection_type":

//$sql = "SELECT * FROM itype WHERE ity_scope = '".$_SESSION["auth"]["default_scope"]."' ORDER BY ity_title";
		$sql = "SELECT * FROM itype ORDER BY ity_title";
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage() . $sql);
		}
		while ($row = $q->fetchRow()) {
			$itype[$row["ity_id"]] = $row["ity_title"];
		}

		$formData1 = array(
			'app_subtype'=> array(
				'type'      => 'select',
				'label'     => 'Type of Inspection',
				'options'   => $itype,
				'attributes'=> array('class'=> 'wide')
			)
		);

		$form = new Form();

		$form->addForm("", "GET", $PHP_SELF);
		$form->addHtml("<div id=\"standard_form\">\n");
		$form->addField("hidden", "stage", "", "inspection_address");
		$form->addField("hidden", "cli_id", "", $cli_id);
		$form->addField("hidden", "app_id", "", $_GET["app_id"]);
		$form->addField("hidden", "dea_id", "", $dea_id);

		$formName = 'form1';
		$form->addHtml("<fieldset>\n");
		$form->addHtml('<div class="block-header">Arrange Inspection</div>');
		$form->addHtml('<div id="' . $formName . '">');
		$form->addData($formData1, $_GET);
		$form->addHtml($form->addDiv($form->makeField("submit", "", "", "Next", array('class'=> 'submit'))));
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

		$page->setTitle("Arrange Inspection");
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

		break;

/////////////////////////////////////////////////////////////////////////////
// viewing_address
// search deal+property and display any linked properties
// allow mulitple properties
// id app_id is present, add select properties to that appointment, else create new
/////////////////////////////////////////////////////////////////////////////
	case "inspection_address":

// if dea_id is present (i.e. pre-selected property to view), skip property lookup
		if ($_GET["dea_id"]) {
			$carry_deal = '';
			if (is_array($_GET["dea_id"])) {
				foreach ($_GET["dea_id"] as $deal) {
					$carry_deal .= "$deal|";
				}
			} else {
				$carry_deal = $_GET["dea_id"];
			}
			header("Location:?stage=inspector&cli_id=" . $_GET["cli_id"] . "&dea_id=" . $carry_deal . "&app_subtype=" . $_GET["app_subtype"]);
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
				'scope'         => array(
					'type'      => 'radio',
					'label'     => 'Sales or Lettings',
					'value'     => $_GET["scope"],
					'init'      => 'Sales',
					'options'   => array('Sales'   => 'sale',
										 'Lettings'=> 'let'),
					'attributes'=> array('onClick'=> 'javascript:disableTermField(\'scope\',\'term\');')
				),
				'keyword'       => array(
					'type'      => 'text',
					'label'     => 'Keyword(s)',
					'value'     => $_GET["keyword"],
					'attributes'=> array('class'=> 'addr'),
					'tooltip'   => 'Seperate multiple keywords with commas'
				),
				'status_special'=> array(
					'type'   => 'radio',
					'label'  => 'Status<br><br><br><br><br><br><br><br><br>',
					'value'  => $status_special_selected,
					'options'=> $status_special_array_flip
				),
				'branch'        => array(
					'type'      => 'select_branch',
					'label'     => 'Branch',
					'value'     => 'Any',
					'options'   => array(''=> 'Any'),
					'attributes'=> array('style'=> 'width:200px')
				),
				'status'        => array(
					'type'      => 'select',
					'label'     => 'Status',
					'value'     => $_GET["status"],
					'default'   => 'Under Offer',
					// add "any" to top of status list
					// db_enum("deal","dea_status","array")

					'options'   => db_enum("deal", "dea_status", "array"),
					'attributes'=> array('style'=> 'width:200px')
					/*'options'=>array(
								'Available'=>'Available',
								''=>'Any',
								'Instructed'=>'Instructed',
								'Under Offer'=>'Under Offer',
								'Under Offer with Other'=>'Under Offer with Other'
								) */
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
											'per month'=> 'per month'),
					'tooltip'      => 'If you enter a price range, properties without a price will not appear'
				),
				'bed'           => array(
					'type' => 'select_number',
					'label'=> 'Minimum Beds'
				)
			);

			$form = new Form();

			$form->addForm("", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "stage", "", "inspection_address");
			$form->addField("hidden", "action", "", "advanced_search");
			$form->addField("hidden", "cli_id", "", $cli_id);
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);
			$form->addField("hidden", "app_subtype", "", $_GET["app_subtype"]);

			$formName = 'form1';
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Property to Inspect</div>');
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

			$page->setTitle("Arrange Inspection");
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

			/*
			   if ($_GET["keyword"]) {
				   $return["keyword"] = $_GET["keyword"];
				   #$keyword = str_replace(" ",",",$_GET["keyword"]);
				   $keywords = explode(",",$keyword);
				   foreach ($keywords as $keyword) {
					   $keyword = format_data($keyword);
					   $keyword_sql .= "pro_addr1 LIKE '%$keyword%' OR pro_addr2 LIKE '%$keyword%' OR pro_addr3 LIKE '%$keyword%' OR
					   pro_addr4 LIKE '%$keyword%' OR pro_addr5 LIKE '%$keyword%' OR pro_postcode LIKE '%$keyword%' OR
					   cli_fname LIKE '%$keyword%' OR cli_sname LIKE '%$keyword%' OR concat_ws(' ',cli_fname, cli_sname)  LIKE '%$keyword%' OR ";
					   }
				   $keyword_sql = "(".remove_lastchar($keyword_sql,"OR").") AND ";
				   $q[] = $keyword_sql;
				   }
			   */
			if ($_GET["keyword"]) {
				$return["keyword"] = $_GET["keyword"];
				//$keyword = str_replace(" ",",",$_GET["keyword"]);
				$keyword = $_GET["keyword"];

				$keywords = explode(" ", $keyword);

				foreach ($keywords as $keyword) {
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

			if ($_GET["branch"]) {
				$return["branch"] = $_GET["branch"];
				$q[]              = "dea_branch = '" . $_GET["branch"] . "' AND ";
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
				echo error_message($errors);
				exit;
			}
			$returnLink = '?cli_id=' . $cli_id . '&app_id=' . $app_id . '&' . http_build_query($return);
			$searchLink = $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']);
			foreach ($q AS $statement) {
				$sql .= $statement . " ";
			}
			$sql = remove_lastchar($sql, "AND");
			$sql = remove_lastchar($sql, "OR");
			$sql1 = "SELECT
dea_id,dea_prop,dea_status,dea_marketprice,dea_valueprice,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr,
bra_id,bra_title,
cli_id,GROUP_CONCAT(CONCAT(cli_fname,' ',cli_sname))  AS cli_name
FROM deal
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN branch ON deal.dea_branch = branch.bra_id
LEFT JOIN area ON property.pro_area = area.are_id
WHERE
$sql
AND dea_status != 'Archived'
GROUP BY deal.dea_id
ORDER BY $orderby $direction";
			$q    = $db->query($sql1);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql1);
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
		<td width="10"><label><input type="radio" name="dea_id" id="check_deal_' . $row["dea_id"] . '" value="' . $row["dea_id"] . '"></label></td>
		<td class="bold" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $row["pro_addr"] . '</td>
		<td width="200" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $cli_name . '</td>
		<td width="100" onmousedown="document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["dea_id"] . '\').checked ? false : true);">' . $row["dea_status"] . '</td>
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
			$pager_params = array(
				'mode'     => 'Sliding',
				'append'   => false, //don't append the GET parameters to the url
				'path'     => '',
				'fileName' => 'javascript:showResultPage(%d)', //Pager replaces "%d" with the page number...
				'perPage'  => 20, //show n items per page
				'delta'    => 100,
				'itemData' => $data,
			);
			$pager        = & Pager::factory($pager_params);
			$n_pages      = $pager->numPages();
			$links        = $pager->getLinks();

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

			if ($n_pages) {

				for ($i = 1; $i <= $n_pages; ++$i) {
					$results .= '<div class="page" id="page' . $i . '">';

					// put an href in the first page link
					$links['pages'] = str_replace('<b><u>1</u></b>', '<a href="javascript:showResultPage(1)" title="page 1">1</a>', $links['pages']);
					$links['pages'] = str_replace('page', 'Page', $links['pages']);

					$results .= '

<div id="header">
<table>
  <tr>
	<td>' . $numRows . ' records found';
					if ($links[pages]) {
						$results .= ' - Page: ' . str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;", $links['pages']);
					}
					$results .= '</td>
    <td align="right"><a href="' . urldecode($returnLink) . '&stage=inspection_address">Modify Search</a> / <a href="property_search.php">New Search</a></td>
  </tr>
</table>
</div>

	';

					$results .= '<table>
  <tr>
    ' . columnHeader(array(
						  array('title'  => 'Address',
								'column' => 'pro_addr3',
								'colspan'=> '2'),
						  array('title' => 'Vendor(s)',
								'column'=> 'cli_name'),
						  array('title' => 'Status',
								'column'=> 'dea_status'),
						  array('title'=> 'Price'),
						  #array('title'=>'Branch','column'=>'dea_branch'),
						  array('title'=> '&nbsp;')
					 ), $_SERVER["QUERY_STRING"]) . '
  </tr>';
					foreach ($pager->getPageData($i) as $item) {
						$results .= $item;
					}
					$results .= '</table>';
					$results .= '</div>' . "\n\n";
				}
				/*
	if ($pager->numItems()) {

	$header = '
	<div id="header">
	<table>
	  <tr>
		<td>'.$pager->numItems().' records found';
		if ($pager->numPages() > 1) {
			$header .= ' - Page: '.$back.' '.str_replace("&nbsp;&nbsp;&nbsp;","&nbsp;",$links['pages']).' '.$next.'';
			}
		$header .='</td>
		<td align="right"><a href="'.urldecode($returnLink).'">Modify Search</a></td>
	  </tr>
	</table>
	</div>
	';



	$results = '
	<table>
	  <tr>
		'.columnHeader(array(
		array('title'=>'Address','column'=>'pro_addr3','colspan'=>'2'),
		array('title'=>'Vendor','column'=>'cli_name'),
		array('title'=>'Status','column'=>'dea_status'),
		array('title'=>'Price','column'=>'dea_marketprice'),
		array('title'=>'&nbsp;')
		#array('title'=>'Branch','column'=>'dea_branch')
		),$_SERVER["QUERY_STRING"]).'
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
	*/
				$footer = '
<div id="footer">
<table>
  <tr>
    <td>
	<input type="submit" value="Use Selected Properties" class="button"></td>
    <td align="right">';
				if ($links["pages"]) {
					$footer .= 'Page: ' . str_replace("&nbsp;&nbsp;&nbsp;", "&nbsp;", $links['pages']);
				}
				$footer .= '</td>
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
			$form->addField("hidden", "stage", "", "inspector");
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);
			$form->addField("hidden", "app_subtype", "", $_GET["app_subtype"]);
			$form->addField("hidden", "searchLink", "", $searchLink);
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Property to Inspect</div>');
			$form->addHtml('<div id="results_table">');
			$form->addHtml($header);
			$form->addHtml($results);
			$form->addHtml($footer);
			$form->addHtml('</div>');
			$form->addHtml("</fieldset>\n");

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $returnLink . '&stage=inspection_address'),
				'search'=> array('title'=> 'Property Search',
								 'label'=> 'Property Search',
								 'link' => 'property_search.php')
			);
			$navbar       = navbar2($navbar_array);

			$additional_js = '
var n_pages = ' . $n_pages . ';
function showResultPage(n)	{
	for (var count = 1; count <= n_pages; count++) {
		document.getElementById("page"+count).style.display = \'none\';
		}
	document.getElementById("page"+n).style.display = \'block\';

	currentPage = n;
	}
';

			$page->setTitle("Arrange Inspection");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addScriptDeclaration($additional_js);
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content_wide">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
			$page->addBodyContent('<script type="text/javascript" language="javascript">showResultPage(1);</script>');
			$page->display();

			exit;

		}

		break;

// inspector - select or add inspector from contacts
	case "inspector":

// was going to divert contact page, but contact page layout is grouped into companies so not possible to select an individual
//header("Location:contact.php?dest=inspection_add.php&dea_id=".$dea_id."&app_subtype=".$_GET["app_subtype"]);
//exit;

		if (!$_GET["action"]) {

			$sql = "SELECT * FROM ctype ORDER BY cty_title";
			$q   = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql);
			}
			$ctype[] = '(any)';
			while ($row = $q->fetchRow()) {
				$ctype[$row["cty_id"]] = $row["cty_title"];
			}

			$formData1 = array(
				'keyword'=> array(
					'type'      => 'text',
					'label'     => 'Keyword(s)',
					'value'     => $_GET["keyword"],
					'attributes'=> array('class'=> 'addr'),
					'tooltip'   => 'Seperate multiple keywords with commas'
				),
				'type'   => array(
					'type'      => 'select',
					'label'     => 'Type',
					'value'     => $_GET["type"],
					'attributes'=> array('class'=> 'addr'),
					'options'   => $ctype
				)
			);

			$form = new Form();

			$form->addForm("contact_form", "GET", $PHP_SELF);
			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addField("hidden", "stage", "", "inspector");
			$form->addField("hidden", "action", "", "advanced_search");
			$form->addField("hidden", "dea_id", "", $dea_id);
			$form->addField("hidden", "app_subtype", "", $_GET["app_subtype"]);

			// adding an insepctor to an appointment
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);

			$formName = 'form1';
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Search Inspectors</div>');
			$form->addHtml('<div id="' . $formName . '">');
			$form->addData($formData1, $_GET);
			$form->addHtml($form->addDiv($form->makeField("submit", "", "", "Search", array('class'=> 'submit'))));
			$form->addHtml("</div>\n");
			$form->addHtml("</fieldset>\n");

			$navbar_array = array(
				'back'  => array('title'=> 'Back',
								 'label'=> 'Back',
								 'link' => $searchLink),
				'search'=> array('title'=> 'Contact Search',
								 'label'=> 'Contact Search',
								 'link' => 'contact.php')
			);
			$navbar       = navbar2($navbar_array);
			$page         = new HTML_Page2($page_defaults);
			$page->setTitle("Arrange Inspection");
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

			// if form is submitted
		} else {

			// list all inspectors and companies

			// construct sql

			if ($_GET["type"]) {
				$return["type"] = $_GET["type"];
				$type           = $_GET["type"];
				$sql .= "(com_type = $type) AND ";

			}

			if ($_GET["keyword"]) {
				$return["keyword"] = $_GET["keyword"];
				$keyword           = str_replace(" ", ",", $_GET["keyword"]);
				$keywords          = explode(",", $keyword);
				foreach ($keywords AS $keyword) {
					$keyword = trim($keyword);
					$sql .= " (com_title LIKE '%$keyword%' OR con_fname LIKE '%$keyword%' OR con_sname LIKE '%$keyword%') OR ";
				}
			}

			if ($_GET["orderby"]) {
				$orderby           = $_GET["orderby"];
				$return["orderby"] = $orderby;
			} else {
				$orderby = 'con_name';
			}
			if ($_GET['direction']) {
				$direction = $_GET['direction'];
			} else {
				$direction = 'ASC';
			}

			if ($sql) {
				$sql = remove_lastchar($sql, "AND");
				$sql = remove_lastchar($sql, "OR");
				$sql = "WHERE $sql ";
			}
			$returnLink = '?stage=inspector&dea_id=' . $dea_id . '&app_subtype=' . $app_subtype . '&app_id=' . $app_id;
			$searchLink = $_SERVER['PHP_SELF'] . urlencode('?' . $_SERVER['QUERY_STRING']);

			$sql = "SELECT
con_id,CONCAT(con_fname,' ',con_sname) AS con_name,
com_title,
cty_title
FROM contact
LEFT JOIN company ON contact.con_company = company.com_id
LEFT JOIN ctype ON contact.con_type = ctype.cty_id
$sql
ORDER BY $orderby $direction";

			$q = $db->query($sql);
			if (DB::isError($q)) {
				die("db error: " . $q->getMessage() . $sql);
			}
			$numRows = $q->numRows();
			if ($numRows !== 0) {
				while ($row = $q->fetchRow()) {

					$data[] = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td width="10"><label><input type="radio" name="con_id" id="check_deal_' . $row["con_id"] . '" value="' . $row["con_id"] . '"></label></td>
		<td class="bold" onmousedown="document.getElementById(\'check_deal_' . $row["con_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["con_id"] . '\').checked ? false : true);">' . $row["con_name"] . '</td>
		<td onmousedown="document.getElementById(\'check_deal_' . $row["con_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["con_id"] . '\').checked ? false : true);">' . $row["com_title"] . '</td>
		<td onmousedown="document.getElementById(\'check_deal_' . $row["con_id"] . '\').checked = (document.getElementById(\'check_deal_' . $row["con_id"] . '\').checked ? false : true);">' . $row["cty_title"] . '</td>
		<td width="50" nowrap="nowrap">
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
	<td align="right"><a href="contact_add.php?dest=inspection_add.php&returnTo=' . urlencode($searchLink) . '&dea_id=' . $dea_id . '&app_subtype=' . $app_subtype . '&app_id=' . $app_id . '">Add Inspector</a></td>
  </tr>
</table>
</div>
';

				$results = '
<table>
  <tr>
    ' . columnHeader(array(
						  array('title'  => 'Name',
								'column' => 'con_name',
								'colspan'=> '2'),
						  array('title' => 'Company',
								'column'=> 'com_title'),
						  array('title' => 'Type',
								'column'=> 'cty_title'),
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
	<input type="submit" value="Use Selected Inspector" class="button" /></td>
  </tr>
</table>
</div>
';

			} else {
				// no results
				$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="' . urldecode($returnLink) . '">try again</a></strong>, or <strong><a href="contact_add.php?dest=inspection_add.php&returnTo=' . urlencode($searchLink) . '&dea_id=' . $dea_id . '&app_subtype=' . $app_subtype . '&app_id=' . $app_id . '">click here</a></strong> to add a new contact</td>
  </tr>
</table>';
			}

			$form = new Form();

			$form->addHtml("<div id=\"standard_form\">\n");

			$form->addForm("", "get");
			$form->addField("hidden", "stage", "", "appointment");
			$form->addField("hidden", "dea_id", "", $dea_id);
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);
			$form->addField("hidden", "app_subtype", "", $_GET["app_subtype"]);
			$form->addField("hidden", "searchLink", "", $searchLink);
			$form->addHtml("<fieldset>\n");
			$form->addHtml('<div class="block-header">Inspectors</div>');
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

			$page->setTitle("Arrange Inspection");
			$page->addStyleSheet(getDefaultCss());
			$page->addScript('js/global.js');
			$page->addBodyContent($header_and_menu);
			$page->addBodyContent('<div id="content">');
			$page->addBodyContent($navbar);
			$page->addBodyContent($form->renderForm());
			$page->addBodyContent('</div>');
			$page->display();

			exit;

		}

		break;

// appointment
	case "appointment":

// adding contact to appointment, update table and go back to appointnent
		if ($_GET["app_id"]) {

			$db_data["c2a_con"] = $_GET["con_id"];
			$db_data["c2a_app"] = $_GET["app_id"];
			db_query($db_data, "INSERT", "con2app", "c2a_id");

			header("Location:appointment_edit.php?app_id=" . $_GET["app_id"]);
		}

		/*

  create appointment and link to deals via link_deal_to_appointment link table...

  maybe here we should skip to separate appointment page, which will be used to view/edit
  all appointments? this would allow user to add/remove deals from the apointment now and
  at any future time.... leave it here for now to develop, move later

  the appointment page (or this stage) would require cli_id, dea_id(array)

  ch-ch-changes (25/10/06)
  dont save anything to appointment table until date and time have been entered, just carry the dea_id array over.....

  */

		if (!$_GET["con_id"]) {
			/* echo "no con_id";
				exit; */
			$con_id = $_POST["con_id"];
		} else {
			$con_id = $_GET["con_id"];
		}

		if (!$_GET["dea_id"]) {
			/* echo "no dea_id";
				exit; */
			$dea_id = $_POST["dea_id"];
		} else {
			$dea_id = $_GET["dea_id"];
		}

// default date and time set to now
		$app_date = date('d/m/Y');
		$app_time = date('G:i');

		$duration = $default_inspection_duration;

		$sql = "SELECT dea_neg,dea_branch FROM deal WHERE dea_id = " . $dea_id;
		$q   = $db->query($sql);
		if (DB::isError($q)) {
			die("db error: " . $q->getMessage() . $sql);
		}
		while ($row = $q->fetchRow()) {
			$dea_user   = $row["dea_neg"];
			$dea_branch = $row["dea_branch"];
		}
		if (!$dea_user) {
			$dea_user = $_SESSION["auth"]["use_id"];
		}
		if (!$dea_branch) {
			$dea_branch = $_SESSION["auth"]["use_branch"];
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
				'attributes'=> array('class'=> 'medium'),
				'tooltip'   => 'Assigned branch is selected by deafult'
			),
			'app_user'    => array(
				'type'      => 'select_user',
				'label'     => 'Negotiator',
				'value'     => $dea_user,
				'attributes'=> array('class'=> 'medium'),
				'options'   => array(''=> '(unassigned)'),
				'tooltip'   => 'Assigned negotiator is selected by deafult'
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
				'tooltip'   => 'Duration is estimated at ' . $default_inspection_duration . ' minutes'
			),
			'notes'       => array(
				'type'      => 'textarea',
				'label'     => 'Notes',
				'value'     => $app["notes"],
				'attributes'=> array('class'=> 'noteInput')
			)
		);

		if (!$_GET["action"]) {

			$form = new Form();

			$form->addHtml("<div id=\"standard_form\">\n");
			$form->addForm("", "get");
			$form->addField("hidden", "stage", "", "appointment");
			$form->addField("hidden", "action", "", "update");
			$form->addField("hidden", "con_id", "", $con_id);
			$form->addField("hidden", "app_id", "", $_GET["app_id"]);
			$form->addField("hidden", "dea_id", "", $dea_id);
			$form->addField("hidden", "app_subtype", "", $_GET["app_subtype"]);
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

			$page->setTitle("Arrange Inspection");
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

			$dea_id = $dea_id;

			// create appointment row
			$db_data["app_type"]    = 'Inspection';
			$db_data["app_subtype"] = $_GET["app_subtype"];

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

			// extract notes from _GET and store in notes table
			if ($_GET["notes"]) {
				$notes = clean_input($_GET["notes"]);
				unset($db_data["notes"]);
				if ($notes) {
					$db_data2 = array(
						'not_blurb'=> $notes,
						'not_row'  => $app_id,
						'not_type' => 'appointment',
						'not_user' => $_SESSION["auth"]["use_id"],
						'not_date' => $date_mysql
					);
					db_query($db_data2, "INSERT", "note", "not_id");
				}
			}

			// we currently don't have a client, just a deal
			/*
	  // add to cli2app table
	  $db_data["c2a_cli"] = $cli_id;
	  $db_data["c2a_app"] = $app_id;
	  db_query($db_data,"INSERT","cli2app","c2a_id");
	  unset($db_data);
	  */

			// add to con2app table
			$db_data["c2a_con"] = $con_id;
			$db_data["c2a_app"] = $app_id;
			db_query($db_data, "INSERT", "con2app", "c2a_id");
			unset($db_data);

			$db_data["d2a_dea"] = $dea_id;
			$db_data["d2a_app"] = $app_id;
			db_query($db_data, "INSERT", "link_deal_to_appointment", "d2a_id");
			unset($db_data);

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