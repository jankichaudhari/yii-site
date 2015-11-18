<?php
/**
 * @var $this                    PropertyController
 * @var $model                   Deal
 * @var $smallDevice             bool
 */
$href = '/property-gallery/' . $model->dea_id;
$galleryClass = $smallDevice ? 'small-device-gallery' : 'gallery';
?>
<div class="slider-container">
	<?php if ($model->photos) : ?>
		<div class="slider">
			<?php
			foreach ($model->photos as $photo):
				$href = $smallDevice ? $href : $photo->getMediaImageURIPath(Media::SUFFIX_ORIGINAL);
				?>
				<?php if (file_exists($photo->getFullPath(Media::SUFFIX_FULL))) : ?>
				<div class="item" id="item<?php echo $photo->med_id; ?>">
					<a href="<?php echo $href ?>" rel="property-gallery"
					   class="<?php echo $galleryClass ?>"
					   data-id="<?php echo $photo->med_id; ?>">
						<img src="<?php echo $photo->getMediaImageURIPath(Media::SUFFIX_FULL) ?>"
							 alt="<?php echo $photo->med_title ?>">
						<span class="zoom-symbol"></span>
					</a>
				</div>
			<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div class="move-arrows">
			<div id="slider-left-arrow" class="arrow left"><span></span></div>
			<div id="slider-right-arrow" class="arrow right"><span></span></div>
		</div>

	<?php endif; ?>
</div>
