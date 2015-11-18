<?php
/**
 * @var String $objectId
 */
?>
<div class="content-fluid">
	<div class="row-fluid">
		<div class="span12">
			<div id="mapCanvas">

			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	console.log($(window).height());
	$('#mapCanvas').height($(window).height() -25);
	var lat =  <?php echo (isset($lat) ? $lat : "51.471952345537105")   ?>;
	var lng =  <?php echo (isset($lng) ? $lng : "-0.08856922388076782")   ?>;
	if (lat && lng) {
		var latlng = new google.maps.LatLng(lat, lng);
		var myOptions = {
			zoom      : 16,
			center    : latlng,
			mapTypeId : google.maps.MapTypeId.ROADMAP
		};
		var map = new google.maps.Map(document.getElementById("mapCanvas"),
									  myOptions);
		var marker = new google.maps.Marker({
												map       : map,
												draggable : true,
												position  : latlng
											});
		google.maps.event.addListener(marker, 'dragend', (function (event)
		{
			$('#useSelAddLoading').hide();
			marker.setPosition(event.latLng);
			document.getElementById("Address_lat").value = event.latLng.lat();
			document.getElementById("Address_lng").value = event.latLng.lng();
		}));
		google.maps.event.addListener(map, "click", function (event)
		{
			$('#useSelAddLoading').hide();
			marker.setPosition(event.latLng);
			document.getElementById("Address_lat").value = event.latLng.lat();
			document.getElementById("Address_lng").value = event.latLng.lng();
		});

	}
</script>