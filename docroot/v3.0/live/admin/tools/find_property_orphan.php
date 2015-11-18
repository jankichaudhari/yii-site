<?php
require_once("../inx/global.inc.php");


/* 
orpahned property records

property records can be linked via anuy of the following tables:

pro2cli - client address (also found in client.cli_pro, as default address)
deal.dea_prop - address for a deal
pro2con - contact address
pro2com - company address
directory.dir_pro - directory company address

if the property record appears in none of the above, it is not in use.

anything with a pcid <> -1 must be kept
*/


$sql = "SELECT * 
FROM property

LEFT JOIN pro2cli ON property.pro_id = pro2cli.p2c_pro
LEFT JOIN client ON pro2cli.p2c_cli = client.cli_id

LEFT JOIN deal ON property.pro_id = deal.dea_prop

LEFT JOIN pro2con ON property.pro_id = pro2con.p2c_pro
LEFT JOIN contact ON pro2con.p2c_con = contact.con_id

LEFT JOIN pro2com ON property.pro_id = pro2com.p2c_pro
LEFT JOIN company ON pro2com.p2c_com = company.com_id

LEFT JOIN directory ON property.pro_id = directory.dir_pro

LEFT JOIN pro2use ON property.pro_id = pro2use.p2u_pro
LEFT JOIN user ON pro2use.p2u_use = user.use_id

WHERE 

pro_pcid = '-1' 

";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
$count++;

	echo '<a href="../postcode_tools.php?pro_id='.$row["pro_id"].'">'.$row["pro_id"]."</a> - ".$row["pro_addr1"]." ".$row["pro_addr3"]." ".$row["pro_postcode"]."<BR>";
	if ($row["dea_id"]) {
		echo 'Deal <a href="../deal_summary.php?dea_id='.$row["dea_id"].'">'.$row["dea_id"].'</a>';
		if ($row["dea_status"] == 'Available' || $row["dea_status"] == 'Under Offer' || $row["dea_status"] == 'Under Offer with Other' || $row["dea_status"] == 'Exchanged') {
			echo " - ON SITE";
			}
		echo "<br>";
		}
	if ($row["cli_id"]) {
		echo 'Client <a href="../client_edit.php?cli_id='.$row["cli_id"].'">'.$row["cli_id"].'</a><br>';
		}
	if ($row["con_id"]) {
		echo 'Contact <a href="../contact_edit.php?con_id='.$row["con_id"].'">'.$row["con_id"].'</a><br>';
		}
	if ($row["com_id"]) {
		echo 'Company <a href="../company_edit.php?com_id='.$row["com_id"].'">'.$row["com_id"].'</a><br>';
		}
	if ($row["dir_id"]) {
		echo 'Directory <a href="../directory/edit.php?dir_id='.$row["dir_id"].'">'.$row["dir_id"].'</a><br>';
		}
	if ($row["use_id"]) {
		echo 'User <a href="../user.php?stage=2&use_id=='.$row["use_id"].'">'.$row["use_id"].'</a><br>';
		}
	echo "<hr>";
	}

echo $count;


?>