<?php
session_start();
require("global.php"); 
require("secure.php"); 
$pageTitle = "Places";

if ($_GET["type"] == "Station") {
	$type_sql = "places.place_type = 1 AND ";
	}
elseif ($_GET["type"] == "Tube") {
	$type_sql = "places.place_type = 2 AND ";
	}

$sql = "SELECT * FROM places, pl_type WHERE ".$type_sql."places.place_type = pl_type.pl_type_id ORDER BY place_title";		
$q = $db->query($sql);
if (DB::isError($q)) {  die("insert error: ".$q->getMessage()); }
$render = '<table border="1" cellspacing="0" cellpadding="3">';
while ($row = $q->fetchRow()) {
	$render .= '<tr>
	<td>'.$row["place_title"].'</td>
	<td>'.$row["pl_type_title"].'</td>
	<td>'.str_replace("<br>",", ",$row["place_desc"]).'</td>
	</tr>
	';
	}
$render .= '</table>';

echo html_header($pageTitle);
?>
<h2><?php echo $pageTitle;?></h2>
<?php echo $render; ?>
</body>
</html>