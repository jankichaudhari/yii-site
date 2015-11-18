<?php
/**
 * @var $this         PropertyController
 * @var $instructions Deal []
 * @var $type
 * @var $propCatModel PropertyCategory[ ]
 */
$this->pageTitle = $propCatModel->title;
?>

<div class="detail-box-listings property-listings">
	<div class="page-top-block"
		 style="background : url('<?php echo $propCatModel->getImageURIPath("_wide_top") ?>') center no-repeat;">
		<div class="page-content">
			<div class="row">
			</div>
		</div>
	</div>

	<div class="body">
		<div class="page-content property-category">
			<div class="row margin-bottom"></div>
			<div class="row">
				<div class="span12">
					<div class="orange-big-heading"><?php echo $propCatModel->title ?></div>
				</div>
			</div>

			<div class="orange-big-separator"></div>

			<div class="row margin-bottom">
				<div class="span8 listings">
					<div class="info-container" style="margin-bottom: 48px">
						<div class="description">
							<?php echo $propCatModel->description ?>
						</div>
					</div>
					<?php $count = 0;
					foreach ($instructions as $instruction) {
						$count++;
						?>
						<div class="detail-box with-shadow" style="position: relative">
							<div class="thumbnail">
								<?php if ($instruction->photos) : ?>
									<a href="<?php echo $this->detailPage($instruction->dea_id) ?>">
										<img src="<?php echo $instruction->getMainImage() ? $instruction->getMainImage()->getMediaImageURIPath('_thumb1') : "" ?>"
											 alt="">
									</a>
								<?php endif; ?>
							</div>
							<div class="info">

								<div class="block titles">
									<div class="inner-block">
										<h3 class="title">
											<a href="<?php echo $this->detailPage($instruction->dea_id) ?>"><?php echo $instruction->property->getShortAddressString(', ', true) ?></a>
										</h3>

										<div class="subtitle">
										<span>
										<?php
										echo
										Locale::formatPrice(
											  $instruction->getPrice(isset($_GET['Deal']['priceMode']) ? $_GET['Deal']['priceMode'] : ""),
											  $instruction->dea_type == 'Sales' ? false : true,
											  isset($_GET['Deal']['priceMode']) ? $_GET['Deal']['priceMode'] : ""
										)
										?>
										</span>
										<?php if ($instruction->getQualifier()): ?>
											<div class="vertical-separator"></div>
											<span><?php echo $instruction->getQualifier() ?></span>
										<?php endif ?>
										<div class="vertical-separator"></div>
										<?php $statusClass = ($this->getStatusString($instruction->dea_status, $instruction->dea_type) == 'Under Offer') ? 'red' : 'orange' ?>
										<span class="uppercase <?php echo $statusClass ?>">
											<?php echo $this->getStatusString($instruction->dea_status, $instruction->dea_type) ?>
										</span>
										</div>
									</div>
								</div>

								<div class="block strapline">
									<div class="inner-block">
										<a href="<?php echo $this->detailPage($instruction->dea_id) ?>"><?php echo Util::strapString(CHtml::encode($instruction->dea_strapline), 0, 300) ?></a>
									</div>
								</div>

							</div>
						</div>
					<?php } ?>
				</div>
				<div class="span4 additional-widgets">
					<div class="contact-us-widget">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>