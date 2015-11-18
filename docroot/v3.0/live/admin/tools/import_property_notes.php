<?php

/*

fix mis-assignment of property to neg

*/
require_once("../inx/global.inc.php");







$sql = "SELECT prop_ID, notes,
GROUP_CONCAT(CONCAT(DATE_FORMAT(not_date,'%D %M %Y'),': ',not_note) ORDER BY not_date ASC SEPARATOR '\n')  AS note
FROM 
property_old
LEFT JOIN note_old ON not_row = prop_ID AND not_table = 'property' 

group by prop_ID
";


$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {	
	
	if ($row["note"]) {
		$data[$row["prop_ID"]] = "Imported Notes: 
".trim($row["note"])."
".trim($row["notes"]);
		}
	}

echo count($data);
echo "<hr>";

foreach($data AS $key=>$val) {
	
	$sql2 = "SELECT dea_id FROM deal WHERE dea_oldid = $key";
	$dea_id = $db->getOne($sql2);
	
	
	if ($dea_id) {
		$db_data['not_type'] = 'deal_general';
		$db_data['not_row'] = $dea_id ;
		$db_data['not_user'] = 1; // mark for all notes
		$db_data['not_date'] = $date_mysql;
		$db_data['not_blurb'] = $val;	
		db_query($db_data,"INSERT","note","not_id");
		}
	unset($dea_id,$db_data);
	
	}	



	
	
?>