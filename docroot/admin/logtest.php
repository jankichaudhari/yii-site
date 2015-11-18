<?php
session_start();
include("global.php");

	$intClientID = $_POST["ClientID"];
	$Email = trim($_POST["Email"]);
	$Name = $_POST["Name"];
	$Client_ID = $_POST["Client_ID"];
	$Password = $_POST["Password"];
	$Email2 = $_POST["Email2"];
	$Salutation = $_POST["Salutation"];
	$Address1 = $_POST["Address1"];
	$Address2 = $_POST["Address2"];
	$Address3 = $_POST["Address3"];
	$City = $_POST["City"];
	$Country = $_POST["Country"];
	$Postcode = $_POST["Postcode"];
	$Tel = $_POST["Tel"];
	$Fax = $_POST["Fax"];
	$Mobile = $_POST["Mobile"];
	$PropertyType = $_POST["PropertyType"];
	$MinPrice = $_POST["MinPrice"];
	$MaxPrice = $_POST["MaxPrice"];
	$Receptions = $_POST["Receptions"];
	$Bedrooms = $_POST["Bedrooms"];
	$Bathrooms = $_POST["Bathrooms"];
	
	if ($_POST["Areas"]) {
		foreach ($_POST["Areas"] as $area) {
			$AreaSQL .= $area.", ";
			}
		}
	
	$Areas2 = $_POST["Areas2"];
	$Notes = $_POST["Notes"];
	$DG = $_POST["DG"];
	$GCH = $_POST["GCH"];
	$Modern = $_POST["Modern"];
	$Period = $_POST["Period"];
	$Tenure = $_POST["Tenure"];
	$Garden = $_POST["Garden"];
	$Parking = $_POST["Parking"];
	$BuyToLet = $_POST["BuyToLet"];
	$HeardBy = $_POST["HeardBy"];
	$Selling = $_POST["Selling"];
	$Valuation = $_POST["Valuation"];
	$DateModified = $_POST["DateModified"];
	$Status = $_POST["Status"];
	$Hits = $_POST["Hits"];
	$PropertyTypeLet = $_POST["PropertyTypeLet"];
	$BedroomsLet = $_POST["BedroomsLet"];
	$MinPriceLet = $_POST["MinPriceLet"];
	$MaxPriceLet = $_POST["MaxPriceLet"];
	$FurnishedLet = $_POST["FurnishedLet"];
	$TermLet = $_POST["TermLet"];
	$StatusLet = $_POST["StatusLet"];
	$Lettings = $_POST["Lettings"];
	$Sales  = $_POST["Sales"];
	
$sql = "Password = '$Password',
	Email = '$Email',
	Email2 = '$Email2',
	Name = '$Name',
	Salutation = '$Salutation',
	Address1 = '$Address1',
	Address2 = '$Address2',
	Address3 = '$Address3',
	City = '$City',
	Country = '$Country',
	Postcode = '$Postcode',
	Tel = '$Tel',
	Fax = '$Fax',
	Mobile = '$Mobile',
	PropertyType = '$PropertyType',
	MinPrice = '$MinPrice',
	MaxPrice = '$MaxPrice',
	Receptions = '$Receptions',
	Bedrooms = '$Bedrooms',
	Bathrooms = '$Bathrooms',
	Areas = '$AreaSQL',
	Areas2 = '$Areas2',
	Notes = '$Notes',
	DG = '$DG',
	GCH = '$GCH',
	Modern = '$Modern',
	Period = '$Period',
	Tenure = '$Tenure',
	Garden = '$Garden',
	Parking = '$Parking',
	BuyToLet = '$BuyToLet',
	HeardBy = '$HeardBy',
	Selling = '$Selling',
	Valuation = '$Valuation',
	DateModified = '$dateToday',
	Status = '$Status',
	PropertyTypeLet = '$PropertyTypeLet',
	BedroomsLet = '$BedroomsLet',
	MinPriceLet = '$MinPriceLet',
	MaxPriceLet = '$MaxPriceLet',
	FurnishedLet = '$FurnishedLet',
	TermLet = '$TermLet',
	StatusLet = '$StatusLet',
	Lettings = '$Lettings',
	Sales  = '$Sales'
	";
	
	

// record database changes
// works on update querys only, and requires the $sql body between the SET and the WHERE
function change_log($cha_user,$cha_table,$cha_field,$cha_row,$cha_sql,$cha_session)
	{
	// split $sql
	$sql_split = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/","",$cha_sql);
	$sql_split = explode(",",$sql_split);
	$sql_count = count($sql_split);
	for ($i = 0; $i < $sql_count; $i++) { 
		$split = explode("=",$sql_split[$i]);
		$fields .= trim($split[0]).",";
		$values .= trim($split[1]).",";
		}
	// comma seperate list of database field names
	$cha_columns = removeCharacter($fields,",");
	// comma seperated list of current values for above field names
	$cha_values = removeCharacter($values,",");
	
	// split both into arrays to loop through
	$cha_columns_array = explode(",",$cha_columns);
	$cha_values_array = explode(",",$cha_values);
	
	// compare number of fields to columns
	if (count($cha_columns_array) <> count($cha_values_array)) {
		echo "number of fields and values do not match";
		exit;
		} else {
		$cha_columns_count = count($cha_columns_array);	
		}
	
	// select current values
	$sql = "SELECT ".$cha_columns." FROM ".$cha_table." WHERE ".$cha_field." = ".$cha_row."";
	echo $sql;
	$result = mysql_query($sql);	
	if (!$result)
		die("MySQL Error:  ".mysql_error());
		
	while($row = mysql_fetch_array($result))
		{			
		for ($i=0; $i<$cha_columns_count; $i++) // loop through array of fields
			{			
			$array_field[] = mysql_field_name($result, $i);
			$array_current[] = $row[$i];
			$new_value = str_replace("'","",$cha_values_array[$i]);
			
			// compare old and new values, if different, insert into changelog
			if ($row[$i] <> $new_value) {
				$render .= "<p>".mysql_field_name($result, $i)." has changed from ".$row[$i]." to ".$cha_values_array[$i]."</p>";
				$sqlChangeLog = "INSERT INTO changelog 
				(cha_user,cha_session,cha_table,cha_field,cha_row,cha_old,cha_new)
				VALUES 
				('$cha_user','$cha_session','$cha_table','$array_field[$i]','$cha_row','$row[$i]','$new_value')
				";
				mysql_query($sqlChangeLog) or die ("Error in ChangeLog Query: ".mysql_error()."\n".$sqlChangeLog);
				}			
			}			
		}		
	//return $render;
	}


change_log("1","clients","Client_ID","3000",$sql,$PHPSESSID);

?>