<?php
// transfer ws_db.clients => wsv3.client



/* 

this page will import all clients from admin database, with address checks 


must merge fig data too. those without web_id have been manually added to fig, those with have come from 
download therfore must be in admin already so can be ignored.
those that are being imported should be checked against existing clients (those that have been imported from
admin) - if the following criteria are matched, skip....
email exists.
name AND telephone number (strip space, strtolower on all for comparison)

fig contains less than 200 lettings clients, so i am ignoring all lettings related info. 
all the clients from fig will be imported with email updates set to no
clients set to archived will be ignored to save credits. no point in importing them as most info will be out of date.

i have 4750 clients from fig that dont have web id and are not archived.


common CRAP in name fields...
"keep", "Keep", "Keep this record"
"(developer)" - anything in parenthesis
"(ex member of staff)"
"(Record 1 & 2)" - checking for duplicates anyway so this should be a problem, just remove

filtering:
if name is less than 5 characters in total, scrap
if there is no phone numbers, address OR email, scrap
prevent duplicates by adding to an array of accepted clients on each loop. in this array, store name, phone 
and email - exact matches are scrapped - not failsafe but better than nothing.

once done, check database for duplicate names and display.


										


*/


require_once("../inx/global.inc.php");


if ($_GET["button"]) {



$sql = "SELECT sou_id,sou_title FROM source";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$source[$row["sou_id"]] = strtolower(str_replace(" ","",$row["sou_title"]));
	}

$sql = "SELECT are_id,are_title FROM area";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$areas[$row["are_id"]] = $row["are_title"];
	}










$counter = 1;
$salutations = array(
	' mr. ',
	' mr ',
	' mrs. ',
	' mrs ',
	' ms. ',
	' ms ',
	' miss. ',
	' miss ',
	' dr. ',
	' dr ' 
	);
	
if (!$_GET["start"]) {
	$start = 0;
	} else {
	$start = $_GET["start"];
	}
if (!$_GET["limit"]) {
	$limit = 10;
	} else {
	$limit = $_GET["limit"];
	}
	
// filter clients to transfer, maybe disgregard those older than 3 years?
$sql = "SELECT 

* 
FROM client_import

LIMIT $start,$limit

";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error at the top: ".$q->getMessage()."\n".$sql); }
while ($row = $q->fetchRow()) {
	
	
	
	
	
	
	$original_name = ' '.strtolower($row["FullName"]).' ';
	// remove anything within parenthesis
	$original_name = preg_replace("/\([a-z0-9\ ]+\)/", "", $original_name);
	$original_name = str_replace(
		array('(',')','.',' keep ',' developer '),
		' ',
		$original_name
		);	
	// if "and" is present, skip as this is probably 2 clients, add their id to a separate array
	if (
		stripos($original_name, ' and ') || 
		stripos($original_name, ' & ') || 
		stripos($original_name, ' &amp; ') || 
		stripos($original_name, ' ltd ') || 
		stripos($original_name, ' company ')
		) {
		$skipped[$cli_id] = $original_name;
		}
	else {		
		// loop through to search for salutation
		foreach ($salutations AS $salutation) {
			
			$pos = stripos($original_name, $salutation);
			if ($pos === false) {
				$cli_salutation = '';
				
			} else {
				
				$length = strlen($salutation);
				$cli_salutation = substr($original_name, $pos, $length);  
				// get rid of any full stops
				$cli_salutation = str_replace(".","",$cli_salutation);
				break;
				
				
				}
			}		
			
		
		if (!$cli_salutation) {
			$new_name = trim($original_name);
			} else {
			$new_name = trim(str_replace(strtolower($cli_salutation),"",strtolower($original_name)));
			$cli_salutation = trim(ucwords($cli_salutation)).".";				
			}

		$name_parts = explode(" ",trim($new_name));
		$name_count = count($name_parts);
		
		
		// only one name, assume it is forename
		if ($name_count == 1) {
			$forenames = ucwords($name_parts[($name_count-1)]);	
			$surname = '?';
			} else {		
			
			
			// attepmt fix of "O'reilly" and "De-souza"
			$singlequote = strpos($name_parts[($name_count-1)], "'");
			$hyphen = strpos($name_parts[($name_count-1)], "-");
			if ($singlequote) {
				$surname_parts = explode("'",$name_parts[($name_count-1)]);
				foreach($surname_parts as $surname_part) {
					$surname_formatted[] = ucwords($surname_part);
					}
				$surname = implode("'",$surname_formatted);
				unset($surname_formatted);
				} 
			elseif ($hyphen) {
				$surname_parts = explode("-",$name_parts[($name_count-1)]);
				foreach($surname_parts as $surname_part) {
					$surname_formatted[] = ucwords($surname_part);
					}
				$surname = implode("-",$surname_formatted);
				unset($surname_formatted);
				} 
			else {
				$surname = ucwords($name_parts[($name_count-1)]);		
				}			
			
			// remove the surname
			unset($name_parts[($name_count-1)]);			
			
			
			$forenames = ucwords(implode(" ",$name_parts));
			
			// hyphenated forename
			$forename_hyphen = strpos($forenames, "-");
			if ($forename_hyphen) {
				$forename_parts = explode("-",$forenames);
				foreach($forename_parts as $forename_part) {
					$forename_formatted[] = ucwords($forename_part);
					}
				$forenames = implode("-",$forename_formatted);
				unset($forename_formatted);
				} 
			}
			
		if (strlen($row["Title"] > 1)) {
			$db_data["cli_salutation"] = $row["Title"].".";
			} else {
			$db_data["cli_salutation"] = $cli_salutation;
			}
		$db_data["cli_fname"] = $forenames;
		$db_data["cli_sname"] = $surname;
		
		//$db_data["cli_id"] = $row["Client_ID"];
		if (check_email(trim(strtolower($row["Email"])))) {
			$db_data["cli_email"] = trim(strtolower($row["Email"]));
			}
	
		
		// current address, attempt some reformatting and add to notes field
		if (trim($row["HouseNo"])) {
			$old_address = trim($row["HouseNo"]).", ";
			}
		if (trim($row["Addr1"])) {
			$old_address .= trim($row["Addr1"]).", ";
			}
		if (trim($row["Addr2"])) {
			$old_address .= trim($row["Addr2"]).", ";
			}
		if (trim($row["Addr3"])) {
			$old_address .= trim($row["Addr3"]).", ";
			}
		if (trim($row["Town"])) {
			$old_address .= trim($row["Town"]).", ";
			}
		if (trim($row["PostCode"])) {
			$old_address .= format_postcode($row["PostCode"]);
			}
		$old_address = str_replace('marion puttings on web','',$old_address);
		if (strlen($old_address) > 3) {	
			$db_data["cli_oldaddr"] = $old_address;
			$addresses[$row["Client_ID"]] = $old_address;	
			}	
		/*
		// disregard addresses containing : not given, none and ng
		if (			
			strstr(strtolower($db_data["cli_oldaddr"]), 'not given') || 
			strstr(strtolower($db_data["cli_oldaddr"]), 'none') || 
			strstr(strtolower(' '.$db_data["cli_oldaddr"].' '), ' ng ') || 
			strstr(strtolower(' '.$db_data["cli_oldaddr"].' '), ' nonegiven ') || 
			strstr(strtolower(' '.$db_data["cli_oldaddr"].' '), ' none given ')
			) {
			unset($db_data["cli_oldaddr"]);
			} else {
			// add to batch clense array, with client id as identifier
			$addresses[$row["Client_ID"]] = $old_address;			
			}
		*/
		unset($old_address);
		
		
		// property type sales
		$PropertyType = array(
			'Any'=>'4|5|6|7|8|9|10|22|25|11|12|13|19|20|21|23|14|15|16|17', // all
			'House'=>'4|5|6|7|8|9|10|22|25', // all houses 
			'Apartment'=>'11|12|13|19|20|21|23', // all apartments
			'Commercial'=>'14', // all commercial
			'Live/Work'=>'10|21' // all live/work, houses and flats
			);		
		
		if ($row["PropertyType"] == 'Any') {
			$db_data["cli_saleptype"] = $PropertyType['Any'];
			} elseif ($row["PropertyType"] == 'commercial') {
			$db_data["cli_saleptype"] = $PropertyType['Commercial'];
			} elseif ($row["PropertyType"] == 'Flat') {
			$db_data["cli_saleptype"] = $PropertyType['Apartment'];
			} elseif ($row["PropertyType"] == 'Flat-Penthouse') {
			$db_data["cli_saleptype"] = "11";
			} elseif ($row["PropertyType"] == 'Flat/House' || $row["PropertyType"] == 'House/Flat') {
			$db_data["cli_saleptype"] = $PropertyType['House'].'|'.$PropertyType['Apartment'];
			} elseif ($row["PropertyType"] == 'House') {
			$db_data["cli_saleptype"] = $PropertyType['House'];
			} elseif ($row["PropertyType"] == 'shop' || $row["PropertyType"] == 'shop commercial ?') {
			$db_data["cli_saleptype"] = "29";
			} elseif ($row["PropertyType"] == 'House Semi-detached') {
			$db_data["cli_saleptype"] = "5";
			} elseif ($row["PropertyType"] == 'House Terraced') {
			$db_data["cli_saleptype"] = "6";
			} else {
			$db_data["cli_saleptype"] = '';
			}
		
		// round prices to nearest 10000 to fit drop-downs
		$db_data["cli_salemin"] = round_to_nearest(numbers_only($row["StartRange"]),10000);
		$db_data["cli_salemax"] = round_to_nearest(numbers_only($row["EndRange"]),10000);
		
		
		$db_data["cli_salebed"] = $row["Beds"];
		
				
		
		// dates are not already be in the correct format
		$date = explode(" ",$row["DateReceived"]);
		$date_parts = explode("/",$date[0]);
		$DateReceived = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0].' '.$date[1];		
		$db_data["cli_created"] = $DateReceived; 
		
		// all these people are having email updates set to no
		// if they regsitered before 2006, they are set to inactive
		if ($date_parts[2] > 2005) {
			if ($row["Buying"] == 1) {			
				$db_data["cli_sales"] = 'Yes';
				if ($db_data["cli_email"]) {
					$db_data["cli_saleemail"] = 'Yes';
					} else {					
					$db_data["cli_saleemail"] = 'No';
					}
				} else {						
				$db_data["cli_sales"] = 'No';
				}
			}
		
		$db_data["cli_method"]  = 'Import';
		
		/*
		echo $db_data["cli_fname"].' '.$db_data["cli_sname"];
		$pos1 = strstr($db_data["cli_fname"],"****");
		$pos2 = strstr($db_data["cli_sname"],"****");
		if ($pos1) {
			echo " found 1<p>";
			} else {
			echo " not found 1<p>";
			}
		if ($pos2) {
			echo " found 2<p>";
			} else {
			echo " not found 2<p>";
			}
		*/
		// decided to skip part-names, so anything with a ? is now ignored
		if (strstr($db_data["cli_fname"],"?") || strstr($db_data["cli_sname"],"?")) {
		
		
			$skipped[] = $db_data["cli_fname"].' '.$db_data["cli_sname"];
		
		} else {
		
		
		
			// insert the client, but before we do, maybe check for duplicates?
			// only worth doing if first and surnames are of a reasonable length
			if (strlen($db_data["cli_fname"]) > 2  && strlen($db_data["cli_sname"]) > 2) {
				$sql_check = "SELECT * FROM client WHERE cli_fname = '".addslashes($forenames)."' AND cli_sname = '".addslashes($surname)."'";
				//echo $sql_check."<p>\n\n";
					
				$q_check = $db->query($sql_check);
				if (DB::isError($q_check)) {  die("db check error: ".$q_check->getMessage()."\n".$sql_check); }
				while ($row_check = $q_check->fetchRow()) {
					$duplicates[] = $row_check["cli_id"];
					}
				}
		
		
		
		
		
			
			
			$cli_id = db_query($db_data,"INSERT","client","cli_id");
		
			
			// telephone numbers must be placed in tel table, and linked to current client
			$ord = 1;
			if (strlen($row["HomeTelephone"] > 3)) {
				$db_data_tel = array(
					'tel_number'=>phone_format($row["HomeTelephone"]),
					'tel_type'=>'Home',
					'tel_cli'=>$cli_id,
					'tel_ord'=>$ord
					);
					db_query($db_data_tel,"INSERT","tel","tel_id");
					$ord++;
					unset($db_data_tel);
				}
			if (strlen($row["WorkTelephone"] > 3)) {
				$db_data_tel = array(
					'tel_number'=>phone_format($row["WorkTelephone"]),
					'tel_type'=>'Work',
					'tel_cli'=>$cli_id,
					'tel_ord'=>$ord
					);
					db_query($db_data_tel,"INSERT","tel","tel_id");
					$ord++;
					unset($db_data_tel);
				}
			if (strlen($row["Facsimile"] > 3)) {
				$db_data_tel = array(
					'tel_number'=>phone_format($row["Facsimile"]),
					'tel_type'=>'Fax',
					'tel_cli'=>$cli_id,
					'tel_ord'=>$ord
					);
					db_query($db_data_tel,"INSERT","tel","tel_id");
					$ord++;
					unset($db_data_tel);
				}
			if (strlen($row["Mobile"] > 3)) {
				$db_data_tel = array(
					'tel_number'=>phone_format($row["Mobile"]),
					'tel_type'=>'Mobile',
					'tel_cli'=>$cli_id,
					'tel_ord'=>$ord
					);
					db_query($db_data_tel,"INSERT","tel","tel_id");
					$ord++;
					unset($db_data_tel);
				}
			
				
			}
		
		
		unset($db_data,$db_data_tel);
		#print_r($db_data);
		$counter++;
		}

	}


$db_data_import["imp_source"] = 'figthedog';
$db_data_import["imp_report"] = serialize($duplicates);
$db_data_import["imp_skipped"] = serialize($skipped);
db_query($db_data_import,"INSERT","import","imp_id");
}




echo '
<form>
Start from row: <input type="text" name="start" value="'.($start+$limit).'"><br>
Limit to: <input type="text" name="limit" value="'.$limit.'"><br>
<input type="submit" name="button" value="Submit">
</form>'.count($skipped).' skipped / '.count($duplicates) .' dups';
?>