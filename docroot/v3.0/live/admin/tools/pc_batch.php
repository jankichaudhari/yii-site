<?php
require_once("../inx/global.inc.php");

function pcaBatchCleanse($addresses=array())
	{
	
	//Built with help from James at http://www.omlet.co.uk/
	$counter = 0;
	foreach ($addresses as $addr) {
		$url_inner .= "&address$counter=" . urlencode($addr);
		$counter++;
		}
	
	//Build the url
	$url = "http://services.postcodeanywhere.co.uk/xml.aspx?";
	$url .= "&action=batch_cleanse";
	$url .= $url_inner;
	$url .= "&account_code=WOOST11112";
	$url .= "&license_code=YJ67-YN69-YY93-MG96";
	
	
	//echo $url; exit;
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
	
	//Return the result
	return $output;
	
	}
	
$addresses[] = '360 oxford street, london, w1';
$addresses[] = '107 sydenham road, london, se26 5EZ ';
$addresses[] = '4 renfrew road, se11 4na ';
print_r(pcaBatchCleanse($addresses));

exit;

?>