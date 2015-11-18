<?php

// used to import all old road properties, with separate property and deal record for each, assigning them all to same LL
require_once("../inx/global.inc.php");



$props = array(2,3,4,5,6,7,8,9,10,11,12,13,14);

//for($i = 2; $i <= 14; $i++) {
foreach ($props AS $num) {


// insert property record
$sql = "INSERT INTO `property`
( `pro_status` , `pro_addr1` , `pro_addr2` , `pro_addr3` , `pro_addr4` , `pro_addr5` , `pro_addr6` , `pro_country` , `pro_postcode` , `pro_area` , `pro_pcid` , `pro_dump` , `pro_std` , `pro_ward` , `pro_authority` , `pro_east` , `pro_north` , `pro_latitude` , `pro_longitude` , `pro_ptype` , `pro_psubtype` , `pro_built` , `pro_refurbed` , `pro_floors` , `pro_floor` , `pro_listed` , `pro_parking` , `pro_garden` , `pro_gardenlength` , `pro_reception` , `pro_bedroom` , `pro_bathroom` , `pro_tenure` , `pro_leaseend` , `pro_location` , `pro_timestamp` )
VALUES
( 'Current', 'Flat $num', '1', 'Chestnut Road', '', 'London', '', '217', 'SE27 9EZ', '28' , '-1', '', NULL , '', 'Lambeth', '532128', '172373', '51.4349020190881', '-0.100540958972472', NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , NULL , '', NULL , NULL , NULL , NULL , NULL , '', '$date_mysql'
);
";
mysql_query($sql);
$pro_id = mysql_insert_id();
//echo $sql."<hr>";


// then create deal record with last insert id
$sql = "INSERT INTO `deal` (
`dea_status` ,
`dea_created` ,
`dea_type` ,
`dea_prop` ,
`dea_branch` ,
`dea_neg` ,
`dea_tenure` ,
`dea_share` ,
`dea_ptype` ,
`dea_psubtype` ,
`dea_floors` ,
`dea_floor` ,
`dea_listed` ,
 `dea_reception` ,
 `dea_bedroom` ,
 `dea_bathroom` ,
 `dea_leaseend` ,
  `dea_strapline` ,
  `dea_description` ,
`dea_available` ,
`dea_servicecharge` ,
`dea_groundrent` ,
`dea_othercharge` ,
`dea_key` ,
`dea_board` )
VALUES (
 'Instructed',
 '2007-06-28 11:18:20',
 'Sales',
 '$pro_id',
 '2',
 '3',
'Leasehold',
'Sole',
 '2',
 '20',
 '1',
 'NA',
 'No',
 '1',
 '2',
 '1',
 '',
 '',
 '',
 NULL ,
 '',
 '',
 '',
 '',
 'TBC'
)";
mysql_query($sql);
$dea_id = mysql_insert_id();
//echo $sql."<hr>";


$sql = "INSERT INTO link_client_to_instruction (clientId,dealId) VALUES ('34477','$dea_id')";
mysql_query($sql);
//echo $sql."<hr>";

}



?>