<?php
require_once("../inx/global.inc.php");

$pro_id = 1702;

for ($i = 1; $i <= 20; $i++) {

$sql = "INSERT INTO `deal` ( `dea_status` , `dea_created` , `dea_exchdate` , `dea_compdate` , 
`dea_type` , `dea_prop` , `dea_branch` , `dea_neg` , `dea_applicant` , `dea_vendor` , `dea_solicitor` , 
`dea_lender` , `dea_valueprice` , `dea_marketprice` , `dea_tenure` , `dea_commission` , `dea_commissiontype` , 
`dea_qualifier` , `dea_share` , `dea_otheragent` , `dea_chainfree` , `dea_managed` , `dea_conditions` , 
`dea_notes` , `dea_ptype` , `dea_psubtype` , `dea_built` , `dea_refurbed` , `dea_floors` , `dea_floor` , 
`dea_listed` , `dea_reception` , `dea_bedroom` , `dea_bathroom` , `dea_leaseend` , `dea_strapline` , `dea_description` , 
`dea_available` , `dea_servicecharge` , `dea_groundrent` , `dea_othercharge` , `dea_key` , `dea_board` ) 
VALUES (
 'Available', '2007-06-16 18:18:53', NULL , NULL , 'Sales', '$pro_id', '1', '0', '0', '0', '0', '0', NULL , 
 '349999', 'Leasehold', NULL , '%', 'None', 'Sole', '', 'No', NULL , '', '', '2', '19', '', '', '0', 'NA', 
 'No', '1', '2', '1', '', '', '', NULL , '', '', '', '', 'TBC'
)";
mysql_query($sql);
$pro_id++;
}


/*
for ($i = 1; $i <= 20; $i++) {
echo $i;
$sql = "INSERT INTO property ( `pro_status` , `pro_addr1` , `pro_addr2` , `pro_addr3` , `pro_addr4` , `pro_addr5` , `pro_addr6` , `pro_country` , `pro_postcode` , `pro_area` , `pro_pcid` , `pro_dump` , `pro_std` , `pro_ward` , `pro_authority` , `pro_east` , `pro_north` , `pro_latitude` , `pro_longitude` , `pro_ptype` , `pro_psubtype` , `pro_built` , `pro_refurbed` , `pro_floors` , `pro_floor` , `pro_listed` , `pro_parking` , `pro_garden` , `pro_gardenlength` , `pro_reception` , `pro_bedroom` , `pro_bathroom` , `pro_tenure` , `pro_leaseend` , `pro_location` , `pro_timestamp` ) 
VALUES  ( 'Current', 'Unit $i', '8', 'Dog Kennel Hill', '', 'London', '', 217, 'SE22 8AA', 10, '-1', 
'',
NULL, 
'', 
'Southwark', 
533213, 
175659, 
'51.4641784253246', 
'-0.0837046532739434', 
NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '', '2007-06-16 18:17:46')";
echo $sql."<hr>";
	mysql_query($sql);
	}
	
*/

?>