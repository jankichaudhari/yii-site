<?php
/**
 * @var $this        PropertyController
 * @var $model       Deal
 * @var $pdf         WKPDF
 * @var $settings    InstructionToPdfSettings
 * @var $cssFileName String
 */
$price = $this->getPriceWithQualifier($model);
$office = $model->branch->office;
$area = 0;
foreach ($model->floorplans as $floorplan) {
	if ($floorplan->med_dims) {
		$area += $floorplan->med_dims;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php foreach ($cssFiles as $key => $cssFileName): ?>
		<link rel="stylesheet" href="<?= $pdf->getResource('css', $cssFileName) ?>">
	<?php endforeach; ?>
	<style type="text/css"></style>
	<meta charset="UTF-8">
</head>
<body>
<?php

if (!$office) {
	echo "Sorry you must choose a correct branch for this instruction!";
	return;
}

?>
<div class="pdfWrapper">
<div class="office-info">
	<?php if ($office->hasBranch(Branch::SALES)): ?>
		<span class="bold">sales</span><span class="sep-dot">·</span> <?= $office->getBranch(Branch::SALES)->bra_tel ?> <span class="sep-pipe"></span>
	<?php endif ?>
	<?php if ($office->hasBranch(Branch::LETTINGS)): ?>
		<span class="bold">lettings</span>
		<span class="sep-dot">·</span>
		<?= $office->getBranch(Branch::LETTINGS)->bra_tel ?>
		<span class="sep-pipe"></span>
	<?php endif ?>
	<span class="bold">mail</span> <span class="sep-dot">·</span> <?= $office->email ?>
</div>


<div class="vertical-orange-line"></div>
<div class="property-details">

	<?php if ($model->getMainImage() && $model->getMainImage()->getFullPath('full')): ?>
		<div class="MAIN-IMAGE">
			<img src="<?= $pdf->addImage($model->getMainImage()->getFullPath('full')) ?>" alt="">
		</div>
	<?php elseif ($model->photos): ?>
		<div class="MAIN-IMAGE-THUMBS">
			<?php foreach ($model->photos as $key => $value): ?>
				<?php if ($value->getFullPath('thumb1')): ?>
					<img src="<?= $pdf->addImage($value->getFullPath('thumb1')) ?>" alt="">
				<?php endif ?>
				<?php if ($key + 1 == 9) break ?>

			<?php endforeach; ?>
		</div>

	<?php endif ?>


	<div class="right">
		<div class="box">
                <span style="line-height: 21pt; height:20pt; padding-right: 4.501mm; ">
					<span class="bold">Property</span>
					<span style="letter-spacing: .1em" class="light italic">Details</span>
				</span>
		</div>

		<div class="box small-vertical-padding">
                <span style="line-height: 14pt; color: white; height:48pt" class="ten light" id="block1">
                <?php //var_dump($model->property->getAddressObject()->getAreaObject()); exit; ?>
					<?= $model->property->getAddressObject()->getLine(3); ?><br>
					<?= $model->property->getAddressObject()->getAreaObject() ? $model->property->getAddressObject()->getAreaObject()->are_title . ', ' : '' ?>
					<?= $model->property->getAddressObject()->getPostcodePart() ?><br>
					<span class="light-bold"><?= $price ?></span>

                </span>
		</div>

		<div class="box medium-vertical-padding" style="height:98.5pt;">
			<table class="property-rooms-details">
				<tr>
					<td style="text-align: right;">
                            <span class="light italic thirteen" style="padding-right: 10pt"><?= $model->dea_bedroom ? : '' ?>
								Bedroom<?= $model->dea_bedroom != 1 ? 's' : '' ?><?= $model->dea_bedroom ? '' : ' N/A' ?></span>
					</td>
					<td class="image"><img src="<?= $pdf->addImage(Yii::app()->params['imgPath'] . '/exportToPDF/bedrooms.png') ?>"></td>
				</tr>
				<tr>
					<td style="text-align: right;">
                            <span class="light italic thirteen" style="padding-right: 10pt"><?= $model->dea_reception ? : '' ?>
								Reception<?= $model->dea_reception != 1 ? 's' : '' ?><?= $model->dea_reception ? '' : ' N/A' ?></span>
					</td>
					<td class="image"><img src="<?= $pdf->addImage(Yii::app()->params['imgPath'] . '/exportToPDF/receptions.png') ?>"></td>
				</tr>
				<tr>
					<td style="text-align: right;">
                            <span class="light italic thirteen" style="padding-right: 10pt"><?= $model->dea_bathroom ? : '' ?>
								Bathroom<?= $model->dea_bathroom != 1 ? 's' : '' ?><?= $model->dea_bathroom ? '' : ' N/A' ?></span>
					</td>
					<td class="image"><img src="<?= $pdf->addImage(Yii::app()->params['imgPath'] . '/exportToPDF/bathrooms.png') ?>"></td>
				</tr>
			</table>
		</div>
		<div class="box medium-vertical-padding" style="height: 105pt">
			<div class="light italic thirteen" style="text-align: right; line-height: 21pt">
				<?php foreach ($model->features as $feature): ?>
					<div><span class="bold plus">+</span><?= $feature->fea_title ?></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

</div>
<div style="clear:both"></div>
<div class="property-description-header">
	<div class="bold twentytwo"><?= $model->property->getAddressObject()->getLine(3); ?></div>
	<div class="seventeen"><?= $price ?>, <?=
		$model->property->getAddressObject()->getAreaObject() ? $model->property
						->getAddressObject()->getAreaObject()->are_title . ', ' : '' ?>
		<?= $model->property->getAddressObject()->getPostcodePart() ?></div>
</div>
<div class="property-description" id="property-description">
	<p class="strapline"><?= $model->dea_strapline ?></p>
	<?= $model->dea_description ?>
</div>

<div class="property-images">
	<?php foreach ($model->photos as $i => $value): ?>
		<?php if ($value->getFullPath('large')): ?>
			<img src="<?= $value->getFullPath('large') ?>" alt="">
		<?php elseif ($value->getFullPath('small')): ?>
			<img src="<?= $value->getFullPath('small') ?>" alt="">
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if (count($model->photos) % 2 !== 0): ?>
		<?php $officeImage = $pdf->addImage(Yii::app()->params['imgPath'] . '/exportToPDF/' . strtolower($office->shortTitle) . '-square.jpg') ?>
		<img src="<?= $officeImage ?>" alt="" />
	<?php endif ?>
</div>

<div style="clear:both; page-break-before: always"></div>

<?php foreach ($model->floorplans as $floorplan): ?>
	<div class="section">
		<div class="section-header">
			Floorplan
			<?php if ($area): ?>
				<span class="normal">
        - Approximate Gross Internal Area: <em><?php echo round($area) ?></em> square
        metres / <em><?php echo round(Locale::metersToFeet($area)) ?>
						square feet</em>
		</span>
			<?php endif ?>
		</div>
		<?php if ($floorplan->getFullPath('')): ?>
			<div style="text-align: center; clear:both; " class="floorplan-img"><img src="<?= $pdf->addImage($floorplan->getFullPath('')) ?>" alt=""></div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>

<?php if ($model->epc && ($model->epc->getFullPath('original'))): ?>
	<div class="section epc">
		<div class="section-header">
			Energy Performance Certificate
		</div>
		<div style="text-align: center; clear:both;"><img src="<?= $pdf->addImage($model->epc->getFullPath('original')) ?>"></div>
	</div>
<?php endif ?>
<div class="section no-page-break-after">
	<div class="section-header">
		Property Details
	</div>
	<div class="property-details">
		<div class="MAIN-IMAGE" style="background:white">
			<img src="https://maps.googleapis.com/maps/api/staticmap?center=<?= $model->property->getAddressObject()->getLat() ?>,<?=
			$model->property->getAddressObject()
							->getLng() ?>&zoom=16&markers=color:blue|<?= $model->property->getAddressObject()->getLat() ?>,<?=
			$model->property->getAddressObject()
							->getLng() ?>&size=600x600&sensor=false&key=AIzaSyCQyNFU65v5VDb0mUaO_1tMA2nVsuENZr0" alt="">
		</div>

		<?php if ($model->dea_type == Deal::TYPE_SALES): ?>
			<div class="right">
				<div class="box leftText">
									                <span style="line-height: 21pt; height:20pt; text-align: left">
														<span class="bold">Property</span>
														<span style="letter-spacing: .1em" class="light italic">Information</span>
													</span>
				</div>
				<div class="box medium-vertical-padding leftText" style="padding-right: 1.8mm; line-height: 21pt; font-size: 11pt">
					<div style="float:left; font-weight: bold">+ Tenure</div>
					<div style="font-style: italic; text-align: right;"><?= ($model->dea_tenure ? $model->dea_tenure : "N/A") ?></div>
					<div style="float:left; font-weight: bold">+ Lease Expires</div>
					<div style="font-style: italic; text-align: right;"><?= $settings->displayLeaseExpires && $model->dea_leaseend ? $model->dea_leaseend : 'N/A' ?></div>
					<div style="float:left; font-weight: bold">+ Ground Rent</div>
					<div style="font-style: italic; text-align: right;"><?= $settings->displayGroundRent && $model->dea_groundrent ? $model->dea_groundrent : 'N/A' ?></div>
					<div style="float:left; font-weight: bold">+ Service Charge</div>
					<div style="font-style: italic; text-align: right;"><?= $settings->displayServiceCharge && $model->dea_servicecharge ? $model->dea_servicecharge : 'N/A' ?></div>
				</div>
			</div>
		<?php endif ?>
		<?php if ($settings->additionalNotes): ?>
			<div class="right" style="margin-top: 14pt">
				<div class="box leftText">
																                <span style="line-height: 21pt; height:20pt; text-align: left">
																					<span class="bold">Additional</span>
																					<span style="letter-spacing: .1em" class="light italic">Notes</span>
																				</span>
				</div>
				<div class="box medium-vertical-padding leftText" style="padding-right: 1.8mm; line-height: 14pt; font-size: 11pt">
					<div style="float:left;"><?= nl2br($settings->additionalNotes) ?></div>
					<div style="clear:both;"></div>
				</div>
			</div>
		<?php endif ?>
		<div class="right" style="margin-top: 5mm; font-size: 8pt; line-height: 1.4; color:rgb(105,105,105); margin-bottom: 5mm; ">
			Lease details, service charge and ground rent (where applicable) should be checked by your solicitor prior to exchange of contracts, they are displayed for guide
			purposes only.
		</div>
	</div>

	<div style="margin-top: 33.568mm;background: rgb(204, 204,204); height:74mm;">

		<div style="width:103mm; float:left">
			<div style="padding-top:3.693mm; padding-left: 3.75mm ">
				<div><span class="bold">WOOSTER<span style="color:white">&</span>STOCK</span>
					<span style="font-style: italic; letter-spacing: .1em;" class="light"><?= strtoupper($office->shortTitle) ?></span></div>
				<p style="font-size: 11pt; color:rgb(88,88,90)">
					<?= $office->getAddressObject()->getLine(1) ?> <?php echo $office->getAddressObject()->getLine(3) ?>
					<br>
					<?= $office->getAddressObject()->getLine(5) ?>
					<br><?= $office->getAddressObject()->getPostcode() ?>
				</p>

				<p style="font-size: 11pt; color:rgb(88,88,90)">
					<?php if ($office->hasBranch(Branch::SALES)): ?>
						<span style="color:black">Sales: </span><?= $office->getBranch(Branch::SALES)->bra_tel ?>
					<?php endif ?>
					<?php if ($office->hasBranch(Branch::LETTINGS)): ?>
						<span style="color:black">Lettings: </span><?= $office->getBranch(Branch::LETTINGS)->bra_tel ?>
					<?php endif ?>
					<br>
					<?= $office->email ?>
				</p>

				<p style="font-size: 11pt; color:rgb(88,88,90)">
					<span style="color:#000000;">Mondays to Fridays</span><br>
					9am to 6pm <br>
					<span style="color:#000000;">Saturdays</span><br>
					9am to 5pm
				</p>
			</div>
		</div>
		<div style="width:74.4mm; float:right;">
			<?php if ($office->getAddressObject()->getLat() && $office->getAddressObject()->getLng()): ?>
				<img style="width:74.4mm; height:74mm;" src="https://maps.googleapis.com/maps/api/staticmap?center=<?=
				$office->getAddressObject()
					   ->getLat() ?>,<?= $office->getAddressObject()->getLng() ?>&zoom=16&markers=color:red|<?=
				$office->getAddressObject()
					   ->getLat() ?>,<?= $office->getAddressObject()->getLng() ?>&size=600x600&sensor=false&key=AIzaSyCQyNFU65v5VDb0mUaO_1tMA2nVsuENZr0" alt="">
			<?php endif ?>
		</div>
	</div>
</div>

</body>
</html>
