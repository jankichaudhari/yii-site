<?php
/**
 * @var $this                    PropertyController
 * @var $model                   Deal
 * @var $title                   String
 * @var $price                   String
 * @var $smallDevice             bool
 */
$smallDeviceClass = $smallDevice ? 'smallDevice' : '';
?>

<div class="info-box options properties">
	<div class="inner-padding">
		<div class="header">Property Options</div>

		<?php if ($model->property->getLat() && $model->property->getLng()) : ?>
			<div class="info-row">
				<a href="<?php echo
				$this->createUrl('/property/showMap/', [
						'id'   => $model->dea_id,
						'mode' => 'map'
				]) ?>"
				   class="show-map<?php echo $smallDeviceClass ?>" title="<?php echo $title . ', ' . $price ?>">
										<span class="icon">
                                            <span class="map"></span>View Map
                                        </span>
				</a>
			</div>
			<div class="info-row">
				<a href="<?php echo
				$this->createUrl('/property/showMap/', [
						'id'   => $model->dea_id,
						'mode' => 'streetview'
				]) ?>"
				   class="show-map<?php echo $smallDeviceClass ?>" title="<?php echo $title . ', ' . $price ?>">
										<span class="icon">
                                            <span class="streetview"></span>Street View
                                        </span>
				</a>
			</div>
		<?php endif; ?>
		<div class="info-row">
			<a href="<?php echo $this->createUrl('/sendToFriend/' . $model->dea_id) ?>"
			   id="sendFriendButton<?php echo $smallDeviceClass ?>">
									<span class="icon">
                                        <span class="mail"></span>Send To Friend
                                    </span>
			</a>
		</div>
		<div class="info-row">
			<a href="/property/Pdf/<?php echo $model->dea_id ?>" target="_blank">
									<span class="icon">
                                        <span class="printer"></span>Print Brochure
                                    </span>
			</a>
		</div>
		<?php if ($model->floorplans): ?>
			<div class="info-row">
				<a href="<?php echo
				$this->createUrl('/property/floorplans/', [
						'id' => $model->dea_id,
				]) ?>" class="floorplanButton">
					<span class="icon">
						<span class="floorplan"></span>Floorplan
					</span>
				</a>
			</div>
		<?php endif; ?>
		<?php
		if ($model->epc && (file_exists($model->epc->getFullPath(Media::SUFFIX_ORIGINAL)))):
			?>
			<div class="info-row">
				<a href="<?php echo $model->epc->getMediaImageURIPath(Media::SUFFIX_ORIGINAL) ?>"
				   id="epcButton<?php echo $smallDeviceClass ?>">
									<span class="icon">
                                        <span class="epc"></span>EPC
                                    </span>
				</a>
			</div>
		<?php
		endif;
		?>
	</div>
</div>
