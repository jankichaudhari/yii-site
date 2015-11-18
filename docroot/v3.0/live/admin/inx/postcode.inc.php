<?php

// postcode lookup related info


// postcodeanywhere
$postcode_account_code = "WOOST11112";
$postcode_license_code = "YJ67-YN69-YY93-MG96";



// query by postcode and building number
function postcode_bypostcode($postcode,$building){

	global $postcode_account_code,$postcode_license_code;
	
	/* Build up the URL to send the request to. */
	$sURL = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$sURL .= "account_code=" . urlencode($postcode_account_code);
	$sURL .= "&license_code=" . urlencode($postcode_license_code);
	$sURL .= "&action=lookup";
	$sURL .= "&type=by_postcode";
	$sURL .= "&postcode=" . urlencode($postcode);    
	$sURL .= "&building=" . urlencode($building);    
	
	postcode_render($sURL);
	}

// freetext query (house number or building name, street, postcode)
function postcode_freetext($formatted_address){

	global $postcode_account_code,$postcode_license_code;
	
	/* Build up the URL to send the request to. */
	$sURL = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$sURL .= "account_code=" . urlencode($postcode_account_code);
	$sURL .= "&license_code=" . urlencode($postcode_license_code);
	$sURL .= "&action=lookup";
	$sURL .= "&type=by_freetext";
	$sURL .= "&freetext=" . urlencode($formatted_address);    
	
	postcode_render($sURL);
	}


// get address info from UDPRN
function postcode_fetchaddr($AddressID){
   
	global $postcode_account_code,$postcode_license_code;
	
	$sURL = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$sURL .= "account_code=" . urlencode($postcode_account_code);
	$sURL .= "&license_code=" . urlencode($postcode_license_code);
	$sURL .= "&action=fetch";
	$sURL .= "&style=raw";
	$sURL .= "&id=" . $AddressID;
	
	postcode_render($sURL);
	}


// get geocode data (coordinates etc) from UDPRN
function postcode_geocode($AddressID){

	global $postcode_account_code,$postcode_license_code;
	
	$sURL = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$sURL .= "account_code=" . urlencode($postcode_account_code);
	$sURL .= "&license_code=" . urlencode($postcode_license_code);
	$sURL .= "&action=geocode";
	$sURL .= "&accuracy=high";
	$sURL .= "&id=" . $AddressID; 
	
	postcode_render($sURL);
	}
   

// renders xml data from postcodeanywhere into $Data array
function postcode_render($URL){
   
   global $Data;
   
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
}





// check if a postcode is a valid format
function is_valid_uk_postcode($postcode) {
	$postcode = str_replace(" ", "", $postcode);
	$postcode = strtoupper($postcode);
		if ( eregi('^[A-Z]{1,2}[0-9]{1,2}[A-Z]? ?[0-9][A-Z]{2}$', $postcode) and 
		   ( strlen($postcode) < 8 ) and 
		   ( strlen($postcode) > 0 )
		 ) {
	
	$halve[1] = substr($postcode, -3, 3);
	$position = strpos($postcode, $halve[1]);
	$halve[0] = substr($postcode, 0, $position);
	//return $halve;
	return true;
	}
	else
	return false;
	}



// function to insert property record from UDPRN
// checks existing property table for matches against UDPRN, return id if found
// get full goecode data, format addresses according to property table, insert and return id
// this costs 2 or 3?? credits if property not already in table
function postcode_property($_UDPRN) {
	
	global $Data; // won't work without this
	
	// query existing property to see if UDPRN is already present
	$_sql = "SELECT pro_id,pro_pcid FROM property WHERE pro_pcid = '".$_UDPRN."' LIMIT 1";
	$_result = mysql_query($_sql);	
	if (!$_result)	die("MySQL Error:  ".mysql_error()."<pre>postcode_property: ".$_sql."</pre>");
	$_numrows = mysql_num_rows($_result);
	if ($_numrows !== 0) {
		while($_row = mysql_fetch_array($_result)) {			
			$_pro_id = $_row["pro_id"];
			}
		}
			
	// if UDPRN not found, get goecode data and insert into property table
	else {		
		// fetch address data (1 credit)
		postcode_fetchaddr($_UDPRN); 
		
		if(count($Data)==0){
			$errors[] = "There was an error fetching your address. Please try again.";
			echo error_message($errors);
			exit;
			}
		else {		
			foreach ($Data as $keyd => $postcode_data) {    
				$_id = $postcode_data["id"];				
				} 
			
			// get geocode coordinate details - 2 credits (but maybe just 1?)
			postcode_geocode("$_id"); 
			
			foreach ($Data as $keyd => $postcode_geodata) { 				
				// if there is a flat number, use it as addr1, else use building number (house)
				if ($postcode_geodata["sub_building_name"]) {
					$_db_data["pro_addr1"] = $postcode_geodata["sub_building_name"];
					} else {
					$_db_data["pro_addr1"] = $postcode_geodata["name_or_number"];
					}
				// building name and number if the property is a flat (guessing)
				if ($postcode_geodata["building_name"]) {
					$addr2 = $postcode_geodata["building_name"];
					if ($postcode_geodata["building_number"]) {
						$addr2 .= ', '.$postcode_geodata["building_number"];
						}
					if ($_db_data["pro_addr1"] == $addr2) {	// if addr1 and addr2 are the same, clear addr2
						$_db_data["pro_addr2"] = "";
						} else {
						$_db_data["pro_addr2"] = $addr2;
						}					
					} 
				else {
					if ($postcode_geodata["building_number"]) {
						$addr2 = $postcode_geodata["building_number"];
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
				// street name gathered from parts	
				$_db_data["pro_addr3"] = $postcode_geodata["thoroughfare_name"].' '.$postcode_geodata["thoroughfare_descriptor"];
				$_db_data["pro_addr5"] = $postcode_geodata["post_town"];
				$_db_data["pro_postcode"] = $postcode_geodata["postcode"];
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
			}
			
		mysql_free_result($_result);
			
		// insert record into property table if it dosent already exist
		$_pro_id = db_query($_db_data,"INSERT","property","pro_id","0");		
		}
		
	return($_pro_id);
	unset($_UDPRN,$_sql,$_result,$_row,$keyd,$postcode_data,$_db_data,$Data);
	}


function pcaBatchCleanse($addresses=array())
	{
	
	$counter = 0;
	foreach ($addresses as $cli=>$addr) {
		$url_inner .= "&address$counter=" . urlencode($addr);
		$counter++;
		}
	
	//Build the url
	$url = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$url .= "&action=batch_cleanse";
	$url .= $url_inner;
	$url .= "&account_code=WOOST11112";
	$url .= "&license_code=YJ67-YN69-YY93-MG96";
	
	
	//Make the request
	$data = simplexml_load_string(file_get_contents($url));
	
	//Check for an error
	if ($data->Schema['Items']==2)
		{
		throw new exception ($data->Data->Item['message']);
		}
	
	//Create the response
	foreach ($data->Data->children() as $row)
	 {
		  $rowItems="";
		  foreach($row->attributes() as $key => $value)
			  {
				  $rowItems[$key]=strval($value);
			  }
		  $output[] = $rowItems;
		 
	 }
	 print_r($output);
	//Return the result
	return $output;
	
	}

?>