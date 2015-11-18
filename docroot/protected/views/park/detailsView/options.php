<?php
/**
 * @var $this         ParkController
 * @var $model        Place
 * @var $title        String
 * @var $smallDevice bool
 */
$smallDeviceClass = $smallDevice ? 'smallDevice' : '';
?>

<div class="info-box options park">
	<div class="inner-padding">
		<div class="header">PARK OPTIONS</div>

		<?php if (!empty($model->location->latitude) && !empty($model->location->longitude)) : ?>
			<div class="info-row">
				<a href="<?php echo $this->createUrl('/park/showMap/id/' . $model->id . '/mode/map') ?> ?>"
				   class="show-map<?php echo $smallDeviceClass ?>" title="<?php echo $title ?>">
                                <span class="map">
                                    <span class="icon"></span>View Map
                                </span>
				</a>
			</div>
			<div class="info-row">
				<a href="<?php echo $this->createUrl('/park/showMap/id/' . $model->id . '/mode/streetview') ?>"
				   class="show-map<?php echo $smallDeviceClass ?>" title="<?php echo $title ?>">
                                <span class="streetview">
                                    <span class="icon"></span>Street View
                                </span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
