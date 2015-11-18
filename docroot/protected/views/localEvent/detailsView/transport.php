<?php
/**
 * @var $this         LocalEventController
 * @var $model        LocalEvent
 * @var $title string
 * @var $clientScript CClientScript
 * @var $smallDevice bool
 */
$clientScript = Yii::app()->clientScript;
$smallDeviceClass = $smallDevice ? 'smallDevice' : '';
?>

<div class="transport-options localevent">
	<?php if ($model->address->latitude && $model->address->longitude) {
		echo '<a href="' . $this->createUrl('/localEvent/showMap/id/' . $model->id . '/mode/map/directionId/') . '" class="show-map'.$smallDeviceClass.'" id="show-direction" title="' . $title . '"></a>';
		$this->widget("application.components.public.widgets.nearestPlaces.nearestPlaces", array(
				'lat'              => $model->address->latitude,
				'lng'              => $model->address->longitude,
				'maxDistance'      => Yii::app()->params['localeventNearestTransportDistance'],
				'count'            => Yii::app()->params['localeventTotalNearestTransports'],
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
