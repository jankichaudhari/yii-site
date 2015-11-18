<?php
session_start();
$pageTitle = "Property";
//echo "SESSID = ".$PHPSESSID;
require("global.php");
require("secure.php");
require("HTTP/Upload.php");
require_once('phpthumb/phpthumb.class.php');
require("queryLog.php");

if ($_POST["searchLink"]) {
	$searchLink = $_POST["searchLink"];
	} else {
	$searchLink = $_GET["searchLink"];
	}

// values used in queryLog
$_row = $intPropID;
$_table = "property";
$_field = "prop_ID";

// what section to view. this is also used in actions for redirects
if (!$_GET["view"]) {
	$view = "details";
	} else {
	$view = $_GET["view"];
	}

if ($_GET["action"] == "undo") {
	if (!$_GET["propID"]) {
		$errors[] = "Missing Property ID";
		} else {
		$intPropID = $_GET["propID"];
		}
	if (!$_GET["log"]) {
		$errors[] = "ID missing";
		} else {
		$intLogID = $_GET["log"];
		}
	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}
	queryLogUndo($intLogID,"prop_ID");
	header("Location:?propID=$intPropID&view=$view&searchLink=".urlencode($searchLink));
	}


elseif ($_GET["action"] == "delete_image") {
	if (!$_GET["propID"]) {
		$errors[] = "Missing Property ID";
		} else {
		$intPropID = $_GET["propID"];
		}
	if ($_GET["image"] == "") {
		$errors[] = "Missing Image ID";
		} else {
		$fieldnames[] = "image".$_GET["image"];
		$newvalues[] = '';
		}
	/* // removed file delete to facilitate undo
	if ($_GET["file"]) {
		if (file_exists($uploadPath."/".$file)) {
			unlink($uploadPath."/".$file);
			}
		}
	*/
	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}
	queryLog($fieldnames,$newvalues,$_table,$_field,$intPropID,"Update");
	header("Location:?propID=$intPropID&view=$view&searchLink=".urlencode($searchLink));
	}

elseif ($_GET["action"] == "proof") {
	if (!$_GET["propID"]) {
		$errors[] = "Missing Property ID";
		} else {
		$intPropID = $_GET["propID"];
		}

	$sql = "SELECT * FROM property WHERE property.prop_ID = $intPropID LIMIT 1";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("property select error: ".$q->getMessage()); }

	while ($row = $q->fetchRow()) {

		$postcodeLen = strlen($row["Postcode"]);
		$osxLen = strlen($row["osx"]);
		$osyLen = strlen($row["osy"]);

		if (!is_numeric($row["Price"])) {
			$errors[] = "Price must be a number";
			}
		if (!$row["house_number"]) {
			$errors[] = "House Number is required";
			}
		if (!$row["Address1"]) {
			$errors[] = "Street is required";
			}
		if ($postcodeLen < 6) {
			$errors[] = "Full Postcode is required";
			}
		if ($osxLen <> 6) {
			$errors[] = "OSX must be 6 characters";
			}
		if ($osyLen <> 6) {
			$errors[] = "OSY must be 6 characters";
			}
		if (!$row["description"]) {
			$errors[] = "Strap Line is required";
			}
		if (!$row["longDescription"]) {
			$errors[] = "Description is required";
			}
		if (!$row["image0"]) {
			$errors[] = "Main Image is required";
			} else {
			// check presence and dimensions of main (400x400), ftx (146x146), ftxx (56x56)
			$image_size = getimagesize($image_folder.$row["image0"]); // Read the size
			if ($image_size[0] <> 400) {
				$errors[] = "Main Image must be 400 pixels width";
				}
			if ($image_size[1] <> 400) {
				$errors[] = "Main Image must be 400 pixels height";
				}
			if (!file_exists($image_folder.get_thumb1($row["image0"]))) {
				//$errors[] = "Small Thumbnail not found";
				}
			if (!file_exists($image_folder.get_thumb2($row["image0"]))) {
				//$errors[] = "Medium Thumbnail not found";
				}
			}
		// check all floorplans for maximum width (750)
		$notes = $row["notes"];
		$address = $row["house_number"].", ".$row["Address1"].", ".$row["Postcode"];
		}
		if ($errors) {
			echo html_header("Error");
			echo error_message($errors);
			exit;
			}

		// update table and log
		$fieldnames[] = "state_of_trade_id";
		//$fieldnames[] = "Dates";
		$newvalues[] = "12";
		//$newvalues[] = $dateToday;
		queryLog($fieldnames,$newvalues,$_table,$_field,$intPropID,"Update");

		// mail admin
		$EmailSubject  = "Property has been submitted to the proofing list";
		$EmailBody = $address."\nhttp://www.woosterstock.co.uk/admin/property.php?propID=$intPropID\n".$dateFriendly."\nSubmitted by ".$_SESSION["s_name"];

		$EmailHeaders = "Content-Type:text/plain;CHARSET=iso-8859-8-1\r\n";
		$EmailHeaders	.="From:mark@woosterstock.co.uk\r\n";
		$proofer_email = "emma@woosterstock.co.uk,becky@woosterstock.co.uk,mark@woosterstock.co.uk";
		mail($proofer_email, $EmailSubject, $EmailBody, $EmailHeaders);

		header("Location:?propID=$intPropID&type=new&searchLink=".urlencode($searchLink)."&changes=Property%20has%20been%20submitted%20to%20Proofing%20List");



		}

elseif ($_GET["action"] == "activate") {

	// check user permissions
	// allowed users from array in global "proofers"
	if (!in_array($s_userid,$proofers)) {
		echo "You do not have persmission to release this property";
		exit;
		}


	if (!$_GET["propID"]) {
		$errors[] = "Missing Property ID";
		} else {
		$intPropID = $_GET["propID"];
		}

	$sql = "SELECT * FROM property WHERE property.prop_ID = $intPropID LIMIT 1";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("property select error: ".$q->getMessage()); }

	while ($row = $q->fetchRow()) {

		$postcodeLen = strlen($row["Postcode"]);
		$osxLen = strlen($row["osx"]);
		$osyLen = strlen($row["osy"]);

		if (!is_numeric($row["Price"])) {
			$errors[] = "Price must be a number";
			}
		if (!$row["house_number"]) {
			$errors[] = "House Number is required";
			}
		if (!$row["Address1"]) {
			$errors[] = "Street is required";
			}
		if ($postcodeLen < 6) {
			$errors[] = "Full Postcode is required";
			}
		if ($osxLen <> 6) {
			$errors[] = "OSX must be 6 characters";
			}
		if ($osyLen <> 6) {
			$errors[] = "OSY must be 6 characters";
			}
		if (!$row["description"]) {
			$errors[] = "Strap Line is required";
			}
		if (!$row["longDescription"]) {
			$errors[] = "Description is required";
			}
		if (!$row["image0"]) {
			$errors[] = "Main Image is required";
			} else {
			// check presence and dimensions of main (400x400), ftx (146x146), ftxx (56x56)
			$image_size = getimagesize($image_folder.$row["image0"]); // Read the size
			if ($image_size[0] <> 400) {
				$errors[] = "Main Image must be 400 pixels width";
				}
			if ($image_size[1] <> 400) {
				$errors[] = "Main Image must be 400 pixels height";
				}
			if (!file_exists($image_folder.get_thumb1($row["image0"]))) {
				//$errors[] = "Small Thumbnail not found";
				}
			if (!file_exists($image_folder.get_thumb2($row["image0"]))) {
				//$errors[] = "Medium Thumbnail not found";
				}
			}
		// check all floorplans for maximum width (750)
		$notes = $row["notes"];
		}
		if ($errors) {
			echo html_header("Error");
			echo error_message($errors);
			exit;
			}

		// update table and log
		$fieldnames[] = "state_of_trade_id";
		$fieldnames[] = "Dates";
		$newvalues[] = "1";
		$newvalues[] = $dateToday;
		queryLog($fieldnames,$newvalues,$_table,$_field,$intPropID,"Update");

		// mail admin
		/*
		$EmailSubject  = "Property has been activated";
		$EmailBody = "http://www.woosterstock.co.uk/admin/property.php?propID=$intPropID";

		$EmailHeaders = "Content-Type:text/html;CHARSET=iso-8859-8-i\r\n";
		$EmailHeaders	.="From:$admin_email\r\n";
		mail($admin_email, $EmailSubject, $EmailBody, $EmailHeaders);
		*/
		header("Location:?propID=$intPropID&type=new&searchLink=".urlencode($searchLink)."&changes=Property%20has%20been%20Activated");



		}

elseif ($_GET["action"] == "archive") {
	if (!$_GET["propID"]) {
		echo "Missing Property ID";
		exit;
		} else {
		$intPropID = $_GET["propID"];
		}

	// update table and log
	$fieldnames[] = "state_of_trade_id";
	$newvalues[] = "7";
	queryLog($fieldnames,$newvalues,$_table,$_field,$intPropID,"Update");
	header("Location:?propID=$intPropID&type=new&searchLink=".urlencode($searchLink)."&changes=Property%20has%20been%20Archived");
	}


elseif ($_GET["action"] == "save_location") {
	if (!$_GET["propID"]) {
		$errors[] = "ID missing";
		} else {
		$intPropID = $_GET["propID"];
		}
	if (!$_GET["x"]) {
		$errors[] = "OSX missing";
		} else {
		$fieldnames[] = "osx";
		$newvalues[] = trim($_GET["x"]);
		}
	if (!$_GET["y"]) {
		$errors[] = "OSY missing";
		} else {
		$fieldnames[] = "osy";
		$newvalues[] = trim($_GET["y"]);
		}

	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}

	queryLog($fieldnames,$newvalues,$_table,$_field,$intPropID,"Update");
	header("Location:?propID=$intPropID&view=$view&searchLink=".urlencode($searchLink)."&changes=Location%20Saved");
	}





elseif ($_POST["action"] == "Update") {
	// validation - POST names do not have to be the same as field names, but it helps
	// perform all validation on post values, and add them to the 2 arrays
	if (!$_POST["propID"]) {
		$errors[] = "ID missing";
		} else {
		$intPropID = $_POST["propID"];
		}
	if (!$_POST["Branch"]) {
		//$errors[] = "Branch is a required field";
		} else {
		$fieldnames[] = "Branch";
		$newvalues[] = trim($_POST["Branch"]);
		}
	if (!$_POST["Neg"]) {
		//$errors[] = "Negotiator is a required field";
		} else {
		$fieldnames[] = "Neg";
		$newvalues[] = trim($_POST["Neg"]);
		}
	if (!$_POST["Address1"]) {
		//$errors[] = "Street Name is a required field";
		} else {
		$fieldnames[] = "Address1";
		$newvalues[] = trim(format_street($_POST["Address1"]));
		}
	if (!$_POST["house_number"]) {
		//$errors[] = "House Number is a required field";
		} else {
		$fieldnames[] = "house_number";
		$newvalues[] = trim($_POST["house_number"]);
		}
	if (!$_POST["Postcode"]) {
		//$errors[] = "Postcode is a required field";
		} else {
		$fieldnames[] = "Postcode";
		$newvalues[] = trim(format_postcode($_POST["Postcode"]));
		}
	if (!$_POST["osx"]) {
		$osx = 0;
		} else {
		$fieldnames[] = "osx";
		$newvalues[] = trim($_POST["osx"]);
		}
	if (!$_POST["osy"]) {
		$osy = 0;
		} else {
		$fieldnames[] = "osy";
		$newvalues[] = trim($_POST["osy"]);
		}
	if (!$_POST["Price"]) {
		$price = 0;
		} else {
		$fieldnames[] = "Price";
		$newvalues[] = trim($_POST["Price"]);
		}
	if (!$_POST["PriceType"]) {
		//
		} else {
		$fieldnames[] = "PriceType";
		$newvalues[] = trim($_POST["PriceType"]);
		}
	if (!$_POST["Price2"]) {
		//
		} else {
		$fieldnames[] = "Price2";
		$newvalues[] = trim($_POST["Price2"]);
		}
	if (!$_POST["PriceType2"]) {
		//
		} else {
		$fieldnames[] = "PriceType2";
		$newvalues[] = trim($_POST["PriceType2"]);
		}
	if (!$_POST["lease_free"]) {
		//
		} else {
		$fieldnames[] = "lease_free";
		$newvalues[] = trim($_POST["lease_free"]);
		}
	if (!$_POST["state_of_trade_id"]) {
		//
		} else {
		$fieldnames[] = "state_of_trade_id";
		$newvalues[] = trim($_POST["state_of_trade_id"]);
		}
	if (!$_POST["type_id"]) {
		//
		} else {
		$fieldnames[] = "type_id";
		$newvalues[] = trim($_POST["type_id"]);
		}
	if (!$_POST["area_id"]) {
		//
		} else {
		$fieldnames[] = "area_id";
		$newvalues[] = trim($_POST["area_id"]);
		}
	if (!$_POST["longDescription"]) {
		//
		} else {
		$fieldnames[] = "longDescription";
		$newvalues[] = trim(format_description($_POST["longDescription"]));
		}
	if (!$_POST["description"]) {
		//
		} else {
		$fieldnames[] = "description";
		$newvalues[] = trim(format_strap($_POST["description"]));
		}
	if (!$_POST["gch"]) {
		//
		} else {
		$fieldnames[] = "gch";
		$newvalues[] = trim($_POST["gch"]);
		}
	if (!$_POST["doubleGlazed"]) {
		//
		} else {
		$fieldnames[] = "doubleGlazed";
		$newvalues[] = trim($_POST["doubleGlazed"]);
		}
	if (!$_POST["receptions"]) {
		//
		} else {
		$fieldnames[] = "receptions";
		$newvalues[] = trim($_POST["receptions"]);
		}
	if ($_POST["bedrooms"] == "") {
		//
		} else {
		$fieldnames[] = "bedrooms";
		$newvalues[] = trim($_POST["bedrooms"]);
		}
	if (!$_POST["bathrooms"]) {
		//
		} else {
		$fieldnames[] = "bathrooms";
		$newvalues[] = trim($_POST["bathrooms"]);
		}
	if (!$_POST["garden"]) {
		//
		} else {
		$fieldnames[] = "garden";
		$newvalues[] = trim($_POST["garden"]);
		}
	if (!$_POST["parking"]) {
		//
		} else {
		$fieldnames[] = "parking";
		$newvalues[] = trim($_POST["parking"]);
		}
	// add to notes
	if ($_POST["notes"] || $_POST["newnote"]) {
		$newnote = $_POST["notes"];
		if ($_POST["newnote"]) {
			$newnote = $_POST["newnote"]." (".$dateFriendly." ".$_SESSION["s_name"].")\n".$newnote;
			}
		$fieldnames[] = "notes";
		$newvalues[] = $newnote;
		}
	if (!$_POST["total_area"]) {
		//
		} else {
		$fieldnames[] = "total_area";
		$newvalues[] = trim($_POST["total_area"]);
		}


// images
$mode = "uniq"; //$dateFile.".jpg";
$upload = new HTTP_Upload();
$files = $upload->getFiles(); // returns an array of file objects or error

$prepend = $intPropID.'_';
$i = 0;
foreach ($files as $key => $value) {
	if (!$value->isMissing()) { // if there is a file
		if ($value->isValid()) { // if the file is valid
			$value->setName ($mode, $prepend, $append); // naming
			$properties = $value->getProp(); // get properties array
			$file_name = $value->moveTo($uploadPath); //move to upload folder

			// get the image number from form field
			$image_number = $key;

			$image_number = str_replace("image[","",$image_number);
			$image_number = str_replace("]","",$image_number);

			$fieldnames[] = "image$image_number";
			//print_r($fieldnames);
			$newvalues[] = $file_name;
			//print_r($newvalues);

			if ($image_number == 0) { // file is main image, check 400x400
				$image_size = getimagesize($uploadPath."/".$file_name); // Read the size
				if ($image_size['mime'] <> "image/jpeg") {
					$errors[] = "Image".$image_number." must be a JPG";
					}
        	 	if ($image_size[0] <> $image_main_w) {
					$errors[] = "Image".$image_number." must be ".$image_main_w." pixels width.";
					}
        	 	if ($image_size[1] <> $image_main_h) {
					$errors[] = "Image".$image_number." must be ".$image_main_h." pixels height";
					}
				// resize and copy main image to ft and ftxx
				$thumb1 = $uploadPath."/".make_thumb1($file_name);
				$phpThumb1 = new phpThumb();
				$phpThumb1->src = $uploadPath."/".$file_name;

				$phpThumb1->w = $image_thumb1_w;
				$phpThumb1->h = $image_thumb1_h;
				$phpThumb1->config_output_format = 'jpeg';
				$phpThumb1->config_error_die_on_error = true;

				if ($phpThumb1->GenerateThumbnail())
					{
					$phpThumb1->RenderToFile($thumb1);
					}
				else
					{
					$errors[] = "Image creation Failed (".$file_name."): ". $phpThumb1->error;
					}

				$thumb2 = $uploadPath."/".make_thumb2($file_name);
				$phpThumb2 = new phpThumb();
				$phpThumb2->src = $uploadPath."/".$file_name;

				$phpThumb2->w = $image_thumb2_w;
				$phpThumb2->h = $image_thumb2_h;
				$phpThumb2->config_output_format = 'jpeg';
				$phpThumb2->config_error_die_on_error = true;

				if ($phpThumb2->GenerateThumbnail())
					{
					$phpThumb2->RenderToFile($thumb2);
					}
				else
					{
					$errors[] = "Image creation Failed (".$file_name."): ". $phpThumb2->error;
					}

				}
			elseif ($image_number <= 10) { // check all other files 200x200
				$image_size = getimagesize($uploadPath."/".$file_name); // Read the size
				if ($image_size['mime'] <> "image/jpeg") {
					$errors[] = "Image".$image_number." must be a JPG";
					}
        	 	if ($image_size[0] <> $image_internal_w) {
					$errors[] = "Image".$image_number." must be ".$image_internal_w." pixels width";
					}
        	 	if ($image_size[1] <> $image_internal_h) {
					$errors[] = "Image".$image_number." must be ".$image_internal_h." pixels height";
					}
				}
			elseif ($image_number > 10) { // check floorplans are no wider than 750
				$image_size = getimagesize($uploadPath."/".$file_name); // Read the size
				if ($image_size['mime'] <> "image/gif") {
					$errors[] = "Image".$image_number." must be a GIF";
					}
        	 	if ($image_size[0] > $floorplan_max_width) {
					$errors[] = "Image".$image_number." must be less than ".$floorplan_max_width." pixels width";
					}
				}
			$img_sql .= "image".$image_number." = '".$file_name."', "; // construct sql
			}
		else // if the file is invalid
			{
			$errors[] = "File $i is invalid\n";
			}
		$i++;
		}
	}


	// end images








	if (!$_POST["banner"]) {
		//
		} else {
		$fieldnames[] = "banner";
		$newvalues[] = trim($_POST["banner"]);
		}
	if (!$_POST["SaleLet"]) {
		//
		} else {
		$fieldnames[] = "SaleLet";
		$newvalues[] = trim($_POST["SaleLet"]);
		}
	if (!$_POST["furnished"]) {
		//
		} else {
		$fieldnames[] = "furnished";
		$newvalues[] = trim($_POST["furnished"]);
		}
	if (!$_POST["managed"]) {
		//
		} else {
		$fieldnames[] = "managed";
		$newvalues[] = trim($_POST["managed"]);
		}
	if (!$_POST["lease_length"]) {
		//
		} else {
		$fieldnames[] = "lease_length";
		$newvalues[] = trim($_POST["lease_length"]);
		}
	if (!$_POST["ground_rent"]) {
		//
		} else {
		$fieldnames[] = "ground_rent";
		$newvalues[] = trim($_POST["ground_rent"]);
		}
	if (!$_POST["service_charge"]) {
		//
		} else {
		$fieldnames[] = "service_charge";
		$newvalues[] = trim($_POST["service_charge"]);
		}
	if (!$_POST["other_details"]) {
		//
		} else {
		$fieldnames[] = "other_details";
		$newvalues[] = trim($_POST["other_details"]);
		}
	if (!$_POST["card"]) {
		//
		} else {
		$fieldnames[] = "card";
		$newvalues[] = trim($_POST["card"]);
		}
	if (!$_POST["suffix"]) {
		//
		} else {
		$fieldnames[] = "suffix";
		$newvalues[] = trim($_POST["suffix"]);
		}

	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}

	queryLog($fieldnames,$newvalues,$_table,$_field,$intPropID,$_POST["action"]);
	header("Location:?propID=$intPropID&view=$view&searchLink=".urlencode($searchLink)."&changes=Update%20Successful");
	}



//insert


elseif ($_POST["action"] == "Insert") {

	if (!$_POST["Branch"]) {
		$errors[] = "Branch is a required field";
		}
	else {
		$Branch = trim($_POST["Branch"]);
		}

	if (!$_POST["Neg"]) {
		$errors[] = "Negotiator is a required field";
		}
	else {
		$Neg = trim($_POST["Neg"]);
		}

	if (!$_POST["Address1"]) {
		$errors[] = "Street Name is a required field";
		}
	else {
		$Address1 = trim(format_street($_POST["Address1"]));
		}

	if (!$_POST["Postcode"]) {
		$errors[] = "Postcode is a required field";
		}
	else {
		$Postcode = trim(format_postcode($_POST["Postcode"]));
		}

	if (!$_POST["house_number"]) {
		$errors[] = "House Number is a required field";
		}
	else {
		$house_number = trim($_POST["house_number"]);
		}

	$Price = trim($_POST["Price"]);
	$PriceType = trim($_POST["PriceType"]);
	$Price2 = trim($_POST["Price2"]);
	$PriceType2 = trim($_POST["PriceType2"]);

	$lease_free = $_POST["lease_free"];
	$state_of_trade_id = $_POST["state_of_trade_id"];

	if (!$_POST["type_id"]) {
		$errors[] = "Property Type is a required field";
		}
	else {
		$type_id = trim($_POST["type_id"]);
		}
	if (!$_POST["area_id"]) {
		$errors[] = "Area is a required field";
		}
	else {
		$area_id = trim($_POST["area_id"]);
		}


	if (!$_POST["description"]) { // create temporary strap line
		if ($_POST["bedrooms"]) {
			$description = $_POST["bedrooms"].' Bed ';
			}
		if ($type_id) {
			$sqlD = "SELECT * FROM proptype WHERE type_ID = $type_id";
			$qD = $db->query($sqlD);
			while ($rowD = $qD->fetchRow()) {
				$description .= $rowD["type_Title"];
				}
			}
		}
	else {
		$description = trim(format_strap($_POST["description"]));
		}

	if (!$_POST["longDescription"]) {
		$longDescription = '<p>Full details to follow</p>';
		}
	else {
		$longDescription = trim($_POST["longDescription"]);
		}

	$gch 		= $_POST["gch"];
	$doubleGlazed = $_POST["doubleGlazed"];
	$receptions = $_POST["receptions"];
	$bedrooms 	= $_POST["bedrooms"];
	$bathrooms 	= $_POST["bathrooms"];
	$measure 	= $_POST["measure"];
	$total_area = $_POST["total_area"];
	$image0 	= $_POST["image0"];
	$image1 	= $_POST["image1"];
	$image2 	= $_POST["image2"];
	$image3 	= $_POST["image3"];
	$image4 	= $_POST["image4"];
	$image5 	= $_POST["image5"];
	$image6 	= $_POST["image6"];
	$image7 	= $_POST["image7"];
	$image8 	= $_POST["image8"];
	$image9 	= $_POST["image9"];
	$image10 	= $_POST["image10"];
	$image11 	= $_POST["image11"];
	$image12 	= $_POST["image12"];
	$image13 	= $_POST["image13"];
	$image14 	= $_POST["image14"];
	$image15 	= $_POST["image15"];
	$banner 	= $_POST["banner"];
	$BannerLink = $_POST["BannerLink"];
	$ThumbNail 	= $_POST["ThumbNail"];
	$Countdown 	= $_POST["Countdown"];
	$Dates 		= $_POST["Dates"];
	$managed 	= $_POST["managed"];
	$furnished 	= $_POST["furnished"];
	$lease_length		= $_POST["lease_length"];
	$ground_rent		= htmlspecialchars($_POST["ground_rent"]);
	$ground_rent = str_replace("�","&pound;",$ground_rent);
	$service_charge		= htmlspecialchars($_POST["service_charge"]);
	$service_charge = str_replace("�","&pound;",$service_charge);
	$other_details = htmlspecialchars($_POST["other_details"]);
	$notes = "Record Created (".$dateFriendly." ".$_SESSION["s_name"].")";
	if (!$_POST["SaleLet"]) {
		$errors[] = "Sales or Lettings missing";
		}
	else {
		$SaleLet = $_POST["SaleLet"];
		}
	$card = $_POST["card"];
	$suffix = $_POST["suffix"];

	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}

	$sql = "
	INSERT INTO property
	(Price,Pricetype,Price2,PriceType2,house_number,Address1,Postcode,osx,osy,lease_free,state_of_trade_id,type_id,
	area_id,longDescription,description,gch,doubleGlazed,receptions,bedrooms,bathrooms,garden,parking,
	notes,total_area,image0,image1,image2,image3,image4,image5,image6,image7,image8,
	image9,image10,image11,image12,image13,image14,image15,banner,BannerLink,ThumbNail,
	Countdown,Dates,Hits,Neg,Branch,SaleLet,managed,furnished,lease_length,service_charge,ground_rent,other_details,card, suffix)
	VALUES
	('$Price','$PriceType','$Price2','$PriceType2','$house_number','$Address1','$Postcode','$osx','$osy','$lease_free','$state_of_trade_id','$type_id',
	'$area_id','$longDescription','$description','$gch','$doubleGlazed','$receptions','$bedrooms','$bathrooms','$garden','$parking',
	'$notes','$total_area','$image0','$image1','$image2','$image3','$image4','$image5','$image6','$image7','$image8',
	'$image9','$image10','$image11','$image12','$image13','$image14','$image15','$banner','$BannerLink','$ThumbNail',
	'$Countdown','$dateToday','$Hits','$Neg','$Branch','$SaleLet','$managed','$furnished','$lease_length','$service_charge','$ground_rent','$other_details','$card','$suffix')";
	$q = $db->query($sql);
	//echo $sql;
	if (DB::isError($q)) {  die("insert error: ".$q->getMessage()); }

	$query = 'SELECT LAST_INSERT_ID()';
	$result = mysql_query($query);
	$rec = mysql_fetch_array($result);
	$insert_id = $rec[0];
	$intPropID = $insert_id;

	//$sql_body = "Dates = 'Record Created'";

	//change_log($_SESSION["s_userid"],"property","prop_ID",$intPropID,$sql_body,$PHPSESSID);

	// images
$mode = $dateFile.".jpg";
$upload = new HTTP_Upload();
$files = $upload->getFiles(); // returns an array of file objects or error
//print_r($files);
$prepend = $intPropID.'_';
$i = 0;
foreach ($files as $key => $value) {
	if (!$value->isMissing()) { // if there is a file
		if ($value->isValid()) { // if the file is valid
			$value->setName ($mode, $prepend, $append); // naming
			$properties = $value->getProp(); // get properties array
			$file_name = $value->moveTo($uploadPath); //move to upload folder

			// get the image number from form field
			$image_number = $key;
			$image_number = str_replace("image[","",$image_number);
			$image_number = str_replace("]","",$image_number);

			if ($image_number == 0) { // file is main image, check 400x400
				$image_size = getimagesize($uploadPath."/".$file_name); // Read the size
        	 	if ($image_size[0] <> 400) {
					$errors[] = "Image".$image_number." must be 400 pixels width";
					}
        	 	if ($image_size[1] <> 400) {
					$errors[] = "Image".$image_number." must be 400 pixels height";
					}

				// resize and copy main image to ft and ftxx
				$thumb1 = $uploadPath."/".make_thumb1($file_name);
				$phpThumb1 = new phpThumb();
				$phpThumb1->src = $uploadPath."/".$file_name;

				$phpThumb1->w = 146;
				$phpThumb1->h = 146;
				$phpThumb1->config_output_format = 'jpeg';
				$phpThumb1->config_error_die_on_error = true;

				if ($phpThumb1->GenerateThumbnail()) {
					$phpThumb1->RenderToFile($thumb1);
					}
				else {
					$errors[] = "Image creation Failed (".$file_name."): ". $phpThumb1->error;
					}

				$thumb2 = $uploadPath."/".make_thumb2($file_name);
				$phpThumb2 = new phpThumb();
				$phpThumb2->src = $uploadPath."/".$file_name;

				$phpThumb2->w = 56;
				$phpThumb2->h = 56;
				$phpThumb2->config_output_format = 'jpeg';
				$phpThumb2->config_error_die_on_error = true;

				if ($phpThumb2->GenerateThumbnail()) {
					$phpThumb2->RenderToFile($thumb2);
					}
				else {
					$errors[] = "Image creation Failed (".$file_name."): ". $phpThumb2->error;
					}
				}
			elseif ($image_number <= 10) { // check all other files 200x200
				$image_size = getimagesize($uploadPath."/".$file_name); // Read the size
        	 	if ($image_size[0] <> 200) {
					$errors[] = "Image".$image_number." must be 200 pixels width";
					}
        	 	if ($image_size[1] <> 200) {
					$errors[] = "Image".$image_number." must be 200 pixels height";
					}
				}
			elseif ($image_number > 10) { // check floorplans are no wider than 750
				$image_size = getimagesize($uploadPath."/".$file_name); // Read the size
        	 	if ($image_size[0] > 750) {
					$errors[] = "Image".$image_number." must be less than 750 pixels width";
					}
				}
			$img_sql .= "image".$image_number." = '".$file_name."', "; // construct sql
			}
		else // if the file is invalid
			{
			$errors[] = "File $i is invalid\n";
			}
		$i++;
		}
	}

	if ($errors) {
		echo html_header("Error");
		echo error_message($errors);
		exit;
		}
	else {
		if ($img_sql) { // only run insert is there are images
			if (substr($img_sql,-2) == ", ") { // remove the last ", "
				$img_sql = substr($img_sql,0,-2);
				}

			$sql = "UPDATE property SET
			".$img_sql."
			WHERE prop_ID = $intPropID";
			$q = $db->query($sql);
			//echo $sql;
			if (DB::isError($q)) {  die("insert error: ".$q->getMessage()); }
			}
		}
	// end images

	// email negotiator
	$sqlNeg = "SELECT * FROM staff WHERE Staff_ID = '$Neg'";
	$qNeg = $db->query($sqlNeg);
	if (DB::isError($qNeg)) {  die("insert error: ".$qNeg->getMessage()); }
	while ($rowNeg = $qNeg->fetchRow()) {
		$strNegName .= $rowNeg["Staff_Fname"];
		$strNegEmail .= $rowNeg["Staff_Email"];
		}

	$EmailSubject  = $strNegName." - A property has been assigned to you";
	$EmailBody = "You have been assigned a new property at the following address:\n\n";
	$EmailBody .= $house_number." ".$Address1." ".$Postcode."\n\n";
	$EmailBody .= "http://www.woosterstock.co.uk/admin/property.php?propID=$intPropID\n\n".$dateFriendly."\n\nSubmitted by ".$_SESSION["s_name"];

	$EmailHeaders = "Content-Type:text/plain;CHARSET=iso-8859-8-1\r\n";
	$EmailHeaders	.="From:mark@woosterstock.co.uk\r\n";
	mail($strNegEmail, $EmailSubject, $EmailBody, $EmailHeaders);


	$pageTitle = "Add Property Complete";
	echo html_header($pageTitle);
	echo '
<table width="600" align="center">
  <tr>
	<td><span class="pageTitle">'.$pageTitle.'</span></td>
	<td align="right"><a href="index.php">Main Menu</a></td>
  </tr>
  <tr>
    <td colspan="2">
	  <p>&nbsp;</p>
	  <p><a href="property.php?propID='.$insert_id.'">Edit the property</p>
	  <p><a href="property.php?propID='.$intPropID.'&action=print">Print the property</a></p>
	  <p><a href="property.php?SaleLet='.$SaleLet.'">Add another property</a></p>';
	  if ($_GET["searchLink"]) {
	  echo '<p><a href="'.urldecode($_GET["searchLink"]).'">Back to last search</a></p>';
	  }
	  echo '
	</td>
  </tr>
</table>
';
	}


elseif (!$_POST["action"]) { // if form is not submitted

	if (!$_GET["propID"]) { // if id not entered, show insert form
		if (!$_GET["SaleLet"]) {
			echo html_header("Sales or Lettings?").'
			<p><a href="?SaleLet=1">Sales</a></p>
			<p><a href="?SaleLet=2">Lettings</a></p>
			';
			exit;
			}
		$action = "Insert";
		$intStatus = 11; // set status to pending
		} else {
		$intPropID = $_GET["propID"];
		$action = "Update";

		$sql = "SELECT * FROM property WHERE property.prop_ID = $intPropID LIMIT 1";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("property select error: ".$q->getMessage()); }

		while ($row = $q->fetchRow()) {
			$intPrice 		= $row["Price"];
			$strPrice 		= price_format($intPrice);
			$intPriceType	= $row["PriceType"];
			$intPrice2 		= $row["Price2"];
			$intPriceType2	= $row["PriceType2"];
			$strNumber 		= $row["house_number"];
			$strStreet 		= format_street($row["Address1"]);
			$strPostcode 	= $row["Postcode"];
			$strPC1 = explode(" ",$strPostcode);
			$strPC1 = $strPC1[0];
			$intOSX 		= $row["osx"];
			$intOSY 		= $row["osy"];
			$intTenure 		= $row["lease_free"];
			$intStatus 		= $row["state_of_trade_id"];
			$intType 		= $row["type_id"];

			if ($intType == "1"||$intType == "2"||$intType == "5"||$intType == "7") {
				$strLinkType = "House";
				}
			elseif ($intType == "3"||$intType == "4"||$intType == "6") {
				$strLinkType = "Apartment";
				}
			elseif ($intType == "8") {
				$strLinkType = "Commercial";
				}
			elseif ($intType == "9") {
				$strLinkType = "Live/Work";
				}

			$intArea 		= $row["area_id"];
			$strLongDescription = format_description($row["longDescription"]);
			$strDescription = format_strap($row["description"]);
			$strGCH 		= $row["gch"];
			$strDG 			= $row["doubleGlazed"];
			$intReceptions 	= $row["receptions"];
			$intBedrooms 	= $row["bedrooms"];
			$intBathrooms 	= $row["bathrooms"];
			$intGarden 		= $row["garden"];
			$intParking 	= $row["parking"];
			$strNotes	 	= $row["notes"];
			$strTotalArea 	= $row["total_area"];
			$strImage0 		= $row["image0"];
			$strImage1 		= $row["image1"];
			$strImage2 		= $row["image2"];
			$strImage3 		= $row["image3"];
			$strImage4 		= $row["image4"];
			$strImage5 		= $row["image5"];
			$strImage6 		= $row["image6"];
			$strImage7 		= $row["image7"];
			$strImage8 		= $row["image8"];
			$strImage9 		= $row["image9"];
			$strImage10 	= $row["image10"];
			$strImage11 	= $row["image11"];
			$strImage12 	= $row["image12"];
			$strImage13 	= $row["image13"];
			$strImage14 	= $row["image14"];
			$strImage15 	= $row["image15"];
			$strBanner 		= $row["banner"];
			$strBannerLink 	= $row["BannerLink"];
			$strThumbNail 	= $row["ThumbNail"];
			$strCountdown 	= $row["Countdown"];
			$strDates 		= $row["Dates"];
			$strHits 		= $row["Hits"];
			$intNeg 		= $row["Neg"];
			$intBranch 		= $row["Branch"];
			$intSaleLet		= $row["SaleLet"];
			$strFurnished	= $row["furnished"];
			$strManaged		= $row["managed"];
			$SaleLet		= $row["SaleLet"];

			$strLeaseLength		= $row["lease_length"];
			$strGroundRent		= $row["ground_rent"];
			if (!$strGroundRent) { $strGroundRent = "TBC"; }
			$strServiceCharge		= $row["service_charge"];
			if (!$strServiceCharge) { $strServiceCharge = "TBC"; }
			$strOtherDetails = $row["other_details"];

			$intCard		= $row["card"];
			$intSuffix		= $row["suffix"];
			}
		}





	$sqlNeg = "SELECT * FROM staff WHERE Staff_Type = 'SalesNegotiator' OR Staff_Type = 'LettingsNegotiator' AND Staff_Status = 'Current' ORDER BY Staff_Fname";
	$qNeg = $db->query($sqlNeg);
	if (DB::isError($qNeg)) {  die("insert error: ".$qNeg->getMessage()); }
	if (!$intNeg) {
		$strRenderNeg .= '<option value=""> -- select -- </option>';
		}
	while ($rowNeg = $qNeg->fetchRow()) {
		$strRenderNeg .= '<option value="'.$rowNeg["Staff_ID"].'"';
		if ($intNeg == $rowNeg["Staff_ID"]) {
			$strRenderNeg .= ' selected';
			}
		$strRenderNeg .= '>'.$rowNeg["Staff_Fname"].' '.$rowNeg["Staff_Sname"].'</option>';
		}


	$sqlBranch = "SELECT * FROM branch ORDER BY Branch_Title";
	$qBranch = $db->query($sqlBranch);
	if (DB::isError($qBranch)) {  die("insert error: ".$qBranch->getMessage()); }
	if (!$intBranch) {
		$strRenderBranch .= '<option value=""> -- select -- </option>';
		}
	while ($rowBranch = $qBranch->fetchRow()) {
		$strRenderBranch .= '<option value="'.$rowBranch["Branch_ID"].'"';
		if ($intBranch == $rowBranch["Branch_ID"]) {
			$strRenderBranch .= ' selected';
			$strBranchTitle = $rowBranch["Branch_Title"];
			$strBranchTel = $rowBranch["Branch_Tel"];
			}
		$strRenderBranch .= '>'.$rowBranch["Branch_Title"].'</option>';
		}



	$sqlPropType = "SELECT type_ID, type_Title FROM proptype";
	$qPropType = $db->query($sqlPropType);
	if (DB::isError($qPropType)) {  die("insert error: ".$qPropType->getMessage()); }
	if (!$intType) {
		$strRenderPropType .= '<option value=""> -- select -- </option>';
		}
	while ($rowPropType = $qPropType->fetchRow()) {
		$strRenderPropType .= '<option value="'.$rowPropType["type_ID"].'"';
		if ($intType == $rowPropType["type_ID"]) {
			$strRenderPropType .= ' selected';
			}
		$strRenderPropType .= '>'.$rowPropType["type_Title"].'</option>';
		}


	$sqlArea = "SELECT * FROM area ORDER BY area_title";
	$qArea = $db->query($sqlArea);
	if (DB::isError($qArea)) {  die("insert error: ".$qArea->getMessage()); }
	//if (!$intArea) {
		$strRenderArea .= '<option value=""> -- select -- </option>';
	//	}
	while ($rowArea = $qArea->fetchRow()) {
		$strAreaArrayX[] = $rowArea["area_osx"];
		$strAreaArrayY[] = $rowArea["area_osy"];
		$strAreaArrayPC[] = $rowArea["area_pc"];

		$strRenderArea .= '<option value="'.$rowArea["area_ID"].'"';
		if ($intArea == $rowArea["area_ID"]) {
			$strArea = $rowArea["area_title"];
			$strRenderArea .= ' selected';
			}
		$strRenderArea .= '>'.$rowArea["area_title"].'</option>';
		}

	if ($SaleLet == 1) {
		$sqlStatus = "SELECT state_ID, state_Title FROM state_of_trade WHERE state_ID != 12";
		}
	else {
		$sqlStatus = "SELECT state_ID, state_Title FROM state_of_trade_let WHERE state_ID != 12";
		}
	$qStatus = $db->query($sqlStatus);
	if (DB::isError($qStatus)) {  die("insert error: ".$qStatus->getMessage()); }

	while ($rowStatus = $qStatus->fetchRow()) {
		$strRenderStatus .= '<option value="'.$rowStatus["state_ID"].'"';
		if ($intStatus == $rowStatus["state_ID"]) {
			$strRenderStatus .= ' selected';
			}
		$strRenderStatus .= '>'.$rowStatus["state_Title"].'</option>';
		}


	$sqlTenure = "SELECT id_LeaseFree, leaseFree_Name FROM leasefree";
	$qTenure = $db->query($sqlTenure);
	if (DB::isError($qTenure)) {  die("insert error: ".$qTenure->getMessage()); }
	if (!$intTenure) {
		//$strRenderTenure .= '<option value=""> -- select -- </option>';
		}
		while ($rowTenure = $qTenure->fetchRow()) {
		$strRenderTenure .= '<option value="'.$rowTenure["id_LeaseFree"].'"';
		if ($intTenure == $rowTenure["id_LeaseFree"]) {
			$strRenderTenure .= ' selected';
			$strTenureTitle = $rowTenure["leaseFree_Name"];
			}
		$strRenderTenure .= '>'.$rowTenure["leaseFree_Name"].'</option>';
		}

	$sqlFurnished = "SELECT * FROM furnished";
	$qFurnished = $db->query($sqlFurnished);
	if (DB::isError($qFurnished)) {  die("insert error: ".$qFurnished->getMessage()); }

	while ($rowFurnished = $qFurnished->fetchRow()) {
		$strRenderFurnished .= '<option value="'.$rowFurnished["Furnished_ID"].'"';
		if ($strFurnished == $rowFurnished["Furnished_ID"]) {
			$strRenderFurnished .= ' selected';
			}
		$strRenderFurnished .= '>'.$rowFurnished["Furnished_Title"].'</option>';
		}

	if ($intBranch == 1) {
		$strCardFolder = "CardsShip";
		} elseif ($intBranch == 2) {
		$strCardFolder = "CardsSyd";
		} elseif ($intBranch == 3) {
		$strCardFolder = "CardsShad";
		}

	if ($intCard == "Proofed") {
		$renderCard = ' - <a href="P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf" target="_blank">PDF</a>';
		}


if ($action == "Insert") {
	$pageTitle = "Insert New Property";
	} else {
	$pageTitle = "Editing: ".$strStreet;
	}
echo html_header($pageTitle);
?>
<script language="JavaScript" type="text/JavaScript">
function populateCoords() {
var osx = new Array()
<?php
$count = count($strAreaArrayX);
for($counter=0; $counter < $count; $counter++)
{
  echo 'osx['.$counter.'] = "'.$strAreaArrayX[$counter].'"
  ';
}
?>

var osy = new Array()
<?php
$count = count($strAreaArrayY);
for($counter=0; $counter < $count; $counter++)
{
  echo 'osy['.$counter.'] = "'.$strAreaArrayY[$counter].'"
  ';
}
?>

var pc = new Array()
<?php
$count = count($strAreaArrayPC);
for($counter=0; $counter < $count; $counter++)
{
  echo 'pc['.$counter.'] = "'.$strAreaArrayPC[$counter].'"
  ';
}
?>
var optionNumber = document.form.area_id.selectedIndex

if (document.form.osx.value == "") {
	document.form.osx.value = osx[(optionNumber-1)]
	}
if (document.form.osy.value == "") {
	document.form.osy.value = osy[(optionNumber-1)]
	}
if (document.form.Postcode.value == "") {
	document.form.Postcode.value = pc[(optionNumber-1)]
	}
}
</script>

<form method="post" enctype="multipart/form-data" name="form">
  <input type="hidden" name="propID" value="<?php echo $intPropID; ?>">
  <input type="hidden" name="SaleLet" value="<?php echo $SaleLet; ?>">
  <input type="hidden" name="action" value="<?php echo $action; ?>">
  <input type="hidden" name="searchLink" value="<?php echo $searchLink; ?>">
	<table width="600" align="center">
	  <tr>
		<td><span class="pageTitle"><?php echo $pageTitle; ?></span></td>
		<td align="right"><?php if ($searchLink) { echo '<a href="'.urldecode($searchLink).'">Last Search</a> &nbsp; &nbsp; '; } ?><a href="index.php">Main Menu</a></td>
	  </tr>
	</table>
	<?php if ($action <> "Insert") { ?>
	<table width="600" align="center" cellpadding="4" cellspacing="3">
	  <tr>
	    <td class="anchorNav">
		<a href="?propID=<?php echo $intPropID; ?>&view=details&searchLink=<?php echo urlencode($searchLink); ?>">Details</a> ::
		<a href="?propID=<?php echo $intPropID; ?>&view=descriptions&searchLink=<?php echo urlencode($searchLink); ?>">Descriptions</a> ::
		<a href="?propID=<?php echo $intPropID; ?>&view=images&searchLink=<?php echo urlencode($searchLink); ?>">Images</a> ::
		<a href="?propID=<?php echo $intPropID; ?>&view=floorplans&searchLink=<?php echo urlencode($searchLink); ?>">Floorplans</a> ::
		<a href="?propID=<?php echo $intPropID; ?>&view=map&searchLink=<?php echo urlencode($searchLink); ?>">Map</a> ::
		<a href="?propID=<?php echo $intPropID; ?>&view=notes&searchLink=<?php echo urlencode($searchLink); ?>">Notes</a> ::
		<a href="?propID=<?php echo $intPropID; ?>&view=email&searchLink=<?php echo urlencode($searchLink); ?>">Email</a> ::
		<a href="?propID=<?php echo $intPropID; ?>&view=log&searchLink=<?php echo urlencode($searchLink); ?>">Log</a></td>
		<td align="right" class="anchorNav"><a href="print.php?propID=<?php echo $intPropID; ?>">Print</a> <?php echo $renderCard; ?></td>
      </tr>
	<?php
	if ($_GET["changes"]) {
	?>
	  <tr>
	    <td colspan="2" class="changes"><?php echo $_GET["changes"];?></td>
      </tr>
	<?php
	  }
	?>
	</table>
	<?php } ?>
	<?php
	if ($view == "details") {
	?>

  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="6" class="greyForm">Property Details
        <input type="hidden" name="osx"> <input type="hidden" name="osy"></th>
    </tr>
    <tr>
      <td align="right" class="greyForm">Branch</td>
      <td align="left" class="greyForm"><select name="Branch" id="Branch" style="width: 190px;">
          <?php echo $strRenderBranch; ?> </select></td>
      <td align="right" class="greyForm">Negotiator</td>
      <td colspan="3" class="greyForm"><select name="Neg" style="width: 190px;">
          <?php echo $strRenderNeg; ?> </select></td>
    </tr>
    <?php if ($SaleLet == 1) { ?>
    <tr>
      <td align="right" class="greyForm">Price</td>
      <td width="200" align="left" class="greyForm"><input name="Price" type="text" style="width: 80px;" onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;" value="<?php echo $intPrice; ?>" size="8" maxlength="8">
	  <select name="suffix" style="width: 100px;">
          <?php echo db_enum("property","suffix",$intSuffix); ?> </select></td>
      <td align="right" class="greyForm">Property Type</td>
      <td width="200" colspan="3" class="greyForm"><select name="type_id" style="width: 190px;">
          <?php echo $strRenderPropType; ?> </select></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Number</td>
      <td class="greyForm"><input type="text" name="house_number" value="<?php echo $strNumber; ?>" style="width: 190px;">
      </td>
      <td align="right" nowrap class="greyForm">Market State</td>
      <td colspan="3" class="greyForm">
        <?php if ($intStatus == 11) { ?>
        <input type="hidden" name="state_of_trade_id" value="11">
        Pending &nbsp; <a href="?action=<?php if (in_array($s_userid,$proofers)) { ?>activate<?php } else { ?>proof<?php } ?>&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>ACTIVATE</strong></a>
        / <a href="?action=archive&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>ARCHIVE</strong></a>
        <?php } elseif ($intStatus == 12)  {  ?>
        <input type="hidden" name="state_of_trade_id" value="12"> <strong>Submitted
        to Proofing List</strong>
        <?php if (in_array($s_userid,$proofers)) { ?>
        <a href="?action=activate&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>RELEASE</strong></a>
        <?php } ?>
        <?php } else  {  ?>
        <select name="state_of_trade_id" style="width: 190px;" onChange="statusChange()">
          <?php echo $strRenderStatus; ?> </select>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Street</td>
      <td class="greyForm"><input type="text" name="Address1" value="<?php echo $strStreet; ?>" style="width: 190px;">
      </td>
      <td align="right" class="greyForm">C/Heating</td>
      <td class="greyForm"><input type="checkbox" name="gch" value="1"<?php if ($strGCH) { echo " checked"; } ?>>
      </td>
      <td align="right" class="greyForm">D/Glazing</td>
      <td class="greyForm"><input type="checkbox" name="doubleGlazed" value="1"<?php if ($strDG) { echo " checked"; } ?>></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Area</td>
      <td class="greyForm"><select name="area_id" style="width: 190px;" onChange="populateCoords()">
          <?php echo $strRenderArea; ?> </select> </td>
      <td align="right" class="greyForm">Tenure</td>
      <td colspan="3" class="greyForm"><select name="lease_free" style="width: 190px;">
          <?php echo $strRenderTenure; ?> </select> <input type="hidden" name="furnished" value="0"></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Postcode</td>
      <td class="greyForm"><input name="Postcode" type="text" value="<?php echo $strPostcode; ?>" size="8" maxlength="8" style="width: 80px;">
      </td>
      <td align="right" class="greyForm">&nbsp;</td>
      <td colspan="3" class="greyForm">&nbsp;</td>
    </tr>
    <?php } elseif ($SaleLet == 2) { ?>
    <tr>
      <td width="100" align="right" class="greyForm">Price p/w </td>
      <td width="200" class="greyForm"><input name="Price" type="text" style="width: 80px;" onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;" value="<?php echo $intPrice; ?>" size="8" maxlength="8">
        <select name="PriceType">
          <option value="1"<?php if ($intPriceType == 1) { echo " selected"; } ?>>Long
          Term</option>
          <option value="2"<?php if ($intPriceType == 2) { echo " selected"; } ?>>Short
          Term</option>
        </select> </td>
      <td width="100" align="right" nowrap class="greyForm">Property Type</td>
      <td width="200" colspan="3" class="greyForm"><select name="type_id" style="width: 190px;">
          <?php echo $strRenderPropType; ?> </select> </td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Price p/w </td>
      <td class="greyForm"><input readonly="true" name="Price2" type="text" style="width: 80px;" onKeypress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;" value="<?php echo $intPrice2; ?>" size="8" maxlength="8">
        <select name="PriceType2">
          <!--
          <option value="0"<?php if ($intPriceType2 == 0) { echo " selected"; } ?>></option>
          <option value="1"<?php if ($intPriceType2 == 1) { echo " selected"; } ?>>Long
          Term</option>
          <option value="2"<?php if ($intPriceType2 == 2) { echo " selected"; } ?>>Short
          Term</option>-->
        </select></td>
      <td align="right" class="greyForm">Market State</td>
      <td colspan="3" class="greyForm">
        <?php if ($intStatus == 11) { ?>
        <input type="hidden" name="state_of_trade_id" value="11">
        Pending &nbsp; <a href="?action=<?php if (in_array($s_userid,$proofers)) { ?>activate<?php } else { ?>proof<?php } ?>&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>ACTIVATE</strong></a>
        / <a href="?action=archive&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>ARCHIVE</strong></a>
        <?php } elseif ($intStatus == 12)  {  ?>
        <input type="hidden" name="state_of_trade_id" value="12"> <strong>Submitted
        to Proofing List</strong>
        <?php if (in_array($s_userid,$proofers)) { ?>
        <a href="?action=activate&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>RELEASE</strong></a>
        <?php } ?>
        <?php } else  {  ?>
        <select name="state_of_trade_id" style="width: 190px;" onChange="statusChange()">
          <?php echo $strRenderStatus; ?> </select>
        <?php } ?>
        <!--
        <?php if ($intStatus <> 11) { ?>
        <select name="state_of_trade_id" style="width: 190px;">
          <?php echo $strRenderStatus; ?> </select>
        <?php } else {  ?>
        <input type="hidden" name="state_of_trade_id" value="11">
        Pending &nbsp; <a href="?action=activate&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>ACTIVATE</strong></a>
        / <a href="?action=archive&propID=<?php echo $intPropID; ?>&searchLink=<?php echo urlencode($searchLink); ?>"><strong>ARCHIVE</strong></a>
        <?php } ?>
		-->
      </td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Number</td>
      <td class="greyForm"><input type="text" name="house_number" value="<?php echo $strNumber; ?>" style="width: 190px;">
      </td>
      <td align="right" class="greyForm">Managed</td>
      <td colspan="3" class="greyForm"><select name="managed" style="width: 190px;">
          <option value="No"<?php if ($strManaged == "No") { echo " selected"; } ?>>No</option>
          <option value="Yes"<?php if ($strManaged == "Yes") { echo " selected"; } ?>>Yes</option>
        </select></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Street</td>
      <td class="greyForm"><input type="text" name="Address1" value="<?php echo $strStreet; ?>" style="width: 190px;">
      </td>
      <td align="right" class="greyForm">C/Heating</td>
      <td width="200" class="greyForm"><input type="checkbox" name="gch" value="1"<?php if ($strGCH) { echo " checked"; } ?>>
      </td>
      <td align="right" class="greyForm">D/Glazing</td>
      <td width="200" class="greyForm"><input type="checkbox" name="doubleGlazed" value="1"<?php if ($strDG) { echo " checked"; } ?>></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Area</td>
      <td class="greyForm"><select name="area_id" style="width: 190px;" onChange="populateCoords()">
          <?php echo $strRenderArea; ?> </select> </td>
      <td align="right" class="greyForm">Furnished</td>
      <td colspan="3" class="greyForm"><select name="furnished" style="width: 190px;">
          <?php echo $strRenderFurnished; ?> </select></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Postcode</td>
      <td class="greyForm"><input name="Postcode" type="text" value="<?php echo $strPostcode; ?>" size="8" maxlength="8" style="width: 80px;">
      </td>
      <td align="right" class="greyForm">&nbsp;</td>
      <td colspan="3" class="greyForm">&nbsp;</td>
    </tr>
    <?php } ?>
    <tr align="center">
      <td colspan="6" class="greyForm">Receptions:
        <select name="receptions">
          <?php
		for ($i = 1; $i <= 9; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $intReceptions) {
				echo ' selected';
				}
			echo '>'.$i.'</option>';
			}
			?>
        </select> &nbsp;&nbsp;Bedrooms:
        <select name="bedrooms">
          <?php
		for ($i = 0; $i <= 9; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $intBedrooms) {
				echo ' selected';
				}
			echo '>'.$i.'</option>';
			}
			?>
        </select> &nbsp;&nbsp;Bathrooms:
        <select name="bathrooms">
          <?php
		for ($i = 1; $i <= 9; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == $intBathrooms) {
				echo ' selected';
				}
			echo '>'.$i.'</option>';
			}
			?>
        </select></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Garden</td>
      <td class="greyForm"><select name="garden" style="width: 190px;">
          <?php echo db_enum("property","garden",$intGarden); ?> </select></td>
      <td align="right" class="greyForm">Parking</td>
      <td colspan="3" class="greyForm"><select name="parking" style="width: 190px;">
          <?php echo db_enum("property","parking",$intParking); ?> </select></td>
    </tr>
    <tr>
      <td class="greyForm" align="right">Card</td>
      <td class="greyForm"><select name="card" style="width: 190px;">
          <?php echo db_enum("property","card",$intCard); ?> </select></td>
      <td align="right" class="greyForm">&nbsp;</td>
      <td colspan="3" class="greyForm">&nbsp;</td>
    </tr>
  </table>
  <?php if ($SaleLet == 1 && $intTenure <> 1) { ?>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <td colspan="2"><EM>The following details have been provided by the vendor and have not been verified.</EM></td>
    </tr>
    <tr>
      <td><strong>Tenure:</strong> </td>
      <td><em><?php echo $strTenureTitle; ?> with some
          <input name="lease_length" type="text" id="lease_length" style="font-style:italic" size="4" maxlength="4"value="<?php echo $strLeaseLength; ?>">
years remaining on the lease</em></td>
    </tr>
    <tr>
      <td nowrap><strong>Service Charge:</strong> </td>
      <td><input name="service_charge" type="text" id="service_charge" style="width:450px; font-style:italic" value="<?php echo $strServiceCharge; ?>" maxlength="220"></td>
    </tr>
    <tr>
      <td><strong>Grount Rent:</strong> </td>
      <td><input name="ground_rent" type="text" id="ground_rent" style="width:450px; font-style:italic" value="<?php echo $strGroundRent; ?>" maxlength="220"></td>
    </tr>
    <tr>
      <td><strong>Other Details: </strong></td>
      <td><input name="other_details" type="text" id="other_details" style="width:450px; font-style:italic" value="<?php echo $strOtherDetails; ?>" maxlength="220"></td>
    </tr>
  </table>
	<?php } ?>
	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <td align="center"><b><?php echo $strHits; ?></b> hits since added on <b><?php echo $strDates; ?></b></td>
    </tr>
      <tr>
        <td align="center"><hr noshade size="1">
            <input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
            <hr noshade size="1">
        </td>
      </tr>
    </table>
    <?php
		}
	elseif ($view == "descriptions") {
	?>
<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th class="greyForm">Strap Line</th>
    </tr>
    <tr>
      <td class="greyForm"><input name="description" type="text" style="width:580px;" value="<?php echo $strDescription; ?>" maxlength="110"></td>
    </tr>
    <tr>
      <th class="greyForm">Description</th>
    </tr>
    <tr>
      <td class="greyForm">
	  <?php
	  $strLongDescription = str_replace("\n","",$strLongDescription);
	  $strLongDescription = str_replace("\r","",$strLongDescription);
	  $strLongDescription = str_replace("'","&#039;",$strLongDescription);
	  ?>
	  <script type="text/javascript">
		var oFCKeditor = new FCKeditor( 'longDescription','580','400','Test' ) ;
		oFCKeditor.BasePath	= 'fckeditor/' ;
		oFCKeditor.Value	= '<?php echo $strLongDescription; ?>';
		oFCKeditor.Create() ;
		//-->
	  </script>
	  <noscript>
	  <p><strong>NOTICE:</strong> javascript not enabled, editing not possible</p>
	  <?php echo $strLongDescription; ?></noscript>
	  </td>
    </tr>
    <tr>
      <td align="center">
	  <hr noshade size="1">
	  	<script language="JavaScript" type="text/javascript">spellcheckbutton();</script> <input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
      <hr noshade size="1">
	  </td>
    </tr>
  </table>
	<?php
		}
	elseif ($view == "images") {
	?>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="2" class="greyForm">Images</th>
    </tr>
  </table>
  <table align="center" width="410" border="0" cellspacing="5" cellpadding="5">
    <tr>
      <td colspan="2" align="center" class="greyForm"><?php if ($strImage0) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&view=<?php echo $view; ?>&image=0&file=<?php echo $strImage0; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage0; ?>" alt="Click to delete this image" width="400" height="400" border="0"></a><br>
	    <?php } ?>
	    Main Image
      <input type="file" name="image[0]"></td>
    </tr>
    <tr>
      <td align="right" valign="bottom" class="greyForm"><?php if ($strImage1) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=1&view=<?php echo $view; ?>&file=<?php echo $strImage1; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage1; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
	    <?php }  ?>
      <input type="file" name="image[1]" style="width:200px"></td>
      <td width="50%" align="left" valign="bottom" class="greyForm"><?php if ($strImage2) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=2&view=<?php echo $view; ?>&file=<?php echo $strImage1; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage2; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
	    <?php } ?>
      <input type="file" name="image[2]" style="width:200px"></td>
    </tr>
    <tr>
      <td align="right" valign="bottom" class="greyForm"><?php if ($strImage3) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=3&view=<?php echo $view; ?>&file=<?php echo $strImage3; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage3; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
	    <?php } ?><input type="file" name="image[3]" style="width:200px">
      </td>
      <td align="left" valign="bottom" class="greyForm"><?php if ($strImage4) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=4&view=<?php echo $view; ?>&file=<?php echo $strImage4; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage4; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
      <?php } ?><input type="file" name="image[4]" style="width:200px"></td>
    </tr>
    <tr>
      <td align="right" valign="bottom" class="greyForm"><?php if ($strImage5) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=5&view=<?php echo $view; ?>&file=<?php echo $strImage5; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage5; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
      <?php } ?><input type="file" name="image[5]" style="width:200px"></td>
      <td align="left" valign="bottom" class="greyForm"><?php if ($strImage6) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=6&view=<?php echo $view; ?>&file=<?php echo $strImage6; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage6; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
      <?php } ?><input type="file" name="image[6]" style="width:200px"></td>
    </tr>
    <tr>
      <td align="right" valign="bottom" class="greyForm"><?php if ($strImage7) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=7&view=<?php echo $view; ?>&file=<?php echo $strImage7 ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage7; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
      <?php } ?><input type="file" name="image[7]" style="width:200px"></td>
      <td align="left" valign="bottom" class="greyForm"><?php if ($strImage8) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=8&view=<?php echo $view; ?>&file=<?php echo $strImage8; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage8; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
      <?php } ?><input type="file" name="image[8]" style="width:200px"></td>
    </tr>
    <tr>
      <td align="right" valign="bottom" class="greyForm"><?php if ($strImage9) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=9&view=<?php echo $view; ?>&file=<?php echo $strImage9; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage9; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
      <?php }  ?><input type="file" name="image[9]" style="width:200px"></td>
      <td align="left" valign="bottom" class="greyForm"><?php if ($strImage10) {
	  ?><a href="javascript:confirmDelete('Delete Image?','?action=delete_image&propID=<?php echo $intPropID; ?>&image=10&view=<?php echo $view; ?>&file=<?php echo $strImage10; ?>&searchLink=<?php echo urlencode($searchLink); ?>');"><img src="<?php echo $image_folder.$strImage10; ?>" alt="Click to delete this image" width="200" height="200" border="0"></a><br>
      <?php } ?><input type="file" name="image[10]" style="width:200px"></td>
    </tr>
  </table>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <td align="center"> <hr noshade size="1"> <input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
        <hr noshade size="1"> </td>
    </tr>
  </table>
	<?php
		}
	elseif ($view == "floorplans") {
	?>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="2" align="center" class="greyForm">Floorplans</th>
    </tr>
  </table>
  <table align="center" border="0" cellspacing="5" cellpadding="5">
    <tr>
      <td align="right" class="greyForm">Gross Internal Area </td>
      <td class="greyForm"><input name="total_area" type="text" value="<?php echo $strTotalArea; ?>" size="4" maxlength="6">
      square meters</td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="greyForm"><?php
		if ($strImage11) {
		$image_props = getimagesize($uploadPath."/".$strImage11);
		echo '<a href="javascript:confirmDelete(\'Delete Image?\',\'?action=delete_image&propID='.$intPropID.'&image=11&view='.$view.'&file='.$strImage11.'&searchLink='.urlencode($searchLink).'\');"><img src="'.$image_folder.$strImage11.'" alt="'.$image_props[0].' x '.$image_props[1].' / click to delete this image"border="1"></a><br>';
		} ?>
	  Floorplan 1  <input type="file" name="image[11]"></td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="greyForm">
	<?php
	if ($strImage12) {
		$image_props = getimagesize($uploadPath."/".$strImage12);
		echo '<a href="javascript:confirmDelete(\'Delete Image?\',\'?action=delete_image&propID='.$intPropID.'&image=12&view='.$view.'&file='.$strImage12.'&searchLink='.urlencode($searchLink).'\');"><img src="'.$image_folder.$strImage12.'" alt="'.$image_props[0].' x '.$image_props[1].' / click to delete this image"border="1"></a><br>';
		} ?>Floorplan2 <input type="file" name="image[12]"></td>
    </tr>
    <tr align="center">
      <td colspan="2" class="greyForm">
	<?php
	if ($strImage13) {
		$image_props = getimagesize($uploadPath."/".$strImage13);
		echo '<a href="javascript:confirmDelete(\'Delete Image?\',\'?action=delete_image&propID='.$intPropID.'&image=13&view='.$view.'&file='.$strImage13.'&searchLink='.urlencode($searchLink).'\');"><img src="'.$image_folder.$strImage13.'" alt="'.$image_props[0].' x '.$image_props[1].' / click to delete this image"border="1"></a><br>';
		} ?>
	  Floorplan3 <input type="file" name="image[13]"></td>
    </tr>
    <tr align="center">
      <td colspan="2" class="greyForm">
	<?php
	if ($strImage14) {
		$image_props = getimagesize($uploadPath."/".$strImage14);
		echo '<a href="javascript:confirmDelete(\'Delete Image?\',\'?action=delete_image&propID='.$intPropID.'&image=14&view='.$view.'&file='.$strImage14.'&searchLink='.urlencode($searchLink).'\');"><img src="'.$image_folder.$strImage14.'" alt="'.$image_props[0].' x '.$image_props[1].' / click to delete this image"border="1"></a><br>';
		} ?>
	  Floorplan4  <input type="file" name="image[14]"></td>
    </tr>
    <tr align="center">
      <td colspan="2" class="greyForm">
	<?php
	if ($strImage15) {
		$image_props = getimagesize($uploadPath."/".$strImage15);
		echo '<a href="javascript:confirmDelete(\'Delete Image?\',\'?action=delete_image&propID='.$intPropID.'&image=15&view='.$view.'&file='.$strImage15.'&searchLink='.urlencode($searchLink).'\');"><img src="'.$image_folder.$strImage15.'" alt="'.$image_props[0].' x '.$image_props[1].' / click to delete this image"border="1"></a><br>';
		} ?>
		Floorplan5 <input type="file" name="image[15]"></td>
    </tr>
  </table>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <td align="center"><hr noshade size="1"> <input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
        <hr noshade size="1"> </td>
    </tr>
  </table>
  <?php
  }
  elseif ($view == "map") {

	$locationimage = "/images/mapping/crosshair.gif";
	$pixelwidth = 44;
	$pixelheight = 44;

	if ($intOSX) {
		$default_location_x = $intOSX;
		}
	if ($intOSY) {
		$default_location_y = $intOSY;
		}

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
	$sql = "SELECT p1.prop_ID, p1.price, p1.state_of_trade_id, p1.Address1, p1.house_number, p1.Postcode, p1.type_id, p1.Bedrooms, p1.osx, p1.osy, p1.SaleLet, proptype.type_ID, proptype.type_Title, count(*) AS mycount FROM property p1, property p2, proptype WHERE p1.osx = p2.osx AND p1.osy=p2.osy AND (p1.osx > ".($basex - 500)." AND p1.osx < ".($basex + 1000)." AND p1.osy > ".($basey - 500)." AND p1.osy < ".($basey + 1000).") AND p1.type_id = proptype.type_ID GROUP BY p1.prop_ID, p1.price, p1.state_of_trade_id, p1.Address1, p1.Postcode, p1.type_id, p1.Bedrooms, p1.osx, p1.osy, p1.SaleLet,  proptype.type_ID, proptype.type_Title ORDER BY p1.Address1";
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

		if ($osxdb == $intOSX && $osydb == $intOSY) {
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
		$layerOther .= '<div id="layer'.$pid.'" style="position:absolute; left:'.$osxdb.'; top:'.$osydb.'; width:12px; height:12px; z-index:'.$layerindex.'"><a href="javascript:confirmUpdate(\'Are you sure you want to use this location?\',\'?propID='.$intPropID.'&view=map&action=save_location&x='.$row["osx"].'&y='.$row["osy"].'&searchLink='.urlencode($searchLink).'\');"><img src="/images/mapping/'.$dot.'" alt="'.$overinfo.'" width="12" height="12" border="0" name="img'.$pid.'" id="img'.$pid.'"></a></div>';
		}

		$LayerOther .= '</div>';



	if ($locx <> 0 || $locy <> 0) {
	$layer = "<div id=\"layer\" style=\"position:absolute; left:".($locx-round($pixelwidth/2))."; top:".($locy-round($pixelheight/2))."; width:".$pixelwidth."px; height:".$pixelheight."px; z-index:2\"><img src=\"".$locationimage."\" width=\"".$pixelwidth."\" height=\"".$pixelheight."\"></div>";
	}
  ?>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="3" class="greyForm">Map
	  <?php if ($_GET["y"]) { ?>
      <input type="hidden" name="osx" value="<?php echo $osx; ?>">
	  <input type="hidden" name="osy" value="<?php echo $osy; ?>">
	  <?php } ?></th>
    </tr>
  </table>
  <table align="center" width="600" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex-500).'&y='.($basey+500); ?>&searchLink=<?php echo urlencode($searchLink); ?>">NW</a></td>
      <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex).'&y='.($basey+500); ?>&searchLink=<?php echo urlencode($searchLink); ?>">N</a></td>
      <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex+500).'&y='.($basey+500); ?>&searchLink=<?php echo urlencode($searchLink); ?>">NE</td>
    <tr>
	  <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex-500).'&y='.($basey); ?>&searchLink=<?php echo urlencode($searchLink); ?>">W</td>
      <td>
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
          <?php echo $layer; ?> <?php echo $layerOther; ?>
          <div id="linklayer" style="position:absolute; width: 561px; height: 562px; z-index: 3; left: 0; top: 0;">
            <table border="0" cellpadding="0" cellspacing="0" align="left">
              <tr>
                <td><a href="?propID=<?php echo $intPropID; ?>&view=map&searchLink=<?php echo urlencode($searchLink); ?>&x=<?php echo $basex; ?>&y=<?php echo $basey; ?>" style="cursor:crosshair;"><img src="/images/spacer.gif" width="561" height="561" border="0" ismap></a></td>
              </tr>
            </table>
          </div>
        </div></td>
      <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex+500).'&y='.($basey); ?>&searchLink=<?php echo urlencode($searchLink); ?>">E</a></td>
    <tr>
      <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex-500).'&y='.($basey-500).""; ?>&searchLink=<?php echo urlencode($searchLink); ?>">SW</a></td>
      <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex).'&y='.($basey-500); ?>&searchLink=<?php echo urlencode($searchLink); ?>">S</a></td>
      <td align="center"><a href="?propID=<?php echo $intPropID; ?>&view=map&<?php echo 'x='.($basex+500).'&y='.($basey-500); ?>&searchLink=<?php echo urlencode($searchLink); ?>">SE</a></td>
    <tr>
      <td colspan="3" align="center"><hr noshade size="1"> <input name="Submit" type="submit" value="   <?php echo $action; ?> Property   ">
        <hr noshade size="1"> </td>
    </tr>
    <tr><td colspan="3"></tr>
  </table>
	<?php
		}
	elseif ($view == "notes") {
	?>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th class="greyForm">Notes</th>
    </tr>
    <tr>
      <td class="greyForm">New note:
        <input name="newnote" type="text" id="newnote" style="width: 400px;">
        <input type="submit" name="Submit" value="Add"></td>
    </tr>
    <tr>
      <td class="greyForm"> <textarea readonly="true" name="notes" cols="30" rows="25" style="width: 590px;"><?php echo $strNotes; ?></textarea>
      </td>
    </tr>
  </table>
	<?php
		}
	elseif ($view == "email") {

	$sql = "SELECT * FROM mailshot, admin WHERE mai_userid = adm_id AND mai_prop = $intPropID ORDER BY mai_date DESC";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("mailshot select error: ".$q->getMessage()); }

	$render = '<table width="100%" align="center" border="0" cellspacing="3" cellpadding="3">
	<tr>
	<td width="20"><strong>ID</strong></td>
	<td width="80"><strong>Date</strong></td>
	<td width="80"><strong>Type</strong></td>
	<td width="80"><strong>Sent</strong></td>
	<td width="80"><strong>Hits</strong></td>
	<td><strong>Sender</strong></td>
	</tr>';
	while ($row = $q->fetchRow()) {
		$render .= '<tr>
		<td>'.$row["mai_id"].'</td>
		<td>'.$row["mai_date"].'</td>
		<td>'.ucwords($row["mai_type"]).'</td>
		<td>'.$row["mai_count"].'</td>
		<td>'.$row["mai_hits"].'</td>
		<td>'.$row["adm_name"].'</td>
		</tr>';
		}

	$render .= '</table>';

	?>
  <table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th class="greyForm">Email</th>
    </tr>
	<?php if ($intStatus == 11) { ?>
    <tr>
      <td><a href="mailto:?subject=Wooster%20%26%20Stock%20-%20Preview%20your%20property&body=Dear VENDOR,%0D%0A%0D%0AClick the link below to preview your property:%0D%0Ahttp://www.woosterstock.co.uk/preview.php?propID=<?php echo $intPropID; ?>%0D%0A%0D%0APlease let me know if there are any changes you would like made. Once you are happy with the details we will release the property on our web site.%0D%0A%0D%0ARegards,%0D%0A%0D%0A<?php echo $_SESSION["s_name"];?>%0D%0AWooster %26 Stock">Email a preview of this property to Vendor</a></td>
    </tr>
	<?php } elseif ($intStatus == 1 || $intStatus == 2 || $intStatus == 4) { ?>
    <tr>
      <td><a href="mailto:?subject=Wooster%20%26%20Stock&body=Dear CLIENT,%0D%0A%0D%0A<?php echo $strDescription; ?>%0D%0A<?php echo $strStreet; ?>, <?php echo $strArea; ?> <?php echo $strPC1; ?>%0D%0A<?php echo $strPrice; ?> <?php if ($SaleLet == 1) { echo $strTenureTitle; } else { echo "p/w"; } ?>%0D%0Ahttp://www.woosterstock.co.uk/Detail.php?propID=<?php echo $intPropID; ?>%0D%0A%0D%0ATo arrange a viewing or for more details, please contact our <?php echo $strBranchTitle; ?> office on <?php echo $strBranchTel; ?>%0D%0A%0D%0ARegards,%0D%0A%0D%0A<?php echo $_SESSION["s_name"];?>%0D%0AWooster %26 Stock">Email this property to a Client</a></td>
    </tr>
	<?php } else { ?>
	<tr>
      <td>You cannot Email this property, it is off the market</td>
    </tr>
	<?php } ?>
    <tr>
      <th class="greyForm">Mailshot</th>
    </tr>
    <tr>
      <td><a href="list.php?action=compose&propID=<?php echo $intPropID; ?>">Compose New Mailshot</a></td>
    </tr>
    <tr>
      <td><?php echo $render; ?></td>
    </tr>
  </table>

  <?php
  }
  elseif ($view == "log") {
  $sqlLog = "SELECT *, date_format(changelog.cha_datetime, '%d/%m/%y %h:%i:%s') as cha_date FROM changelog, admin WHERE changelog.cha_user = admin.adm_id AND changelog.cha_table = 'property' AND changelog.cha_row = $intPropID ORDER BY changelog.cha_datetime DESC";
		$qLog = $db->query($sqlLog);
		if (DB::isError($qLog)) {  die("error: ".$qLog->getMessage()); }
		$strRenderLog = '
       	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="5" class="greyForm">Log</th>
    </tr>
    <tr>
	<td>
	<table width="100%" cellspacing="2" cellpadding="1">
		<tr>
		<td><strong>Date</strong></td>
		<td><strong>Field</strong></td>
		<td><strong>Old Value</strong></td>
		<td><strong>New Value</strong></td>
		<td><strong>User</strong></td>
		<td>&nbsp;</td>
		</tr>
		';
		while ($rowLog = $qLog->fetchRow()) {

			$cha_date = $rowLog["cha_date"];

			if ($rowLog["cha_field"] == "state_of_trade_id") {
				$sqlSot = "SELECT * FROM state_of_trade WHERE state_ID = ".$rowLog["cha_old"]." LIMIT 1";
				$qSot = $db->query($sqlSot);
				if (DB::isError($qSot)) {  die("error: ".$qSot->getMessage()); }
				while ($rowSot = $qSot->fetchRow()) {
					$rowLog["cha_old"] = $rowSot["state_title"];
					}
				$sqlSot = "SELECT * FROM state_of_trade WHERE state_ID = ".$rowLog["cha_new"]." LIMIT 1";
				$qSot = $db->query($sqlSot);
				if (DB::isError($qSot)) {  die("error: ".$qSot->getMessage()); }
				while ($rowSot = $qSot->fetchRow()) {
					$rowLog["cha_new"] = $rowSot["state_title"];
					}
				}

			$strRenderLog .= '<tr>
			<td>'.$cha_date.'</td>
			<td>'.$rowLog["cha_field"].'</td>
			<td><span title="'.$rowLog["cha_old"].'">'.strip_tags(substr($rowLog["cha_old"],0,25)).'</span></td>
			<td><span title="'.$rowLog["cha_new"].'">'.strip_tags(substr($rowLog["cha_new"],0,25)).'</span></td>
			<td>'.$rowLog["adm_name"].'</td>
			<td><a href="javascript:confirmUpdate(\'Are you sure you want to Undo?\',\'?propID='.$intPropID.'&action=undo&log='.$rowLog["cha_id"].'\');"><img src="images/ed_undo.gif" width="18" height="18" border="0" alt="Undo"></a></td>
			</tr>
			';
			}
		$strRenderLog .= '
		</table>
		</td>
		</tr>
		</table>';
		echo $strRenderLog;
  ?>

  <?php
  } // end view if
  ?>
</form>
</body>
</html>
<?php } // end action if ?>