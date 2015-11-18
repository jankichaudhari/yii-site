<?php
require_once("inx/db.inc.php");


$sql = "SELECT 
con_id,CONCAT(con_fname,' ',con_sname) AS con_name,
com_title,com_id,
CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',LEFT(pro_postcode,4)) AS pro_addr 
FROM contact
LEFT JOIN company ON con_company = com_id 
LEFT JOIN pro2com ON company.com_id = pro2com.p2c_com
LEFT JOIN property ON pro2com.p2c_pro = property.pro_id
WHERE 
(con_fname LIKE '%".$_POST['cli_solicitor']."%' OR con_sname LIKE '%".$_POST['cli_solicitor']."%' OR com_title LIKE '%".$_POST['cli_solicitor']."%')
AND con_type = 2
LIMIT 10";
$rs = mysql_query($sql);

echo "<ul>\n";
while($data = mysql_fetch_assoc($rs)) {
	echo '<li id="'.$data['con_id'].'">'.stripslashes($data['con_name']);
	if ($data["com_title"]) {
		echo '<span class="informal"> ('.$data["com_title"].')</span>';
		}
	echo '</li>'."\n";
	}
echo "</ul>";
?>