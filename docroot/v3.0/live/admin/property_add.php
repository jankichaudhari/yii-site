<?php
/*
add_property_second.php 21/03/06
adds a property record (nothing else)
full postcode lookup integration and alternate method

converting to new form and page classes - started 11/07
*/

require_once("inx/global.inc.php");

//$postcode_on = ""; // turn postcode search on or off

if ($_GET["stage"]) {
	$stage = $_GET["stage"];
	}
elseif ($_POST["stage"]) {
	$stage = $_POST["stage"];
	}
else {
	$stage = "address_search";
	}




switch ($stage):
/////////////////////////////////////////////////////////////////////////////
// address_search: enter address info and search postcode and existing property table
/////////////////////////////////////////////////////////////////////////////
case "address_search":

// message from refering page, or stage title
if ($_GET["msg"]) {
	$msg = '<p class="stageTitleNotice">'.urldecode($_GET["msg"]).'</p>';
	} else {
	$msg = '<p class="stageTitle">Please enter address details</p>';
	}
		
$render .= $msg.'
<form method="GET">
<input type="hidden" name="stage" value="lookup">
<table>
<tr>
<td>House or flat number</td>
<td><input type="text" name="number" value="'.$number.'"></td>
</tr>
<tr>
<td>Street</td>
<td><input type="text" name="street" value="'.format_street($street).'"></td>
</tr>
<tr>
<td>Postcode</td>
<td><input type="text" name="postcode" value="'.format_postcode($postcode).'"></td>
</tr>
<tr>
<td colspan="2"><input type="submit"></td>
</tr>
<tr>
<td>Use postcode lookup?</td>
<td><input type="radio" name="postcode_on" value="1" checked> Yes 
<input type="radio" name="postcode_on" value=""> No </td>
</tr>
</table> 
</form>
';

echo html_header("Add Property");
echo $render;
echo $html_footer;

// end of stage 1
break;



/////////////////////////////////////////////////////////////////////////////
// lookup
// (if using postcode lookup)
// format search string for freetext address lookup
// perform address lookup and display results
// (else)
// search against existing property and display results
// show manual address form
/////////////////////////////////////////////////////////////////////////////
case "lookup":   

if ($_GET["postcode_on"] == 1) {
	$_SESSION["postcode_on"] = "1";
	} else {
	$_SESSION["postcode_on"] = "";
	}

// format search values and string for freetext and database search
if ($_GET["number"]) {
	$terms["number"] = trim($_GET["number"]);
	}
if ($_GET["street"]) {
	$terms["street"] = format_street($_GET["street"]);
	}
if ($_GET["postcode"]) {
	$terms["postcode"] = format_postcode($_GET["postcode"]);
	}
if ($terms) {
	$return_url = http_build_query($terms);
	}
if (count($terms) < 3) {
	header("Location:?$return_url&msg=Please+fill+in+all+the+fields");
	exit;
	}


// if postcode lookup is on (available)	
if ($postcode_on) {	

	// prepare array
	foreach($terms as $val) {
		$search_string .= $val.',';
		}	
	$search_string = remove_lastchar($search_string,",");
	
	$postcode = new Postcode();
	$render .='
	<p class="stageTitle">Double click the correct address</p>
	<form method="get">
	<input type="hidden" name="stage" value="elaborate">
	'.$postcode->lookup('by_freetext',$search_string).'
	</form>
	<p>If the address is not listed above <a href="?'.$return_url.'">click here</a> to try again.</p>
	';
		
	} 
	
	
	
// if postcode lookup is off, query existing database
else {
	
	// prepare search terms from array
	foreach($terms as $val) {	
		$sql .= " ( property.pro_addr1 LIKE '%".$val."%' OR property.pro_addr2 LIKE '%".$val."%' OR";
		$sql .= " property.pro_addr3 LIKE '".$val."%' OR property.pro_postcode LIKE '%".$val."%' ) AND";
		}
	
	$sql = "SELECT pro_id,pro_addr1,pro_addr2,pro_addr3,pro_postcode FROM property WHERE ".remove_lastchar($sql,"AND");
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();

	if ($numRows !== 0) {
		$render .= '
		<p class="stageTitle">Double click the correct address (postcode search offline)</p>
		<form method="get">
		<input type="hidden" name="stage" value="elaborate">
		<select name="pro_id" size="8" style="width:500px" onDblClick="document.forms[0].submit();">';	
		while ($row = $q->fetchRow()) {
			$render .= '<option value="'.$row["pro_id"].'">'.$row["pro_addr1"].' '.$row["pro_addr2"].' '.$row["pro_addr3"].', '.$row["pro_postcode"].'</option>
			';
			}
		$render .= '</select>
		</form>
		<p>If the property is not listed above, please enter full details below</p>
		';
		}


// add manual input form		

// get list of areas that match first half of postcode or full list if no postcode was entered
// match first 4 characters to prevent "SE1" matching "SE1 " and "SE15" etc
if ($postcode) {
	$pc1 = explode(" ",$postcode);
	$pc1 = $pc1[0];
	$len = strlen($pc1);
	$sql = "SELECT are_id,are_title,are_postcode 
	FROM area WHERE 
	LEFT(area.are_postcode,4) = '".$pc1."' 
	ORDER BY are_title";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();	
	if ($numRows == 0) {
		$render_area = db_lookup("pro_area","area","select",$pro_area,"are_title");
		} else {
		while ($row = $q->fetchRow()) {	
			if ($numRows == 1) {
				$render_area .= '<input type="hidden" name="pro_area" value="'.$row["are_id"].'">'.$row["are_title"].'';
				} else {
				$render_area .= '<input type="radio" name="pro_area" value="'.$row["are_id"].'"> '.$row["are_title"].'<br> ';
				}
			}
		}
	}


$render .='
<form method="GET">
<input type="hidden" name="stage" value="elaborate">
<table width="500" border="1" cellspacing="0" cellpadding="5">
   <tr>
     <td>House or flat no.</td>
     <td><input type="text" name="pro_addr1" value="'.$number.'"></td>
   </tr>
   <tr>
     <td>Building name &amp; no.</td>
     <td><input type="text" name="pro_addr2" value=""></td>
   </tr>
   <tr>
     <td>Street</td>
     <td><input type="text" name="pro_addr3" value="'.format_street($street).'"></td>
   </tr>
   <tr>
     <td>Area</td>
     <td>'.$render_area.'</td>
   </tr>
   <tr>
     <td>Town</td>
     <td><input type="text" name="pro_addr5" value=""></td>
   </tr>
   <tr>
     <td>Postcode</td>
     <td><input type="text" name="pro_postcode" value="'.format_postcode($postcode).'"></td>
   </tr>
   <tr align="center">
     <td colspan="2"><input type="submit"></td>
   </tr>
 </table>
</form>
';


	} // end postcode_on


echo html_header("Add Property");
echo $render;
echo $html_footer;

// end of stage
break;




/////////////////////////////////////////////////////////////////////////////
// stage 3: create property and elaborate
// (postcode)
// if selected address (postcode_id OR UDPRN) is present in database, use that property
// else get and format address data from postcode lookup and enter into database
// (no postcode)
// if selected existing property ($pro_id) is selected 
// if manual form has been used, verify values and enter into database
// goto stage 4
/////////////////////////////////////////////////////////////////////////////
case "elaborate":  


// if using postcode_id (or UDPRN in future)
if ($_GET["postcode_id"]) {	
	$postcode = new Postcode();
	$pro_id = $postcode->property($_GET["postcode_id"]);
	}	
// if property is in database
elseif ($_GET["pro_id"]) {
	$pro_id = $_GET["pro_id"];	
	}
	
		
//if using manual input form, validate values and enter into database
else {
	
	foreach($_GET as $key=>$val) 
		{
		$_GET[$key] = trim($val);
		}
	if ($pro_addr1) {
		$db_data["pro_addr1"] = trim($pro_addr1);
		} else {
		$errors[] = "Missing or Invalid data: House or Flat number";
		}
	if ($pro_addr2) {		
		$db_data["pro_addr2"] = trim($pro_addr2);
		} else {
		//$errors[] = "Missing or Invalid data: ";
		}
	if ($pro_addr3) {		
		$db_data["pro_addr3"] = format_street($pro_addr3);
		} else {
		$errors[] = "Missing or Invalid data: Street Name";
		}
	if ($pro_area) {		
		$db_data["pro_area"] = $pro_area;
		} else {
		$errors[] = "Missing or Invalid data: Area";
		}
	if ($pro_addr5) {		
		$db_data["pro_addr5"] = format_street($pro_addr5);
		} else {
		$errors[] = "Missing or Invalid data: City";
		}
	if ($pro_country) {		
		$db_data["pro_country"] = trim($pro_country);
		} else {
		//$errors[] = "Missing or Invalid data: Country";
		}
	if (is_valid_uk_postcode($pro_postcode)) {		
		$db_data["pro_postcode"] = format_postcode($pro_postcode);
		} else {
		$errors[] = "Missing or Invalid data: Postcode";
		}
		
	if ($errors) {
		echo error_message($errors);
		exit;
		}
		
	// insert into property table		
	$pro_id = db_query($db_data,"INSERT","property","pro_id","0");	
	header("Location:?stage=elaborate&pro_id=$pro_id");
	}


// get values from existing property record using $pro_id, as supplied by one of the three actions above

	$sql = "SELECT 
	property.pro_id, 
	property.pro_status,
	property.pro_addr1,
	property.pro_addr2,
	property.pro_addr3,
	property.pro_addr4,
	property.pro_addr5,	
	property.pro_country,
	property.pro_postcode,
	property.pro_area,
	property.pro_ptype,
	property.pro_psubtype,
	
	property.pro_built,
	property.pro_refurbed,
	property.pro_floors,
	property.pro_floor,
	property.pro_listed,
	property.pro_parking,
	property.pro_garden,
	property.pro_gardenlength,
	property.pro_reception,
	property.pro_bedroom,
	property.pro_bathroom,
	property.pro_tenure,
	property.pro_leaseend,
	property.pro_location,
	
    T.pty_title    as type_title,
    ST.pty_title   as subtype_title,
	
	area.are_id,
	area.are_title
	
	FROM property 
	LEFT OUTER 
	JOIN ptype as T
    ON T.pty_id = property.pro_ptype 
	LEFT OUTER 
  	JOIN ptype as ST
    ON ST.pty_id = property.pro_psubtype     
	LEFT OUTER JOIN area ON area.are_id = area.are_id 
 	WHERE 	
	property.pro_id = ".$pro_id;
	
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$numRows = $q->numRows();
	if ($numRows == 0) {
		$errors[] = "Property not found";
		echo error_message($errors);
		exit;
		}
		
	while ($row = $q->fetchRow()) {	
		$pro_status = $row["pro_status"];
		$pro_addr1 = $row["pro_addr1"];
		$pro_addr2 = $row["pro_addr2"];
		$pro_addr3 = $row["pro_addr3"];
		$pro_addr4 = $row["pro_addr4"];
		$pro_addr5 = $row["pro_addr5"];
		$pro_country = $row["pro_country"];
		$pro_postcode = $row["pro_postcode"];
		$pro_area = $row["pro_area"];
		$pro_ptype = $row["pro_ptype"];
		$pro_psubtype = $row["pro_psubtype"];
		$pro_built = $row["pro_built"];
		$pro_refurbed = $row["pro_refurbed"];
		$pro_floors = $row["pro_floors"];
		$pro_floor = $row["pro_floor"];
		$pro_listed = $row["pro_listed"];
		$pro_parking = $row["pro_parking"];
		$pro_garden = $row["pro_garden"];
		$pro_gardenlength = $row["pro_gardenlength"];
		$pro_reception = $row["pro_reception"];
		$pro_bedroom = $row["pro_bedroom"];
		$pro_bathroom = $row["pro_bathroom"];
		$pro_tenure = $row["pro_tenure"];
		$pro_leaseend = $row["pro_leaseend"];
		$pro_location = $row["pro_location"];	
		$type_title = $row["type_title"];	
		$subtype_title = $row["subtype_title"];
		$area_title = $row["are_title"];
		}

// get list of matching area, based on first half of postcode
// show radio buttons, or a full drop down if no postcode match is found
$pc1 = explode(" ",$pro_postcode);
$pc1 = $pc1[0];
$len = strlen($pc1);
$sql = "SELECT are_id,are_title,are_postcode 
FROM area WHERE 
LEFT(area.are_postcode,4) = '".$pc1."' 
ORDER BY are_title";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();	
if ($numRows == 0) {
	$render_area = db_lookup("pro_area","area","select",$pro_area,"are_title");
	$render_area .= '<a href="area_add.php?pc='.$pc1.'">Click here to add a new area</a>';
	} else {
	while ($row = $q->fetchRow()) {	
		if ($numRows == 1) {
			$render_area .= '<input type="radio" name="pro_area" value="'.$row["are_id"].'" checked>'.$row["are_title"].'';
			} else {
			$render_area .= '<input type="radio" name="pro_area" value="'.$row["are_id"].'"';
			if ($row["are_id"] == $pro_area) {
				$render_area .= ' checked';
				}
			$render_area .= '> '.$row["are_title"].'<br> ';
			}
		}
	}

// get property types to populate javascript drop downs
$sql = "SELECT pty_id,pty_type,pty_title FROM ptype";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {	
	if (!$row["pty_type"]) {
		$render_ptype .= '<option value="'.$row["pty_id"].'"';
		if ($row["pty_id"] == $pro_ptype) {
			$render_ptype .= ' selected';
			}
		$render_ptype .= '>'.$row["pty_title"].'</option>
		';
		}
	elseif ($row["pty_type"] == 1) {
		$_js1 .= "'".$row["pty_title"]."','".$row["pty_id"]."',";
		}
	elseif ($row["pty_type"] == 2) {
		$_js2 .= "'".$row["pty_title"]."','".$row["pty_id"]."',";
		}
	elseif ($row["pty_type"] == 3) {
		$_js3 .= "'".$row["pty_title"]."','".$row["pty_id"]."',";
		}
	}

// create master type drop down
$render_ptype = '<select name="pro_ptype" onchange="populate(document.forms[0].pro_ptype,document.forms[0].pro_psubtype)">
<option value="0"></option>
'.$render_ptype.'
</select>';	

// format javascript arrays
$_js1 = "'(select)','',".remove_lastchar($_js1,",");
$_js2 = "'(select)','',".remove_lastchar($_js2,",");
$_js3 = "'(select)','',".remove_lastchar($_js3,",");

// if the property already has a subtype, manually make the drop down
if ($pro_psubtype) {
	$render_ptype .= '
	<select name="pro_psubtype" style="width:150px">';
	$sql = "SELECT pty_id,pty_type,pty_title FROM ptype WHERE pty_type = ".$pro_ptype;
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$render_ptype .= '<option value="'.$row["pty_id"].'"';
		if ($row["pty_id"] == $pro_psubtype) {
			$render_ptype .= ' selected';
			}
		$render_ptype .= '>'.$row["pty_title"].'</option>
		';
		}
	$render_ptype .= '</select>';
	}
else {
	$render_ptype .= '
	<select name="pro_psubtype" style="width:150px">
	<option></option>
	</select>';
	}

$_js = '
var thelist = new Array();
thelist[0] = new Array('.$_js0.');
thelist[1] = new Array('.$_js1.');
thelist[2] = new Array('.$_js2.');
thelist[3] = new Array('.$_js3.');
';


// show property particlars form for selected property
$render .= '
<form method="GET">
<input type="hidden" name="stage" value="store">
<input type="hidden" name="pro_id" value="'.$pro_id.'">
<table width="600" border="1" cellspacing="0" cellpadding="5">
  <tr>
    <td>'.$pro_addr1.' '.$pro_addr2.' '.$pro_addr3.', '.$pro_addr5.' '.$pro_postcode.'</td>
  </tr>
</table>
';
// only ask for pro_area to be chosen if not already stored
if (!$pro_area) {
	$render .= '<br>
	<table width="600" border="1" cellspacing="0" cellpadding="5">
	  <tr>
		<td>Please select the area for this property:<br>
		'.$render_area.'</td>
	  </tr>
	</table>
	';
	} else {
	$render .= '<input type="hidden" name="pro_area" value="'.$pro_area.'">
	';
	}
$render .= '<br>
<table width="600" border="1" cellspacing="0" cellpadding="5">
  <tr>
    <td>Property Type</td>
    <td>'.$render_ptype.'</td>
    <td>Floor</td>
    <td>'.db_enum("property","pro_floor","select",$pro_floor,"NULL").'</td>
  </tr>
  <tr>
    <td>Tenure</td>
    <td>'.db_enum("property","pro_tenure","select",$pro_tenure,"NULL").'</td>
    <td>Total Floors</td>
    <td>'.db_dropdown("pro_floors","10",$pro_floors,"1","NULL").'</td>
  </tr>
  <tr>
    <td>Lease expires</td>
    <td><input name="pro_leaseend" type="text" size="10" value="'.$pro_leaseend.'"></td>
    <td>Receptions</td>
    <td>'.db_dropdown("pro_reception","10",$pro_reception,"0","NULL").'</td>
  </tr>
  <tr>
    <td>Parking</td>
    <td>'.db_enum("property","pro_parking","select",$pro_parking,"NULL").'</td>
    <td>Bedrooms</td>
    <td>'.db_dropdown("pro_bedroom","10",$pro_bedroom,"0","NULL").'</td>
  </tr>
  <tr>
    <td>Garden</td>
    <td>'.db_enum("property","pro_garden","select",$pro_garden,"NULL").'</td>
    <td>Bathrooms</td>
    <td>'.db_dropdown("pro_bathroom","10",$pro_bathroom,"0","NULL").'</td>
  </tr>
  <tr>
    <td>Garden length</td>
    <td><input name="pro_gardenlength" type="text" size="10" value="'.$pro_gardenlength.'">
        <input name="pro_gardenlength_unit" type="radio" value="mtr" checked>
      meters
      <input name="pro_gardenlength_unit" type="radio" value="ft">
      feet </td>
    <td>Built</td>
    <td><input name="pro_built" type="text" size="10" value="'.$pro_built.'"></td>
  </tr>
  <tr>
    <td>Listed</td>
    <td>'.db_enum("property","pro_listed","select",$pro_listed,"NULL").'</td>
    <td>Refurbished</td>
    <td><input name="pro_refurbed" type="text" size="10" value="'.$pro_refurbed.'"></td>
  </tr>
</table>
<input type="submit">
</form>
';

echo html_header("Add Property"," onLoad=\"init(document.forms[0].pro_psubtype)\"",$_js);
echo $render;
echo $html_footer;





// end of stage
break;




/////////////////////////////////////////////////////////////////////////////
// stage 5: validate and store info in property table
/////////////////////////////////////////////////////////////////////////////
case "store":

//if (isset($pro_bedroom)) { // this dosent work, it see empty values as 0	
//if ($pro_bedroom || $pro_bedroom==0) { //this works with zero values

foreach($_GET as $key=>$val) {	
	// assign to vars, including 0 value but not empty value 	
	//if ($val || $val === "0") {		
		$_GET[$key] = trim($val);	
	//	}	
	}

if (!$pro_id) {
	$errors[] = "Missing property identifier";
	echo error_message($errors);
	exit;
	}
	
if ($pro_area) {	
	$db_data["pro_area"] = trim($pro_area);
	} else {
	//$errors[] = "Missing or Invalid data: Area";
	}
if ($pro_ptype) {	
	$db_data["pro_ptype"] = trim($pro_ptype);
	} else {
	//$errors[] = "Missing or Invalid data: Property Type";
	}
if ($pro_psubtype) {	
	$db_data["pro_psubtype"] = trim($pro_psubtype);
	} else {
	//$errors[] = "Missing or Invalid data: Property SubType";
	}
	
if ($pro_built) {	
	$db_data["pro_built"] = trim($pro_built);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}	
if ($pro_refurbed) {	
	$db_data["pro_refurbed"] = trim($pro_refurbed);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}	
if ($pro_floors) {	
	$db_data["pro_floors"] = trim($pro_floors);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}	
if ($pro_floor) {	
	$db_data["pro_floor"] = trim($pro_floor);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}	
if ($pro_listed) {	
	$db_data["pro_listed"] = trim($pro_listed);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}	
if ($pro_parking) {	
	$db_data["pro_parking"] = trim($pro_parking);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}	
if ($pro_garden) {	
	$db_data["pro_garden"] = trim($pro_garden);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}	
if ($pro_gardenlength) {
	// convert feet to meters
	if ($pro_gardenlength_unit == "ft") {
		$pro_gardenlength = ft2mtr($pro_gardenlength);
		}	
	$db_data["pro_gardenlength"] = trim($pro_gardenlength);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}
if ($pro_reception || $pro_reception === "0") {	
	$db_data["pro_reception"] = trim($pro_reception);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}
if ($pro_bedroom || $pro_bedroom === "0") { 	
	$db_data["pro_bedroom"] = "$pro_bedroom";
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}
if ($pro_bathroom || $pro_bathroom === "0") {	
	$db_data["pro_bathroom"] = trim($pro_bathroom);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}
if ($pro_tenure) {	
	$db_data["pro_tenure"] = trim($pro_tenure);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}
if ($pro_leaseend) {	
	$db_data["pro_leaseend"] = trim($pro_leaseend);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}
if ($pro_location) {	
	$db_data["pro_location"] = trim($pro_location);
	} else {
	//$errors[] = "Missing or Invalid data: ";
	}

if ($errors) {
	echo error_message($errors);
	exit;
	}
	
if ($db_data) {
	db_query($db_data,"UPDATE","property","pro_id",$pro_id);
	}
header("Location:?stage=finished&pro_id=$pro_id");


// end of stage 5
break;



case "finished":  

$sql = "SELECT pro_longitude,pro_latitude FROM property WHERE pro_id = ".$_GET["pro_id"];
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows == 0) {
	$errors[] = "Property not found";
	echo error_message($errors);
	exit;
	}
	
while ($row = $q->fetchRow()) {
	$long = $row["pro_longitude"];
	$lat = $row["pro_latitude"];
	}

$render .= '<iframe src="googlemap.php?long='.$long.'&lat='.$lat.'" width="500" height="400" marginwidth="0" marginheight="0" frameborder="0"></iframe>

';

echo html_header("Add Property");
echo $render;

break;




/////////////////////////////////////////////////////////////////////////////
// if no stage is defined
/////////////////////////////////////////////////////////////////////////////
default:

$render = 'Nothing to do';

endswitch;

/*


stage 4: elaborate
the property is in the table, now enter physical attributes

stage 5: validate and save

*/



?>