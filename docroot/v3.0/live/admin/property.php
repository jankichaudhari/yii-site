<?php
require_once("inx/global.inc.php");

/*
Property summary page
List of available properties
List of pending properties

*/

// show available deals
$sql = "SELECT
dea_id,dea_status,
sot.sot_status,
pro_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',LEFT(pro_postcode, 4)) AS pro_addr,
GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname,'(',cli_id,')') ORDER BY client.cli_id ASC SEPARATOR ', ') AS cli_name
FROM
deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN sot AS sot ON sot.sot_deal = deal.dea_id


LEFT OUTER JOIN sot AS s
	ON s.sot_deal = deal.dea_id
	AND s.sot_date =
       ( SELECT max(s2.sot_date)
           FROM sot AS s2
          WHERE s2.sot_deal = deal.dea_id )


LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id AND capacity = 'Owner'
LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
WHERE
(deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other')
AND dea_type = '".$_SESSION["auth"]["default_scope"]."'
GROUP BY deal.dea_id
ORDER BY s.sot_date DESC";

$sql2 = "SELECT dea_id, dea_status, sot.sot_status, sot.sot_date, pro_id, CONCAT( pro_addr1, ' ', pro_addr2, ' ', pro_addr3, ' ', pro_addr4, ' ', LEFT( pro_postcode, 4 ) ) AS pro_addr, GROUP_CONCAT( DISTINCT CONCAT( cli_fname, ' ', cli_sname, '(', cli_id, ')' )
ORDER BY client.cli_id ASC
SEPARATOR ', ' ) AS cli_name
FROM deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT OUTER JOIN sot AS sot ON sot.sot_deal = deal.dea_id
LEFT OUTER JOIN sot AS s ON s.sot_deal = deal.dea_id
AND sot.sot_date = (
SELECT max( s2.sot_date )
FROM sot AS s2
WHERE s2.sot_deal = deal.dea_id )
LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = deal.dea_id
AND capacity = 'Owner'
LEFT JOIN CLIENT ON link_client_to_instruction.clientId = client.cli_id
WHERE (
deal.dea_status = 'Available'
OR deal.dea_status = 'Under Offer'
OR deal.dea_status = 'Under Offer with Other'
)
AND dea_type = '".$_SESSION["auth"]["default_scope"]."'
GROUP BY deal.dea_id, s.sot_date
ORDER BY sot.sot_date DESC
";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows) {
	$deals = '
	<h1 onClick="javascript:toggleDiv(\'deals\');">On Market ('.$numRows.')</h1>
	<div class="scrollDiv" id="deals" style="height:250px;display:;">
	<table>
	';
	while ($row = $q->fetchRow()) {
		$deals .= '
		<tr class="trOff" onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'/admin4/instruction/summary/id/'.$row["dea_id"].'\')">
		<td height="16">'.$row["pro_addr"].'</td>
		<td>'.preg_replace("/\([a-z0-9\ ]+\)/", "", $row["cli_name"]).'</td>
		<td width="70">'.$row["dea_status"].'<!-- '.$row["sot_date"].'--></td>
		<!--<td width="20"><a href="/admin4/instruction/summary/id/'.$row["dea_id"].'"><img src="/images/sys/admin/icons/edit-icon.png" width="16" height="16" border="0" alt="View/Edit Deal" /></a>--></td>
		</tr>
		';
		}
	$deals .= '</table>
	</div>';
	}


$renderLeft .= '<div>'.$deals.'</div>';


$render = '
<h4>Property</h4>
<p style="margin-left:10px"><a href="property_search.php">Search Property</a></p>
<p style="margin-left:10px"><a href="valuation_add.php">New Property</a></p>';

if ($_SESSION["auth"]["default_scope"] == 'Sales' && in_array('Negotiator',$_SESSION["auth"]["roles"])) {
$render .= '
<p style="margin-left:10px"><a href="property_search.php?stage=1&action=advanced_search&scope=Sales&status%5B%5D=Unknown&status%5B%5D=Completed&status%5B%5D=Withdrawn&status%5B%5D=Disinstructed&neg='.$_SESSION["auth"]["use_id"].'&novendor=novendor">Show old property assigned to me ('.$_SESSION["auth"]["use_fname"].' '.$_SESSION["auth"]["use_sname"].')</a></p>
<p style="margin-left:10px"><a href="property_search.php?stage=1&action=advanced_search&scope=Sales&status%5B%5D=Unknown&status%5B%5D=Completed&status%5B%5D=Withdrawn&status%5B%5D=Disinstructed&neg=1&&novendor=novendor">Show old unnassigned property</a></p>
';
}



$page = new HTML_Page2($page_defaults);
$page->setTitle("Property");
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