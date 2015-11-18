<?php
/*
Map class - started 23rd November 2006 with static 500m2 tiles

Draw a map (3x3 image tiles) with given coordinates within centre sqaure
Add locators with an image (can be visible or invisible)
Add property (will contain property details for rollover, href, etc)
Add navigation (north, south, east, west - in vrious formats)
Only add location if they are withing the visible map area (this should be done by DB too)

future thoughts:

*/

class Map {

	var $x; // easting
	var $y; // northing


// constructor
function Map() {
	$this->map_folder = IMAGE_URL_MAPPING;
	}
	
	
// create the map background images 
function drawMap($x,$y) {	

	$basex = $this->map_base($x);
	$basey = $this->map_base($y);	
	
	// define the base of this current map as globals
	define(BASEX,$basex);
	define(BASEY,$basey);
	
	$tile[0] = ($basex - 500)."x".($basey + 500).".gif";
	$tile[1] = ($basex)."x".($basey + 500).".gif";
	$tile[2] = ($basex + 500)."x".($basey + 500).".gif";
	$tile[3] = ($basex - 500)."x".($basey).".gif";
	$tile[4] = ($basex)."x".($basey).".gif";
	$tile[5] = ($basex + 500)."x".($basey).".gif";
	$tile[6] = ($basex - 500)."x".($basey - 500).".gif";
	$tile[7] = ($basex)."x".($basey - 500).".gif";
	$tile[8] = ($basex + 500)."x".($basey - 500).".gif";
	
	$counter = 1;
	foreach ($tile AS $id=>$image) {

		$map_table .= '<td><img src="'.$this->map_folder.$image.'" name="tile1" width="187" height="187" border="0" id="tile'.$counter.'"></td>'."\n";
		
		if($counter % 3==0) {
			$map_table .=  "</tr>\n<tr>\n";
			}
	
		$counter++;
		}
		
		
	$_output = '<table id="mapTable">'."\n".
	'<tr>'."\n".$map_table.
	'<td colspan="3"><img src="'.$this->map_folder.'copyright.gif" alt="Copyright Collins Bartholomew Ltd 2003" width="561" height="9" border="0"></td>'."\n".
	'</tr>'."\n".'</table>';
	
	$this->output.=$_output;
	}
	
// returns base coordinates to nearest 500
function map_base($_coord) {	
	$_chr4 = substr($_coord,3,1); 
	if ($_chr4 < 5) { $_chr4 = 0; } else { $_chr4 = 5; }
	$_result = substr($_coord,0,3) . $_chr4 . "00";
	return $_result;
	}
	
// add locator point to output
function addLocator($x,$y) {
	
	$basex = $this->map_base($x);
	$basey = $this->map_base($y);
	
	$locationimage = "/images/mapping/dot4.gif"; 
	$pixelwidth = 12;
	$pixelheight = 12;	

	$x = (($x - $basex) + 500);
	$x = round($x / 2.675);
	$x = ($x - ($pixelwidth/2)); // minus n to compensate dot size, this is 50% dimensions of dot
	
	$y = ($y - $basey);
	$y = (1000 - $y);
	$y = round($y / 2.65);
	$y = ($y - ($pixelheight/2));
	
	$_locators = '<div id="locator" style="position:absolute; left:'.$x.'px; top:'.$y.'px; width:'.$pixelwidth.'px; height:'.$pixelheight.'px; z-index:2"><img src="'.$locationimage.'" width="'.$pixelwidth.'" height="'.$pixelheight.'"></div>'."\n";
	
	$this->locators.=$_locators;
	}


// add property point to output
function addProperty($dea_id) {
	
	
	
	$_sql = "SELECT 
	dea_status,dea_marketprice,dea_bedroom,dea_type,pty_title,
	pro_addr3,pro_area,LEFT(pro_postcode,4) AS pro_postcode,pro_east,pro_north
	FROM deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	LEFT JOIN ptype ON deal.dea_psubtype = ptype.pty_id
	WHERE deal.dea_id = $dea_id
	LIMIT 1";
	$_result = mysql_query($_sql);	
	if (!$_result)
	die("MySQL Error:  ".mysql_error()."<pre>db_query: ".$_sql."</pre>");
	while($row = mysql_fetch_array($_result)) {	
		
		$ptype = $row["pty_title"];	
		$x = $row["pro_north"];
		$y = $row["pro_east"];
		
		$overinfo = $row["pro_addr3"].' '.$row["pro_postcode"].'<br />'.
		$row["dea_bedroom"].' Bed '.$row["pty_title"].'<br />';
		
		if ($row["dea_status"] == "Available") {
			$overinfo .= format_price($row["dea_marketprice"]);
			$locationimage = "/images/mapping/dot4.gif"; 			
			}
		elseif ($row["dea_status"] == "Under Offer") {
			$overinfo .= '(Under Offer)';
			$locationimage = "/images/mapping/dot3.gif"; 
			}
		elseif ($row["dea_status"] == "Exchanged") {
			$overinfo .= '(Sold)';
			$locationimage = "/images/mapping/dot3.gif"; 
			}
		
		if ($row["dea_type"] == "Sales") {
			$link = 'Detail.php?propID='.$dea_id;
			} 
		elseif ($row["dea_type"] == "Sales") {
			$link = 'DetailLet.php?propID='.$dea_id;
			} 
			
		}
	
	$basex = $this->map_base($x);
	$basey = $this->map_base($y);	
	
	$pixelwidth = 12;
	$pixelheight = 12;	

	$x = (($x - BASEX) + 500);
	$x = round($x / 2.675);
	$x = ($x - ($pixelwidth/2)); // minus n to compensate dot size, this is 50% dimensions of dot
	
	$y = ($y - BASEY);
	$y = (1000 - $y);
	$y = round($y / 2.65);
	$y = ($y - ($pixelheight/2));
	
	$_locators = '<div id="locator_'.$dea_id.'" style="position:absolute; left:'.$x.'px; top:'.$y.'px; width:'.$pixelwidth.'px; height:'.$pixelheight.'px; z-index:2"><a href="'.$link.'"><img src="'.$locationimage.'" width="'.$pixelwidth.'" height="'.$pixelheight.'" border="0" id="location_'.$dea_id.'" alt="'.$overinfo.'" /></a></div>'."\n";
	
	$this->locators.=$_locators;
	}

// return count of property within map display area
function countProperty($x,$y,$dea_id) {
	
	// $dea_id, get dea_type and only show similar
	$sql = "SELECT dea_type FROM deal WHERE dea_id = $dea_id";
	$result = mysql_query($sql);	
	while($row = mysql_fetch_array($result)) {	
		$dea_type = $row["dea_type"];
		}
	
	
	$basex = $this->map_base($x);
	$basey = $this->map_base($y);
	
	$sql = "SELECT COUNT(pro_id) AS pro_count FROM 
	deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	WHERE 
	(pro_east >= ".($basex - 500)." AND pro_east <= ".($basex + 1000)." AND pro_north >= ".($basey - 500)." AND pro_north <= ".($basey + 1000).") 
	AND (dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Exchanged') 
	AND dea_type = '$dea_type'
	AND dea_id != $dea_id";	
	$result = mysql_query($sql);	
	while($row = mysql_fetch_array($result)) {	
		$pro_count = $row["pro_count"];
		}
	return $pro_count;
	
	}
	
// return array of property within map display area
function getProperty($x,$y,$dea_id) {
	
	// $dea_id, get dea_type and only show similar
	$sql = "SELECT dea_type FROM deal WHERE dea_id = $dea_id";
	$result = mysql_query($sql);	
	while($row = mysql_fetch_array($result)) {	
		$dea_type = $row["dea_type"];
		}
	
	
	$basex = $this->map_base($x);
	$basey = $this->map_base($y);
	
	$sql = "SELECT * FROM 
	deal
	LEFT JOIN property ON deal.dea_prop = property.pro_id
	WHERE 
	(pro_east >= ".($basex - 500)." AND pro_east <= ".($basex + 1000)." AND pro_north >= ".($basey - 500)." AND pro_north <= ".($basey + 1000).") 
	AND (dea_status = 'Available' OR dea_status = 'Under Offer' OR dea_status = 'Exchanged') 
	AND dea_type = '$dea_type'
	AND dea_id != $dea_id";	
	$result = mysql_query($sql);	
	while($row = mysql_fetch_array($result)) {	
		$return[$row["dea_id"]] = array(
			'x'=>$row["pro_east"],
			'y'=>$row["pro_north"]
			);
		}
	return $return;
	
	}
	
	
// return the full output
function renderMap(){		
	return '<div id="map_container" style="position:relative; width: 561px; height: 561px;">'."\n".$this->locators.$this->output.'</div>'."\n";
	}	
	
} // end of class



function map_base($_coordinate) {// input coords to get base tile filename	
	$_output = substr($_coordinate,3,1); 
	if ($_output < 5) { $_output = 0; } else { $_output = 5; }
	$_result = substr($_coordinate,0,3) . $_output . "00";
	return $_result;
	}
?>