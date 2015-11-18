<?php
/*
Postcode Class, using postcodeanywhere

Query PAF database with full or partial addresses, or UDPRN to obtain property details

Lookups return a list of matching addresses for given values
Results to be rendered into a select, drop down, or table.

Fetches return full address details for single property
A fetch is only ever performed from a lookup, and the fetched property will always be enetered into the property table.
If we are adding a property to the property table, we also geocode. 

Manual option should be included for addresses not listed, or incorrectly listed in PAF. 
Manual needs to query our database to prevent dups

If action=lookup we require some address details. Lookups do not consume credits.
	
	by_freetext:
	Lists the properties matching the given free text string. Performs a compound search based on the free text string.
	The free text string can contain the organisation name, building name or number, street, town and complete or partial postcode. 
	Any combination of the search elements is valid although they should always be presented in this order and separated with commas. 

	by_postcode:
	Lists the properties in a given postcode.
	The full postcode must be supplied. 
	
	by_outcode:
	Lists the localities served by a given postcode outcode (first part of the postcode).
	The complete postcode outcode should be supplied. 
	A single outcode can cover many localities although typically only 1 post town.

	*many more lookups are possible but not yet implemented (http://www.postcodeanywhere.co.uk/developers/documentation/uk/)


action=fetch - returns address info from UDPRN
action=geocode - returns geocode info from UDPRN
*/

$postcode_account_code = "WOOST11112";
$postcode_license_code = "YJ67-YN69-YY93-MG96";


class Postcode {

function Postcode() {
	$postcode_account_code = "WOOST11112";
	$postcode_license_code = "YJ67-YN69-YY93-MG96";
	}
// address lookup based on entered address values
function lookup($_type,$_address,$_style="select") {

	global $postcode_account_code,$postcode_license_code;
	
	/* Build up the URL to send the request to. */
	$_url = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$_url .= "account_code=" . urlencode($postcode_account_code);
	$_url .= "&license_code=" . urlencode($postcode_license_code);
	$_url .= "&action=lookup";
		
	if ($_type == 'by_freetext') {	
		$_url .= "&type=by_freetext";
		$_url .= "&freetext=" . urlencode($_address);
		}
	elseif ($_type == 'by_postcode') {	
		$_url .= "&type=by_postcode";
		$_split = explode(",",$_address);
		$_url .= "&postcode=" . urlencode($_split[0]);    
		$_url .= "&building=" . urlencode($_split[1]);   
		
		} 
	elseif ($_type == 'by_area') {	
		$_url .= "&type=by_area";
		$_url .= "&area=" . urlencode($_address);    
		} 
	elseif ($_type == 'browse') {	
		$_url .= "&type=browse"; 
		} 
	elseif ($_type == "by_localitykey") {
		$_url .= "&type=by_localitykey";
		$_url .= "&localitykey=" . urlencode($_address); 
		}
	elseif ($_type == "by_streetkey") {
		$_url .= "&type=by_streetkey";
		$_url .= "&streetkey=" . urlencode($_address); 
		}
	else {
		echo "search type invalid";
		exit;
		}
		
		
	$Data = $this->render($_url);
	
	if(count($Data)==0){
		// retry without house_number?
		/*
		$_address = 
		$_url .= "&type=by_freetext";
		$_url .= "&freetext=" . urlencode($_address);  
		$Data = $this->render($_url);
		*/
		echo '<p class="appInfo">No matching addresses were found, please check your details and 
		<a href="javascript:cancelResponse();">try again</a>.<br><br>
		Tip: Try searching just street name to see a list of all properties in that street.<br>
		<br>If you still have no luck, you can enter the address <a href="javascript:ajax_manual();">manually</a>.</p>';
		//echo error_message($errors,'javascript:cancelResponse();','noheader');
		exit;
		}
	
	
	// output styles
	if ($_style == "select") {		
		$render = '<select name="postcode_id" size="10" style="width:500px" onDblClick="document.forms[0].submit();">'."\n";		
		foreach ($Data as $keyd => $data) {
			$render .='<option value="'.$data["id"].'">'.trim($data["description"]).'</option>'."\n";
			}
		$render .='</select>'."\n";
		}
	elseif ($_style == "table") {
		$render = '<table>'."\n";
		foreach ($Data as $keyd => $data) {
			$render .='<tr><td>'.$data["id"].'</td><td>'.trim($data["description"]).'</td></tr>'."\n";
			}
		$render .= '</table>'."\n";
		}
	elseif ($_style == "data") {
		return $Data;
		exit;
		$render = '<table>'."\n";
		foreach ($Data as $keyd => $data) {
			$render .='<tr><td>'.$data["id"].'</td><td>'.trim($data["description"]).'</td></tr>'."\n";
			}
		$render .= '</table>'."\n";
		/*foreach ($Data as $array) {
			foreach ($array as $key => $val) {
				$render .= $key." -> ".$val;
				}
			}*/				
		}
	#print_r($Data);
	return $render;
	}



// get address info from UDPRN
function fetch($_udprn){
	
	global $postcode_account_code,$postcode_license_code;
	
	$_url = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$_url .= "account_code=" . urlencode($postcode_account_code);
	$_url .= "&license_code=" . urlencode($postcode_license_code);
	$_url .= "&action=fetch";
	//$_url .= "&style=raw";
	$_url .= "&style=rawgeographic"; // added 25/04/07 to give local authority value
	$_url .= "&id=" . $_udprn;
	
	return $this->render($_url);
	}

// get geocode data (coordinates etc) from UDPRN
function geocode($_udprn){

	global $postcode_account_code,$postcode_license_code;
		
	$_url = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$_url .= "account_code=" . urlencode($postcode_account_code);
	$_url .= "&license_code=" . urlencode($postcode_license_code);
	$_url .= "&action=geocode";
	$_url .= "&accuracy=high";
	$_url .= "&id=" . $_udprn; 
	
	return $this->render($_url);
	}

// format data array into html select list (for multiple results from lookup)
function output_list($Data) {
	$output = '<select name="udprn" id="udprn" size="10" style="width:500px" onDblClick="javascript:ajax_udrpn();">'."\n";	 //document.forms[0].submit();
	foreach ($Data as $keyd => $data) {
		$output .='<option value="'.$data["id"].'">'.trim($data["description"]).'</option>'."\n";
		}
	$output .='</select>'."\n";	
	return $output;	
	}

// output fetch results into a associative array suitable for my table
function output_form($Data) {

/*
id=21519311.00|
seq=0|
line1=1 Harfield Gardens|
line2=Grove Lane|
post_town=London|
postcode=SE5 8DB|
mailsort=24611|
barcode=(SE58DB1A1)|
is_residential=1|
is_small_organisation=0|
is_large_organisation=0|
delivery_point_suffix=1A|
checksum=1|
name_or_number=1|
building_number=1|
thoroughfare_name=Grove|
thoroughfare_descriptor=Lane|
dependent_thoroughfare_name=Harfield|
dependent_thoroughfare_descriptor=Gardens|
number_of_households=0|building_name_or_number=1|
reformatted_building_number=1|


id=21520855.00|seq=0|
line1=7 Chamberlain Cottages|
line2=Camberwell Grove|
post_town=London|
postcode=SE5 8JD|
mailsort=24611|
barcode=(SE58JD1HM)|
is_residential=1|
is_small_organisation=0|
is_large_organisation=0|
delivery_point_suffix=1H|
checksum=M|
name_or_number=7|
building_number=7|
thoroughfare_name=Camberwell|
thoroughfare_descriptor=Grove|
dependent_thoroughfare_name=Chamberlain|
dependent_thoroughfare_descriptor=Cottages|
number_of_households=1|
building_name_or_number=7|
reformatted_building_number=7|
grid_east_m=532800|
grid_north_m=176600|
district_code=BE|
ward_code=00BEGC|
nhs_code=Q36|
nhs_region_code=Y21|county_code=00|country_code=064|ward_status=1|ward_name=Brunswick Park|district_name=Southwark|objective_2=0|transitional=0|longitude=-0.087703828901419|latitude=51.4722713791961|os_reference=TQ 32800 76600|wgs84_longitude=-0.0892932414434701|wgs84_latitude=51.4727318897149|constituency_code=088|constituency=Camberwell and Peckham|mp=Rt Hon Harriet Harman QC|party=Labour|country_name=England|lea_code=210|lea_name=Southwark|nhs_name=London|nhs_region_name=London|nhs_pct_code=5LE|nhs_pct_name=Southwark|go_code=H|go_name=London|id=21520855.00|seq=0|location=7 Chamberlain Cottages Camberwell Grove London SE5|grid_east_m=532896|grid_north_m=176649|longitude=-0.086303842479008|latitude=51.4726892236396|os_reference=TQ 32896 76649|wgs84_longitude=-0.0878934014873052|wgs84_latitude=51.4731497134118|

	
	*/
	foreach ($Data as $keyd => $postcode_data) {    
		$output["id"] = $postcode_data["id"];
		// if there is a flat number, use it as addr1, else use building number (house)
		if ($postcode_data["sub_building_name"]) {
			$output["addr1"] = $postcode_data["sub_building_name"];
			} else {
			$output["addr1"] = $postcode_data["name_or_number"];
			}
		// added 22/03/07, only just became aware of these two values. they are estate names, or sub-street names like "close", "park", etc
		if ($postcode_data["dependent_thoroughfare_name"] || $postcode_data["dependent_thoroughfare_descriptor"]) {
			$addr2supp = $postcode_data["dependent_thoroughfare_name"].' '.$postcode_data["dependent_thoroughfare_descriptor"];
			}
		
		// building name and number if the property is a flat (guessing)
		if ($postcode_data["building_name"]) {
			$addr2 = $postcode_data["building_name"];
			if ($postcode_data["building_number"]) {
				$addr2 .= ', '.$postcode_data["building_number"];
				}
			if ($output["addr1"] == $addr2) {	// if addr1 and addr2 are the same, clear addr2
				$output["addr2"] = "";
				} else {
				$output["addr2"] = $addr2;
				}					
			} 
		else {
			if ($postcode_data["building_number"]) {
				$addr2 = $postcode_data["building_number"];
				if ($output["addr1"] == $addr2) {
					$output["addr2"] = "";
					} else {
					$output["addr2"] = $addr2;
					}
				} 
			else {
				$output["addr2"] = "";
				}
			}
			
		if (trim($addr2supp)) {
			if ($output["addr2"]) {
				$output["addr2"] .= ', '.$addr2supp;
				} else {
				$output["addr2"] = $addr2supp;
				}
			}
			
		// street name gathered from parts	
		$output["addr3"] = $postcode_data["thoroughfare_name"].' '.$postcode_data["thoroughfare_descriptor"];
		$output["addr5"] = $postcode_data["post_town"];
		$output["postcode"] = $postcode_data["postcode"];
		$output["authority"] = $postcode_data["district_name"];
		// get entire data and store in field in case table structure changes
		foreach ($postcode_data as $keydump => $datadump) {   
			$pro_dump .= $keydump.'='.$datadump.'|';
			}
		$output["dump"] = $pro_dump;
		}
		
	return $output;
	//mail('mail@markdw.com','addr',print_r($output,true));
	}	
	
	
	

// function to insert property record from UDPRN
// checks existing property table for matches against UDPRN, return id if found
// get full goecode data, format addresses according to property table, insert and return id
// this costs 2 or 3?? credits if property not already in table
function property($_udprn) {
	
	// query existing property to see if UDPRN is already present
	$_sql = "SELECT pro_id,pro_pcid,pro_east,pro_north,pro_latitude,pro_longitude 
	FROM property WHERE pro_pcid = '".$_udprn."' LIMIT 1";
	$_result = mysql_query($_sql);	
	if (!$_result)	die("MySQL Error:  ".mysql_error()."<pre>Class Postcode::property: ".$_sql."</pre>");
	$_numrows = mysql_num_rows($_result);
	if ($_numrows !== 0) {
		while($_row = mysql_fetch_array($_result)) {			
			$_pro_id = $_row["pro_id"];
			$_pro_east = $_row["pro_east"];
			$_pro_north = $_row["pro_north"];
			$_pro_latitude = $_row["pro_latitude"];
			$_pro_longitude = $_row["pro_longitude"];
			}
		// check if existing property has been geocoded, and if not, do so
		if (!$_pro_east || !$_pro_north || !$_pro_latitude || ! $_pro_longitude) {
			
			$Data2 = $this->geocode($_udprn); 
			if(count($Data2)==0){
				//$errors[] = "There was an error geocoding your address. Please try again.";
				//echo error_message($errors);
				//exit;
				} 
			else {
				foreach ($Data2 as $keyd => $postcode_geodata) { 								
					// postcode anywhere unique id, will need to be changed UDRPN
					$_db_data["pro_pcid"] = $postcode_geodata["id"];
					$_db_data["pro_east"] = $postcode_geodata["grid_east_m"];
					$_db_data["pro_north"] = $postcode_geodata["grid_north_m"];	
					$_db_data["pro_latitude"] = $postcode_geodata["wgs84_latitude"];
					$_db_data["pro_longitude"] = $postcode_geodata["wgs84_longitude"];
					// get entire data and store in field in case table structure changes
					foreach ($postcode_geodata as $keydump => $datadump) {   
						$pro_dump .= $keydump.'='.$datadump.'|';
						}				
					$_db_data["pro_dump"] = $pro_dump;
					}
				#echo "UPDATE";
				#print_r($_db_data);
				#exit;
				$_pro_id = db_query($_db_data,"UPDATE","property","pro_id",$_pro_id);			
				}
			}
		}
			
	// if UDPRN not found, get goecode data and insert into property table
	else {		
		// fetch address data (1 credit)
		$Data1 = $this->fetch($_udprn); 
		
		if(count($Data1)==0){
			$errors[] = "There was an error fetching your address. Please try again.";
			echo error_message($errors);
			exit;
			}
		else {		
			foreach ($Data1 as $keyd => $postcode_data) {    
				//$_id = $postcode_data["id"];
				// if there is a flat number, use it as addr1, else use building number (house)
				if ($postcode_data["sub_building_name"]) {
					$_db_data["pro_addr1"] = $postcode_data["sub_building_name"];
					} else {
					$_db_data["pro_addr1"] = $postcode_data["name_or_number"];
					}
				
				// added 22/03/07, only just became aware of these two values. they are estate names, or sub-street names like "close", "park", etc
				if ($postcode_data["dependent_thoroughfare_name"] || $postcode_data["dependent_thoroughfare_descriptor"]) {
					$addr2supp = $postcode_data["dependent_thoroughfare_name"].' '.$postcode_data["dependent_thoroughfare_descriptor"];
					}
				
				// building name and number if the property is a flat (guessing)
				if ($postcode_data["building_name"]) {
					$addr2 = $postcode_data["building_name"];
					if ($postcode_data["building_number"]) {
						$addr2 .= ', '.$postcode_data["building_number"];
						}
					if ($_db_data["pro_addr1"] == $addr2) {	// if addr1 and addr2 are the same, clear addr2
						$_db_data["pro_addr2"] = "";
						} else {
						$_db_data["pro_addr2"] = $addr2;
						}					
					} 
				else {
					if ($postcode_data["building_number"]) {
						$addr2 = $postcode_data["building_number"];
						if ($_db_data["pro_addr1"] == $addr2) {
							$_db_data["pro_addr2"] = "";
							} else {
							$_db_data["pro_addr2"] = $addr2;
							}
						} 
					else {
						$_db_data["pro_addr2"] = "";
						}
					}
				
				if (trim($addr2supp)) {
					if ($output["addr2"]) {
						$output["addr2"] .= ', '.$addr2supp;
						} else {
						$output["addr2"] = $addr2supp;
						}
					}
				
				// street name gathered from parts	
				$_db_data["pro_addr3"] = $postcode_data["thoroughfare_name"].' '.$postcode_data["thoroughfare_descriptor"];
				$_db_data["pro_addr5"] = $postcode_data["post_town"];
				$_db_data["pro_postcode"] = $postcode_data["postcode"];
				$_db_data["pro_authority"] = $postcode_data["district_name"]; // added 25/04/07
				// get entire data and store in field in case table structure changes
				foreach ($postcode_data as $keydump => $datadump) {   
					$pro_dump .= $keydump.'='.$datadump.'|';
					}
				} 
			
			
			// get geocode coordinate details - 2 credits (but maybe just 1?)
			$Data2 = $this->geocode($_udprn); 
			if(count($Data2)==0){
				//$errors[] = "There was an error geocoding your address. Please try again.";
				//echo error_message($errors);
				//exit;
				} 
			else {
				foreach ($Data2 as $keyd => $postcode_geodata) { 								
					// postcode anywhere unique id, will need to be changed UDRPN
					$_db_data["pro_pcid"] = $postcode_geodata["id"];
					$_db_data["pro_east"] = $postcode_geodata["grid_east_m"];
					$_db_data["pro_north"] = $postcode_geodata["grid_north_m"];	
					$_db_data["pro_latitude"] = $postcode_geodata["wgs84_latitude"];
					$_db_data["pro_longitude"] = $postcode_geodata["wgs84_longitude"];
					// get entire data and store in field in case table structure changes
					foreach ($postcode_geodata as $keydump => $datadump) {   
						$pro_dump .= $keydump.'='.$datadump.'|';
						}				
					$_db_data["pro_dump"] = addslashes($pro_dump); // addslashes, temp fix as some dumps contained apostrophes
					}
				}
			}
			
		mysql_free_result($_result);
		#echo "INSERT";
		#print_r($_db_data);
		#exit;
		
		// insert record into property table if it dosent already exist
		$_pro_id = db_query($_db_data,"INSERT","property","pro_id");
		}
	
	//print_r($_db_data);
	// need some method of returning the $_db_data array, or the property id (or both?)
	return($_pro_id);
	unset($_udprn,$_sql,$_result,$_row,$keyd,$postcode_data,$_db_data,$Data);
	}

// function to insert property record from UDPRN without geocode (used for client import)
function property_nogeocode($_udprn) {
	
	// query existing property to see if UDPRN is already present
	$_sql = "SELECT pro_id,pro_pcid,pro_east,pro_north,pro_latitude,pro_longitude 
	FROM property WHERE pro_pcid = '".$_udprn."' LIMIT 1";
	$_result = mysql_query($_sql);	
	if (!$_result)	die("MySQL Error:  ".mysql_error()."<pre>Class Postcode::property: ".$_sql."</pre>");
	$_numrows = mysql_num_rows($_result);
	if ($_numrows !== 0) {
		while($_row = mysql_fetch_array($_result)) {			
			$_pro_id = $_row["pro_id"];
			$_pro_east = $_row["pro_east"];
			$_pro_north = $_row["pro_north"];
			$_pro_latitude = $_row["pro_latitude"];
			$_pro_longitude = $_row["pro_longitude"];
			}
		/*
		// check if existing property has been geocoded, and if not, do so
		if (!$_pro_east || !$_pro_north || !$_pro_latitude || ! $_pro_longitude) {
			
			$Data2 = $this->geocode($_udprn); 
			if(count($Data2)==0){
				$errors[] = "There was an error geocoding your address. Please try again.";
				echo error_message($errors);
				exit;
				}
			foreach ($Data2 as $keyd => $postcode_geodata) { 								
				// postcode anywhere unique id, will need to be changed UDRPN
				$_db_data["pro_pcid"] = $postcode_geodata["id"];
				$_db_data["pro_east"] = $postcode_geodata["grid_east_m"];
				$_db_data["pro_north"] = $postcode_geodata["grid_north_m"];	
				$_db_data["pro_latitude"] = $postcode_geodata["wgs84_latitude"];
				$_db_data["pro_longitude"] = $postcode_geodata["wgs84_longitude"];
				// get entire data and store in field in case table structure changes
				foreach ($postcode_geodata as $keydump => $datadump) {   
					$pro_dump .= $keydump.'='.$datadump.'|';
					}				
				$_db_data["pro_dump"] = $pro_dump;
				}
			#echo "UPDATE";
			#print_r($_db_data);
			#exit;
			$_pro_id = db_query($_db_data,"UPDATE","property","pro_id",$_pro_id);			
			}
			*/
		}
			
	// if UDPRN not found, get goecode data and insert into property table
	else {		
		// fetch address data (1 credit)
		$Data1 = $this->fetch($_udprn); 
		
		if(count($Data1)==0){
			$errors[] = "There was an error fetching your address. Please try again.";
			echo error_message($errors);
			exit;
			}
		else {		
			foreach ($Data1 as $keyd => $postcode_data) {    
				//$_id = $postcode_data["id"];
				// if there is a flat number, use it as addr1, else use building number (house)
				if ($postcode_data["sub_building_name"]) {
					$_db_data["pro_addr1"] = $postcode_data["sub_building_name"];
					} else {
					$_db_data["pro_addr1"] = $postcode_data["name_or_number"];
					}
				
				// added 22/03/07, only just became aware of these two values. they are estate names, or sub-street names like "close", "park", etc
				if ($postcode_data["dependent_thoroughfare_name"] || $postcode_data["dependent_thoroughfare_descriptor"]) {
					$addr2supp = $postcode_data["dependent_thoroughfare_name"].' '.$postcode_data["dependent_thoroughfare_descriptor"];
					}
				
				// building name and number if the property is a flat (guessing)
				if ($postcode_data["building_name"]) {
					$addr2 = $postcode_data["building_name"];
					if ($postcode_data["building_number"]) {
						$addr2 .= ', '.$postcode_data["building_number"];
						}
					if ($_db_data["pro_addr1"] == $addr2) {	// if addr1 and addr2 are the same, clear addr2
						$_db_data["pro_addr2"] = "";
						} else {
						$_db_data["pro_addr2"] = $addr2;
						}					
					} 
				else {
					if ($postcode_data["building_number"]) {
						$addr2 = $postcode_data["building_number"];
						if ($_db_data["pro_addr1"] == $addr2) {
							$_db_data["pro_addr2"] = "";
							} else {
							$_db_data["pro_addr2"] = $addr2;
							}
						} 
					else {
						$_db_data["pro_addr2"] = "";
						}
					}
				
				if (trim($addr2supp)) {
					if ($output["addr2"]) {
						$output["addr2"] .= ', '.$addr2supp;
						} else {
						$output["addr2"] = $addr2supp;
						}
					}
				
				// street name gathered from parts	
				$_db_data["pro_addr3"] = $postcode_data["thoroughfare_name"].' '.$postcode_data["thoroughfare_descriptor"];
				$_db_data["pro_addr5"] = $postcode_data["post_town"];
				$_db_data["pro_postcode"] = $postcode_data["postcode"];
				$_db_data["pro_authority"] = $postcode_data["district_name"]; // added 25/04/07
				// get entire data and store in field in case table structure changes
				foreach ($postcode_data as $keydump => $datadump) {   
					$pro_dump .= $keydump.'='.$datadump.'|';
					}
				} 
			
			/*
			// get geocode coordinate details - 2 credits (but maybe just 1?)
			$Data2 = $this->geocode($_udprn); 
			if(count($Data2)==0){
				$errors[] = "There was an error geocoding your address. Please try again.";
				echo error_message($errors);
				exit;
				}
			foreach ($Data2 as $keyd => $postcode_geodata) { 								
				// postcode anywhere unique id, will need to be changed UDRPN
				$_db_data["pro_pcid"] = $postcode_geodata["id"];
				$_db_data["pro_east"] = $postcode_geodata["grid_east_m"];
				$_db_data["pro_north"] = $postcode_geodata["grid_north_m"];	
				$_db_data["pro_latitude"] = $postcode_geodata["wgs84_latitude"];
				$_db_data["pro_longitude"] = $postcode_geodata["wgs84_longitude"];
				// get entire data and store in field in case table structure changes
				foreach ($postcode_geodata as $keydump => $datadump) {   
					$pro_dump .= $keydump.'='.$datadump.'|';
					}				
				$_db_data["pro_dump"] = addslashes($pro_dump); // addslashes, temp fix as some dumps contained apostrophes
				}
			*/
			}
			
		mysql_free_result($_result);
		#echo "INSERT";
		#print_r($_db_data);
		#exit;
		
		// insert record into property table if it dosent already exist
		$_pro_id = db_query($_db_data,"INSERT","property","pro_id");
		}
	
	//print_r($_db_data);
	// need some method of returning the $_db_data array, or the property id (or both?)
	return($_pro_id);
	unset($_udprn,$_sql,$_result,$_row,$keyd,$postcode_data,$_db_data,$Data);
	}


// renders xml data from postcodeanywhere into $Data array
function render($URL){   
	
	/* Open the URL into a file */
	$ContentsFetch=file("$URL");
	
	foreach ($ContentsFetch as $line_num => $line) {
		if (strpos($line,"<Item ")!=false) { $Contents[]= $line;}
		}
	
	for ($i=0;$i<count($Contents);$i++) {		
		/* Strip out "<Item " and " />" from the XML */
		$Contents[$i]=substr($Contents[$i], 6+strpos($Contents[$i],"<Item "));
		$Contents[$i]=substr($Contents[$i], 0, strlen($Contents[$i])-4);
		$breakapart=explode("\"",$Contents[$i]);
	
		/* Extract field names and values */
		for ($x=0;$x<count($breakapart);$x++){
			if ($x % 2 == 0){
				$k=trim(str_replace("=", "", $breakapart[$x]));
				if ($k!='') { $Data[$i][$k]=$breakapart[$x+1]; }
				}
			}
		}	
	return $Data;
	unset($Data,$ContentsFetch,$Contents,$breakapart,$x);
	}






} // end of Postcode class
?>