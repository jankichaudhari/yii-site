<?php /*
 * @var Location $model;
 * @var AdminForm $form
 * @var $parentModel
 * @var $parentModelName
 * @var $showMap
 */
?>
	<div class="row-fluid">
		<div class="span12">
			<div id="locationFields">
				<?php echo $form->hiddenField($model, 'latitude'); ?>
				<?php echo $form->hiddenField($model, 'longitude'); ?>
				<?php echo $form->beginControlGroup($model, 'address') ?>
				<?php echo $form->controlLabel($model, 'address'); ?>
				<div class="controls">
					<?php echo $form->textArea($model, 'address'); ?>
				</div>
				<?php echo $form->endControlGroup(); ?>
				<?=
				$form->beginControlGroup($model, 'city'); ?>
				<?=
				$form->controlLabel($model, 'city'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'city'); ?>
				</div>
				<?=
				$form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'postcode'); ?>
				<?= $form->controlLabel($model, 'postcode'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'postcode'); ?>
				</div>
				<?= $form->endControlGroup(); ?>


			</div>

			<?php if (!isset($showMap) || $showMap): ?>
				<div id="locationMap" class="map" style="height:426px;"></div>
			<?php endif; ?>
		</div>
	</div>

<?php if (!isset($showMap) || $showMap) { ?>
	<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
		var setMarkerPosition = function (lat, lng) {
			document.getElementById("Location_latitude").value = lat;
			document.getElementById("Location_longitude").value = lng;
		};

		var lat = document.getElementById("Location_latitude").value || 51.471952345537105;
		var lng = document.getElementById("Location_longitude").value || -0.08856922388076782;

		var map;

		var initialize = function (mapId) {
			if (lat && lng) {
				var latlng = new google.maps.LatLng(lat, lng);
				var myOptions = {
					zoom: 16,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById(mapId),
						myOptions);
				var marker = new google.maps.Marker({
					map: map,
					draggable: true,
					position: latlng
				});
				setMarkerPosition(lat, lng);
				google.maps.event.addListener(marker, 'dragend', (function (event) {
					marker.setPosition(event.latLng);
					setMarkerPosition(event.latLng.lat(), event.latLng.lng());
				}));
				google.maps.event.addListener(map, "click", function (event) {
					marker.setPosition(event.latLng);
					setMarkerPosition(event.latLng.lat(), event.latLng.lng());
				});
			}
		};

		initialize("locationMap");

	</script>
<?php } ?>