<?php
require_once("inx/global.inc.php");

/*
keybook

list of keys, and associated deals.
assign new key will select next available key number


the key numbering system has to be standardised for all branches and departments, and be flexible enough to handle many more 
branches in the future...
 
it would make my life easier if we assign a number of key number range to each branch and department, e.g.
 
Camberwell Sales use keys 1 to 100
Sydenham Sales use 101 to 200
Camberwell Lettings use 201 to 300 and so on....
 
but this may confuse the users, and would mean we have to re-number all the key-boards, and it would be limiting each 
department to 100 keys (for example)
 
so instead, we could have some kind of convention. If each department have keys 1 to 100 (or more), each will need to be 
identified in the system as belinging to a particular branch and department, e.g.
 
CamSale9 (key 9 for camberwell sales dept)
SydLet9 (key 9 for sydenham lettings)
CamLet100 and so on...

or a more technical, but hidden approach:
1_s_9 (branch_dept_#)


*/

// register a key, associate it with a deal
if ($_GET["action"] == "register_key" && $_GET["dea_id"]) {
	
	// get required info from deal (branch and sale/let)
	$sql = "SELECT dea_branch, dea_type FROM deal WHERE dea_id = ".$_GET["dea_id"];
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	while ($row = $q->fetchRow()) {		
		$branch = $row["dea_branch"];
		$type = $row["dea_type"];
		}
	
	// scan all keys assigned to current branch, and get first available one (i.e. one that has nothing in the key_deal field)
	$sql = "SELECT key_id,key_code 
	FROM 
	keybook
	WHERE key_branch = $branch AND 
	key_deal = 0 OR key_deal IS NULL
	ORDER BY key_id DESC
	";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	
	if ($numRows) {
		while ($row = $q->fetchRow()) {		
			$key_code = $row["key_code"];		
			}	
		} else { 
		// no spare keys found, create a new one.
		
		// need to get highest used code to increment
		
		}
		
		echo "Automatic selection of available key code resulted in : $key_code";
	
		
	}
	
// view all keys in list
else {

	$sql = "SELECT key_id,key_code,
	CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_postcode) AS pro_addr ,
	bra_title
	FROM 
	keybook
	LEFT JOIN deal ON key_deal = deal.dea_id
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN branch ON key_branch = branch.bra_id
	";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	while ($row = $q->fetchRow()) {	
		
		$render .= '<tr>
		<td>'.$row["key_code"].'</td>
		<td>'.$row["pro_addr"].'</td>
		<td>'.$row["bra_title"].'</td>
		</tr>
		';
		
		}

	echo '
	<table>
	  <tr>
		<td>Key</td>
		<td>Property</td>
		<td>Branch</td>
	  </tr>
	  '.$render.'
	</table>';
	}
	


?>