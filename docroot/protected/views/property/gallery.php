<?php
/**
 * @var $this                PropertyController
 * @var $model               Deal
 * @var $title               String
 * @var $currentPhotoId      integer
 *
 */
$this->pageTitle = $title;
$slideStart = 1;
?>
<div class="container">
	<?php if ($model->photos) : ?>
		<div class='small-slider-container' id="gallery-slider">
			<div class='small-slider'>
				<?php $count = 0;
				foreach ($model->photos as $photo):
					if (file_exists($photo->getFullPath(Media::SUFFIX_ORIGINAL))) :
						$count++;
						if ($currentPhotoId == $photo->med_id) {
							$slideStart = $count;
						}
						list($width, $height) = getimagesize($photo->getFullPath(Media::SUFFIX_ORIGINAL));
						$class = "";
						if ($height > $width) {
							$class = "vertical";
						} else if ($width > $height) {
							$class = "horizontal";
						} else {
							$class = "square";
						}
						?>
						<div class="item" id="item<?= $photo->med_id; ?>">
							<img src="<?= $photo->getMediaImageURIPath(Media::SUFFIX_ORIGINAL) ?>"
								 alt="<?= $photo->med_title ?>" width="<?= $width ?>" height="<?= $height ?>"
								 class="<?= $class ?>" id="<?= $photo->med_id; ?>"/>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="move-arrows">
				<div id="slider-left-arrow" class="arrow left"><span></span></div>
				<div id="slider-right-arrow" class="arrow right"><span></span></div>
			</div>
		</div>
	<?php endif; ?>

	<div class="body">
		<div class="page-content">
		</div>
	</div>
</div>
<script type="text/javascript">

	var totalItems = $('.small-slider .item').length;
	var itemWidth = $('.small-slider .item').width();
	repeatSlides('.small-slider', totalItems, itemWidth);

	updatePhotoSizes();

	$(window).resize(function () {
		$('#gallery-slider').slider('update');
		updatePhotoSizes();
	});

	sliderGallery('#gallery-slider', {
		startAtSlide: '<?= $slideStart ?>',
		autoSlide: false
	});
</script>