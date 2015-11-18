<?php

require_once("inx/global.inc.php");


	
$sql = "SELECT COUNT(*) as hits, deal.dea_id, deal.dea_hits
FROM propertyviews 
LEFT JOIN deal ON propertyviews.dea_id = deal.dea_id

GROUP BY propertyviews.dea_id
ORDER BY hits DESC

";


$q = $db->query($sql);
echo "<h1>".$q->numRows()."</h1>";

while ($row = $q->fetchRow()) {	
	
	echo $row["dea_id"]." : ".$row["hits"]." / ".$row["dea_hits"]."<br>\n";
	
	$sql2 = "UPDATE deal SET dea_hits = '".($row["hits"]+$row["dea_hits"])."' WHERE dea_id = '".$row["dea_id"]."'";
	echo "$sql2<br>\n";
	$q2 = $db->query($sql2);
	
	}
	
?>