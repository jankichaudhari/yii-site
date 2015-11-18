<?php
require_once("inx/global.inc.php");

// only accesible to SuperAdmin and Administrator
pageAccess($_SESSION["auth"]["roles"],array('SuperAdmin','SuperProduction'));

// version for gemma, which deals with property only, not clients or contacts

if ($_GET["action"] == "delete_property" && $_GET["pro_id"]) {
	$sql = "DELETE FROM property WHERE pro_id = ".$_GET["pro_id"];
	$q = $db->query($sql);
	header("Location:".$PHP_SELF);
	}
if ($_GET["action"] == "replace_deal" && $_GET["pro_pro_id"] && $_GET["dea_id"]) {
	// get area from old property record
	$sql = "SELECT pro_area FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	WHERE deal.dea_id = ".$_GET["dea_id"];
	$pro_area = $db->getOne($sql);

	// update new property with area
	if ($pro_area) {
		$sql = "UPDATE property SET pro_area = $pro_area WHERE pro_id = ".$_GET["pro_pro_id"];
		$q = $db->query($sql);
		}

	// update deal record with new prop
	$sql = "UPDATE deal SET dea_prop = '".$_GET["pro_pro_id"]."' WHERE dea_id = ".$_GET["dea_id"];
	$q = $db->query($sql);


	header("Location:".$PHP_SELF."?pro_id=".$_GET["pro_id"]);
	}

if ($_GET["action"] == "replace_deal_location" && $_GET["pro_pro_id"] && $_GET["pro_id"]) {
	// get locations from just added property
	$sql = "SELECT pro_authority,pro_east,pro_north,pro_latitude,pro_longitude FROM property
	WHERE pro_id = ".$_GET["pro_pro_id"];
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	//$prop = "<pre>";
	while ($row = $q->fetchRow()) {
		$pro_authority = $row["pro_authority"];
		$pro_east = $row["pro_east"];
		$pro_north = $row["pro_north"];
		$pro_latitude = $row["pro_latitude"];
		$pro_longitude = $row["pro_longitude"];
		}

	$sql = "UPDATE property SET
	pro_authority = '$pro_authority',
	pro_east = '$pro_east',
	pro_north = '$pro_north',
	pro_latitude = '$pro_latitude',
	pro_longitude = '$pro_longitude'
	WHERE pro_id = ".$_GET["pro_id"];
	$q = $db->query($sql);

	header("Location:".$PHP_SELF."?pro_id=".$_GET["pro_id"]);
	}

if ($_GET["action"] == "replace_client" && $_GET["pro_pro_id"] && $_GET["cli_id"] && $_GET["pro_id"]) {
	// replace value in pro2cli table
	$sql = "UPDATE pro2cli SET p2c_pro = ".$_GET["pro_pro_id"]." WHERE p2c_cli = ".$_GET["cli_id"];

	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	unset($sql,$q);

	// if old pro_id is client's default address (cli_pro), replace
	$sql = "SELECT cli_pro FROM client WHERE cli_id = ".$_GET["cli_id"]." AND cli_pro = ".$_GET["pro_id"];
	$q = $db->query($sql);
	$numRows = $q->numRows();
	if ($numRows) {
		$sql2 = "UPDATE client SET cli_pro = '".$_GET["pro_pro_id"]."' WHERE cli_id = ".$_GET["cli_id"];
		$q = $db->query($sql2);
		}
	header("Location:".$PHP_SELF."?pro_id=".$_GET["pro_id"]);
	}


/*


how to re-assign a manually entered property to a genuine one:
postcode_tools already has the property id, which will also give all deals using it (retin with hidden field)
perform lookup (postcode or freetext) which will yeild a pro_id of entered (or detected) property
link to re-assign associated dela with new pro_id in the dea_prop field
------------------------------
allow manual edits to manually or automatcally entered property records.
if any info is changed on an automatically found record, pcid should be set to -1
------------------------------
include manually entered property records in postcode lookup
check property table first, match against postcode, house number and street name
check for exact matches on all. if none found, check for likely matches.
add array of proeprties from lookup


*/

// if manual proeprty is being entered
/*pro_id=6629&
lookup_type=by_postcode&
scope=pro&number=&
postcode=&
pro_pcid=-1&
pro_addr1=1&
pro_addr2=2&
pro_addr3=3&
pro_addr5=4&
pro_postcode=5&
pro_country=217&
form2=Save+Changes

*/
if ($_GET["pro_pcid"] == '-1') {
	$db_data["pro_pcid"] = '-1';
	$db_data["pro_addr1"] = format_street($_GET["pro_addr1"]);
	$db_data["pro_addr2"] = format_street($_GET["pro_addr2"]);
	$db_data["pro_addr3"] = format_street($_GET["pro_addr3"]);
	$db_data["pro_addr5"] = format_street($_GET["pro_addr5"]);
	$db_data["pro_postcode"] = format_postcode($_GET["pro_postcode"]);
	$db_data["pro_country"] = $_GET["pro_country"];

	$_GET["pro_pro_id"] = db_query($db_data,"INSERT","property","pro_id");
	}


// if a property id is given, get info from pro table, deal and client tables
if ($_GET["pro_id"]) {

	$sql = "SELECT
	dea_id,pro_pcid,
	CONCAT('<a href=tools/edit_property.php?pro_id=',pro_id,'>',property.pro_addr1,' ',property.pro_addr2,' ',property.pro_addr3,' ',property.pro_addr4,' ',property.pro_postcode,'</a>') AS Property,
	CONCAT('<a href=deal_summary.php?dea_id=',deal.dea_id,'>',property.pro_addr1,' ',property.pro_addr2,'  ',property.pro_addr3,' ',property.pro_postcode,' (',deal.dea_type,' / ',deal.dea_status,')</a>') AS Deal
	FROM
	property
	LEFT JOIN deal ON deal.dea_prop	= property.pro_id
	WHERE
	pro_id = ".$_GET["pro_id"];
	if ($_GET["dea_id"]) {
		$sql .= " AND deal.dea_id = ".$_GET["dea_id"];
		}

	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	//$prop = "<pre>";
	while ($row = $q->fetchRow()) {
		if ($row["dea_id"]) {
			$dea_id = $row["dea_id"];
			unset($row["dea_id"]);
			}
		if ($row["cli_id"]) {
			$cli_id = $row["cli_id"];
			unset($row["cli_id"]);
			}
		if ($row["pro_pcid"]) {
			$pro_pcid = $row["pro_pcid"];
			unset($row["pro_pcid"]);
			}
		if ($pro_pcid == '-1') {
			$valid = '<p style="color:red">This is a Manually Entered Address</p>';
			$inf = '(click to manually edit this address)';
			} else {
			$valid = '<p style="color:red">This is an Address from Royal Mail Database</p>';
			$inf = '(manual editing not allowed)';
			}

		//$prop .= print_r(str_replace("  "," ",$row),true);
		$prop = '<p>Address: '.$row["Property"].' '.$inf.'</p>';
		$deals .= '<li>'.$row["Deal"].'</li>';
		}

	//$prop .=  "</pre>";
	//$prop .= '<p><a href="tools/edit_property.php?pro_id='.$_GET["pro_id"].'">Edit Property Record</a></p>';
	//$prop .= '<p><a href="?action=delete_property&pro_id='.$_GET["pro_id"].'">Delete Property Record</a></p>';

	}

// show just entered property
if ($_GET["pro_pro_id"]) {

	$sql = "SELECT
	pro_addr1,pro_addr2,pro_addr3,pro_addr4,pro_addr5,pro_postcode,pro_authority,pro_east,pro_north
	FROM
	property
	WHERE
	pro_id = ".$_GET["pro_pro_id"];
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$render .= "<pre>";
	while ($row = $q->fetchRow()) {
		$render .= print_r($row,true);
		}
	$render .=  "</pre>";
	if ($dea_id) {
		$render .= '<p><a href="?action=replace_deal&pro_id='.$_GET["pro_id"].'&pro_pro_id='.$_GET["pro_pro_id"].'&dea_id='.$dea_id.'">Replace Address</a><br />This will replace the address for the above deal with the address you just looked up.</p>';
		$render .= '<p><a href="?action=replace_deal_location&pro_id='.$_GET["pro_id"].'&pro_pro_id='.$_GET["pro_pro_id"].'&dea_id='.$dea_id.'">Replace Map Location Only</a><br />This will update the current property record with the map location from the address you just looked up.</p>';
		$render .= '<p><a href="?pro_id='.$_GET["pro_id"].'">Start Again</a></p>';
		}
	/*
	if ($cli_id) {
		$render .= '<p><a href="?action=replace_client&pro_id='.$_GET["pro_id"].'&pro_pro_id='.$_GET["pro_pro_id"].'&cli_id='.$cli_id.'">Replace Client Property</a><br />This replaces the client\'s address with the address you just found</p>';
		}
	*/
	}



$form = new Form();
$form->addForm("form1","get",$PHP_SELF);
$form->addField("hidden","pro_id","",$_GET["pro_id"]);
// adding hidden fields for deal, pro2cli, pro2con, pro2com
$form->addField("hidden","dea_id","",$dea_id);
$form->addField("hidden","cli_id","",$cli_id);
$form->addField("hidden","pro2con","",$pro2con);
$form->addField("hidden","pro2com","",$pro2com);


$form->addHtml("<div id=\"standard_form\">\n");
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Postcode Lookup</div>');
$form->ajaxPostcode("by_freetext","pro");
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");

$form2 = new Form();
$form2->addForm("form2","get",$PHP_SELF);
$form2->addField("hidden","pro_id","",$_GET["pro_id"]);
$form2->addField("hidden","dea_id","",$_GET["dea_id"]);
$form2->addHtml("<div id=\"standard_form\">\n");
$form2->addHtml("<fieldset>\n");
$form2->addLegend('Postcode Lookup');
$form2->ajaxPostcode("by_postcode","pro");
$form2->addHtml("</fieldset>\n");
$form2->addHtml("</div>\n");


/*
if (!$_GET["stage"]) {

	$render = '
	<form method="get">
	<select name="lookup_type">
	<tr><td><a href="?stage=2&search_string="></a></td></tr>
	<tr><td><a href="?stage=2&search_string=browse">browse</a></td></tr>
	<tr><td><a href="?stage=2&search_string=by_freetext">by_freetext</a></td></tr>
	<tr><td><a href="?stage=2&search_string=by_postcode">by_postcode</a></td></tr>
	<tr><td><a href="?stage=2&search_string=by_area">by_area</a></td></tr>
	<tr><td><a href="?stage=2&search_string=by_localitykey">by_localitykey</a></td></tr>
	</select>
	<br>
	<input type="text" name="search_string">
	<input type="submit">
	</form>';

	$Data = array(
	'30486'=>'SE1',
	'30501'=>'SE2',
	'30521'=>'SE3',
	'30603'=>'SE4',
	'30618'=>'SE5',
	'30600'=>'SE6',
	'30590'=>'SE7',
	'30617'=>'SE8',
	'30480'=>'SE9',
	'30570'=>'SE10',
	'30608'=>'SE11',
	'30602'=>'SE12',
	'30583'=>'SE13',
	'30581'=>'SE14',
	'30604'=>'SE15',
	'30573'=>'SE16',
	'30500'=>'SE17',
	'30554'=>'SE18',
	'30479'=>'SE19',
	'30551'=>'SE20',
	'30632'=>'SE21',
	'30619'=>'SE22',
	'30558'=>'SE23',
	'30593'=>'SE24',
	'30550'=>'SE25',
	'30579'=>'SE26',
	'30614'=>'SE27',
	'30634'=>'SE28'

	);

	$render .= '<table border="1" cellpadding="5" cellspacing="0"><tr>
	';
	foreach ($Data as $keyd => $data) {
		$render .='<td><a href="?stage=2&search_string='.$keyd.'">'.trim($data).'</a></td>'."\n";
		$i++;
		if ($i % 5 == 0) {
			$render .= "</tr>\n<tr>\n";
			}
		}
	$render .= '</tr></table>';



	} elseif ($_GET["stage"] == 2) {

	$postcode = new Postcode();
	$Data = $postcode->lookup("by_localitykey",$_GET["search_string"],"data");

	$render = '
	<p><a href="?stage=">Back</a></p>
	<table border="1" cellpadding="5" cellspacing="0"><tr>
	';
	foreach ($Data as $keyd => $data) {
		$render .='<td><a href="?stage=3&search_string='.$data["id"].'&back_search='.$_GET["search_string"].'">'.trim($data["description"]).'</a></td>'."\n";
		$i++;
		if ($i % 3 == 0) {
			$render .= "</tr>\n<tr>\n";
			}
		}
	$render .= '</tr></table>';
	$render = '<p>'.$i.' Streets</p>'.$render;

	}

	elseif ($_GET["stage"] == 3) {

	$postcode = new Postcode();
	$Data = $postcode->lookup("by_streetkey",$_GET["search_string"],"data");
	$render = '
	<p><a href="?stage=2&search_string='.$_GET["back_search"].'">Back</a></p>
	<table border="1" cellpadding="5" cellspacing="0"><tr>
	';
	foreach ($Data as $keyd => $data) {
		$render .='<td><a href="?stage=4&search_string='.$data["id"].'">'.trim($data["description"]).'</a></td>'."\n";
		$i++;
		if ($i % 1 == 0) {
			$render .= "</tr>\n<tr>\n";
			}
		}
	$render .= '</tr></table>';
	$render = '<p>'.$i.' Properties</p>'.$render;
	}

	elseif ($_GET["stage"] == 4) {

	$postcode = new Postcode();
	$pro_id = $postcode->property($_GET["search_string"]);

	$sql = "SELECT * FROM property WHERE pro_id = $pro_id LIMIT 1";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$render = '
<a href="?">Start again</a>
<table width="500" border="1" cellspacing="0" cellpadding="5">
   <tr>
     <td>House or flat no.</td>
     <td><input type="text" name="pro_addr1" value="'.$row["pro_addr1"].'"></td>
   </tr>
   <tr>
     <td>Building name &amp; no.</td>
     <td><input type="text" name="pro_addr2" value="'.$row["pro_addr2"].'"></td>
   </tr>
   <tr>
     <td>Street</td>
     <td><input type="text" name="pro_addr3" value="'.$row["pro_addr3"].'"></td>
   </tr>
   <!--<tr>
     <td>Town</td>
     <td><input type="text" name="pro_addr5" value="'.$row["pro_addr5"].'"></td>
   </tr>-->
   <tr>
     <td>Postcode</td>
     <td><input type="text" name="pro_postcode" value="'.$row["pro_postcode"].'"></td>
   </tr>
 </table>


';

$map = new Map();
$map->drawMap($row["pro_east"],$row["pro_north"]);
$map->addLocator($row["pro_east"],$row["pro_north"]);
$render .= $map->renderMap();
		}


	}

*/
$page = new HTML_Page2($page_defaults);

$page->setTitle("Postcode Tools");
$page->addStyleSheet(getDefaultCss());
$page->addStyleDeclaration('pre { color:#000000; }');
$page->addScript('js/global.js');
$page->addScript('js/scriptaculous/prototype.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent('<h4>Address Tools</h4>');
/*
$page->addBodyContent('<p>This page is for editing and correcting manually added addresses, and for replacing incorrectoly chosen
addresses for property we are marketing, client addresses, and contact addresses.</p>
<p>You should see the address record below, this address will either be obtained from the Royal Mail, or be manually entered.</p>
<p>Addresses are entered manually becuase either the correct address is not in the Royal Mail database, or the user is too lazy to
make an effort. Every manually entered address needs to be searched again to see if it is present in the Royal Mail database, and
if it is, the Royal Mail address should be used instead of the manually entered one. If the address is not present, then we need
to enter the map location, and to do this we find the nearest property and use that map location.</p>
<p>Step 1: the address shown immediately below is the one we are working on. Please use the form to try and find that address in
the royal Mail database. If you find it, click the save changes button, and then click the link entitled "Replace Deal Property"
below</p>');
*/
$page->addBodyContent($valid);
$page->addBodyContent($prop);
if ($deals) {
$page->addBodyContent('<p>Used by the following deals:</p>');
$page->addBodyContent('<ol>'.$deals.'</ol>');
}

if (!$_GET["pro_pro_id"]) {

	$page->addBodyContent('<p>Please use the form below to try to find the EXACT address in the Royal Mail Database</p>');

	if ($_GET["type"] == "addr") {
		$page->addBodyContent($form->renderForm());
		} else {
		$page->addBodyContent($form2->renderForm());
		}

	if ($_GET["type"] == 'addr') {
		$page->addBodyContent('<p><a href="?pro_id='.$_GET["pro_id"].'&dea_id='.$_GET["dea_id"].'">Switch to Postcode Search</a></p>');
		} else {
		$page->addBodyContent('<p><a href="?pro_id='.$_GET["pro_id"].'&dea_id='.$_GET["dea_id"].'&type=addr">Switch to Freetext Search</a></p>');
		}


	$page->addBodyContent('

	<p>If you find the exact address, click "Use Selected" and "Save Changes", then click "Replace Deal Property" towards the bottom
	of this page. This will replace the manually entered address with the one you just found using the lookup tool. Alternatively, if
	the address is not present in Royal Mail, you can enter it manually and replace the current address in the same way. Manually
	entered address can be edited as long as they are not in use by any other part of the system (e.g. another deal)</p>

	<p>If you CANNOT find the exact address in Royal Mail, this could be because:</p>
	<ol>
	<li>The property has been divided into flats and the owner has not informed Royal Mail</li>
	<li>The property in question is a Room or House share, therefore the address will not be present</li>
	<li>The property is a new build which has not been registered with Royal Mail</li>
	</ol>
	<p>In these cases the address can remain manually entered, but we do have to specify the map location. This is done by finding the
	closest match. In the case of rooms or buildings that have been split into flats, you will usually be able to find the address for
	the whole building, which will have the same map location as any rooms or flats within. If you find the address of the whole
	building, click "Use Selected " and "Save Changes", then click the link at the bottom of this page that says "Replace Map Location",
	which will use the location of the address you found for the property address you are working on.</p>
	');

} else {

	$page->addBodyContent('
	<p>Below is the adress you just looked up.</p>
	');

}

$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();

?>