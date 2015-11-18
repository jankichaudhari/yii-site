<?php
require_once("inx/global.inc.php");

if ($_SESSION["auth"]["default_scope"] == 'Sales') {
	$status = 'Pending_New_Client_Sales';
	} elseif ($_SESSION["auth"]["default_scope"] == 'Lettings') {
	$status = 'Pending_New_Client_Lettings';
	} else {
	die("error");
	}

if ($_GET["action"] == 'remove' && $_GET["cli_id"]) {
	$sql = "UPDATE client SET cli_sales = 'No',cli_lettings = 'No', cli_status = 'Active' WHERE cli_id = ".$_GET["cli_id"];
	$q = $db->query($sql);
	}


if ($_GET["orderby"]) {
	$orderby = $_GET["orderby"];
	$return["orderby"] = $orderby;
	} else {
	$_GET["cli_timestamp"] = 'cli_timestamp';
	$orderby = 'cli_timestamp';
	}
if ($_GET['direction']) {
	$direction = $_GET['direction'];
	} else {
	$direction = 'ASC';
	}



$sql = "SELECT
client.*,DATE_FORMAT(cli_created, '%D %M %Y') AS created,DATE_FORMAT(cli_timestamp, '%D %M %Y') AS updated,
CONCAT(client.cli_fname,' ',client.cli_sname) AS cli_name,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS cli_addr,
tel.tel_number
FROM client
LEFT JOIN property ON property.pro_id = client.cli_pro
LEFT JOIN tel ON client.cli_id = tel.tel_cli AND tel.tel_id = (SELECT tel_id FROM tel tel2 WHERE tel2.tel_cli = client.cli_id ORDER BY tel2.tel_ord ASC LIMIT 1)
WHERE cli_status = '$status'
ORDER BY $orderby $direction";
//echo $sql;
$q = $db->query($sql);
if ($q->numRows() == 0) {

	}
while ($row = $q->fetchRow()) {

	$data[] = '
		<tr>
		<td width="10"><label><input type="checkbox" name="cli_id[]" id="check_client_'.$row["cli_id"].'" value="'.$row["cli_id"].'"></label></td>
		<td class="bold" width="180">'.$row["cli_name"].'</td>
		<td>'.$row["cli_email"].'</td>
		<td>'.$row["cli_addr"].'</td>
		<td>'.$row["tel_number"].'</td>
		<td width="150">'.$row["created"].'</td>
		<td width="150">'.$row["updated"].'</td>
		<td width="70" nowrap="nowrap">
		<a href="new_client_questionaire.php?cli_id='.$row["cli_id"].'"><img src="/images/sys/admin/icons/edit-icon.png" border="0" width="16" height="16" hspace="1" alt="View/Edit this client" /></a>

		<a href="javascript:confirmDelete(\'Are you sure you want to permanately delete this client?\',\'?cli_id='.$row["cli_id"].'&amp;action=remove\');"><img width="16" height="16" border="0" alt="Delete" src="/images/sys/admin/icons/cross-icon.png"></a>



		</td>
		</tr>';

	}

require_once 'Pager/Pager.php';
$params = array(
    'mode'       => 'Sliding',
    'perPage'    => 26,
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
	array('title'=>'Email','column'=>'cli_email'),
	array('title'=>'Address','column'=>'cli_addr'),
	array('title'=>'Telephone','column'=>'tel_number'),
	array('title'=>'First Reg\'d','column'=>'cli_created'),
	array('title'=>'Last Updated','column'=>'cli_timestamp'),
	array('title'=>'&nbsp;')
	),$_SERVER["QUERY_STRING"]).'
  </tr>';
foreach ($data AS $output) {
	$results .= $output;
	}
$results .= '</table>
';

} else {

$results = '&nbsp;&nbsp;No pending clients found';
}




$page = new HTML_Page2($page_defaults);
$form = new Form();

$form->addHtml("<div id=\"standard_form\">\n");

$form->addForm("","get","client_contact.php");
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">New Pending Clients</div>');
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

$page->setTitle("Client > New Pending Clients");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content_wide">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div><!--'.$sql.'-->');
$page->display();
?>