<?php
/**
 * @var $this         ParkController
 * @var $model        Place
 * @var $title        String
 * @var $clientScript CClientScript
 * @var $smallDevice bool
 */
$clientScript = Yii::app()->clientScript;
$smallDeviceClass = $smallDevice ? 'smallDevice' : '';
?>

<div class="transport-options  park">
	<?php if ($model->location->latitude && $model->location->longitude) {
		echo '<a href="' . $this->createUrl('/park/showMap/id/' . $model->id . '/mode/map/directionId/') . '" class="show-map'.$smallDeviceClass.'" id="show-direction" title="' . $title . '"></a>';
		$this->widget("application.components.public.widgets.nearestPlaces.nearestPlaces", array(
				'lat'              => $model->location->latitude,
				'lng'              => $model->location->longitude,
				'maxDistance'      => Yii::app()->params['ParkNearestTransportDistance'],
				'count'            => Yii::app()->params['ParkTotalNearestTransports'],
				'mapObject'        => 'map',
				'mapJourneyButton' => true,
				'id'               => $model->id,
		));

		$clientScript->registerScript('nearestTrans',
									  '
								var thisWidget = getWidgetById(' . $model->id . ');
						 thisWidget.addEvent("onBeforeClick", function (event) {
							var url = $("#show-direction").attr("href");
							$("#show-direction").attr("href",url+"/"+thisWidget.clickId);
							$("#show-direction").trigger("click");
						});
						',
									  CClientScript::POS_END
		);
	}
	?>
</div>
