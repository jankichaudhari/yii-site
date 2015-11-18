<?php
/**
 * @var $this         PropertyController
 * @var $instructions Deal []
 * @var $isMobile bool
 */
$topProperties = 'twenty';
$this->pageTitle = 'Top ' . ucfirst($topProperties) . ' properties in Sales';
?>

<div class="detail-box-listings property-listings">
	<div class="slider-container">
		<div class="slider">
			<?php $count = 0;
			foreach ($instructions as $instruction):
				$count++;
				$photo = $instruction->getMainImage();
				if ($photo):
					?>
					<div class="item item<?php echo $photo->med_id; ?>">
						<div>
							<a href="<?php echo $this->detailPage($instruction->dea_id) ?>">
								<img src="<?php echo $photo->getMediaImageURIPath('_full') ?>" alt="<?php echo $photo->med_title ?>"
									 width="440">
							</a>
						</div>
						<a href="<?php echo $this->detailPage($instruction->dea_id) ?>">
							<span class="top-properties-orange-round"><?php echo $count ?></span>
						</a>
					</div>
				<?php endif;
			endforeach;
			?>
		</div>
		<div class="move-arrows">
			<div id="slider-left-arrow" class="arrow left"><span></span></div>
			<div id="slider-right-arrow" class="arrow right"><span></span></div>
		</div>
	</div>

	<div class="body">
		<div class="page-content top-properties">
			<div class="row margin-bottom"></div>
			<div class="row">
				<div class="span12">
					<div class="orange-big-heading">Top <?php echo ucwords($topProperties) ?> Properties</div>
				</div>
			</div>

			<div class="orange-big-separator"></div>

			<div class="row margin-bottom">
				<div class="span8 listings">
					<?php $count = 0;
					foreach ($instructions as $instruction) {
						/** @var $instruction Deal [ ] */
						$count++;
						?>
						<div class="detail-box with-shadow" style="position: relative">
							<div class="thumbnail">
								<a href="<?php echo $this->detailPage($instruction->dea_id) ?>">
									<img src="<?php echo $instruction->getMainImage() ? $instruction->getMainImage()->getMediaImageURIPath('_thumb1') : "" ?>"
										 alt="">
								</a>
							</div>
							<div class="info">

								<div class="block titles">
									<div class="inner-block">
										<h3 class="title">
											<a href="<?php echo $this->detailPage($instruction->dea_id) ?>">
												<?php echo $instruction->property->getShortAddressString(', ', $isMobile ? false : true) ?>
											</a>
										</h3>
										<div class="subtitle">
											<?php if ($instruction->dea_qualifier !== Deal::QUALIFIER_POA): ?>
												<?php echo Locale::formatPrice($instruction->getPrice()); ?>
												<div class="vertical-separator"></div>
											<?php endif ?>
											<?php if ($instruction->getQualifier()): ?>
												<?php echo $instruction->getQualifier() ?>
												<div class="vertical-separator"></div>
											<?php endif ?>
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
							<a href="<?php echo $this->detailPage($instruction->dea_id) ?>"><span
										class="top-properties-orange-square"><?php echo $count ?></span></a>
						</div>
					<?php } ?>
				</div>
				<div class="span4 additional-widgets">
					<div class="white-bg top-border-orange with-shadow">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="back-to-top">
		<div class="fixed-wrapper">
			<div class="fixed">
				<img src="<?php echo Icon::PUBLIC_BACK_TO_TOP ?>" alt="BACK TO TOP">
			</div>
		</div>
	</div>
</div>