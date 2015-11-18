<?php
require_once("../inx/global.inc.php");
/*
importing old proprties from admin into v3

importing from property_old table in v3.

only dealing with properties where status != for sale, under offer, sold

sales and lettings. sale added with TEMPORARY VENDOR, lettings with TEMPORARY LANDLORD

use old status where possible.

need following in arrays:
negs - old_id => new_id
status - old_id => new_title

attempt a postcode lookup on each property? should be reasonably reliable. needs testing.
or alternatively, i need to create a page to re-assign an pcid to existing proeprties and remove the manually entered address

add old prop_ID to pro_oldid
*/

// old state_of_trade_id => new status title
// archived should not be used here as they will not appear in any searches. instead use Unclassified
// in deal_summary, Unclassified can be set to any sttaus except Available, Under Offer and Exchanged
$state_of_trade = array(
	7=>'Unknown', //'Archived',
	8=>'Completed',
	9=>'Withdrawn',
	10=>'Disinstructed'
	);

$tenure = array(
	1=>'Freehold',
	2=>'Leasehold',
	3=>'Share of Freehold'
	);

/*
1 Semi Detached
2 Detached
3 Flat
4 Maisonette
5 Bungalow
6 Studio Flat
7 Terrace
8 Commercial
9 Live/Work
10 Garage
11 House/Flat Share
*/



// old_id=>new_id
$neg = array(
	2=>2, 		//Luke Wooster
	5=>7, 		//Arabella Jenkins
	6=>12, 		//Thanh Phan
	7=>4, 		//Jenny Ellis
	10=>10, 	//Colin Lowman
	13=>8, 		//Julia Russell
	14=>3, 		//Becky Munday
	17=>5, 		//James Waller
	21=>43, 	//Robert Huntley
	26=>20, 	//Jenny Holland
	27=>27, 	//Courtney Welsh
	42=>35, 	//Kristy Stephanou
	48=>33, 	//Luke Bishop
	50=>45, 	//Jessica Pursey
	52=>44, 	//David Roberts
	55=>37, 	//Emma Ovenell
	61=>28, 	//Barney Eden
	62=>36, 	//Bruce Alsen
	67=>46 		//Paolo Ulivi
	);


$sql = "SELECT * FROM property_old WHERE
state_of_trade_id  = 7 OR state_of_trade_id  = 8 OR state_of_trade_id  = 9 OR state_of_trade_id = 10
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {


	// attempt property lookup using address
	$address = $row["house_number"].' '.$row["Address1"].' '.$row["Postcode"];

	// or enter manually
	$db_data_pro["pro_addr1"] = $row["house_number"];
	$db_data_pro["pro_addr3"] = $row["Address1"];
	$db_data_pro["pro_addr5"] = 'London';
	$db_data_pro["pro_country"] = '217';
	$db_data_pro["pro_postcode"] = $row["Postcode"];
	$db_data_pro["pro_area"] = $row["area_id"]; // i think area ids are common between dabatases..?
	$db_data_pro["pro_pcid"] = '-1';
	$db_data_pro["pro_east"] = $row["osx"];
	$db_data_pro["pro_north"] = $row["osy"];

	$pro_id = db_query($db_data_pro,"INSERT","property","pro_id");


	$db_data["dea_prop"] = $pro_id;

	if ($row["SaleLet"] == 1) {
		$db_data["dea_type"] = 'Sales';
		if ($row["Branch"] == 1) {
			$db_data["dea_branch"] = 1;
			} else {
			$db_data["dea_branch"] = 2;
			}

		} elseif ($row["SaleLet"] == 2) {
		$db_data["dea_type"] = 'Lettings';
		if ($row["Branch"] == 1) {
			$db_data["dea_branch"] = 3;
			} else {
			$db_data["dea_branch"] = 4;
			}
		}

	$db_data["dea_oldid"] = $row["prop_ID"];
	$db_data["dea_neg"] = $neg[$row["Neg"]];
	$db_data["dea_marketprice"] = $row["Price"];
	$db_data["dea_tenure"] = $tenure[$row["leasefree"]];
	$db_data["dea_status"] = $state_of_trade[$row["state_of_trade_id"]];


	if ($row["type_id"] == 1) {
		$db_data["dea_ptype"] = 1;
		$db_data["dea_psubtype"] = 5;
		}
	elseif ($row["type_id"] == 2) {
		$db_data["dea_ptype"] = 1;
		$db_data["dea_psubtype"] = 4;
		}
	elseif ($row["type_id"] == 3) {
		$db_data["dea_ptype"] = 2;
		$db_data["dea_psubtype"] = 20;
		}
	elseif ($row["type_id"] == 4) {
		$db_data["dea_ptype"] = 2;
		$db_data["dea_psubtype"] = 12;
		}
	elseif ($row["type_id"] == 5) {
		$db_data["dea_ptype"] = 1;
		$db_data["dea_psubtype"] = 9;
		}
	elseif ($row["type_id"] == 6) {
		$db_data["dea_ptype"] = 2;
		$db_data["dea_psubtype"] = 13;
		}
	elseif ($row["type_id"] == 7) {
		$db_data["dea_ptype"] = 1;
		$db_data["dea_psubtype"] = 6;
		}
	elseif ($row["type_id"] == 8) {
		$db_data["dea_ptype"] = 3;
		$db_data["dea_psubtype"] = 14;
		}
	elseif ($row["type_id"] == 9) { // live/work, assume house if freehold, else apartment
		if ($row["leasefree"] == 1) {
			$db_data["dea_ptype"] = 1;
			$db_data["dea_psubtype"] = 10;
			} else {
			$db_data["dea_ptype"] = 2;
			$db_data["dea_psubtype"] = 21;
			}
		}
	elseif ($row["type_id"] == 10) {
		$db_data["dea_ptype"] = 3;
		$db_data["dea_psubtype"] = 16;
		}
	elseif ($row["type_id"] == 11) {
		$db_data["dea_ptype"] = 1;
		$db_data["dea_psubtype"] = 26;
		}


	$db_data["dea_description"] = $row["longDescription"];
	$db_data["dea_strapline"] = $row["description"];
	$db_data["dea_reception"] = $row["receptions"];
	$db_data["dea_bedroom"] = $row["bedrooms"];
	$db_data["dea_bathroom"] = $row["bathrooms"];

	$db_data["dea_servicecharge"] = $row["ground_rent"];
	$db_data["dea_groundrent"] = $row["service_charge"];
	$db_data["dea_othercharge"] = $row["other_details"];
	$db_data["dea_created"] = $row["Dates"];
	$db_data["dea_launchdate"] = $row["Dates"];
	$db_data["dea_board"] = $row["board"];
	$db_data["dea_managed"] = $row["managed"];

	$dea_id = db_query($db_data,"INSERT","deal","dea_id");

	if ($row["SaleLet"] == 1) {
		$db_data_link_client_to_instruction["clientId"] = 1; // temporary vendor
		$db_data_link_client_to_instruction["dealId"] = $dea_id;
		} else {
		$db_data_link_client_to_instruction["clientId"] = 2; // temporary landlord
		$db_data_link_client_to_instruction["dealId"] = $dea_id;
		}

	db_query($db_data_link_client_to_instruction,"INSERT","link_client_to_instruction","id");

	unset($db_data_pro,$db_data,$db_data_link_client_to_instruction);
	}





?>