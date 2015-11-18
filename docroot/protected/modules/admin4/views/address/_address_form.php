<?php
/**
 * @var Address       $model
 * @var CActiveForm   $form
 * @var CModel        $parentModel
 * @var String        $parentField
 * @var               $this CController
 */
?>

<div id="addressForm">
	<table>
		<tr>
			<td>
				<div class="row">
					<?php echo $form->labelEx($model, 'line1'); ?>
					<?php echo $form->textField($model, 'line1', array('size'     => 30,)); ?>
				</div>

				<div class="row">
					<?php echo $form->labelEx($model, 'line2'); ?>
					<?php echo $form->textField($model, 'line2', array('size'     => 30,)); ?>
				</div>

				<div class="row">
					<?php echo $form->labelEx($model, 'line3'); ?>
					<?php echo $form->textField($model, 'line3', array('size'     => 30,)); ?>
				</div>

				<div class="row">
					<?php echo $form->labelEx($model, 'line4'); ?>
					<?php echo $form->textField($model, 'line4', array('size'     => 30,)); ?>
				</div>

				<div class="row">
					<?php echo $form->labelEx($model, 'line5'); ?>
					<?php echo $form->textField($model, 'line5', array(
																	  'size'      => 30,
																	  'maxlength' => '255'
																 )); ?>
				</div>
			</td>
			<td>
				<?php if (!isset($showMap) || $showMap) {
					if(isset($parentModelName) && $parentModelName=='Place') {
						?><div style="width:800px;height:372px;position:absolute;margin:-312px 0px 0px 36px;" id="map_canvas"></div><?php } else {
						?><div style="width:300px;height:200px;" id="map_canvas"></div><?php }
				} ?>
			</td>
		</tr>
		<tr>

			<td colspan="2">
				<div class="row" style="min-height: 50px;">
					<?php echo $form->labelEx($model, 'postcode',['style'=>'margin-top:15px;']); ?>
					<div style="float:left;margin: 15px 10px 5px 0px"><?php echo $form->textField($model, 'postcode', array('size'=> 30)); ?></div>
					<div style="float:left;;margin: 15px 10px 5px 0px"><?php echo CHtml::button("Lookup Address", array('id' => 'address_lookupButton')) ?></div>
					<div style="float:left;margin:10px 0px 5px 0px"><?php echo CHtml::image("/images/loading.gif","Loading",array('id'=>'lookUpAddLoading')) ?></div>
					<br class="clear">
				</div>
				<?php echo $form->hiddenField($model, 'lat') ?>
				<?php echo $form->hiddenField($model, 'lng') ?>
				<?php echo $form->hiddenField($model, 'postcodeAnywhereID') ?>

				<div id="lookup_error" style="display:none; border:1px solid red; background: pink; color: #555; font-weight: bold; padding: 5px;"></div>
				<div id="lookup_result" style="display: none">

				</div>
			</td>
		</tr>
	</table>
</div>
<div style="clear: both"></div>

<script type="text/javascript">
	$('#lookUpAddLoading').hide();
	$('#address_lookupButton').on('click', function ()
	{	$('#lookUpAddLoading').show();
		var postcode = document.getElementById("Address_postcode"),
				line1 = document.getElementById("Address_line1"),
				street = document.getElementById("Address_line2");

		if (!postcode.value) {
			alert('Please input Postcode');
			$('#lookUpAddLoading').hide();
			postcode.focus();
			return;
		}

		var link = '<?php echo $this->createUrl('Address/lookup', array('ajax' => true)) ?>';
		$.getJSON(link, {'postcode' : postcode.value}, function (data)
		{
			if(data.length==0){
                $('#lookUpAddLoading').hide();
				return;
			}
            $('#lookUpAddLoading').hide();
			var errorBox = document.getElementById("lookup_error");
			errorBox.style.display = 'none';
			var resultBox = document.getElementById("lookup_result");
			resultBox.style.display = 'none';
			if (data.error) {
				errorBox.innerHTML = data.error;
				errorBox.style.display = '';
			}
			if (data.html) {
				resultBox.innerHTML = data.html;
				resultBox.style.display = '';
			}
		})
	});

	var callback = function ()
	{
		$('#useSelAddLoading').show();
		var select = $("#addressLookupSelector");

		var value = select.val();

		if (!value) {
			$('#useSelAddLoading').hide();
			return alert("Please select address from the list!");
		}

		var link = '<?php echo $this->createUrl('Address/fetch', array('ajax' => true)) ?>';

		$.getJSON(link, {'id' : value}, function (data)
		{
			$('#useSelAddLoading').hide();
			$("#Address_line1").val(data.line1);
			$("#Address_line2").val(data.line2);
			$("#Address_line3").val(data.line3);
			$("#Address_line4").val(data.line4);
			$("#Address_line5").val(data.post_town);
			$("#Address_lat").val(data.latitude);
			$("#Address_lng").val(data.longitude);
			$("#Address_postcodeAnywhereID").val(data.id);

			$('#lookup_result').hide();
		});
	};
	$('body').on('click', '#selectAddresFromLookupBtn', callback);
	$('body').on('dblclick', '#addressLookupSelector option', callback);
</script>
<?php if (!isset($showMap) || $showMap) : ?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	var lat = document.getElementById("Address_lat").value || 51.471952345537105;
	var lng = document.getElementById("Address_lng").value || -0.08856922388076782;
	if (lat && lng) {
		var latlng = new google.maps.LatLng(lat, lng);
		var myOptions = {
			zoom      : 16,
			center    : latlng,
			mapTypeId : google.maps.MapTypeId.ROADMAP
		};
		var map = new google.maps.Map(document.getElementById("map_canvas"),
									  myOptions);
		var marker = new google.maps.Marker({
												map      : map,
												draggable : true,
												position : latlng
											});
		google.maps.event.addListener(marker, 'dragend', (function(event) {
			$('#useSelAddLoading').hide();
			marker.setPosition(event.latLng);
			document.getElementById("Address_lat").value = event.latLng.lat();
			document.getElementById("Address_lng").value = event.latLng.lng();
		}) );
		google.maps.event.addListener(map, "click", function (event)
		{
			$('#useSelAddLoading').hide();
			marker.setPosition(event.latLng);
			document.getElementById("Address_lat").value = event.latLng.lat();
			document.getElementById("Address_lng").value = event.latLng.lng();
		});

	}

</script>
<?php endif ?>