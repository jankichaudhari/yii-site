<?php
/**
 * @var $this                    LocalEventController
 * @var $model                   LocalEvent
 * @var $title                   string
 * @var $smallDevice             bool
 */
$smallDeviceClass = $smallDevice ? 'smallDevice' : '';
?>

<div class="info-box options localevent">
	<div class="inner-padding">
		<div class="header">EVENT OPTIONS</div>

		<?php if (!empty($model->address->latitude) && !empty($model->address->longitude)) : ?>
			<div class="info-row">
				<a href="<?php echo $this->createUrl('/localEvent/showMap/id/' . $model->id . '/mode/map') ?> ?>"
				   class="show-map<?php echo $smallDeviceClass ?>" title="<?php echo $title ?>">
                                <span class="map">
                                    <span class="icon"></span>View Map
                                </span>
				</a>
			</div>
			<div class="info-row">
				<a href="<?php echo $this->createUrl('/localEvent/showMap/id/' . $model->id . '/mode/streetview') ?>"
				   class="show-map<?php echo $smallDeviceClass ?>" title="<?php echo $title ?>">
                                <span class="streetview">
                                    <span class="icon"></span>Street View
                                </span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
