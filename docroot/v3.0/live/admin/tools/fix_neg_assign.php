<?php
require_once("../inx/global.inc.php");
/*

fix mis-assignment of property to neg

*/





// old_id=>new_id
$neg = array(
	
	1=>1, // unassigned, set to me
	10=>1, //mw
	3=>2, //lw
	8=>3, //bm
	2=>4, //je
	26=>5, //jw
	14=>7, //aj
	6=>8, //jr
	13=>9, //gw
	5=>10, //cl
	48=>29, // zm
	4=>12, //tp
	28=>14, //lbate
	45=>16, //mc
	38=>17, //ld
	37=>19, //nh
	30=>20, //jh
	61=>28, //be
	32=>24, //sn
	19=>27, //cw
	35=>30, //jrob
	47=>33, //lbish
	42=>35, //ks
	64=>36, //ba
	53=>37, //eo
	27=>43, //rh
	49=>44, //dr
	63=>45, //jp
	69=>46, //pu
	21=>50 //lo
	
	);


$sql = "SELECT prop_ID, Neg
FROM 
property_old 
WHERE
state_of_trade_id  = 7 OR state_of_trade_id  = 8 OR state_of_trade_id  = 9 OR state_of_trade_id = 10
AND SaleLet = 1
";


$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {	
	
	$data[$row["prop_ID"]] = $row["Neg"];
	
	}

echo count($data);
//print_r($data);
//exit;
foreach($data AS $key=>$val) {
	
	
	if ($neg[$val]) {
		$dea_neg = $neg[$val];
		} else {
		$dea_neg = 1;
		}
	$sql_update = "UPDATE deal SET dea_neg = $dea_neg WHERE dea_oldid = $key";
	
	mysql_query($sql_update);
	echo $sql_update."<hr>";
	}	

//




	
	
?>