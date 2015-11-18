<?php
require_once("inx/db.inc.php");


$sql = "SELECT
com_title,com_id,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',LEFT(pro_postcode,4)) AS pro_addr
FROM company
LEFT JOIN pro2com ON company.com_id = pro2com.p2c_com
LEFT JOIN property ON pro2com.p2c_pro = property.pro_id
WHERE
com_title LIKE '%" . $_POST['com_title'] . "%'
LIMIT 10";
$rs = mysql_query($sql);

?>

<ul>

<?php while($data = mysql_fetch_assoc($rs)) { ?>
  <li id="<?php echo $data['com_id']; ?>"><?php echo stripslashes($data['com_title']);?><span class="informal"> <?php echo $data["pro_addr"];?></span></li>
<?php } ?>

</ul>
