<style type="text/css">
	.mapDirections:hover {
		color : red;
	}
</style>
<?php
/**
 * @var $transportStations TransportStations
 * @var $thisLatitude
 * @var $thisLongitude
 * @var $id
 * @var $mapObject
 * @var $mapJourneyButton
 * @var $clientScript CClientScript#
 */
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile('https://maps.google.com/maps/api/js?sensor=false', CClientScript::POS_BEGIN);
$clientScript->registerScriptFile('/js/nearestTransports.js', CClientScript::POS_END);

if ($id) {
	if (!$thisLatitude || !$thisLongitude) {
		$thisLatitude  = 51.480106;
		$thisLongitude = -0.092367;
	}
	$clientScript->registerScript('nearestTransport_' . $id,
								  '

									var ' . $mapObject . ' = ' . $mapObject . ' || false;
			NearestTransportWidget(' . $id . ').init(' . $mapObject . ',' . $thisLatitude . ',' . $thisLongitude . ');
		',
								  CClientScript::POS_END
	);
}
?>

<?php if ($transportStations) : ?>
	<div class="info-box nearest-transport-widget">
		<div class="inner-padding">
			<div class="header">Nearest Transport</div>
			<?php $count = 0;
			foreach ($transportStations as $transportStation):
				$stationId = $transportStation['id'];
				$count++;
				?>
				<div class="narrow-info-row first">
					<?php $stationTypes = LinkTransportStationsToTransportTypes::model()->findAllByAttributes(['transportStation' => $stationId]);
					foreach ($stationTypes as $stationType) {
						echo '<span class="station-icon">';
						switch ($stationType->transportType) {
							case 1 :
								echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/tube.png", "tube");
								break;
							case 2 :
								echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/rail.png", "rail");
								break;
							case 3 :
								echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/dlr.png", "DLR");
								break;
							case 4 :
								echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/overground.png", "Overground");
								break;
							case 5 :
								echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/tram.png", "tram");
								break;
							case 6 :
								echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/river-ferry.png", "river");
								break;
						}
						echo '</span>';
					}
					echo '<span class="station-title">' . $transportStation['title'] . '</span>';
					?>
				</div>
				<div class="narrow-info-row station-info">
					(<span><?= $transportStation['description'] ?></span> |
					approx, <?= round($transportStation['distance'] * 1000); ?> metres away)
				</div>
				<?php if ($mapJourneyButton) : ?>
				<div class="narrow-info-row journey-button">
					<a href="javascript:void(0);" class="mapDirections mapDirectionsButton_<?= $id ?>"
					   id="<?= $stationId ?>" data-lat="<?= $transportStation['latitude'] ?>"
					   data-lng="<?= $transportStation['longitude'] ?>">
						map journey »
					</a>
				</div>
			<?php endif; ?>
				<?php if ($count != count($transportStations)) : ?>
				<div class="horizontal-dotted-separator"></div>
			<?php endif; ?>
			<?php endforeach ?>
		</div>
	</div>
<?php endif; ?>