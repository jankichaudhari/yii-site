<?php

// used to import all old road properties, with separate property and deal record for each, assigning them all to same LL
require_once("../inx/global.inc.php");
exit;

$available = array('10C','16E','11C','9B','14C','15A','5B','14B','17G','11F','8A','9A','10F','8C','18B');
$underoffer = array('18C','17F','10G','10A','10B','16B','17A','17B','18D');
$exchanged = array('11D','18A');


foreach($available AS $addr1) {
// insert property record
$sql = "INSERT INTO `property`
( `pro_status` , `pro_addr1` , `pro_addr2` , `pro_addr3` , `pro_addr4` , `pro_addr5` , `pro_addr6` , `pro_country` , `pro_postcode` , `pro_area` , `pro_pcid` , `pro_dump` , `pro_std` , `pro_ward` , `pro_authority` , `pro_east` , `pro_north` , `pro_latitude` , `pro_longitude` , `pro_ptype` , `pro_psubtype` , `pro_built` , `pro_refurbed` , `pro_floors` , `pro_floor` , `pro_listed` , `pro_parking` , `pro_garden` , `pro_gardenlength` , `pro_reception` , `pro_bedroom` , `pro_bathroom` , `pro_tenure` , `pro_leaseend` , `pro_location` , `pro_timestamp` )
VALUES
( 'Current', '$addr1', 'Pentland House, 30', 'Old Road', '', 'London', '', '217', 'SE13 5SZ', NULL , '-1', '', NULL , '', 'Lewisham', '539274', '175048', '51.4572299331935', '0.00325151766874807', NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , '', NULL , NULL , NULL , NULL , NULL , '', '2007-06-16 23:28:25'
);
";
mysql_query($sql);
$pro_id = mysql_insert_id();

// then create deal record with last insert id
$sql = "INSERT INTO `deal` ( `dea_status` , `dea_created` , `dea_exchdate` , `dea_compdate` ,
`dea_type` , `dea_prop` , `dea_branch` , `dea_neg` , `dea_applicant` , `dea_vendor` , `dea_solicitor` ,
`dea_lender` , `dea_valueprice` , `dea_marketprice` , `dea_tenure` , `dea_commission` , `dea_commissiontype` ,
`dea_qualifier` , `dea_share` , `dea_otheragent` , `dea_chainfree` , `dea_managed` , `dea_conditions` ,
`dea_notes` , `dea_ptype` , `dea_psubtype` , `dea_built` , `dea_refurbed` , `dea_floors` , `dea_floor` ,
`dea_listed` , `dea_reception` , `dea_bedroom` , `dea_bathroom` , `dea_leaseend` , `dea_strapline` , `dea_description` ,
`dea_available` , `dea_servicecharge` , `dea_groundrent` , `dea_othercharge` , `dea_key` , `dea_board` )
VALUES (
 'Available', '2007-05-03 00:00:00', NULL , NULL , 'Lettings', '$pro_id', '1', '0', '0', '0', '0', '0', NULL ,
NULL, NULL, NULL , '%', 'None', 'Sole', '', 'No', NULL , '', '', '2', '19', '', '', '0', 'NA',
 'No', '1', '2', '1', '', '', '', NULL , '', '', '', '', 'TBC'
)";
mysql_query($sql);
$dea_id = mysql_insert_id();

$sql = "INSERT INTO link_client_to_instruction (clientId,dealId) VALUES ('34444','$dea_id')";
mysql_query($sql);
}



foreach($underoffer AS $addr1) {
// insert property record
$sql = "INSERT INTO `property`
( `pro_status` , `pro_addr1` , `pro_addr2` , `pro_addr3` , `pro_addr4` , `pro_addr5` , `pro_addr6` , `pro_country` , `pro_postcode` , `pro_area` , `pro_pcid` , `pro_dump` , `pro_std` , `pro_ward` , `pro_authority` , `pro_east` , `pro_north` , `pro_latitude` , `pro_longitude` , `pro_ptype` , `pro_psubtype` , `pro_built` , `pro_refurbed` , `pro_floors` , `pro_floor` , `pro_listed` , `pro_parking` , `pro_garden` , `pro_gardenlength` , `pro_reception` , `pro_bedroom` , `pro_bathroom` , `pro_tenure` , `pro_leaseend` , `pro_location` , `pro_timestamp` )
VALUES
( 'Current', '$addr1', 'Pentland House, 30', 'Old Road', '', 'London', '', '217', 'SE13 5SZ', NULL , '-1', '', NULL , '', 'Lewisham', '539274', '175048', '51.4572299331935', '0.00325151766874807', NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , '', NULL , NULL , NULL , NULL , NULL , '', '2007-06-16 23:28:25'
);
";
mysql_query($sql);
$pro_id = mysql_insert_id();

// then create deal record with last insert id
$sql = "INSERT INTO `deal` ( `dea_status` , `dea_created` , `dea_exchdate` , `dea_compdate` ,
`dea_type` , `dea_prop` , `dea_branch` , `dea_neg` , `dea_applicant` , `dea_vendor` , `dea_solicitor` ,
`dea_lender` , `dea_valueprice` , `dea_marketprice` , `dea_tenure` , `dea_commission` , `dea_commissiontype` ,
`dea_qualifier` , `dea_share` , `dea_otheragent` , `dea_chainfree` , `dea_managed` , `dea_conditions` ,
`dea_notes` , `dea_ptype` , `dea_psubtype` , `dea_built` , `dea_refurbed` , `dea_floors` , `dea_floor` ,
`dea_listed` , `dea_reception` , `dea_bedroom` , `dea_bathroom` , `dea_leaseend` , `dea_strapline` , `dea_description` ,
`dea_available` , `dea_servicecharge` , `dea_groundrent` , `dea_othercharge` , `dea_key` , `dea_board` )
VALUES (
 'Under Offer', '2007-05-03 00:00:00', NULL , NULL , 'Lettings', '$pro_id', '1', '0', '0', '0', '0', '0', NULL ,
NULL, NULL, NULL , '%', 'None', 'Sole', '', 'No', NULL , '', '', '2', '19', '', '', '0', 'NA',
 'No', '1', '2', '1', '', '', '', NULL , '', '', '', '', 'TBC'
)";
mysql_query($sql);
$dea_id = mysql_insert_id();

$sql = "INSERT INTO link_client_to_instruction (clientId,dealId) VALUES ('34444','$dea_id')";
mysql_query($sql);
}


foreach($exchanged AS $addr1) {
// insert property record
$sql = "INSERT INTO `property`
( `pro_status` , `pro_addr1` , `pro_addr2` , `pro_addr3` , `pro_addr4` , `pro_addr5` , `pro_addr6` , `pro_country` , `pro_postcode` , `pro_area` , `pro_pcid` , `pro_dump` , `pro_std` , `pro_ward` , `pro_authority` , `pro_east` , `pro_north` , `pro_latitude` , `pro_longitude` , `pro_ptype` , `pro_psubtype` , `pro_built` , `pro_refurbed` , `pro_floors` , `pro_floor` , `pro_listed` , `pro_parking` , `pro_garden` , `pro_gardenlength` , `pro_reception` , `pro_bedroom` , `pro_bathroom` , `pro_tenure` , `pro_leaseend` , `pro_location` , `pro_timestamp` )
VALUES
( 'Current', '$addr1', 'Pentland House, 30', 'Old Road', '', 'London', '', '217', 'SE13 5SZ', NULL , '-1', '', NULL , '', 'Lewisham', '539274', '175048', '51.4572299331935', '0.00325151766874807', NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , '', NULL , NULL , NULL , NULL , NULL , '', '2007-06-16 23:28:25'
);
";
mysql_query($sql);
$pro_id = mysql_insert_id();

// then create deal record with last insert id
$sql = "INSERT INTO `deal` ( `dea_status` , `dea_created` , `dea_exchdate` , `dea_compdate` ,
`dea_type` , `dea_prop` , `dea_branch` , `dea_neg` , `dea_applicant` , `dea_vendor` , `dea_solicitor` ,
`dea_lender` , `dea_valueprice` , `dea_marketprice` , `dea_tenure` , `dea_commission` , `dea_commissiontype` ,
`dea_qualifier` , `dea_share` , `dea_otheragent` , `dea_chainfree` , `dea_managed` , `dea_conditions` ,
`dea_notes` , `dea_ptype` , `dea_psubtype` , `dea_built` , `dea_refurbed` , `dea_floors` , `dea_floor` ,
`dea_listed` , `dea_reception` , `dea_bedroom` , `dea_bathroom` , `dea_leaseend` , `dea_strapline` , `dea_description` ,
`dea_available` , `dea_servicecharge` , `dea_groundrent` , `dea_othercharge` , `dea_key` , `dea_board` )
VALUES (
 'Exchanged', '2007-05-03 00:00:00', NULL , NULL , 'Lettings', '$pro_id', '1', '0', '0', '0', '0', '0', NULL ,
NULL, NULL, NULL , '%', 'None', 'Sole', '', 'No', NULL , '', '', '2', '19', '', '', '0', 'NA',
 'No', '1', '2', '1', '', '', '', NULL , '', '', '', '', 'TBC'
)";
mysql_query($sql);
$dea_id = mysql_insert_id();

$sql = "INSERT INTO link_client_to_instruction (clientId,dealId) VALUES ('34444','$dea_id')";
mysql_query($sql);
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