<?php
require_once('../inx/global.inc.php'); 
include("menu.php");





if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
	$return["orderby"] = $orderby;	
	} else {
	$orderby = 'dir_title';
	}
if ($_GET['direction']) {
	$direction = $_GET['direction'];
	} else {
	$direction = 'ASC';
	}
	





$sql = "SELECT *,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_postcode) AS pro_addr 
FROM directory,category
LEFT JOIN property ON directory.dir_pro = property.pro_id
WHERE directory.dir_pro = property.pro_id
AND directory.dir_category = category.cat_id 
ORDER BY $orderby $direction";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();	
if ($numRows !== 0) {	
	while ($row = $q->fetchRow()) {	
	// onClick="trClick(\'client_edit.php?cli_id='.$row["cli_id"].'&searchLink='.$searchLink.'\');"
		$data[] = '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)">		
		<td width="10"><label><input type="checkbox" name="dea_id[]" id="check_dir_'.$row["dir_id"].'" value="'.$row["dir_id"].'"></label></td>
		<td class="bold" width="200">'.$row["dir_title"].'</td>
		<td>'.$row["pro_addr"].'</td>
		<td width="175">'.$row["cat_title"].'</td>
		<td width="50" nowrap="nowrap">
		<a href="edit.php?dir_id='.$row["dir_id"].'"><img src="/images/sys/admin/icons/page_edit.gif" border="0" width="16" height="16" hspace="1" alt="View/Edit entry" /></a>
		<a href="image.php?dir_id='.$row["dir_id"].'"><img src="/images/sys/admin/icons/images.gif" border="0" width="16" height="16" hspace="1" alt="View/Edit images" /></a>
		</td>
		</tr>';
		}
	}

require_once 'Pager/Pager.php';
$params = array(
    'mode'       => 'Sliding',
    'perPage'    => 20,
    'delta'      => 4,
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


$header = '
<div id="header">
<table>
  <tr>
    <td>'.$pager->numItems().' records found';
	if ($pager->numPages() > 1) {
		$header .= ' - Page: '.$back.' '.str_replace("&nbsp;&nbsp;&nbsp;","&nbsp;",$links['pages']).' '.$next.'';
		}
	$header .='</td>
	<td align="right"><a href="'.urldecode($returnLink).'">Modify Search</a> / <a href="property_search.php">New Search</a></td>
  </tr>
</table>
</div>
';



$results = '
<table>
  <tr>
    '.columnHeader(array(
	array('title'=>'Title','column'=>'dir_title','colspan'=>'2'),
	array('title'=>'Address','column'=>'pro_addr'),
	array('title'=>'Category','column'=>'cat_title'),
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
	<input type="submit" name="contact_method" value="Print" class="button"></td>
  </tr>
</table>
</div>
';

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

$form->addForm("","get","property_edit.php");
$form->addHtml("<fieldset>\n");
$form->addLegend('Search Results');
$form->addHtml('<div id="results_table">');
$form->addHtml($header);
$form->addHtml($results);
#$form->addHtml($footer);
$form->addHtml('</div>');
$form->addHtml("</fieldset>\n");


$navbar_array = array(	
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$returnLink),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page = new HTML_Page2($page_defaults);
$page->setTitle("Directory List");
$page->addStyleSheet(GLOBAL_URL.'css/styles.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addBodyContent('<div id="content_wide">');
$page->addBodyContent($menu);
#$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;	

?>