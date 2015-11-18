<?php
/**
 * @var $data Deal
 * @var $this PropertyController
 * @var $isMobile bool
 */
$detailPageUrl = $this->detailPage($data->dea_id);
?>

<div class="detail-box with-shadow">
	<div class="thumbnail">
		<a href="<?php echo $detailPageUrl ?>">
			<img src="<?php echo $data->getMainImage() ? $data->getMainImage()->getMediaImageURIPath('_thumb1') : "" ?>" alt="">
		</a>
	</div>
	<div class="info">

		<div class="block titles">
		<div class="inner-block">
			<h3 class="title">
				<a href="<?php echo $detailPageUrl ?>"><?php echo $data->property->getShortAddressString(', ', $isMobile ? false :true) ?></a>
			</h3>
			<div class="subtitle">
				<?php if ($data->dea_qualifier !== Deal::QUALIFIER_POA): ?>
					<?php echo Locale::formatPrice($data->getPrice(isset($_GET['Deal']['priceMode']) ? $_GET['Deal']['priceMode'] : "")); ?>
					<div class="vertical-separator"></div>
				<?php endif ?>
				<?php if ($data->getQualifier()): ?>
					<?php echo $data->getQualifier() ?>
					<div class="vertical-separator"></div>
				<?php endif ?>
				<?php $status = $this->getStatusString($data->dea_status, $data->dea_type);
				$statusClass = ($data->dea_status == Deal::STATUS_AVAILABLE) ? 'available' : '';
				?>
				<span class="status <?php echo $statusClass ?>">
				<?php echo $this->getStatusString($data->dea_status, $data->dea_type) ?>
			</span>
			</div>
		</div>
		</div>


		<div class="block strapline">
			<div class="inner-block">
				<a href="<?php echo $detailPageUrl ?>"><?php echo Util::strapString(CHtml::encode($data->dea_strapline), 0, 300) ?></a>
			</div>
		</div>

	</div>
</div>