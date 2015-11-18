<?php
session_start();
$pageTitle = "Property";
require("global.php"); 
require("secure.php"); 
include("DB/Pager.php");
if (DB::isError($con = DB::connect($dsn))){
    die (DB::errorMessage($con));
}

if ($_GET["action"] == "Search") {

$strPageLink = "?action=Search";
$sql = "SELECT * FROM property, area, state_of_trade_let, proptype, branch WHERE property.SaleLet = 2 AND ";

if ($_GET["Keyword"]) { 
	$sqlKeyword = " ( ";
	$arrayKeyword = explode(",",$_GET["Keyword"]);
	for ($i = 0; $i < count($arrayKeyword); $i++) { 
		$sqlKeyword .= " property.prop_ID LIKE '%".trim($arrayKeyword[$i])."%' OR property.Address1 LIKE '%".trim($arrayKeyword[$i])."%' OR property.house_number LIKE '%".trim($arrayKeyword[$i])."%' OR property.description LIKE '%".trim($arrayKeyword[$i])."%' OR property.postcode LIKE '%".$arrayKeyword[$i]."%' OR area.area_title LIKE '%".$arrayKeyword[$i]."%' OR "	;
		}
	$sqlKeyword = substr($sqlKeyword,0,-3);
	$sqlKeyword .= " ) AND ";
	$strPageLink .= "&Keyword=".$_GET["Keyword"];
	$sql .= $sqlKeyword;
	}

if ($_GET["Area"]) { 
	$sqlArea = " ( ";
	$arrayArea = $_GET["Area"];
	for ($i = 0; $i < count($arrayArea); $i++) { 
		$sqlArea .= " property.area_id = ".$arrayArea[$i]." OR ";
		$strPageLink .= "&Area[]=".$arrayArea[$i];
		}
	$sqlArea = substr($sqlArea,0,-3);
	$sqlArea .= " ) AND ";
	$sql .= $sqlArea;
	}
	
if ($_GET["PropType"] && $_GET["PropType"] !== "Any") {
	if ($_GET["PropType"] == "House") {
		$sql .= " ( property.type_id = 1 OR property.type_id = 2 OR property.type_id = 5 OR property.type_id = 7) AND ";
		} 
	elseif ($_GET["PropType"] == "Apartment") {
		$sql .= " ( property.type_id = 3 OR property.type_id = 4 OR property.type_id = 6) AND ";
		} 
	else { 
		$sql .= " property.type_id = ".$_GET["PropType"]." AND ";
		}	
	$strPageLink .= "&PropType=".$_GET["PropType"];
	}



if ($_GET["Status"]) { 
	$sqlStatus = " ( ";
	$arrayStatus = $_GET["Status"];
	for ($i = 0; $i < count($arrayStatus); $i++) { 
		$sqlStatus .= " property.state_of_trade_id = ".$arrayStatus[$i]." OR ";
		$strPageLink .= "&Status[]=".$arrayStatus[$i];
		}
	$sqlStatus = substr($sqlStatus,0,-3);
	$sqlStatus .= " ) AND ";
	$sql .= $sqlStatus;
	}



if ($_GET["PriceFrom"]) {
$sql .= " property.price >= ".$_GET["PriceFrom"]." AND ";
$strPageLink .= "&PriceFrom=".$_GET["PriceFrom"];
}

if ($_GET["PriceTo"]) {
$sql .= " property.price <= ".$_GET["PriceTo"]." AND ";
$strPageLink .= "&PriceTo=".$_GET["PriceTo"];
}

if ($_GET["Negotiator"]) {
$sql .= " property.Neg = ".$_GET["Negotiator"]." AND ";
$strPageLink .= "&Negotiator=".$_GET["Negotiator"];
}

if ($_GET["Branch"]) {
$sql .= " property.Branch = ".$_GET["Branch"]." AND ";
$strPageLink .= "&Branch=".$_GET["Branch"];
}

if ($_GET["BedFrom"]) {
$sql .= " property.bedrooms >= ".$_GET["BedFrom"]." AND ";
$strPageLink .= "&BedFrom=".$_GET["BedFrom"];
}

if ($_GET["BedTo"]) {
$sql .= " property.bedrooms <= ".$_GET["BedTo"]." AND ";
$strPageLink .= "&BedTo=".$_GET["BedTo"];
}

// added 04/10/06
if ($_GET["board"]) {
$sql .= " property.board = '".$_GET["board"]."' AND ";
$strPageLink .= "&board=".$_GET["board"];
}

if ($_GET["DateFrom"]) {
	$DateFromArray = explode("/",$_GET["DateFrom"]); 
	$strDateFrom = $DateFromArray[2]."-".$DateFromArray[1]."-".$DateFromArray[0]." 00:00:00";
	$sql .= " property.Dates >= '".$strDateFrom."' AND ";
	$strPageLink .= "&DateFrom=".$_GET["DateFrom"];
	}

if ($_GET["DateTo"]) {
	$DateToArray = explode("/",$_GET["DateTo"]); 
	$strDateTo = $DateToArray[2]."-".$DateToArray[1]."-".$DateToArray[0]." 00:00:00";
	$sql .= " property.Dates <= '".$strDateTo."' AND ";
	$strPageLink .= "&DateTo=".$_GET["DateTo"];
	}
	

if (!$_GET["Order"]) {
	$strOrderBy = "property.price DESC";
	$strPageLinkORD = $strPageLink;
	$strPageLink .= "&Order=".$strOrderBy;
	}
else {
	$strOrderBy = $_GET["Order"];	
	$strPageLinkORD = $strPageLink;
	$strPageLink .= "&Order=".$strOrderBy;
	}

$sql .= " property.area_id = area.area_ID AND property.state_of_trade_id = state_of_trade_let.state_ID ";
$sql .= " AND property.type_id = proptype.type_ID  ";
$sql .= " AND property.Branch = branch.Branch_ID ORDER BY ".$strOrderBy;
echo '<!--'.$sql.'-->';



if (DB::isError($res = $con->query($sql))){
    die (DB::errorMessage($res));
}

// reults per page
if (!$_GET["limit"]) { 
	$limit = 10;
	} else {
	$limit = $_GET["limit"];
	}
	
if ($_GET["Layout"] == "plain") {
	$limit = 20;
	$strPageLink .= "&Layout=plain";
	}
elseif ($_GET["Layout"] == "print") {
	$limit = 12;
	$strPageLink .= "&Layout=print";
	}

$pager = new DB_Pager ($res, $from, $limit);

$data = $pager->build();
if (DB::isError($data)){
    die (DB::errorMessage($data));
}
if (!$data) {
// no results

// pages table cells
$pages = '            
	  <tr> 
		<td class="podTextCenter" colspan="2"><b>Found 0 Properties</b></td>
	  </tr>
	  ';

// no results message
	$render = 'No results';
		
} else {



// pages

// direct links to page numbers:
foreach ($data['pages'] as $page => $start_row) {
	if ($page <> $data['current']) {
    $pages .= "[<a href=\"$strPageLink&from=$start_row\">$page</a>] ";
	} else {
	$pages .= "<strong>[$page]</strong> ";
	}
}
// pages table cells
$pages = '            
	  <tr> 
		<td class="podTextCenter" colspan="2"><b>Found '.$data['numrows'].' Properties</b></td>
	  </tr>
	  <tr> 
		<td class="podTextCenter" colspan="2">Page: '.$pages.'</td>
	  </tr>'
	  ;



// Prev  and Next buttons
if ($from) { $prev = "<a href=\"$strPageLink&from=".$data['prev']."\">&lt Prev Page</a>";}
if ($data['next']) { $next = "<a href=\"$strPageLink&from=".$data['next']."\">Next Page &gt</a>";}

// start of table
$render = '		
        <table border="0" cellspacing="2" cellpadding="2" width="700">
		  <tr>
		    <td><a href="?">New Search</a> - '.$data['numrows'].' records found</td>
			<td align="right"><a href="index.php">Main Menu</a></td>
		  </tr>
          <tr> 
            <td align="left">Page: '.$data['current'].' of '.$data['numpages'].'</td>
            <td align="right" nowrap>'.$prev.' &nbsp; '.$next.'</td>
          </tr>
        </table>
';

// loop property results
while ($row = $pager->fetchRow(DB_FETCHMODE_ASSOC)){
	$pc = explode(" ",$row['Postcode']);


	if (!$_GET["Layout"]) {
	
	$render .= '
        <table border="0" cellspacing="2" cellpadding="2" width="700">
          <tr> 
            <td rowspan="3" valign="top" width="56"><a href="property.php?propID='.$row['prop_ID'].'&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'"><img src="'.$image_folder.get_thumb2($row['image0']).'" width="56" height="56" border="0" alt="'.$row['Address1'].'"></a></td>
            <td colspan="2"><strong>'.$row['description'].'</strong></td>
          </tr>
          <tr> 
            <td width="540">'.$row["house_number"].' '.$row['Address1'].', '.$pc[0].' '.$pc[1].'</td>
            <td width="100">'.$row["state_title"].'</td>
          </tr>
          <tr> 
		    <td width="540"><strong>'.price_format($row['Price']).'p/w</strong></td>
            <td width="100" nowrap><a href="print.php?propID='.$row['prop_ID'].'">Print</a> / <a href="property.php?propID='.$row['prop_ID'].'&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">Edit</a></td>
          </tr>
        </table>       
    	';
		}
	
	elseif ($_GET["Layout"] == "plain") {

		if ($counter == 0) {	
			$render .= '
			<table border="0" cellspacing="3" cellpadding="3" width="700">
			  <tr> 
				<td width="40"><strong>ID</strong></td> 
				<td width="100"><strong>Number</strong></td>
				<td><strong>Address</strong></td>
				<td width="70"><strong>Status</strong></td>
				<td><strong>Price</td>
				<td width="100" nowrap>&nbsp;</td>
			  </tr>             
			  ';
			}
		$render .= '	
		  <tr> 
			<td width="40">'.$row["prop_ID"].'</td> 
			<td width="80"><span title="'.$row["house_number"].'">'.substr($row["house_number"],0,12).'</span></td>
			<td>'.$row['Address1'].', '.$pc[0].' '.$pc[1].'</td>
			<td width="70">'.$row["state_title"].'</td>
			<td>'.price_format($row['Price']).' - '.$row["leaseFree_Name"].'</td>
			<td width="100" nowrap><a href="print.php?propID='.$row['prop_ID'].'">Print</a> / <a href="property.php?propID='.$row['prop_ID'].'&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">Edit</a></td>
		  </tr>
		';
		$counter++;
		}
	
	elseif ($_GET["Layout"] == "print") {
		
		$render .= '
        <table border="0" cellspacing="2" cellpadding="2" width="700">
          <tr> 
            <td rowspan="3" valign="top" width="56"><a href="property.php?propID='.$row['prop_ID'].'&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'"><img src="'.$image_folder.get_thumb2($row['image0']).'" width="56" height="56" border="0" alt="'.$row['Address1'].'"></a></td>
            <td colspan="3"><strong>'.$row['description'].'</strong></td>
          </tr>
          <tr> 
            <td width="340">'.$row['Address1'].', '.$pc[0].'</td>
            <td width="150"><span class="greyText">Bedrooms:</span> '.$row["bedrooms"].'</td>
            <td width="150"><span class="greyText">Parking:</span> '.$row["parking"].'</td>
          </tr>
          <tr> 
		    <td width="340"><strong>'.price_format($row['Price']).'p/w</strong></td>
            <td width="150"><span class="greyText">Receptions:</span> '.$row["receptions"].'</td>
            <td width="150"><span class="greyText">Garden:</span> '.$row["garden"].'</td>
          </tr>
		  <tr> 
		    <td colspan="4" bgcolor="#999999"></td>
          </tr>
        </table>
		       
    	';
		}
	}
// add end of table

$render .= '		
        <table border="0" cellspacing="2" cellpadding="2" width="700">
          <tr> 
            <td align="left">Page: '.$data['current'].' of '.$data['numpages'].'</td>
            <td align="right" nowrap>'.$prev.' &nbsp; '.$next.'</td>
          </tr>
        </table>
';
}
echo html_header("Search Results");
echo '<div align="center">
<table>'.$render.'</table>
</div>';



}
else {
echo html_header("Property Search");

?>
<div align="center">
<form name="form" method="GET">
    <table width="650" border="0" cellpadding="5" cellspacing="3">
      <tr> 
        <td class="pageTitle">Lettings Property Search</td>
        <td align="right"><a href="index.php">Main Menu</a></td>
      </tr>
    </table>
    <table width="650" border="0" cellpadding="4" cellspacing="2">
      <tr> 
        <td align="right" class="greyText">Keyword(s)</td>
        <td colspan="3"><input name="Keyword" type="text" style="width:530px" value=""></td>
      </tr>
      <tr> 
        <td align="right" class="greyText">Price&nbsp;Range</td>
        <td nowrap><input name="PriceFrom" type="text" style="width:90px" onKeyPress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;">
          to 
          <input name="PriceTo" type="text" style="width:90px" onKeyPress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;"></td>
        <td align="right" class="greyText">Negotiator</td>
        <td><select name="Negotiator" style="width:200px">
            <option value="" selected>Any</option>
            <?php
			$sqlNeg = "SELECT * FROM staff WHERE (Staff_Type = 'SalesNegotiator' OR Staff_type = 'LettingsNegotiator') AND Staff_Status = 'Current' ORDER BY Staff_Fname";
			$qNeg = $db->query($sqlNeg);
			if (DB::isError($qNeg)) {  die("insert error: ".$qNeg->getMessage()); }
			
			while ($rowNeg = $qNeg->fetchRow()) {		
				echo '<option value="'.$rowNeg["Staff_ID"].'"';
				if ($intNeg == $rowNeg["Staff_ID"]) {
					echo ' selected';
					}
				echo '>'.$rowNeg["Staff_Fname"].' '.$rowNeg["Staff_Sname"].'</option>';
				}
		?>
          </select> </td>
      </tr>
      <tr> 
        <td align="right" class="greyText">Date&nbsp;Range</td>
        <td nowrap><input type="text" name="DateFrom"  style="width:90px" readonly=true onClick="popUpCalendar(this, form.DateFrom, 'dd/mm/yyyy')">
          to 
          <input type="text" name="DateTo" style="width:90px" readonly=true onClick="popUpCalendar(this, form.DateTo, 'dd/mm/yyyy')"></td>
        <td align="right" class="greyText">Branch</td>
        <td><select name="Branch" style="width:200px">
            <option value="" selected>Any</option>
            <?php
			$sqlBranch = "SELECT * FROM branch ORDER BY Branch_Title";
			$qBranch = $db->query($sqlBranch);
			if (DB::isError($qBranch)) {  die("insert error: ".$qBranch->getMessage()); }
			
			while ($rowBranch = $qBranch->fetchRow()) {		
				echo '<option value="'.$rowBranch["Branch_ID"].'"';
				if ($intBranch == $rowBranch["Branch_ID"]) {
					echo ' selected';
					}
				echo '>'.$rowBranch["Branch_Title"].'</option>';
				}
			?>
          </select></td>
      </tr>
      <tr> 
        <td align="right" class="greyText">Bedrooms</td>
        <td nowrap><select name="BedFrom" style="width:90px">
            <option value="" selected>Any</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
          </select>
          to 
          <select name="BedTo" style="width:90px">
            <option value="" selected>Any</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
          </select></td>
        <td align="right" class="greyText">Property&nbsp;Type</td>
        <td><select name="PropType" style="width:200px">
            <option value="" selected>Any</option>
            <option value="House">Houses</option>
            <option value="Apartment">Apartments</option>
            <option value="">--------</option>
            <?php
			$sqlPropType = "SELECT type_ID, type_Title FROM proptype";
			$qPropType = $db->query($sqlPropType);
			if (DB::isError($qPropType)) {  die("insert error: ".$qPropType->getMessage()); }
			
			while ($rowPropType = $qPropType->fetchRow()) {		
				echo '<option value="'.$rowPropType["type_ID"].'"';
				if ($intType == $rowPropType["type_ID"]) {
					echo ' selected';
					}
				echo '>'.$rowPropType["type_Title"].'</option>';
				}
			?>
          </select> </td>
      </tr>
      <tr>
        <td align="right" class="greyText">&nbsp;</td>
        <td nowrap>&nbsp;</td>
        <td align="right" class="greyText">Board
            <!--Cards--></td>
        <td><span class="greyForm">
          
          <select name="board" style="width: 200px;">
            <option value="" selected>Any</option>
            <?php echo db_enum("property","board",$intBoard); ?>
          </select>
          </span>
            <!--<select name="card" style="width: 200px;">
            <option value="" selected>Any</option>
            <option value="1">Not Created</option>
            <option value="2">Created</option>
            <option value="3">Proofed</option>
          </select>--></td>
      </tr>
      <tr> 
        <td align="right" class="greyText">Status</td>
        <td colspan="3"> <table width="100%">
            <tr> 
            <?php		  	
		  	$sqlStatus = "SELECT state_ID, state_Title FROM state_of_trade_let WHERE state_ID <> 6";
			$qStatus = $db->query($sqlStatus);
			if (DB::isError($qStatus)) {  die("insert error: ".$qStatus->getMessage()); }
			
			while ($rowStatus = $qStatus->fetchRow()) {		
				echo '<td><input type="checkbox" name="Status[]" value="'.$rowStatus["state_ID"].'"';
				if ($intStatus == $rowStatus["state_ID"]) {
					echo ' checked';
					}
				echo '>'.$rowStatus["state_Title"].'</td>';							
			
				$i++;
				if ($i % 4 == 0) 
					{
					echo '
					</tr>
					<tr>';
					}
				}
			?>
		  
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" align="center"><input type="submit" value="Search Property"> 
          <input type="hidden" name="action" value="Search"> <input type="reset" value="Reset"></td>
      </tr>
      <tr> 
        <td colspan="4"> <table width="100%">
            <tr>
			<?php
			$sqlArea = "SELECT area_ID, area_title, area_pc FROM area ORDER BY area_title";
			$qArea = $db->query($sqlArea);
			if (DB::isError($qArea)) {  die("insert error: ".$qArea->getMessage()); }
			
			while ($rowArea = $qArea->fetchRow()) {		
				echo '<td><input type="checkbox" name="Area[]" value="'.$rowArea["area_ID"].'"';
				if ($intArea == $rowArea["area_ID"]) {
					echo ' checked';
					}
				echo '>'.$rowArea["area_title"].'</td>';							
			
				$i++;
				if ($i % 4 == 0) 
					{
					echo '
					</tr>
					<tr>';
					}
				}
			?>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" align="center"><input type="submit" value="Search Property"> 
          <input type="hidden" name="action" value="Search"> <input type="reset" value="Reset"></td>
      </tr>
    </table>
    <table width="650" border="0" cellpadding="5" cellspacing="3">
      <tr> 
        <td colspan="4" class="head">Display Options</td>
      </tr>
      <tr> 
        <td valign="top">Order by</td>
        <td><select name="Order" style="width:200px">
            <option value="property.price DESC">Price (cheapest last)</option>
            <option value="property.price ASC">Price (cheapest first)</option>
            <option value="property.Address1 ASC">Street (a - z)</option>
            <option value="property.Address1 DESC">Street (z - a)</option>
            <option value="area.area_title ASC">Area (a - z)</option>
            <option value="area.area_title DESC">Area (z - a)</option>
            <option value="property.state_of_trade_id ASC">Status</option>
            <option value="property.Dates DESC" selected>Date (newest first)</option>
            <option value="property.Dates ASC">Date (newest last)</option>
          </select></td>
        <td>Layout</td>
        <td><select name="Layout">
            <option value="">Normal (thumbnail list)</option>
			<option value="print">Printable List (thumbnail list)</option>
            <option value="plain">Plain (text only)</option>
          </select></td>
      </tr>
    </table>
</form>

</div>
</body>
</html>
<?php } ?>