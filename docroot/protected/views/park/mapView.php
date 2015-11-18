<?php
/**
 * @var $data Place
 */
?>


<?php if ($data->addressId && $data->location->latitude && $data->location->longitude) {?>
<script type="text/javascript">
		markers.push(new google.maps.Marker ({
			map:map,
			position:new google.maps.LatLng('<?php echo $data->location->latitude ?>','<?php echo $data->location->longitude ?>'),
			placeId : '<?php echo $data->id; ?>',
			showInfoBox : false,
			icon : "/images/sys/map-tree-icon-blue.png",
			shadow : "/images/sys/map-tree-icon-shadow.png"
		}));
</script>
<?php } ?>