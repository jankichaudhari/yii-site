<?php
/**
 * @var $this PropertyController
 * @var $model Deal
 * @var $area integer
 */
?>

	<div class="disclaimer">
		Lease details, service charge and ground rent (where applicable) should be checked by
		your solicitor
		prior to exchange of contracts, they are displayed for guide purposes only.
	</div>
<?php if ($area): ?>
	<div class="instruction-area">
		Approximate Gross Internal Area: <?php echo round($area) ?> square metres
		/ <?php echo round(Locale::metersToFeet($area)) ?> square feet
	</div>
<?php endif ?>

<?php if ($model->floorplans): ?>
	<div class="floorplans" id="floorplans">
		<?php foreach ($model->floorplans as $floorplan): ?>
			<div class="floorplan">
				<img src="<?php echo $floorplan->getMediaImageURIPath() ?>"
					 alt="<?php echo $floorplan->med_title ?>"
					 title="<?php echo $floorplan->med_title ?>">
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>