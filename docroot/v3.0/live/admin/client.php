<?php
require_once("inx/global.inc.php");

/*
Property summary page
List of available properties
List of pending properties

*/

if ($_SESSION["auth"]["default_scope"] == 'Sales') {
	$sql_inner = " cli_sales = 'Yes' ";
	} elseif ($_SESSION["auth"]["default_scope"] == 'Lettings') {
	$sql_inner = " cli_lettings = 'Yes' ";
	}

// show clients registered in last 7 days
$now = strtotime($date_mysql);
$range = 14;
$rangesec = (60*60*24*$range);
$dateRange = ($now - $rangesec);
$dateRange = date(MYSQL_DATE_FORMAT,$dateRange);
$sql = "SELECT
cli_id,cli_sales,cli_lettings,
CONCAT(cli_fname,' ',cli_sname) AS cli_name,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
GROUP_CONCAT(DISTINCT CONCAT(tel_number,' (',tel_type,')') ORDER BY tel_ord ASC SEPARATOR '<br />') AS tel
FROM
client
LEFT JOIN property ON client.cli_pro = property.pro_id
LEFT JOIN tel ON client.cli_id = tel.tel_cli
WHERE
cli_created > '$dateRange'
AND $sql_inner
GROUP BY cli_id
ORDER BY cli_created DESC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows) {
	$clients = '
	<h1 onClick="javascript:toggleDiv(\'clients\');">Registered for '.$_SESSION["auth"]["default_scope"].' in past '.$range.' days ('.$numRows.')</h1>
	<div class="scrollDiv" id="clients" style="height:300px;display:;">
	<table>
	';
	while ($row = $q->fetchRow()) {
		$clients .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'client_edit.php?cli_id='.$row["cli_id"].'&searchLink='.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'\')">
		<td height="16">'.$row["cli_name"].'<br />'.$row["pro_addr"].'</td>
		<td>'.$row["tel"].'</td>
		<td>Sales: '.$row["cli_sales"].'<br />Lettings: '.$row["cli_lettings"].'</td>
		</tr>
		';
		}
	$clients .= '</table>
	</div>';
	}


$renderLeft .= '<div>'.$clients.'</div>';


$render = '
<h4>Applicants</h4>
<p style="margin-left:10px"><a href="client_search.php">Search Applicants</a></p>
<p style="margin-left:10px"><a href="applicant_add.php">New Applicant</a></p>
';



$page = new HTML_Page2($page_defaults);
$page->setTitle("Applicants");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="home">');
$page->addBodyContent($render);
$page->addBodyContent('<div class="home Right">'.$renderRight.'</div>');
$page->addBodyContent('<div class="home Left">'.$renderLeft.'</div>');
$page->addBodyContent('</div>');
$page->display();


?>