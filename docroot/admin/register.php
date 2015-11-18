<?php
session_start();
$pageTitle = "Client";
require("global.php"); 
require("secure.php"); 

	if (!$Country) { $Country = 217; }
	$sqlCountry = "SELECT * FROM country ORDER BY Country_Title";
	$qCountry = $db->query($sqlCountry);
	if (DB::isError($qCountry)) {  die("insert error: ".$qCountry->getMessage()); }
	
	while ($rowCountry = $qCountry->fetchRow()) {		
		$RenderCountry .= '<option value="'.$rowCountry["Country_ID"].'"';
		if ($Country == $rowCountry["Country_ID"]) {
			$RenderCountry .= ' selected';
			}
		$RenderCountry .= '>'.$rowCountry["Country_Title"].'</option>';
		}
		
	$sqlFound = "SELECT * FROM foundby ORDER BY FoundBy_Title";
	$qFound = $db->query($sqlFound);
	if (DB::isError($qFound)) {  die("insert error: ".$qFound->getMessage()); }
	
	while ($rowFound = $qFound->fetchRow()) {		
		$RenderFound .= '<option value="'.$rowFound["FoundBy_ID"].'"';
		if ($HeardBy == $rowFound["FoundBy_ID"]) {
			$RenderFound .= ' selected';
			}
		$RenderFound .= '>'.$rowFound["FoundBy_Title"].'</option>';
		}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body, p, td {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}
th {
	font-weight: bold;
	color: #FFFFFF;
	background-color: #666666;
	text-align: left;	
	font-size: 14px;
}
.footnote {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style>

</head>

<body>
<h1>Register - Initial Verbal Contact</h1>

<p class="footnote">&nbsp;</p>
 
<table width="600" border="1" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <th>Enquiry source</th>
  </tr>
  <tr> 
    <td><input type="radio" name="radiobutton" value="radiobutton">
      <strong>Self-registration</strong> <span class="footnote">(the client has 
      registetred via our web site) </span></td>
  </tr>
  <tr> 
    <td><input type="radio" name="radiobutton" value="radiobutton"> <strong>Phone</strong> 
      <span class="footnote">(direct telephone registration) </span></td>
  </tr>
  <tr> 
    <td><input type="radio" name="radiobutton" value="radiobutton"> <strong>Portal</strong> 
      <span class="footnote">(you are registering a client following a lead from 
      a portal web site) </span></td>
  </tr>
</table>
<br>
<br>
<table width="600" border="1" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <td>Branch</td>
    <td colspan="3"><input type="checkbox" name="checkbox" value="checkbox">
      East Dulwich<br> <input type="checkbox" name="checkbox" value="checkbox">
      Shad Thames<br> <input type="checkbox" name="checkbox" value="checkbox">
      Sydenham</td>
  </tr>
  <tr> 
    <td>Sales and/or Lettings</td>
    <td colspan="3"><input type="checkbox" name="checkbox" value="checkbox">
      Sales<br> <input type="checkbox" name="checkbox" value="checkbox">
      Lettings</td>
  </tr>
</table>

<br>
<table width="600" border="1" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <th colspan="2">Personal Details</th>
  </tr>
  <tr> 
    <td>Saluation</td>
    <td><input type="radio" name="radiobutton" value="radiobutton">
      Mr 
      <input type="radio" name="radiobutton" value="radiobutton">
      Ms 
      <input type="radio" name="radiobutton" value="radiobutton">
      Mrs 
      <input type="radio" name="radiobutton" value="radiobutton">
      Miss 
      <input type="radio" name="radiobutton" value="radiobutton">
      Dr 
      <input type="radio" name="radiobutton" value="radiobutton">
      Rev</td>
  </tr>
  <tr> 
    <td>Forename</td>
    <td><input type="text" name="textfield" style="width: 220px"> </td>
  </tr>
  <tr> 
    <td>House Number and Street Name</td>
    <td><input type="text" name="textfield" style="width: 220px"> </td>
  </tr>
  <tr> 
    <td>Town or Area</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>County or City</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Postcode</td>
    <td><input type="text" name="textfield" style="width: 220px"> </td>
  </tr>
  <tr> 
    <td>Country</td>
    <td><select name="Country" style="width: 220px">
        <?php echo $RenderCountry; ?> </select></td>
  </tr>
  <tr> 
    <td> Telephone</td>
    <td><input type="text" name="textfield" style="width: 220px"> </td>
  </tr>
  <tr> 
    <td>Mobile</td>
    <td><input type="text" name="textfield" style="width: 220px"> </td>
  </tr>
  <tr> 
    <td>Email</td>
    <td><input type="text" name="textfield" style="width: 220px"> </td>
  </tr>
  <tr align="center"> 
    <td colspan="2"><input type="submit" name="Submit" value="   Next   "> </td>
  </tr>
</table>
<p>&nbsp;</p>
<p align="center"><span class="footnote">Having clicked Next, the following page 
  will show either the sales form or<br>
  the lettings form depending on what the client has selected...</span><br>
  <br>
</p>
<table width="600" border="1" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <th colspan="2"><strong>Sales Property Requirements</strong></th>
  </tr>
  <tr> 
    <td>Property Type</td>
    <td><select name="PropertyType" style="width:220px">
        <?php echo db_enum("clients","PropertyType",$PropertyType); ?> </select> 
    </td>
  </tr>
  <tr> 
    <td>Price range</td>
    <td><select name="MinPrice" size="1" style="width:100px">
        <option value="0">Minimum</option>
        <?php 			
			for ($i = 80000; $i <= 500000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MinPrice) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+5000;
				}
			for ($i = 600000; $i <= 990000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MinPrice) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+10000;
				}
			for ($i = 1000000; $i <= 3000000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MinPrice) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+1000000;
				}
				?>
      </select>
      to 
      <select name="MaxPrice" size="1" style="width:100px">
        <option value="999999999">Maximum</option>
        <?php 			
			for ($i = 80000; $i <= 500000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MaxPrice) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+5000;
				}
			for ($i = 600000; $i <= 990000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MaxPrice) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+10000;
				}
			for ($i = 1000000; $i <= 3000000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MaxPrice) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+1000000;
				}
				?>
      </select> </td>
  </tr>
  <tr> 
    <td>Minimum bedrooms</td>
    <td><select name="Bedrooms" style="width:60px">
        <?php 
			for ($i = 0; $i <= 9; $i++) { 
			echo '<option value="'.$i.'"';
			if ($i == $Bedrooms) {
				echo ' selected';
				}
			echo '>'.$i.'</option>'; 
			}
			?>
      </select> </td>
  </tr>
  <tr> 
    <td>Location</td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Riverside&nbsp; 
      <input type="checkbox" name="checkbox" value="checkbox">
      Estates&nbsp; 
      <input type="checkbox" name="checkbox" value="checkbox">
      Main road&nbsp; </td>
  </tr>
  <tr> 
    <td>Garden </td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Shared&nbsp; 
      <input type="checkbox" name="checkbox" value="checkbox">
      Private&nbsp;</td>
  </tr>
  <tr> 
    <td>Parking</td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Off-Street&nbsp; 
      <input type="checkbox" name="checkbox" value="checkbox">
      Secure&nbsp; 
      <input type="checkbox" name="checkbox" value="checkbox">
      Garage&nbsp; </td>
  </tr>
  <tr> 
    <td>Property Age</td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Period&nbsp; 
      <input type="checkbox" name="checkbox" value="checkbox">
      Modern&nbsp; 
      <input type="checkbox" name="checkbox" value="checkbox">
      New&nbsp; </td>
  </tr>
  <tr> 
    <td>Any other requirements</td>
    <td><textarea name="textarea" rows="3" style="width: 220px"></textarea></td>
  </tr>
</table>

<br>
<table width="600" border="1" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <th colspan="2">Lettings Property Requirements</th>
  </tr>
  <tr> 
    <td>Property Type</td>
    <td><select name="PropertyTypeLet" style="width:220px">
        <?php echo db_enum("clients","PropertyType",$PropertyTypeLet); ?> </select> 
    </td>
  </tr>
  <tr> 
    <td>Price range</td>
    <td><select name="MinPriceLet" style="width: 100px">
        <option value="0">Minimum</option>
        <?php 			
			for ($i = 50; $i <= 1000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MinPriceLet) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+50;
				}
			for ($i = 1000; $i <= 5000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MinPriceLet) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+250;
				}
				?>
      </select>
      to 
      <select name="MaxPriceLet" style="width: 100px">
        <option value="999999999">Maximum</option>
        <?php 			
			for ($i = 50; $i <= 1000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MaxPriceLet) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+50;
				}
			for ($i = 1000; $i <= 5000;) { 
				echo '<option value="'.$i.'"';
				if ($i == $MaxPriceLet) {
					echo ' selected';
					}
				echo '>'.price_format($i).'</option>'; 
				$i = $i+250;
				}
				?>
      </select> </td>
  </tr>
  <tr> 
    <td>Minimum bedrooms</td>
    <td><select name="BedroomsLet" style="width:60px">
        <?php 
			for ($i = 0; $i <= 9; $i++) { 
			echo '<option value="'.$i.'"';
			if ($i == $BedroomsLet) {
				echo ' selected';
				}
			echo '>'.$i.'</option>'; 
			}
			?>
      </select> </td>
  </tr>
  <tr> 
    <td>Furnished</td>
    <td><select name="FurnishedLet" style="width:220px">
        <option value="Any">Any</option>
        <?php
				$sqlFurn = "SELECT * FROM furnished";		
				$qFurn = $db->query($sqlFurn);
				if (DB::isError($qFurn)) {  die("error: ".$qFurn->getMessage()); }
				
				while ($rowFurn = $qFurn->fetchRow()) {				
         			echo '<option value="'.$rowFurn["Furnished_ID"].'"';
				   	if ($Furnished == $rowFurn["Furnished_ID"]) {
				   		echo ' selected';
						}
				   	echo '>'.$rowFurn["Furnished_Title"].'</option>
				   	';
				   	}
				   	?>
      </select> </td>
  </tr>
  <tr> 
    <td>Number of tenants</td>
    <td><select name="BedroomsLet" style="width:60px">
        <?php 
			for ($i = 0; $i <= 9; $i++) { 
			echo '<option value="'.$i.'"';
			if ($i == $BedroomsLet) {
				echo ' selected';
				}
			echo '>'.$i.'</option>'; 
			}
			?>
      </select></td>
  </tr>
  <tr> 
    <td>Move in by</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Length of tenancy</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Location</td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Riverside&nbsp; <input type="checkbox" name="checkbox" value="checkbox">
      Estates&nbsp; <input type="checkbox" name="checkbox" value="checkbox">
      Main road&nbsp; </td>
  </tr>
  <tr> 
    <td>Garden </td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Shared&nbsp; <input type="checkbox" name="checkbox" value="checkbox">
      Private&nbsp;</td>
  </tr>
  <tr> 
    <td>Parking</td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Off-Street&nbsp; <input type="checkbox" name="checkbox" value="checkbox">
      Secure&nbsp; <input type="checkbox" name="checkbox" value="checkbox">
      Garage&nbsp; </td>
  </tr>
  <tr> 
    <td>Property Age</td>
    <td><input type="checkbox" name="checkbox" value="checkbox">
      Period&nbsp; <input type="checkbox" name="checkbox" value="checkbox">
      Modern&nbsp; <input type="checkbox" name="checkbox" value="checkbox">
      New&nbsp; </td>
  </tr>
  <tr> 
    <td>Any other requirements</td>
    <td><textarea name="textarea" rows="3" style="width: 220px"></textarea></td>
  </tr>
</table>
<br>
<table width="600" border="1" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <th colspan="2">Personal and Financial Information</th>
  </tr>
  <tr> 
    <td>Current status (sales)</td>
    <td><select name="Selling" style="width: 220px">
        <option value="">First Time Buyer</option>
        <option value="">Chain Free Buyer (cash)</option>
        <option value="">Buying to Let</option>
        <option value="">Not on Market</option>
        <option value="">On Market with W&amp;S</option>
        <option value="">On Market with Other agent</option>
        <option value="">Under Offer with W&amp;S</option>
        <option value="">Under Offer with Other agent</option>
        <option value="">Lost Sale with W&amp;S</option>
        <option value="">Lost Sale with Other agent</option>
      </select></td>
  </tr>
  <tr> 
    <td> Please include details of other <br>
      agents if applicable</td>
    <td> <textarea name="textarea" rows="2" wrap="VIRTUAL" style="width: 220px"></textarea> 
    </td>
  </tr>
  <tr> 
    <td>Current status (lettings)</td>
    <td><select name="Selling" style="width: 220px">
        <option value="">Currently renting</option>
        <option value="">Not currently renting</option>
      </select></td>
  </tr>
  <tr> 
    <td> Please include details of current<br>
      letting arrangments if applicable</td>
    <td> <textarea name="textarea" rows="2" wrap="VIRTUAL" style="width: 220px"></textarea> 
    </td>
  </tr>
  <tr> 
    <td>How are you raising finance (sales)</td>
    <td><select name="" style="width: 220px">
        <option value="">Selling home</option>
        <option value="">Selling second property</option>
        <option value="">Mortgage</option>
        <option value="">Re-mortgage</option>
        <option value="">Cash</option>
      </select></td>
  </tr>
  <tr> 
    <td>How much deposit (sales)</td>
    <td> <input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Mortgage arranged with (sales)</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Require valuation (sales)</td>
    <td><input type="radio" name="radiobutton" value="radiobutton">
      Yes 
      <input type="radio" name="radiobutton" value="radiobutton">
      No</td>
  </tr>
  <tr> 
    <td>Occupation</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Salary</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Children</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
  <tr> 
    <td>Pets</td>
    <td><input type="text" name="textfield" style="width: 220px"></td>
  </tr>
</table>
<br>
<br>
<table width="600" border="1" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <td>How did you hear about us?</td>
    <td><select name="HeardBy" style="width: 220px">
	<option value="">Select</option>
        <?php echo $RenderFound;  ?> </select> </td>
  </tr>
  <tr> 
    <td>Other notes, comments or requirements</td>
    <td><textarea name="textarea" rows="6" style="width: 220px"></textarea></td>
  </tr>
  <tr align="center"> 
    <td colspan="2"><input type="submit" name="Submit" value="Next"></td>
  </tr>
</table>
</body>
</html>
