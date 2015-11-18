<?php
/**
 * @var $this                 PropertyController
 * @var $id
 * @var $type                 String
 * @var $multiple
 * @var $latitude             String
 * @var $longitude            String
 * @var $mode                 String
 * @var $nearestTransport
 * @var $properties           Deal[]
 * @var $parks                Place[]
 * @var $mapZoom              String
 * @var $mapDim               String
 */
$isMobile = Yii::app()->device->isDevice('mobile');
if (!isset($latitude) || !isset($longitude)) :
	throw new CHttpException('Latitude or Longitude is not defined');
endif;
$id = isset($id) ? $id : 0;
$type = isset($type) ? $type : '';
$multiple = isset($multiple) ? $multiple : false;
$showBox = isset($showBox) ? $showBox : true;
$nearestTransport = isset($nearestTransport) ? $nearestTransport : false;

$smallDevice = Yii::app()->device->isDevice('smallDevice');
$pixelOffsetX = $smallDevice ? -100 : -170;
$pixelOffsetY = $smallDevice ? -75 : -75;

/** @var $clientScript CClientScript */
$clientScript = Yii::app()->clientScript;
$defaultPin = '';
$defaultShadow = '';
$maxDistance = 2000;
$stationsCount = 10;
switch ($type) {
	case 'instruction' :
		$defaultPin    = Icon::PUBLIC_MAP_PIN_PROPERTY_ACTIVE;
		$defaultShadow = Icon::PUBLIC_MAP_PIN_PROPERTY_SHADOW;
		$maxDistance   = Yii::app()->params['PropertyNearestTransportDistance'];
		$stationsCount = Yii::app()->params['PropertyTotalNearestTransports'];
		break;
	case 'park' :
		$defaultPin    = Icon::PUBLIC_MAP_PIN_PARK_ACTIVE;
		$defaultShadow = Icon::PUBLIC_MAP_PIN_PARK_SHADOW;
		$maxDistance   = Yii::app()->params['ParkNearestTransportDistance'];
		$stationsCount = Yii::app()->params['ParkTotalNearestTransports'];
		break;
	case 'localEvent' :
		$defaultPin    = Icon::PUBLIC_MAP_PIN_LOCALEVENT_ACTIVE;
		$defaultShadow = Icon::PUBLIC_MAP_PIN_LOCALEVENT_SHADOW;
		$maxDistance   = Yii::app()->params['localeventNearestTransportDistance'];
		$stationsCount = Yii::app()->params['localeventTotalNearestTransports'];
		break;
}
if (!$clientScript->isScriptFileRegistered('https://maps.google.com/maps/api/js?sensor=false')) :
	$clientScript->registerScriptFile('https://maps.google.com/maps/api/js?sensor=false', CClientScript::POS_BEGIN);
endif;
if (!$clientScript->isScriptFileRegistered('/js/infobox.js')) :
	$clientScript->registerScriptFile('/js/infobox.js', CClientScript::POS_BEGIN);
endif;
?>


<div class="row-fluid white-bg map-wrap">
	<div class="map-canvas"
		 id="map<?php echo $id ?>" <?php echo isset($mapDim) ? 'style="width:' . $mapDim['w'] . ';height:' . $mapDim['h'] . '"' : '' ?>></div>
	<?php if (isset($nearestTransport) && $nearestTransport && $latitude && $longitude && $id) : ?>
		<div class="transport">
			<?php $this->widget("application.components.public.widgets.nearestPlaces.nearestPlaces", array(
					'lat'              => $latitude,
					'lng'              => $longitude,
					'maxDistance'      => $maxDistance,
					'count'            => $stationsCount,
					'mapObject'        => 'map',
					'mapJourneyButton' => true,
					'id'               => $id,
			));

			if (isset($_GET["directionId"])) {
				$clientScript->registerScript('nearestTrans2',
											  '
												getWidgetById(' . $id . ').showRoute(' . $_GET["directionId"] . ');
					',
											  CClientScript::POS_END
				);
			}
			?>
		</div>
	<?php endif; ?>
</div>

<script type="text/javascript">

	<?php if ($latitude && $longitude) :?>
	var latlng = new google.maps.LatLng(<?php echo $latitude ?>, <?php echo $longitude ?>);
	var myOptions = {
		zoom: <?php echo isset($mapZoom) ? $mapZoom : 16 ?>,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map<?php echo $id ?>"),
			myOptions);
	map.getStreetView().setPosition(latlng);
	<?php if ($mode == 'streetview') : ?>
	showStreetView();
	<?php else : ?>
	showMap();
	<?php endif ?>

	var markers = [];
	<?php if(!$multiple && $id && $latitude && $longitude) : ?>
	markers.push(new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng('<?php echo $latitude ?>', '<?php echo $longitude ?>'),
		id: '<?php echo $id ?>',
		type: '<?php echo $type ?>',
		showInfoBox: '<?php echo $showBox ?>',
		icon: '<?php echo $defaultPin ?>',
		shadow: '<?php echo $defaultShadow ?>'
	}));
	<?php endif;

if(isset($properties) && $properties):
	foreach ($properties as $property) :
		if ($property->property->getLat() && $property->property->getLng() && ($property->dea_id != $id)) {
			?>
	markers.push(new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(<?php echo $property->property->getLat() ?>, <?php echo $property->property->getLng() ?>),
		id: '<?php echo $property->dea_id ?>',
		type: 'instruction',
		showInfoBox: false,
		icon: '<?php echo $multiple&& $type=='instruction' ? Icon::PUBLIC_MAP_PIN_PROPERTY_ACTIVE : Icon::PUBLIC_MAP_PIN_PROPERTY ?>',
		shadow: '<?php echo Icon::PUBLIC_MAP_PIN_PROPERTY_SHADOW ?>'
	}));
	<?php }
endforeach;
endif;

if(isset($parks) && $parks):
foreach ($parks as $park) :
if ($park->location->latitude && $park->location->longitude && ($park->id != $id)) {
	?>
	markers.push(new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(<?php echo $park->location->latitude ?>, <?php echo $park->location->longitude ?>),
		id: '<?php echo $park->id ?>',
		type: 'park',
		showInfoBox: false,
		icon: '<?php echo $multiple&& $type=='park' ? Icon::PUBLIC_MAP_PIN_PARK_ACTIVE : Icon::PUBLIC_MAP_PIN_PARK ?>',
		shadow: '<?php echo Icon::PUBLIC_MAP_PIN_PARK_SHADOW ?>'
	}));
	<?php }
endforeach;
endif;

if(isset($localEvents) && $localEvents):
foreach ($localEvents as $localEvent) :
if ($localEvent->address->latitude && $localEvent->address->longitude && ($localEvent->id != $id)) {
	?>
	markers.push(new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(<?php echo $localEvent->address->latitude ?>, <?php echo $localEvent->address->longitude ?>),
		id: '<?php echo $localEvent->id ?>',
		type: 'localEvent',
		showInfoBox: false,
		icon: '<?php echo $multiple && $type=='localEvent' ? Icon::PUBLIC_MAP_PIN_LOCALEVENT_ACTIVE : Icon::PUBLIC_MAP_PIN_LOCALEVENT ?>',
		shadow: '<?php echo Icon::PUBLIC_MAP_PIN_LOCALEVENT_SHADOW ?>'
	}));
	<?php }
endforeach;
endif;
?>
	var infowindow = new InfoBox({
		content: "",
		map: map,
		disableAutoPan: false,
		maxWidth: 0,
		alignBottom: true,
		pixelOffset: new google.maps.Size(<?php echo $pixelOffsetX ?>,<?php echo $pixelOffsetY ?>),
		zIndex: null,
		boxClass: "infoBox",
		<?php if($isMobile): ?>
		closeBoxURL: '<?php echo Icon::PUBLIC_BOX_CLOSE ?>',
		<?php else: ?>
		closeBoxURL: '<?php echo Icon::PUBLIC_MAP_BOX_CLOSE ?>',
		<?php endif; ?>
		closeBoxMargin: "0px",
		pane: "floatPane",
		enableEventPropagation: false,
		infoBoxClearance: "10px" });

	var openInfoWindow = function (marker) {
		var url = '';
		switch (marker.type) {
			case 'instruction' :
				url = '<?php echo $this->createUrl('/property/infoBox') ?>/id/' + marker.id;
				break;
			case 'park' :
				url = '<?php echo $this->createUrl('/park/infoBox') ?>/id/' + marker.id;
				break;
			case 'localEvent' :
				url = '<?php echo $this->createUrl('/localEvent/infoBox') ?>/id/' + marker.id;
				break;
		}
		if (url) {
			$.get(url, function (data) {
				infowindow.setContent(data);
				infowindow.open(map, marker);
				map.panTo(marker.getPosition());
			});
		}
	};

	for (marker in markers) {
		<?php if($showBox) : ?>
		if (markers[marker].showInfoBox == true) {
			openInfoWindow(markers[marker]);
		}
		google.maps.event.addListener(markers[marker], "click", function (event) {
			openInfoWindow(this);
		});
		<?php endif; ?>
	}
	function showMap() {
		map.getStreetView().setVisible(false);
		return false;
	}
	function showStreetView() {
		map.getStreetView().setVisible(true);
		return false;
	}
	map.getStreetView().setPosition(latlng);
	<?php endif; ?>

	$('.map-wrap').children().each(function () {
		var newH;
		<?php if (isset($mapDim) && $mapDim['h']) : ?>
		newH = '<?php echo $mapDim['h'] ?>';
		<?php else : ?>
		newH = $(window).height() - window.parent.$('#fancybox-title').height();
		<?php endif; ?>
		$(this).height(newH);
	});
</script>