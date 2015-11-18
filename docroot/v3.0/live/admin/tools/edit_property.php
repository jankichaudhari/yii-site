<?php
require_once("../inx/global.inc.php");
// allows manual editing of property records

// only accesible to SuperAdmin
pageAccess($_SESSION["auth"]["roles"], array('SuperAdmin', 'SuperProduction'));

// if a property id is given, get info from pro table
if ($_GET["pro_id"]) {

	$sql = "SELECT
	pro_id,pro_addr1,pro_addr2,pro_addr3,pro_addr4,pro_addr5,pro_addr6,pro_country,pro_postcode,pro_area,pro_pcid,
	pro_authority,pro_east,pro_north,pro_latitude,pro_longitude,pro_dump

	FROM
	property
	WHERE
	pro_id = " . $_GET["pro_id"] . "
	LIMIT 1";
	$q   = $db->query($sql);
	if (DB::isError($q)) {
		die("db error: " . $q->getMessage());
	}
	$render = "<form><table>";
	$row    = $q->fetchRow();

	foreach ($row AS $key=> $val) {

		foreach ($row as $key=> $val) {
			$$key = $val;
		}
	}
}

// get areas
$areas[0] = '';
$sql_area = "SELECT * FROM area ORDER BY are_title";
$q_area   = $db->query($sql_area);
while ($row_area = $q_area->fetchRow()) {
	$areas[$row_area["are_id"]] = $row_area["are_title"] . ' ' . $row_area["are_postcode"];
}
// get country
$sql_area = "SELECT * FROM country ORDER BY cou_title";
$q_area   = $db->query($sql_area);
while ($row_area = $q_area->fetchRow()) {
	$countries[$row_area["cou_id"]] = $row_area["cou_title"];
}

// all are readonly except area for paf addresses
if ($pro_pcid == '-1') {
	$attributes = array('class'=> 'wide');
} else {
	$attributes = array('class'   => 'wide',
						'readonly'=> 'readonly');
	$info       = '<p class="appInfo">This is a Royal Mail address, all you can edit is the Area</p>';
}

$formData1 = array(
	'pro_addr1'    => array(
		'type'      => 'text',
		'label'     => 'House/Flat Number',
		'value'     => $pro_addr1,
		'attributes'=> $attributes,
		'required'  => 2,
		'function'  => 'format_street'
	),
	'pro_addr2'    => array(
		'type'      => 'text',
		'label'     => 'Building Name',
		'value'     => $pro_addr2,
		'attributes'=> $attributes,
		'function'  => 'format_street'
	),
	'pro_addr3'    => array(
		'type'      => 'text',
		'label'     => 'Street',
		'value'     => $pro_addr3,
		'attributes'=> $attributes,
		'required'  => 2,
		'function'  => 'format_street'
	)/*,
	'pro_addr4'=>array(
		'type'=>'text',
		'label'=>'pro_addr4',
		'value'=>$pro_addr4,
		'attributes'=>array('class'=>'wide')
		)*/,
	'pro_area'     => array(
		'type'      => 'select',
		'label'     => 'Area',
		'value'     => $pro_area,
		'attributes'=> array('class'=> 'wide'),
		'options'   => $areas
	),
	'pro_addr5'    => array(
		'type'      => 'text',
		'label'     => 'City or County',
		'value'     => $pro_addr5,
		'attributes'=> $attributes,
		'required'  => 2,
		'function'  => 'format_street'
	),
	'pro_country'  => array(
		'type'      => 'select',
		'label'     => 'Country',
		'value'     => $pro_country,
		'attributes'=> $attributes,
		'options'   => $countries
	),
	'pro_postcode' => array(
		'type'      => 'text',
		'label'     => 'Postcode',
		'value'     => $pro_postcode,
		'attributes'=> $attributes,
		'required'  => 2,
		'function'  => 'format_postcode'
	),
	'pro_authority'=> array(
		'type'      => 'text',
		'label'     => 'Local Authority',
		'value'     => $pro_authority,
		'attributes'=> $attributes
	),
	'pro_east'     => array(
		'type'      => 'text',
		'label'     => 'Easting',
		'value'     => $pro_east,
		'attributes'=> $attributes
	),
	'pro_north'    => array(
		'type'      => 'text',
		'label'     => 'Northing',
		'value'     => $pro_north,
		'attributes'=> $attributes
	),
	'pro_latitude' => array(
		'type'      => 'text',
		'label'     => 'Latitude',
		'value'     => $pro_latitude,
		'attributes'=> $attributes
	),
	'pro_longitude'=> array(
		'type'      => 'text',
		'label'     => 'Longitude',
		'value'     => $pro_longitude,
		'attributes'=> $attributes
	)
);

if (!$_GET["action"]) {

	$form = new Form();
	$form->addForm("app_form", "GET", $PHP_SELF);
	$form->addHtml("<div id=\"standard_form\" style=\"width:700px; float:left\">\n");
	$form->addField("hidden", "action", "", "update");
	$form->addField("hidden", "pro_id", "", $pro_id);
	$form->addField("hidden", "searchLink", "", urlencode($searchLink));
	$form->addHtml("<fieldset>\n");

	$form->addHtml('<div class="block-header">Edit Address</div>');
	$form->addHtml($info);
//$form->addHtml($form->addRow("radio","saveas","Action","Save",array(),array('Save'=>'Save','Add as New'=>'Add as New')));
	$form->addData($formData1, $_GET);
//if ($pro_pcid == '-1') {
	$form->addHtml($form->addDiv($form->makeField("submit", "submit", "", "Save Changes", array('class'=> 'submit'))));
//	}
	$form->addHtml("</fieldset>\n");
	$form->addHtml("</div>\n");

	$form->addHtml('<div style="padding-top: 30px;"><div id="map" style="width:500px; height:435px;"></div></div>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

var lat = document.getElementById("pro_latitude").value || 51.471952345537105;
var lng = document.getElementById("pro_longitude").value || -0.08856922388076782;
if(lat && lng) {
var latlng = new google.maps.LatLng(lat, lng);
			var myOptions = {
				zoom      : 16,
				center    : latlng,
				mapTypeId : google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById("map"),
										  myOptions);
			var marker = new google.maps.Marker({
													map      : map,
													position : latlng
												});
			google.maps.event.addListener(map, "click", function (event)
			{
				marker.setPosition(event.latLng);
				document.getElementById("pro_latitude").value = event.latLng.lat();
				document.getElementById("pro_longitude").value = event.latLng.lng();
			});
}


</script>');

	$navbar_array = array(
		'back'  => array('title'=> 'Back',
						 'label'=> 'Back',
						 'link' => $searchLink),
		'search'=> array('title'=> 'Client Search',
						 'label'=> 'Client Search',
						 'link' => 'client_search.php')
	);
	$navbar       = navbar2($navbar_array);

	$page = new HTML_Page2($page_defaults);
	$page->setTitle("Edit Address");
	$page->addStyleSheet('../css/styles.css');
	$page->addScript('../js/global.js');
	$page->addBodyContent($header_and_menu);
	$page->addBodyContent('<div id="content_wide">');
	$page->addBodyContent($navbar);
	$page->addBodyContent($form->renderForm());
	$page->addBodyContent('</div>');
	$page->display();

} else {

// update
// if making a change to a property record, we should remove pcid and dump data as it will be incorrect

	$result  = new Validate();
	$results = $result->process($formData1, $_GET);
	$db_data = $results['Results'];

	if($db_data['pro_latitude'] && $db_data['pro_longitude']) {
		include_once dirname(__FILE__) . "/../../../../../htdocs/mapping/phpcoord-2.3.php";
		$latLng = new LatLng($db_data['pro_latitude'], $db_data['pro_longitude']);
		$latLng->WGS84ToOSGB36();
		$eastNorth = $latLng->toOSRef();
		$db_data['pro_east'] = $eastNorth->easting;
		$db_data['pro_north'] = $eastNorth->northing;
	}


	if ($_GET["saveas"] == 'Add as New') {
		$pro_id = db_query($db_data, "INSERT", "property", "pro_id");
		header("Location:?pro_id=" . $pro_id);
	}
	else {
		db_query($db_data, "UPDATE", "property", "pro_id", $_GET["pro_id"]);
		header("Location:?pro_id=" . $_GET["pro_id"]);
	}

}
?>