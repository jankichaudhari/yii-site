<?php
require_once("inx/global.inc.php");

// list users


$page = new HTML_Page2($page_defaults);


if ($_GET["stage"]) {
	$stage = $_GET["stage"];
	} elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	} else {
	$stage = 1;
	}


switch ($stage) {
###########################################################
# stage 1 - list
###########################################################
case 1:





if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
	$return["orderby"] = $orderby;
	} else {
	$orderby = 'use_name';
	}
if ($_GET['direction']) {
	$direction = $_GET['direction'];
	} else {
	$direction = 'ASC';
	}


$searchLink = $_SERVER['PHP_SELF'].urlencode('?'.$_SERVER['QUERY_STRING']);


$sql = "SELECT
CONCAT(use_fname,' ',use_sname) AS use_name,
user.*,
branch.bra_title
FROM
user
LEFT JOIN branch ON user.use_branch = branch.bra_id
WHERE
use_status = 'Active'

ORDER BY $orderby $direction";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {

		if ($row["use_branch"] == 1 || $row["use_branch"] == 3) {
			$ext = ' (Cam)';
			} else {
			$ext = ' (Syd)';
			}
		if (!$row["use_ext"]) {
			$ext = '';
			}
		$data[] = '
	  <tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">
		<td style="padding:5px 3px 5px 3px;background-color: #'.$row["use_colour"].'">&nbsp;</td>
		<td class="bold">'.$row["use_name"].'</td>
		<td width="200">'.$row["bra_title"].'</td>
		<td width="130">'.$row["use_mobile"].'</td>
		<td>'.$row["use_ext"].$ext.'</td>
	  </td>';
		}

	}


require_once 'Pager/Pager.php';
$params = array(
    'mode'       => 'Sliding',
    'perPage'    => 40,
    'delta'      => 2,
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
  </tr>
</table>
</div>
';



$results = '
<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'User','column'=>'use_name','colspan'=>'2'),
	array('title'=>'Branch','column'=>'bra_title'),
	array('title'=>'Mobile'),
	array('title'=>'Ext')
	),$_SERVER["QUERY_STRING"]).'
  </tr>';
foreach ($data AS $output) {
	$results .= $output;
	}
$results .= '</table>
';




} else { // no results

$results = '
<table cellpadding="5">
  <tr>
    <td>Your search returned no matches, please <strong><a href="'.urldecode($returnLink).'">try again</a></strong></td>
  </tr>
</table>';
}


$form = new Form();

$form->addHtml("<div id=\"standard_form\">\n");
$form->addForm("","get",$_SERVER['PHP_SELF']);
$form->addField("hidden","searchLink","",$searchLink);
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Users</div>');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");
$form->addHtml('</div>');


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink),
	'print'=>array('title'=>'Print','label'=>'Print','link'=>'javascript:windowPrint();')
	);
$navbar = navbar2($navbar_array);

$page->setTitle('Users');
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;





###########################################################
# default
###########################################################
default:

}
?>