<?php
/**
 * @var $this  AddressController
 * @var $model Address
 * @var $form  AdminForm
 */
$form = $this->beginWidget('AdminForm');
?>

<fieldset>
	<div class="block-header">SELECT COORDINATES</div>
	<div class="content">
		<?php if (Yii::app()->user->hasFlash('address-coordinates-updated')): ?>
			<div class="flash success remove"><?php echo Yii::app()->user->getFlash('address-coordinates-updated') ?></div>
		<?php endif ?>
		<div class="control-group">
			<label class="control-label">Address</label>

			<div class="controls text"><?php echo $model->toString(', ') ?></div>
		</div>
		<div class="control-group">
			<label class="control-label">Latitude</label>

			<div class="controls">
				<?php echo $form->textField($model, 'lat') ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Longitude</label>

			<div class="controls">
				<?php echo $form->textField($model, 'lng') ?>
			</div>
		</div>
	</div>
	<div class="block-buttons force-margin">
		<input type="submit" value="Save" class="btn" /><input type="button" value="Close" onclick="window.close();" class="btn btn-gray" />
	</div>
</fieldset>
<?php
$this->endWidget();
?>

<div class="content">
	<div style="height: 600px; background: #dedede;" id="map"></div>
	<div class="block-buttons">
		<input type="button" value="Close" onclick="window.close();" class="btn btn-gray" />
	</div>
</div>

<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	var lat =  <?php echo ($model->lat ?: "51.471952345537105")   ?>;
	var lng =  <?php echo ($model->lng ?: "-0.08856922388076782")   ?>;
	var latlng = new google.maps.LatLng(lat, lng);
	var myOptions = {
		zoom      : 16,
		center    : latlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map"),
								  myOptions);
	var marker = new google.maps.Marker({
											map       : map,
											draggable : true,
											position  : latlng
										});
	google.maps.event.addListener(marker, 'dragend', (function (event)
	{
		marker.setPosition(event.latLng);
		document.getElementById("Address_lat").value = event.latLng.lat();
		document.getElementById("Address_lng").value = event.latLng.lng();
	}));
	google.maps.event.addListener(map, "click", function (event)
	{
		marker.setPosition(event.latLng);
		document.getElementById("Address_lat").value = event.latLng.lat();
		document.getElementById("Address_lng").value = event.latLng.lng();
	});

</script>