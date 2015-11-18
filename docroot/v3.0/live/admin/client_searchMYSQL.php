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

// disable term (pw/pcm) unless term == let
if ($_GET["scope"] == "Lettings") {
	$term_attributes = array();
	} else {
	$term_attributes = array('disabled'=>'disabled');
	}

$formData1 = array(
	'scope'=>array(
		'type'=>'radio',
		'label'=>'Sales or Lettings',
		'value'=>$_GET["scope"],
		'default'=>'Sales',
		'options'=>array('Sales'=>'sale','Lettings'=>'let'),
		'attributes'=>array('onClick'=>'javascript:toggleField(\'term\');')
		),
	'keyword'=>array(
		'type'=>'text',
		'label'=>'Keyword(s)',
		'value'=>$_GET["keyword"],
		'attributes'=>array('class'=>'addr'),
		'tooltip'=>'Seperate multiple keywords with commas'
		),
	'price'=>array(
		'type'=>'text',
		'label'=>'Price',
		'value'=>$_GET["price"],
		'group'=>'Price',
		'attributes'=>array('style'=>'width:120px')
		),
	'term'=>array(
		'type'=>'select',
		'label'=>'Term',
		'value'=>$_GET["term"],
		'group'=>'Price',
		'last_in_group'=>'1',
		'attributes'=>$term_attributes,
		'options'=>array('per week'=>'per week','per month'=>'per month')
		),
	'bed'=>array(
		'type'=>'select_number',
		'label'=>'Minimum Beds'
		)
	);

$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","stage","","1");
$form->addField("hidden","action","","advanced_search");

$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Search Applicants</div>');
$form->addHtml('<div id="'.$formName.'">');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Search",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");
/*
$areas = area(explode("|",$_GET["cli_area"]));
$formName = 'form2';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Areas</div>');
$form->addHtml('<div id="' . $formName . '">');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch1\');"><strong>East Dulwich Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[1].'</tr></table>');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch2\');"><strong>Sydenham Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[2].'</tr></table>');
$form->addHtml('&nbsp;<a href="javascript:checkToggle(document.forms[0], \'branch3\');"><strong>Shad Thames Branch</strong></a>');
$form->addHtml('<table width="100%" cellspacing="0" cellpadding="0"><tr>'.$areas[3].'</tr></table>');
$form->addHtml($form->addDiv($form->makeField("submit",$formName,"","Search",array('class'=>'submit'))));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");
*/
if (!$_GET["viewForm"]) {
	$viewForm = 1;
	}
$additional_js = '
if (!previousID) {
	var previousID = "form'.$viewForm.'";
	}
';

$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$searchLink),
	'search'=>array('title'=>'Client Search','label'=>'Client Search','link'=>'client_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Client > Search");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScriptDeclaration($additional_js);
$page->setBodyAttributes(array('onLoad'=>$onLoad));
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

} else { // if form is submitted

// construct sql
if ($_GET["scope"] == "sale") {
	$q[] = "cli_sales = 'Yes' AND";
	$return["scope"] = 'Sales';
	} elseif ($_GET["scope"] == "let") {
	$q[] = "cli_lettings = 'Yes' AND";
	$return["scope"] = 'Lettings';
	}

if ($_GET["keyword"]) {
	$return["keyword"] = $_GET["keyword"];
	$keyword = str_replace(" ",",",$_GET["keyword"]);
	$keywords = explode(",",$keyword);
	foreach ($keywords AS $keyword) {
		$keyword = trim($keyword);
		$q[] = "(cli_fname LIKE '%$keyword%' OR cli_sname LIKE '%$keyword%' OR cli_email LIKE '%$keyword%') OR cli_id = '$keyword' AND ";
		}
	}

if ($_GET["price"]) {
	$price = numbers_only($_GET["price"]);
	$return["price"] = $price;
	$return["term"] = $_GET["term"];
	if ($_GET["term"] == "pcm" && $scope == 'let') { // only use term for lettings, per week by default
		$price = pcm2pw($price);
		}
	$q[] = "(cli_".$scope."min <= '$price' AND cli_".$scope."max >= '$price') AND ";
	}
if ($_GET["cli_area"]) {
	#$area = array2string($_GET["cli_area"],",");
	$return["cli_area"] = array2string($_GET["cli_area"],"|");
	foreach ($_GET["cli_area"] as $areaid) {
		#$q[] = " cli_area LIKE '%|".$areaid."|%' OR";
		#$area_sql .= " cli_area IN (".$areaid.") OR";
		}
	$area_sql = remove_lastchar($area_sql,"OR");
	#$q[] = "( ".$area_sql." ) AND";
	}
if ($_GET["bed"]) {
	$return["bed"] = $_GET["bed"];
	$q[] = " cli_".$scope."bed <= ".$_GET["bed"]."  AND ";
	}
if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
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
$returnLink = '?'.http_build_query($return);
$searchLink = $_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']);
foreach ($q AS $statement){
	$sql .= $statement;
	}
$sql = remove_lastchar($sql,"AND");
$sql = remove_lastchar($sql,"OR");

$sql = "SELECT cli_id,cli_preferred,CONCAT(cli_fname,' ',cli_sname) AS cli_name, DATE_FORMAT(cli_created, '%D %M %Y') AS date FROM client WHERE $sql ORDER BY $orderby $direction";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
	// onClick="trClick(\'client_edit.php?cli_id='.$row["cli_id"].'&searchLink='.$searchLink.'\');"
		$data[] = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td width="10"><label><input type="checkbox" name="cli_id[]" id="check_client_'.$row["cli_id"].'" value="'.$row["cli_id"].'"></label></td>
		<td class="bold" width="200" onmousedown="document.getElementById(\'check_client_'.$row["cli_id"].'\').checked = (document.getElementById(\'check_client_'.$row["cli_id"].'\').checked ? false : true);">'.$row["cli_name"].'</td>
		<td width="200" onmousedown="document.getElementById(\'check_client_'.$row["cli_id"].'\').checked = (document.getElementById(\'check_client_'.$row["cli_id"].'\').checked ? false : true);">'.$row["cli_preferred"].'</td>
		<td width="200" onmousedown="document.getElementById(\'check_client_'.$row["cli_id"].'\').checked = (document.getElementById(\'check_client_'.$row["cli_id"].'\').checked ? false : true);">'.$row["date"].'</td>
		<td width="70" nowrap="nowrap">
		<a href="client_edit.php?cli_id='.$row["cli_id"].'&searchLink='.$searchLink.'"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this client" /></a>
		<a href="client_contact.php?contact_method=email&cli_id='.$row["cli_id"].'"><img src="/images/sys/admin/icons/mail-icon.png" border="0" width="16" height="16" hspace="1" alt="Email this client" /></a>
		<a href="client_contact.php?contact_method=telephone&cli_id='.$row["cli_id"].'"><img src="/images/sys/admin/icons/telephone.gif" border="0" width="16" height="16" hspace="1" alt="Telephone this client" /></a>
		</td>
		</tr>';
		}
	}


require_once 'Pager/Pager.php';
$params = array(
    'mode'       => 'Sliding',
    'perPage'    => 20,
    'delta'      => 3,
    'itemData'   => $data
);
$pager = & Pager::factory($params);
$data  = $pager->getPageData();
$links = $pager->getLinks();

// convert the querystring into hidden fields
$qs = parse_str($_SERVER['QUERY_STRING'], $output);
foreach ($output AS $key=>$val) {
	if ($key !== "setPerPage") {
		$hidden_fields .= '<input type="hidden" name="'.$key.'" value="'.$val.'">';
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
    <td>'.$pager->numItems().' records found';
	if ($pager->numPages() > 1) {
		$header .= ' - Page: '.$back.' '.str_replace("&nbsp;&nbsp;&nbsp;","&nbsp;",$links['pages']).' '.$next.'';
		}
	$header .='</td>
	<td align="right"><a href="'.urldecode($returnLink).'">Modify Search</a> / <a href="client_search.php">New Search</a></td>
  </tr>
</table>
</div>
';



$results = '
<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'Name','column'=>'cli_name','colspan'=>'2'),
	array('title'=>'Contact','column'=>'cli_preferred'),
	array('title'=>'Date','column'=>'cli_created'),
	array('title'=>'&nbsp;')
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
    <td>With selected:
	<!--<select name="contact_method">
	  <option value="view">View</option>
	  <option value="email">Email</option>
	</select>-->
	<input type="submit" name="contact_method" value="View" class="button">
	<input type="submit" name="contact_method" value="Email" class="button"></td>
  </tr>
</table>
</div>
';

} else {
// no results
$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="'.urldecode($returnLink).'">try again</a></strong></td>
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

$form->addForm("","get","client_contact.php");
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Search Results</div>');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink),
	'search'=>array('title'=>'Client Search','label'=>'Client Search','link'=>'client_search.php')
	);
$navbar = navbar2($navbar_array);

$page->setTitle("Client > Search Results");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
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