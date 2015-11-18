<?php
session_start();
$pageTitle = "Map";
require("global.php");
require("secure.php");

$locationimage = "/images/mapping/crosshair.gif";
$pixelwidth = 44;
$pixelheight = 44;

if (!$_GET["x"]) { $x = $default_location_x; } else { $x = $_GET["x"]; }

if (strlen($_GET["y"]) > 6) {
	preg_match('/^(\d*?)\?(\d*?),(\d*?)$/', $_GET["y"], $matches);
	$y=$matches[1];
	$locx=$matches[2];
	$locy=$matches[3];
} else {
	if (!$_GET["y"]) { $y = $default_location_y; } else { $y = $_GET["y"]; }
}

$basex = format_tile($x);
$basey = format_tile($y);

$osx = round(($locx*2.67) + ($basex-500));
$osy = round(($basey+1000) - ($locy*2.67));


// get other properties

//$sql = "SELECT p1.prop_ID, p1.price, p1.state_of_trade_id, p1.Address1, p1.house_number, p1.Postcode, p1.type_id, p1.Bedrooms, p1.osx, p1.osy, p1.SaleLet, proptype.type_ID, proptype.type_Title, count(*) AS mycount FROM property p1, property p2, proptype WHERE (p1.state_of_trade_id = 1 OR p1.state_of_trade_id = 2 OR p1.state_of_trade_id = 4) AND (p2.state_of_trade_id = 1 OR p2.state_of_trade_id = 2 OR p1.state_of_trade_id = 4) AND p1.osx = p2.osx AND p1.osy=p2.osy AND (p1.osx > ".($basex - 500)." AND p1.osx < ".($basex + 1000)." AND p1.osy > ".($basey - 500)." AND p1.osy < ".($basey + 1000).") AND p1.type_id = proptype.type_ID GROUP BY p1.prop_ID, p1.price, p1.state_of_trade_id, p1.Address1, p1.Postcode, p1.type_id, p1.Bedrooms, p1.osx, p1.osy, p1.SaleLet,  proptype.type_ID, proptype.type_Title ORDER BY p1.Address1";
$sql = "SELECT p1.prop_ID, p1.price, p1.state_of_trade_id, p1.Address1, p1.house_number, p1.Postcode, p1.type_id, p1.Bedrooms, p1.osx, p1.osy, p1.SaleLet, proptype.type_ID, proptype.type_Title, count(*) AS mycount FROM property p1, property p2, proptype WHERE p1.osx = p2.osx AND p1.osy=p2.osy AND (p1.osx > ".($basex - 500)." AND p1.osx < ".($basex + 1000)." AND p1.osy > ".($basey - 500)." AND p1.osy < ".($basey + 1000).") AND p1.type_id = proptype.type_ID GROUP BY p1.prop_ID, p1.price, p1.state_of_trade_id, p1.Address1, p1.Postcode, p1.type_id, p1.Bedrooms, p1.osx, p1.osy, p1.SaleLet,  proptype.type_ID, proptype.type_Title ORDER BY p1.Address1 LIMIT 100";
$q = $db->query($sql);

if (DB::isError($q)) {  die("property select error: ".$q->getMessage()); }

$layerOther = '<div id="propforsale" style="position:relative; z-index:100;">';

while ($row = $q->fetchRow()) {

	$pid = $row['prop_ID'];
	$price = $row['price'];
	$house_number = $row['house_number'];
	$street = $row['Address1'];
	$pc = explode(" ",$row['Postcode']);
	$postcode = $pc[0];
	$osxdb = $row['osx'];
	$osydb = $row['osy'];

	if ($osxdb == $_GET["x"] && $osydb == $_GET["y"]) {
		$dot = "dot3.gif";
		}
	else {
		$dot = "ring4.gif";
		}

	$osxdb = (($osxdb - $basex) + 500);
	$osxdb = round($osxdb / 2.675);
	$osxdb = ($osxdb - 6); // minus n to compensate dot size, this is 50% dimensions of dot

	$osydb = ($osydb - $basey);
	$osydb = (1000 - $osydb);
	$osydb = round($osydb / 2.65);
	$osydb = ($osydb - 6);

	$layerindex = "100";
	//$overinfo = "<b>".$house_number."<br>".$street."</b><br>";
	$overinfo = $house_number." ".$street;
	//$layerOther .= '<div id="layer'.$pid.'" style="position:absolute; left:'.$osxdb.'; top:'.$osydb.'; width:12px; height:12px; z-index:'.$layerindex.'"><a href="javascript:save_coords(\''.$row["osx"].'\',\''.$row["osy"].'\')" onmouseover="return overlib(\''.$overinfo.'\', REF,\'img'.$pid.'\', REFP,\'UR\', WRAP);" onmouseout="return nd();"><img src="/images/mapping/'.$dot.'" width="12" height="12" border="0" name="img'.$pid.'" id="img'.$pid.'"></a></div>';
	$layerOther .= '<div id="layer'.$pid.'" style="position:absolute; left:'.$osxdb.'; top:'.$osydb.'; width:12px; height:12px; z-index:'.$layerindex.'"><a href="javascript:save_coords(\''.$row["osx"].'\',\''.$row["osy"].'\')"><img src="/images/mapping/'.$dot.'" alt="'.$overinfo.'" width="12" height="12" border="0" name="img'.$pid.'" id="img'.$pid.'"></a></div>';
	}

	$LayerForSale .= '</div>';
	$LayerUnderOffer .= '</div>';



if ($locx <> 0 || $locy <> 0) {
$layer = "<div id=\"layer\" style=\"position:absolute; left:".($locx-round($pixelwidth/2))."; top:".($locy-round($pixelheight/2))."; width:".$pixelwidth."px; height:".$pixelheight."px; z-index:2\"><img src=\"".$locationimage."\" width=\"".$pixelwidth."\" height=\"".$pixelheight."\"></div>";
}
/*
else {
$osx = $_GET["x"];
$osy = $_GET["y"];
$osx = (($osx - $basex) + 500);
$osx = round($osx / 2.675);
$osx = ($osx - ($pixelwidth/2)); // minus n to compensate dot size, this is 50% dimensions of dot

$osy = ($osy - $basey);
$osy = (1000 - $osy);
$osy = round($osy / 2.65);
$osy = ($osy - ($pixelheight/2));

$layer = '<div id="layer" style="position:absolute; left:'.$osx.'; top:'.$osy.'; width:'.$pixelwidth.'px; height:'.$pixelheight.'px; z-index:2"><img src="'.$locationimage.'" width="'.$pixelwidth.'" height="'.$pixelheight.'"></div>';

}
*/
echo html_header($pageTitle);
?>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<script type="text/javascript">
self.focus();
</script>
  <table border="0" cellpadding="5" cellspacing="0">
    <tr>
      <td align="center"><table border="0" cellpadding="0" cellspacing="0" width="120">
        <tr>
          <td><a href="?<?php echo "x=".($basex-500)."&y=".($basey+500).""; //.($locx+187).",".($locy+187); ?>"><img src="images/comp_nw.gif" alt="North West" width="40" height="40" border="0"></a></td>
          <td><a href="?<?php echo "x=".($basex)."&y=".($basey+500).""; //".($locx).",".($locy+187); ?>"><img src="images/comp_n.gif" alt="North" width="40" height="40" border="0"></a></td>
          <td><a href="?<?php echo "x=".($basex+500)."&y=".($basey+500).""; //".($locx-187).",".($locy+187); ?>"><img src="images/comp_ne.gif" alt="North East" width="40" height="40" border="0"></a></td>
        </tr>
        <tr>
          <td><a href="?<?php echo "x=".($basex-500)."&y=".($basey).""; //".($locx+187).",".($locy); ?>"><img src="images/comp_w.gif" alt="West" width="40" height="40" border="0"></a></td>
          <td><img src="images/spacer.gif" name="comp" width="40" height="40" id="comp"></td>
          <td><a href="?<?php echo "x=".($basex+500)."&y=".($basey).""; //".($locx-187).",".($locy); ?>"><img src="images/comp_e.gif" alt="East" width="40" height="40" border="0"></a></td>
        </tr>
        <tr>
          <td><a href="?<?php echo "x=".($basex-500)."&y=".($basey-500).""; //".($locx+187).",".($locy-187); ?>"><img src="images/comp_sw.gif" alt="South West" width="40" height="40" border="0"></a></td>
          <td><a href="?<?php echo "x=".($basex)."&y=".($basey-500).""; //".($locx).",".($locy-187); ?>"><img src="images/comp_s.gif" alt="South" width="40" height="40" border="0"></a></td>
          <td><a href="?<?php echo "x=".($basex+500)."&y=".($basey-500).""; //".($locx-187).",".($locy-187); ?>"><img src="images/comp_se.gif" alt="South East" width="40" height="40" border="0"></a></td>
        </tr>
        <tr align="center">
          <td height="50" colspan="3">&nbsp;</td>
        </tr>
        <tr align="center" valign="top">
          <td height="100" colspan="3">
		    <?php if (strlen($_GET["y"]) > 6) {
			  echo '<p>Pointer Location<br>'.$osx.' x '.$osy;
			  echo "<p><a href=\"javascript:save_coords('".$osx."','".$osy."')\">Use Current Location</a></p>\n";
			  } else {
			  echo 'Use the compass above to navigate the map. Click the map to choose the location, or use an existing location by clicking one of the circles.'; }
			  ?></td>
          </tr>
      </table></td>
      <td width="561" height="561" valign="top">
	  		<div id="masterlayer" style="position:relative; width: 561px; height: 563px;">
          	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
        	<script language="JavaScript" src="/overlibmws.js"></script>
			<div id="maplayer" style="position:absolute; width: 561px; height: 562px; z-index: 1; left: 0; top: 0;">
            <table border="0" cellpadding="0" cellspacing="0" align="left">
              <tr>
                <td><img src="/images/mapping/<?php echo ($basex-500)."x".($basey+500).".gif"; ?>" width="187" height="187" border="0"></td>
                <td><img src="/images/mapping/<?php echo $basex."x".($basey+500).".gif"; ?>" width="187" height="187" border="0"></td>
                <td><img src="/images/mapping/<?php echo ($basex+500)."x".($basey+500).".gif"; ?>" width="187" height="187" border="0"></td>
              </tr>
              <tr>
                <td><img src="/images/mapping/<?php echo ($basex-500)."x".$basey.".gif"; ?>" width="187" height="187" border="0"></td>
                <td><img src="/images/mapping/<?php echo $basex."x".$basey.".gif"; ?>" width="187" height="187" border="0"></td>
                <td><img src="/images/mapping/<?php echo ($basex+500)."x".$basey.".gif"; ?>" width="187" height="187" border="0"></td>
              </tr>
              <tr>
                <td><img src="/images/mapping/<?php echo ($basex-500)."x".($basey-500).".gif"; ?>" width="187" height="187" border="0"></td>
                <td><img src="/images/mapping/<?php echo $basex."x".($basey-500).".gif"; ?>" width="187" height="187" border="0"></td>
                <td><img src="/images/mapping/<?php echo ($basex+500)."x".($basey-500).".gif"; ?>" width="187" height="187" border="0"></td>
              </tr>
            </table>
          </div>
          <?php echo $layer; ?>
		  <?php echo $layerOther; ?>
          <div id="linklayer" style="position:absolute; width: 561px; height: 562px; z-index: 3; left: 0; top: 0;">
            <table border="0" cellpadding="0" cellspacing="0" align="left">
              <tr>
                <td><a href="?x=<?php echo $basex; ?>&y=<?php echo $basey; ?>" style="cursor:crosshair;"><img src="/images/spacer.gif" width="561" height="561" border="0" ismap></a></td>
              </tr>
            </table>
          </div></div></td>
    </tr>
  </table>
</body>
</html>
