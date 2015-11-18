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

require_once("DB.php");
require_once("../inx/format.inc.php");


if ($_GET["button"]) {

// get required table values into arrays for comparison
$dsn = array(
    'phptype'  => "mysql",
    'database' => "wsv3_test",
    'username' => "wsv3_db_user",
    'password' => "CHe9adru+*=!a!uC7ubRad!TRu#raN"
);

$db = DB::connect($dsn);
if (DB::isError($db)) {  die("Fatal error: ".$db->getMessage()); }
$db->setFetchMode(DB_FETCHMODE_ASSOC);

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






$dsn = array(
    'phptype'  => "mysql",
    'database' => "ws_db",
    'username' => "root",
    'password' => "changeoninstall"//"a345uyv"
);

$db = DB::connect($dsn);
if (DB::isError($db)) {  die("Fatal error: ".$db->getMessage()); }
$db->setFetchMode(DB_FETCHMODE_ASSOC);



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
FROM clients 
LEFT JOIN foundby ON clients.HeardBy = foundby.FoundBy_ID

LIMIT $start,$limit

";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	
	
	
	
	
	
	$original_name = ' '.strtolower($row["Name"]).' ';
	$original_name = str_replace(
		array('(',')','.'),
		' ',
		$original_name
		);	
	// if "and" is present, skip as this is probably 2 clients, add their id to a separate array
	if (stripos($original_name, ' and ') || stripos($original_name, ' & ') || stripos($original_name, ' &amp; ')) {
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
			
		if ($row["Title"]) {
			$db_data["cli_salutation"] = $row["Title"];
			} else {
			$db_data["cli_salutation"] = $cli_salutation;
			}
		$db_data["cli_fname"] = $forenames;
		$db_data["cli_sname"] = $surname;
		
		$db_data["cli_id"] = $row["Client_ID"];
		$db_data["cli_email"] = trim(strtolower($row["Email"]));
	
		
		// current address, attempt some reformatting and add to notes field
		if (trim($row["Address1"])) {
			$old_address = trim($row["Address1"]).", ";
			}
		if (trim($row["Address2"])) {
			$old_address .= trim($row["Address2"]).", ";
			}
		if (trim($row["Address3"])) {
			$old_address .= trim($row["Address3"]).", ";
			}
		if (trim($row["City"])) {
			$old_address .= trim($row["City"]).", ";
			}
		if (trim($row["Postcode"])) {
			$old_address .= format_postcode($row["Postcode"]);
			}
		$old_address = str_ireplace(
			array('marion putting on web','not given','none',' ng ',' nonegiven ',' none given '),
			'',
			$old_address
			);
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
		if (array_key_exists($row["PropertyType"],$PropertyType)) {
			$db_data["cli_saleptype"] = $PropertyType[$row["PropertyType"]];
			}
		
		// round prices to nearest 10000 to fit drop-downs
		$db_data["cli_salemin"] = round_to_nearest(numbers_only($row["MinPrice"]),10000);
		$db_data["cli_salemax"] = round_to_nearest(numbers_only($row["MaxPrice"]),10000);
		
		
		$db_data["cli_salebed"] = $row["Bedrooms"];
		
		if ($row["Areas"]) {
			// areas are full area titles split with hats. explode into array, match against area table in v3?
			$original_areas = explode("^",$row["Areas"]); 
			
			foreach($original_areas as $area_title) {	
				//if (in_array($area_title,$areas)) {
					$area_array[] = array_search($area_title,$areas);			
					//}
				}
			$new_areas = implode("|",$area_array);
			$new_areas = str_replace("||","|",$new_areas);
			$new_areas = str_replace("||","|",$new_areas);
			$new_areas = remove_firstchar($new_areas,"|");
			$new_areas = remove_lastchar($new_areas,"|");
			$db_data["cli_area"] = $new_areas;		
			
			unset($original_areas,$area_array,$new_areas);
			}
		
		
		// heardby - match title to title on source table
		if ($row["FoundBy_Title"]) {
			$db_data["cli_source"] = array_search(strtolower(str_replace(" ","",$row["FoundBy_Title"])),$source);
			}
		
		
		// dates should already be in the correct format
		$db_data["cli_created"] = $row["DateCreated"]; 
		
		// status (sales) L = live, S = not live
		if ($row["Status"] == 'L') {			
			$db_data["cli_sales"] = 'Yes';
			$db_data["cli_saleemail"] = 'Yes';
			}
		
		//////////// lettings specific at end of table //////////////
		// property type sales
		$PropertyTypeLet = array(
			'Any'=>'4|5|6|7|8|9|10|22|25|11|12|13|19|20|21|23|14|15|16|17', // all
			'House'=>'4|5|6|7|8|9|10|22|25', // all houses 
			'Apartment'=>'11|12|13|19|20|21|23', // all apartments
			'Commercial'=>'14', // all commercial
			'Live/Work'=>'10|21' // all live/work, houses and flats
			);
		if (array_key_exists($row["PropertyTypeLet"],$PropertyTypeLet)) {
			$db_data["cli_letptype"] = $PropertyTypeLet[$row["PropertyTypeLet"]];
			}
		
		$db_data["cli_letbed"] = $row["BedroomsLet"];
		$db_data["cli_letmin"] = numbers_only($row["MinPriceLet"]);
		$db_data["cli_letmax"] = numbers_only($row["MaxPriceLet"]);
		
		// status (lettings) L = live, S = not live
		if ($row["StatusLet"] == 'L') {			
			$db_data["cli_lettings"] = 'Yes';
			$db_data["cli_letemail"] = 'Yes';
			} 
			
		
		//////////// general fields, added after table was created //////////////
		
		
		$db_data["cli_branch"] = $row["Branch"];
		$db_data["cli_neg"] = $row["neg"];
		
		//cli_preferred
		if ($row["contact_method"] == "Email") {
			$db_data["cli_preferred"]  = 'Email';
			} 
		elseif ($row["contact_method"] == "Tel" || $row["contact_method"] == "Mobile") {
			$db_data["cli_preferred"]  = 'Telephone';
			} 
		else { // if not specified, set to email if there is an email address, else set to phone
			if ($row["Email"]) {
				$db_data["cli_preferred"]  = 'Email';
				} else {
				$db_data["cli_preferred"]  = 'Telephone';
				}
			}
		
		
		
		// noone has selected any of these in current table
		/*
		if ($row["DG"]) {
			$feature[] = 9;
			}
		if ($row["GCH"]) {
			$feature[] = 11;
			}
		
		$additional_requirements["Modern"] = $row["Modern"];
		$additional_requirements["Period"] = $row["Period"];
		$additional_requirements["Tenure"] = $row["Tenure"];
		$additional_requirements["Garden"] = $row["Garden"];
		$additional_requirements["Parking"] = $row["Parking"];
		$additional_requirements["BuyToLet"] = $row["BuyToLet"];
		*/
		
		// other stuff, get as much into features as possible, others into textfield
		$additional_requirements["Receptions"] = $row["Receptions"]; // $row["Receptions"]; receptions have no place in new system
		$additional_requirements["Bathrooms"] = $row["Bathrooms"]; // $row["Bathrooms"]; bathrooms have no place in new system
		$additional_requirements["AdminNotes"] = $row["Areas2"]; // areas2 is a private notes field used in admin
		$additional_requirements["UserNotes"] = $row["Notes"]; // notes was used for clients requirements a year or two ago
		
		
		$additional_requirements["SellingStatus"] = $row["Selling"];
		$additional_requirements["FurnishedLet"] = $row["FurnishedLet"];
		$additional_requirements["TermLet"] = $row["TermLet"];
		
		/*
		$row["Selling"];
		enum('Not Specified', 'First Time Buyer', 'Chain Free Buyer', 'Property Not Yet on Market', 'Property Currently on Market', 'Property Currently Under Offer', 'Buying to Let')
		*/
		
		
		/*
		$row["Mortgage"];
		enum('NA', 'Not Required', 'Mortgage Arranged', 'Requires Mortgage')
		*/
		
		$db_data["cli_oldnotes"] = serialize($additional_requirements);
		
		$db_data["cli_method"]  = 'Import';
		
		
		
		
		
		
		
		
		// telephone numbers must be placed in tel table, and linked to current client
		$ord = 1;
		if (strlen($row["Tel"] > 3)) {
			$db_data_tel[$row["Client_ID"]][] = array(
				'tel_number'=>phone_format($row["Tel"]),
				'tel_type'=>'Home',
				'tel_cli'=>$row["Client_ID"],
				'tel_ord'=>$ord
				);
				$ord++;
			}
		if (strlen($row["Fax"] > 3)) {
			$db_data_tel[$row["Client_ID"]][] = array(
				'tel_number'=>phone_format($row["Fax"]),
				'tel_type'=>'Fax',
				'tel_cli'=>$row["Client_ID"],
				'tel_ord'=>$ord
				);
				$ord++;
			}
		if (strlen($row["Mobile"] > 3)) {
			$db_data_tel[$row["Client_ID"]][] = array(
				'tel_number'=>phone_format($row["Mobile"]),
				'tel_type'=>'Mobile',
				'tel_cli'=>$row["Client_ID"],
				'tel_ord'=>$ord
				);
				$ord++;
			}
		
			
		
		
		$db_dimension[$counter]["client"] = $db_data;
		$db_dimension[$counter]["tel"] = $db_data_tel;
		unset($db_data,$db_data_tel);
		#print_r($db_data);
		$counter++;
		}

	}






// inserting into new table
require_once("../inx/db.inc.php");
require_once("../inx/postcode.inc.php");
require_once("../inx/postcode.class.inc.php");

foreach($db_dimension AS $key=>$values) {

// decided to skip part-names, so anything with a ? is now ignored
//if (!strpos($db_data["cli_fname"],"?") && !strpos($db_data["cli_sname"],"?")) {
	
	//echo strstr($values["client"]["cli_fname"],"?").' '.$values["client"]["cli_fname"];
	//echo strstr($values["client"]["cli_sname"],"?").' '.$values["client"]["cli_sname"];
	//echo "<hr>";
	if (
		strstr($values["client"]["cli_fname"],"?") ||
		strstr($values["client"]["cli_sname"],"?") ||
		(strlen($values["client"]["cli_fname"]) < 2  && strlen($values["client"]["cli_sname"]) < 2)
		) {
		
		//echo "skip";
		$skipped[] = $values["client"]["cli_fname"].' '.$values["client"]["cli_sname"];
		
		
		} else {
		
		
		$sql_check = "SELECT * FROM client WHERE cli_fname = '".addslashes($values["client"]["cli_fname"])."' AND cli_sname = '".addslashes($values["client"]["cli_sname"])."'";
		//echo $sql_check."<p>\n\n";
			
		$q_check = $db->query($sql_check);
		if (DB::isError($q_check)) {  die("db check error: ".$q_check->getMessage()."\n".$sql_check); }
		while ($row_check = $q_check->fetchRow()) {
			$duplicates[] = $values["client"]["cli_id"];
			}
		

		$cli_id = db_query($values["client"],"INSERT","client","cli_id");
		if ($values["tel"]) {
			foreach($values["tel"] as $telkey=>$telval) {
				foreach($telval as $telsubkey=>$telsubval) {				
					db_query($telsubval,"INSERT","tel","tel_id");
					}
				} 
			} 
		}
	}


$db_data_import["imp_source"] = 'admin';
$db_data_import["imp_total"] = $counter;
$db_data_import["imp_report"] = serialize($duplicates);
$db_data_import["imp_skipped"] = serialize($skipped);
db_query($db_data_import,"INSERT","import","imp_id");

/*
$counter = 0;
$cleansed = pcaBatchCleanse($addresses);

$pc = new Postcode();

foreach ($addresses as $cli=>$addr) {
	//echo "<hr>\n\nCOUNTER: $counter\nCLIENT: $cli\nADDR: $addr\n";
	if ($cleansed[$counter]['id']) {
		//echo "PCID: ".$cleansed[$counter]['id']."\n";
		$pro_id = $pc->property_nogeocode($cleansed[$counter]['id']);
		$db_data_p2c["p2c_cli"] = $cli;
		$db_data_p2c["p2c_pro"] = $pro_id;
		db_query($db_data_p2c,"INSERT","pro2cli","p2c_id");
		//echo $pro_id."\n".print_r($db_data_p2c,true);
		
		$db_data_cli["cli_pro"] = $pro_id;
		db_query($db_data_cli,"UPDATE","client","cli_id",$cli);
		
		unset($db_data_p2c,$db_data_cli,$pro_id);
		
		}
	//echo "\n\n<hr>\n\n";
	$counter++;
	}
*/
}
echo '
<form>
Start from row: <input type="text" name="start" value="'.($start+$limit).'"><br>
Limit to: <input type="text" name="limit" value="'.$limit.'"><br>
<input type="submit" name="button" value="Submit">
</form>'.count($skipped).' skipped / '.count($duplicates) .' dups';
?>