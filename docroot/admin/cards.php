<?php
session_start();
$pageTitle = "Property";
require("global.php"); 
require("secure.php"); 
require("queryLog.php");
include("DB/Pager.php");
if (DB::isError($con = DB::connect($dsn))){
    die (DB::errorMessage($con));
}

if ($_POST["searchLink"]) {
	$searchLink = $_POST["searchLink"];
	} else {
	$searchLink = $_GET["searchLink"];
	}
// values used in queryLog
$_table = "property";
$_field = "prop_ID";

if ($_GET["action"] == "Update") {
	if ($_GET["status"] == "Created") {		
		$fieldnames[] = "card";
		$newvalues[] = 'Created';		
		queryLog($fieldnames,$newvalues,$_table,$_field,$_GET["propID"],"Update");
		header("Location:".urldecode($searchLink));
		
		} elseif ($_GET["status"] == "Proofed") {
		$fieldnames[] = "card";
		$newvalues[] = 'Proofed';		
		queryLog($fieldnames,$newvalues,$_table,$_field,$_GET["propID"],"Update");			
		header("Location:".urldecode($searchLink));
		
		} elseif ($_GET["status"] == "Insufficient") {
		$fieldnames[] = "card";
		$newvalues[] = 'Insufficient Images';		
		queryLog($fieldnames,$newvalues,$_table,$_field,$_GET["propID"],"Update");			
		header("Location:".urldecode($searchLink));
		
		} elseif ($_GET["status"] == "Mistakes") {
		$fieldnames[] = "card";
		$newvalues[] = 'Mistakes';
		$fieldnames[] = "card_notes";
		$newvalues[] = $_GET["card_notes"];		
		queryLog($fieldnames,$newvalues,$_table,$_field,$_GET["propID"],"Update");
		header("Location:".urldecode($searchLink));
		}
		 
		
		
	}

elseif ($_GET["action"] == "Search") {

$strPageLink = "?action=Search";
$sql = "SELECT * FROM property, area, state_of_trade, proptype, branch WHERE ";

if ($_GET["Keyword"]) { 
	$sqlKeyword = " ( ";
	$arrayKeyword = explode(",",$_GET["Keyword"]);
	for ($i = 0; $i < count($arrayKeyword); $i++) { 
		$sqlKeyword .= " property.prop_ID LIKE '%".trim($arrayKeyword[$i])."%' OR property.Address1 LIKE '%".trim($arrayKeyword[$i])."%' OR property.house_number LIKE '%".trim($arrayKeyword[$i])."%' OR property.postcode LIKE '%".$arrayKeyword[$i]."%' OR area.area_title LIKE '%".$arrayKeyword[$i]."%' OR "	;
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
	
// added 18/07/05
if ($_GET["M2From"]) {
$sql .= " property.total_area >= ".$_GET["M2From"]." AND ";
$strPageLink .= "&M2From=".$_GET["M2From"];
}

if ($_GET["M2To"]) {
$sql .= " property.total_area <= ".$_GET["M2To"]." AND ";
$strPageLink .= "&M2To=".$_GET["M2To"];
}

// added 02/11/05
if ($_GET["card"]) {
$sql .= " property.card = ".$_GET["card"]." AND ";
$strPageLink .= "&card=".$_GET["card"];
}

if (!$_GET["Order"]) {
	$strOrderBy = "property.Dates DESC";
	$strPageLinkORD = $strPageLink;
	$strPageLink .= "&Order=".$strOrderBy;
	}
else {
	$strOrderBy = $_GET["Order"];	
	$strPageLinkORD = $strPageLink;
	$strPageLink .= "&Order=".$strOrderBy;
	}

$sql .= " property.area_id = area.area_ID AND property.state_of_trade_id = state_of_trade.state_ID ";
$sql .= " AND property.type_id = proptype.type_ID ";
$sql .= " AND property.Branch = branch.Branch_ID ORDER BY ".$strOrderBy;
//echo $sql;



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
	$render = 'No results - <a href="cards.php">New search</a>';
		
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
		    <td><a href="cards.php">New Search</a> - '.$data['numrows'].' records found</td>
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
	$intBranch = $row["Branch_ID"];		
	if ($intBranch == 1) {
		$strCardFolder = "CardsShip";
		} elseif ($intBranch == 2) {
		$strCardFolder = "CardsSyd";
		} elseif ($intBranch == 3) {
		$strCardFolder = "CardsShad";
		} 
		
	if ($intCard == "Created" || $intCard == "Proofed") { 
		$renderCard = ' - <a href="P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf" target="_blank">PDF</a>'; 
		}

	if (!$_GET["Layout"]) {
	
	/*
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
		    <td width="540"><strong>'.price_format($row['Price']).'</strong> - '.$row["leaseFree_Name"].'</td>
            <td width="100" nowrap><a href="print.php?propID='.$row['prop_ID'].'">Print</a> / <a href="property.php?propID='.$row['prop_ID'].'&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">Edit</a></td>
          </tr>
        </table>       
    ';
	*/
	}
	elseif ($_GET["Layout"] == "plain") {

	if ($counter == 0) {	
		$render .= '
        <table border="1" cellspacing="3" cellpadding="3" width="700">
		<tr>
		<td><strong>ID</strong></td>
		<td><strong>Branch</strong></td>
		<td><strong>Address</strong></td>
		<td><strong>Area</strong></td>
		<td><strong>Postcode</strong></td>
		<td><strong>Price</strong></td>
		<td><strong>Recs</strong></td>
		<td><strong>Beds</strong></td>
		</tr>';
            
    	  
		}
	$render .= '	
	  <tr height="30"> 
	  <form method="GET">
	  <input type="hidden" name="propID" value="'.$row["prop_ID"].'">
		<td><a href="property.php?propID='.$row["prop_ID"].'">'.$row["prop_ID"].'</a></td>
		<td>'.$row['Branch_Title'].'</td>
		<td>'.$row['Address1'].'</td>
		<td>'.$row['area_title'].'</td>
		<td>'.$pc[0].'</td>
		<td>'.price_format($row['Price']).'</td>
		<td>'.$row["receptions"].' rec</td>
		<td>'.$row["bedrooms"].' bed</td>';
		if ($row['card'] == "Not Created") {
			$render .= '<td width="5%" align="center">
			<input type="hidden" name="searchLink" value="'.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">
			<input type="submit" name="action" value="Create" style="width:60px"></td>';
			} elseif ($row['card'] == "Created") {
			$render .= '<td width="5%" align="center">
			<input type="hidden" name="searchLink" value="'.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">
			<input type="submit" name="action" value="Proof" style="width:60px"></td>';
			} elseif ($row['card'] == "Proofed") {
			$render .= '<td width="5%" align="center">Complete</td>';
			} elseif ($row['card'] == 'Insufficient Images') {
			$render .= '<td width="5%" align="center"><a href="?action=Create&propID='.$row["prop_ID"].'&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">Insufficient Images</a></td>';
			} elseif ($row['card'] == "Mistakes") {
			$render .= '<td width="5%" align="center">
			<input type="hidden" name="searchLink" value="'.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">
			<input type="submit" name="action" value="Mistakes" style="width:60px"></td>';
			}
		$render .= '
	  </form>
	  </tr>
	';
	$counter++;
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
elseif ($_GET["action"] == "Create") {
$intPropID = $_GET["propID"];

$sql = "SELECT * FROM property, area, state_of_trade, proptype, branch WHERE property.prop_ID = ".$intPropID." AND ";
$sql .= " property.area_id = area.area_ID AND property.state_of_trade_id = state_of_trade.state_ID ";
$sql .= " AND property.type_id = proptype.type_ID ";
$sql .= " AND property.Branch = branch.Branch_ID";

if (DB::isError($res = $con->query($sql))){
    die (DB::errorMessage($res));
}
$from = isset($_GET['from']) ? (int)$_GET['from'] : 0;
$limit = 1;
$pager = new DB_Pager ($res, $from, $limit);

$data = $pager->build();
if (DB::isError($data)){
    die (DB::errorMessage($data));
}
while ($row = $pager->fetchRow(DB_FETCHMODE_ASSOC)){

	$pc = explode(" ",$row['Postcode']);
	$intOSX = $row["osx"];
	$intOSY = $row["osy"];
	
	$SQLTrans = "SELECT places.place_ID, places.place_type, places.place_title, places.place_desc, places.place_osx, places.place_osy, pl_type.pl_type_id, pl_type_title FROM places, pl_type WHERE (places.place_type = 1 OR places.place_type = 2) AND places.place_type = pl_type.pl_type_id  ORDER BY sqrt((abs(places.place_osx-".$intOSX.")*abs(places.place_osx-".$intOSX."))+(abs(places.place_osy-".$intOSY.")*abs(places.place_osy-".$intOSY."))) LIMIT 1";
	$rowTrans = $con->getRow($SQLTrans, DB_FETCHMODE_ASSOC);
	
	if (DB::isError($resTrans = $con->query($SQLTrans))){
		die (DB::errorMessage($resTrans));
	}
	
	$intTransType = $rowTrans['place_type'];
	$strTransType = $rowTrans['pl_type_title'];
	$strTransTitle = $rowTrans['place_title'];
	$strTransDesc = $rowTrans['place_desc'];
	$intTransOSX = $rowTrans['place_osx'];
	$intTransOSY = $rowTrans['place_osy'];
	
	if ($strTransType == "Station") {
		$strTransType = "trains";
		}
	
	$intTransDistance = round(sqrt((abs($intTransOSX-$intOSX)*abs($intTransOSX-$intOSX))+(abs($intTransOSY-$intOSY)*abs($intTransOSY-$intOSY))));
	
	if ($intTransType == 1) {
		$strTrans = '<img src="http://www.woosterstock.co.uk/images/mapping/rail.gif" width="17" height="10" align="absmiddle">&nbsp;<b>'.$strTransTitle.'</b><br><font color="#666666">(approx. '.$intTransDistance.' meters)</font>';
		} elseif ($intTransType == 2) {
		$strTrans = '<img src="http://www.woosterstock.co.uk/images/mapping/tube.gif" width="12" height="10" align="absmiddle">&nbsp;<b>'.$strTransTitle.'</b><br><font color="#666666">(approx. '.$intTransDistance.' meters)</font>';
		}

	$intBranch = $row["Branch_ID"];		
	if ($intBranch == 1) {
		$strCardFolder = "CardsShip";
		} elseif ($intBranch == 2) {
		$strCardFolder = "CardsSyd";
		} elseif ($intBranch == 3) {
		$strCardFolder = "CardsShad";
		} 
		
	if ($intCard == "Created" || $intCard == "Proofed") { 
		$renderCard = ' - <a href="P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf" target="_blank">PDF</a>'; 
		}

	$intSaleLet = $row["SaleLet"];
	if ($intSaleLet == 1) {
		$template = 'P:\\'.$strCardFolder.'\\templates\sales.indt';
		} elseif ($intSaleLet == 2) {
		$template = 'P:\\'.$strCardFolder.'\\templates\lettings.indt';
		}
	
	$render = '
	<p>Create a new card from <a href="'.$template.'">'.$template.'</a> with the following details:</p>
	<table width="400">
	<tr>
	<td valign="top">Front Page:</td>
	<td><span class="footerTitle">'.$row['Address1'].', '.$pc[0].'<br>'.price_format($row['Price']);
	if ($intSaleLet == 2) {
		$render .= ' p/w';
		}
	$render .= '</span><br><br><br></td>
	</tr>
	<tr>
	<td valign="top">Back Page:</td>
	<td><span class="footerTitle">'.$row['Address1'].'<br>'.$row["area_title"].'</span><br><br>
	<span class="greyText">Price:</span><br> '.price_format($row['Price']);
	if ($intSaleLet == 2) {
		$render .= ' per week / '.price_format(pw2pcm($row['Price'])).' per month';
		}
	$render .= '<br><br>';
	if ($intSaleLet == 1) {
		if ($row["lease_free"] == 1) {
			$leasefree = "Freehold";
			} elseif ($row["lease_free"] == 2) {
			$leasefree = "Leasehold";
			} elseif ($row["lease_free"] == 3) {
			$leasefree = "Share of Freehold";
			}
		$render .='<span class="greyText">Tenure:</span><br>'.$leasefree.'<br><br>';
	} elseif ($intSaleLet == 2) {
		if ($row["furnished"] == 1) {
			$furnished = "Unfurnished";
			} elseif ($row["furnished"] == 2) {
			$furnished = "Part-furnished";
			} elseif ($row["furnished"] == 3) {
			$furnished = "Furnished";
			} elseif ($row["furnished"] == 4) {
			$furnished = "Furnished or unfurnished";
			}
			$render .='<span class="greyText">Furnished:</span><br>'.$furnished.'<br><br>';
			}
	
	if ($row["total_area"]) {
		$render .= '<span class="greyText">Internal Area:</span><br>Approx. '.round($row["total_area"]).' square meters<br><br>';
		}
	$render .='
	<span class="greyText">Nearest Transport:</span><br>'.$strTransTitle.' '.strtolower($strTransType).'</td>
	</tr>
	</table>
	<p>When you have completed the card in InDesign, save it as P:\\'.$strCardFolder.'\\'.$intPropID.'.indd<br>
	Then export the file and save it as P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf<br>
	When you have finished the card, click <a href="?propID='.$intPropID.'&action=Update&status=Created&searchLink='.$searchLink.'">HERE</a></p>
	<p>If you have insufficient images to create a card, please click <a href="?propID='.$intPropID.'&action=Update&status=Insufficient&searchLink='.$searchLink.'">HERE</a></p>';	
	}


echo html_header("Create Card");
echo '<div align="center">
'.$render.'
</div>';
}
elseif ($_GET["action"] == "Proof") {
$intPropID = $_GET["propID"];

$sql = "SELECT * FROM property, area, state_of_trade, proptype, branch WHERE property.prop_ID = ".$intPropID." AND ";
$sql .= " property.area_id = area.area_ID AND property.state_of_trade_id = state_of_trade.state_ID ";
$sql .= " AND property.type_id = proptype.type_ID ";
$sql .= " AND property.Branch = branch.Branch_ID";

if (DB::isError($res = $con->query($sql))){
    die (DB::errorMessage($res));
}
$from = isset($_GET['from']) ? (int)$_GET['from'] : 0;
$limit = 1;
$pager = new DB_Pager ($res, $from, $limit);

$data = $pager->build();
if (DB::isError($data)){
    die (DB::errorMessage($data));
}
while ($row = $pager->fetchRow(DB_FETCHMODE_ASSOC)){

	$pc = explode(" ",$row['Postcode']);
	$intOSX = $row["osx"];
	$intOSY = $row["osy"];
	
	$SQLTrans = "SELECT places.place_ID, places.place_type, places.place_title, places.place_desc, places.place_osx, places.place_osy, pl_type.pl_type_id, pl_type_title FROM places, pl_type WHERE (places.place_type = 1 OR places.place_type = 2) AND places.place_type = pl_type.pl_type_id  ORDER BY sqrt((abs(places.place_osx-".$intOSX.")*abs(places.place_osx-".$intOSX."))+(abs(places.place_osy-".$intOSY.")*abs(places.place_osy-".$intOSY."))) LIMIT 1";
	$rowTrans = $con->getRow($SQLTrans, DB_FETCHMODE_ASSOC);
	
	if (DB::isError($resTrans = $con->query($SQLTrans))){
		die (DB::errorMessage($resTrans));
	}
	
	$intTransType = $rowTrans['place_type'];
	$strTransType = $rowTrans['pl_type_title'];
	$strTransTitle = $rowTrans['place_title'];
	$strTransDesc = $rowTrans['place_desc'];
	$intTransOSX = $rowTrans['place_osx'];
	$intTransOSY = $rowTrans['place_osy'];
	
	if ($strTransType == "Station") {
		$strTransType = "trains";
		}
	
	$intTransDistance = round(sqrt((abs($intTransOSX-$intOSX)*abs($intTransOSX-$intOSX))+(abs($intTransOSY-$intOSY)*abs($intTransOSY-$intOSY))));
	
	if ($intTransType == 1) {
		$strTrans = '<img src="http://www.woosterstock.co.uk/images/mapping/rail.gif" width="17" height="10" align="absmiddle">&nbsp;<b>'.$strTransTitle.'</b><br><font color="#666666">(approx. '.$intTransDistance.' meters)</font>';
		} elseif ($intTransType == 2) {
		$strTrans = '<img src="http://www.woosterstock.co.uk/images/mapping/tube.gif" width="12" height="10" align="absmiddle">&nbsp;<b>'.$strTransTitle.'</b><br><font color="#666666">(approx. '.$intTransDistance.' meters)</font>';
		}

	$intBranch = $row["Branch_ID"];		
	if ($intBranch == 1) {
		$strCardFolder = "CardsShip";
		} elseif ($intBranch == 2) {
		$strCardFolder = "CardsSyd";
		} elseif ($intBranch == 3) {
		$strCardFolder = "CardsShad";
		} 
		
	if ($intCard == "Created" || $intCard == "Proofed") { 
		$renderCard = ' - <a href="P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf" target="_blank">PDF</a>'; 
		}

	$intSaleLet = $row["SaleLet"];
	if ($intSaleLet == 1) {
		$template = 'P:\\'.$strCardFolder.'\\templates\sales.indt';
		} elseif ($intSaleLet == 2) {
		$template = 'P:\\'.$strCardFolder.'\\templates\lettings.indt';
		}
	
	$render = '
	<p>Open the pdf file <a href="P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf" target="_blank">(click here)</a> and check the card against the details below:</p>
	<table width="400">
	<tr>
	<td valign="top">Front Page:</td>
	<td><span class="footerTitle">'.$row['Address1'].', '.$pc[0].'<br>'.price_format($row['Price']);
	if ($intSaleLet == 2) {
		$render .= ' p/w';
		}
	$render .= '</span><br><br><br></td>
	</tr>
	<tr>
	<td valign="top">Back Page:</td>
	<td><span class="footerTitle">'.$row['Address1'].'<br>'.$row["area_title"].'</span><br><br>
	<span class="greyText">Price:</span><br> '.price_format($row['Price']);
	if ($intSaleLet == 2) {
		$render .= ' per week / '.price_format(pw2pcm($row['Price'])).' per month';
		}
	$render .= '<br><br>';
	if ($intSaleLet == 1) {
		if ($row["lease_free"] == 1) {
			$leasefree = "Freehold";
			} elseif ($row["lease_free"] == 2) {
			$leasefree = "Leasehold";
			} elseif ($row["lease_free"] == 3) {
			$leasefree = "Share of Freehold";
			}
		$render .='<span class="greyText">Tenure:</span><br>'.$leasefree.'<br><br>';
	} elseif ($intSaleLet == 2) {
		if ($row["furnished"] == 1) {
			$furnished = "Unfurnished";
			} elseif ($row["furnished"] == 2) {
			$furnished = "Part-furnished";
			} elseif ($row["furnished"] == 3) {
			$furnished = "Furnished";
			} elseif ($row["furnished"] == 4) {
			$furnished = "Furnished or unfurnished";
			}
			$render .='<span class="greyText">Furnished:</span><br>'.$furnished.'<br><br>';
			}
	
	if ($row["total_area"]) {
		$render .= '<span class="greyText">Internal Area:</span><br>Approx. '.round($row["total_area"]).' square meters<br><br>';
		}
	$render .='
	<span class="greyText">Nearest Transport:</span><br>'.$strTransTitle.' '.strtolower($strTransType).'</td>
	</tr>
	</table>
	<p>When you have finished proofing the card, click <a href="?propID='.$intPropID.'&action=Update&status=Proofed&searchLink='.$searchLink.'">HERE</a></p>
	<p>If the card contains errors, please enter them here and click Save</p>
	<form method="GET">
	<textarea name="card_notes"></textarea>
	<input type="submit" value="Save">
	<input type="hidden" name="propID" value="'.$intPropID.'">
	<input type="hidden" name="action" value="Update">
	<input type="hidden" name="status" value="Mistakes">
	<input type="hidden" name="searchLink" value="'.$searchLink.'">
	
	</form>';	
	}


echo html_header("Proof Card");
echo '<div align="center">
'.$render.'
</div>';
}
elseif ($_GET["action"] == "Mistakes") {
$intPropID = $_GET["propID"];

$sql = "SELECT * FROM property, area, state_of_trade, proptype, branch WHERE property.prop_ID = ".$intPropID." AND ";
$sql .= " property.area_id = area.area_ID AND property.state_of_trade_id = state_of_trade.state_ID ";
$sql .= " AND property.type_id = proptype.type_ID ";
$sql .= " AND property.Branch = branch.Branch_ID";

if (DB::isError($res = $con->query($sql))){
    die (DB::errorMessage($res));
}
$from = isset($_GET['from']) ? (int)$_GET['from'] : 0;
$limit = 1;
$pager = new DB_Pager ($res, $from, $limit);

$data = $pager->build();
if (DB::isError($data)){
    die (DB::errorMessage($data));
}
while ($row = $pager->fetchRow(DB_FETCHMODE_ASSOC)){

	$pc = explode(" ",$row['Postcode']);
	$intOSX = $row["osx"];
	$intOSY = $row["osy"];
	
	$SQLTrans = "SELECT places.place_ID, places.place_type, places.place_title, places.place_desc, places.place_osx, places.place_osy, pl_type.pl_type_id, pl_type_title FROM places, pl_type WHERE (places.place_type = 1 OR places.place_type = 2) AND places.place_type = pl_type.pl_type_id  ORDER BY sqrt((abs(places.place_osx-".$intOSX.")*abs(places.place_osx-".$intOSX."))+(abs(places.place_osy-".$intOSY.")*abs(places.place_osy-".$intOSY."))) LIMIT 1";
	$rowTrans = $con->getRow($SQLTrans, DB_FETCHMODE_ASSOC);
	
	if (DB::isError($resTrans = $con->query($SQLTrans))){
		die (DB::errorMessage($resTrans));
	}
	
	$intTransType = $rowTrans['place_type'];
	$strTransType = $rowTrans['pl_type_title'];
	$strTransTitle = $rowTrans['place_title'];
	$strTransDesc = $rowTrans['place_desc'];
	$intTransOSX = $rowTrans['place_osx'];
	$intTransOSY = $rowTrans['place_osy'];
	
	if ($strTransType == "Station") {
		$strTransType = "trains";
		}
	
	$intTransDistance = round(sqrt((abs($intTransOSX-$intOSX)*abs($intTransOSX-$intOSX))+(abs($intTransOSY-$intOSY)*abs($intTransOSY-$intOSY))));
	
	if ($intTransType == 1) {
		$strTrans = '<img src="http://www.woosterstock.co.uk/images/mapping/rail.gif" width="17" height="10" align="absmiddle">&nbsp;<b>'.$strTransTitle.'</b><br><font color="#666666">(approx. '.$intTransDistance.' meters)</font>';
		} elseif ($intTransType == 2) {
		$strTrans = '<img src="http://www.woosterstock.co.uk/images/mapping/tube.gif" width="12" height="10" align="absmiddle">&nbsp;<b>'.$strTransTitle.'</b><br><font color="#666666">(approx. '.$intTransDistance.' meters)</font>';
		}

	$intBranch = $row["Branch_ID"];		
	if ($intBranch == 1) {
		$strCardFolder = "CardsShip";
		} elseif ($intBranch == 2) {
		$strCardFolder = "CardsSyd";
		} elseif ($intBranch == 3) {
		$strCardFolder = "CardsShad";
		} 
		
	if ($intCard == "Created" || $intCard == "Proofed") { 
		$renderCard = ' - <a href="P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf" target="_blank">PDF</a>'; 
		}

	$intSaleLet = $row["SaleLet"];
	if ($intSaleLet == 1) {
		$template = 'P:\\'.$strCardFolder.'\\templates\sales.indt';
		} elseif ($intSaleLet == 2) {
		$template = 'P:\\'.$strCardFolder.'\\templates\lettings.indt';
		}
	
	
	
	////////////////////////////////////////////////////////////////////
	
	
	
	
	
	$render = '
	<p>Open the InDesign file <a href="P:\\'.$strCardFolder.'\\'.$intPropID.'.indd" target="_blank">(click here)</a> and make the following corrections:</p>
	
	<table width="400">
	<tr>
	<td colspan="2"><p><strong><font color="red">'.nl2br($row['card_notes']).'</font></strong></p><br><br></td>
	</tr>	
	<tr>
	<td valign="top">Front Page:</td>
	<td><span class="footerTitle">'.$row['Address1'].', '.$pc[0].'<br>'.price_format($row['Price']);
	if ($intSaleLet == 2) {
		$render .= ' p/w';
		}
	$render .= '</span><br><br><br></td>
	</tr>
	<tr>
	<td valign="top">Back Page:</td>
	<td><span class="footerTitle">'.$row['Address1'].'<br>'.$row["area_title"].'</span><br><br>
	<span class="greyText">Price:</span><br> '.price_format($row['Price']);
	if ($intSaleLet == 2) {
		$render .= ' per week / '.price_format(pw2pcm($row['Price'])).' per month';
		}
	$render .= '<br><br>';
	if ($intSaleLet == 1) {
		if ($row["lease_free"] == 1) {
			$leasefree = "Freehold";
			} elseif ($row["lease_free"] == 2) {
			$leasefree = "Leasehold";
			} elseif ($row["lease_free"] == 3) {
			$leasefree = "Share of Freehold";
			}
		$render .='<span class="greyText">Tenure:</span><br>'.$leasefree.'<br><br>';
	} elseif ($intSaleLet == 2) {
		if ($row["furnished"] == 1) {
			$furnished = "Unfurnished";
			} elseif ($row["furnished"] == 2) {
			$furnished = "Part-furnished";
			} elseif ($row["furnished"] == 3) {
			$furnished = "Furnished";
			} elseif ($row["furnished"] == 4) {
			$furnished = "Furnished or unfurnished";
			}
			$render .='<span class="greyText">Furnished:</span><br>'.$furnished.'<br><br>';
			}
	
	if ($row["total_area"]) {
		$render .= '<span class="greyText">Internal Area:</span><br>Approx. '.round($row["total_area"]).' square meters<br><br>';
		}
	$render .='
	<span class="greyText">Nearest Transport:</span><br>'.$strTransTitle.' '.strtolower($strTransType).'</td>
	</tr>
	</table>
	<p>When you have completed the card in InDesign, save it as P:\\'.$strCardFolder.'\\'.$intPropID.'.indd<br>
	Then export the file and save it as P:\\'.$strCardFolder.'\\'.$intPropID.'.pdf<br>
	When you have finished the card, click <a href="?propID='.$intPropID.'&action=Update&status=Created&searchLink='.$searchLink.'">HERE</a></p>
	<p>If you have insufficient images to create a card, please click <a href="?propID='.$intPropID.'&action=Update&status=Insufficient&searchLink='.$searchLink.'">HERE</a></p>';	
	}


echo html_header("Create Card");
echo '<div align="center">
'.$render.'
</div>';
}
else {
echo html_header("Cards Search");

?>
<div align="center">
<form method="get">
<table border="1">
  <tr>
    <td>Keyword</td>
    <td><input type="text" name="Keyword" style="width:200px"></td>
    </tr>
  <tr>
    <td>Branch</td>
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
    <td>Card Status</td>
    <td><select name="card" style="width: 200px;">
        <option value="" selected>Any</option>
        <option value="1">Not Created</option>
        <option value="2">Created</option>
        <option value="3">Proofed</option>
        <option value="4">Insufficient Images</option>
        <option value="5">Mistakes</option>
    </select></td>
  </tr>
  <tr>
    <td>Property Status</td>
    <td><table width="100%">
            <tr> <?php	
			
			$ticked = array('1','2');	  	
		  	$sqlStatus = "SELECT state_ID, state_Title FROM state_of_trade WHERE state_ID <> 6";
			$qStatus = $db->query($sqlStatus);
			if (DB::isError($qStatus)) {  die("insert error: ".$qStatus->getMessage()); }
			
			while ($rowStatus = $qStatus->fetchRow()) {		
				echo '<td><input type="checkbox" name="Status[]" value="'.$rowStatus["state_ID"].'"';
				if (in_array($rowStatus["state_ID"],$ticked)) {
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
  <tr align="center">
    <td colspan="2"><input type="submit" value="Search Cards"> 
        <input type="hidden" name="action" value="Search">
		<input type="hidden" name="Layout" value="plain"></td>
    </tr>
</table>
</form>
</div>
</body>
</html>
<?php } ?>