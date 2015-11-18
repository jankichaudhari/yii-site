<?php
require_once("inx/global.inc.php");
// AJAX postcode lookup screen

$lookup_type   = $_GET["lookup_type"];
$search_string = $_GET["search_string"];

// scope can be pro, cli or con (more to follow perhaps)
if ($_GET["scope"]) {
	$scope = $_GET["scope"];
} else {
	$scope = 'pro';
}

// start new postcode object
$postcode = new Postcode();

if ($lookup_type == "udprn") {

	/* 
	here we need to switch between scope : cli pro con (other)
	maybe change the way the data is handled for each
	as we are doing a fetch, we may aswell add the data to the property table without geocode
	geocode can be done later if they property is ever associated with a deal
	*/

	// fetch the full address info
	$Data = $postcode->output_form($postcode->fetch($search_string));
	// geocode the bastard, and get id from property table
	$pro_id           = $postcode->property($Data["id"]);
	$addr4_type       = 'text';
	$addr4_options    = '';
	$addr4_attributes = array('class'=> 'addr');

	// make the form
	$formData = array(
		$scope . '_pcid'    => array(
			'type' => 'hidden',
			'value'=> $Data["id"]
		),
		$scope . '_addr1'   => array(
			'type'      => 'text',
			'label'     => 'House Number',
			'value'     => $Data["addr1"],
			'required'  => 2,
			'attributes'=> array('class'   => 'addr',
								 'readonly'=> 'readonly'),
			'function'  => 'format_street'
		),
		$scope . '_addr2'   => array(
			'type'      => 'text',
			'label'     => 'Building Name',
			'value'     => $Data["addr2"],
			'required'  => 1,
			'attributes'=> array('class'=> 'addr'),
			'function'  => 'format_street'
		),
		$scope . '_addr3'   => array(
			'type'      => 'text',
			'label'     => 'Street',
			'value'     => $Data["addr3"],
			'required'  => 2,
			'attributes'=> array('class'   => 'addr',
								 'readonly'=> 'readonly'),
			'function'  => 'format_street'
		),

		$scope . '_addr5'   => array(
			'type'      => 'text',
			'label'     => 'City or County',
			'value'     => $Data["addr5"],
			'required'  => 2,
			'attributes'=> array('class'   => 'addr',
								 'readonly'=> 'readonly'),
			'function'  => 'format_street'
		),
		$scope . '_postcode'=> array(
			'type'      => 'text',
			'label'     => 'Postcode',
			'value'     => $Data["postcode"],
			'required'  => 2,
			'attributes'=> array('class'    => 'pc',
								 'maxlength'=> 9,
								 'readonly' => 'readonly'),
			'function'  => 'format_postcode'
		),
		$scope . '_pro_id'  => array(
			'type' => 'hidden',
			'value'=> $pro_id
		),
	);

	$form = new Form();
	$form->addData($formData, $_GET);
	$formName = "form2";
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class'=> 'submit'))));
	echo '<div id="inset">If this address is incorrect please <a href="javascript:cancelResponse();">try again</a></div>';
	echo $form->renderForm();
	exit;

}
elseif ($lookup_type == "manual") {

	// make the form
	$formData = array(
		$scope . '_pcid'    => array(
			'type' => 'hidden',
			'value'=> '-1'
		),
		$scope . '_addr1'   => array(
			'type'      => 'text',
			'label'     => 'House Number',
			'required'  => 2,
			'attributes'=> array('class'=> 'addr'),
			'function'  => 'format_street'
		),
		$scope . '_addr2'   => array(
			'type'      => 'text',
			'label'     => 'Building Name',
			'required'  => 1,
			'attributes'=> array('class'=> 'addr'),
			'function'  => 'format_street'
		),
		$scope . '_addr3'   => array(
			'type'      => 'text',
			'label'     => 'Street',
			'required'  => 2,
			'attributes'=> array('class'=> 'addr'),
			'function'  => 'format_street'
		),
		$scope . '_addr5'   => array(
			'type'      => 'text',
			'label'     => 'City or County',
			'required'  => 2,
			'attributes'=> array('class'=> 'addr'),
			'function'  => 'format_street'
		),
		$scope . '_postcode'=> array(
			'type'      => 'text',
			'label'     => 'Postcode',
			'required'  => 2,
			'attributes'=> array('class'    => 'pc',
								 'maxlength'=> 9),
			'function'  => 'format_postcode'
		),
		$scope . '_country' => array(
			'type'      => 'select',
			'label'     => 'Country',
			'value'     => $default_country,
			'required'  => 2,
			'options'   => db_lookup("pro_country", "country", "array"),
			'attributes'=> array('class'=> 'addr')
		)
	);

	$form = new Form();
	$form->addData($formData, $_GET);
	$formName = "form2";
	$form->addHtml($form->addDiv($form->makeField("submit", $formName, "", "Save Changes", array('class'=> 'submit'))));
	echo '<div id="inset">If this address is incorrect please <a href="javascript:cancelResponse();">try again</a></div>';
	echo $form->renderForm();
	exit;

}
else {

	$Data = $postcode->lookup($lookup_type, $search_string, "data");

	if (count($Data) == 1) {
		foreach ($Data as $keyd => $data) {
			$udprn = $data["id"];
		}
		header("Location:?lookup_type=udprn&search_string=" . $udprn . "&scope=" . $scope);
		exit;
	}
	else {
		$form = new Form();
		$form->addHtml($form->addLabel("select", "Select Property", $postcode->output_list($Data)));
		$buttons = $form->makeField("button", "button", "", "Use Selected", array('class'  => 'submit',
																				  'onClick'=> 'ajax_select_address(this.id);'));
		$buttons .= $form->makeField("button", "tryagain", "", "Try Again", array('class'  => 'button',
																				  'onClick'=> 'cancelResponse();'));
		$form->addHtml($form->addDiv($buttons));
		echo $form->renderForm();
		exit;
	}
}
