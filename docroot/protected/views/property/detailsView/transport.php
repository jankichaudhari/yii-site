<?php
/**
 * @var $this                    PropertyController
 * @var $model                   Deal
 * @var $title                   String
 * @var $price                   String
 * @var $clientScript            CClientScript
 * @var $smallDevice             bool
 */
$clientScript = Yii::app()->clientScript;
$smallDeviceClass = $smallDevice ? 'smallDevice' : '';
?>

<div class="transport-options properties">
	<?php if ($model->property->getLat() && $model->property->getLng()) {
		echo '<a href="' . $this->createUrl('/property/showMap/id/' . $model->dea_id . '/mode/map/directionId/') . '" class="show-map'.$smallDeviceClass.'" id="show-direction" title="' . $title . ', ' . $price . '" ></a>';
		$this->widget("application.components.public.widgets.nearestPlaces.nearestPlaces", array(
				'lat'              => $model->property->getLat(),
				'lng'              => $model->property->getLng(),
				'maxDistance'      => Yii::app()->params['PropertyNearestTransportDistance'],
				'count'            => Yii::app()->params['PropertyTotalNearestTransports'],
				'mapObject'        => 'map',
				'mapJourneyButton' => true,
				'id'               => $model->dea_id,
		));

		$clientScript->registerScript('nearestTrans',
									  '
							var thisWidget = getWidgetById(' . $model->dea_id . ');
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