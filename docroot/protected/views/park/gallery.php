<?php
/**
 * @var $this                ParkController
 * @var $model               Place
 * @var $title               String
 * @var $currentPhotoId      integer
 *
 */
$this->pageTitle = $title;
$slideStart = 1;
$photoPath = Yii::app()->params['imgUrl'] . "/Place/" . $model->id . "/";
$photoRealPath = Yii::app()->params['imgPath'] . "/Place/" . $model->id . "/";
?>
<div class="container">
	<?php if ($model->images) : ?>
		<div class='small-slider-container' id="gallery-slider">
			<div class='small-slider'>
				<?php $count = 0;
				foreach ($model->images as $photo):
					if (file_exists($photoRealPath . $photo->name)):
						$count++;
						if ($currentPhotoId == $photo->id) {
							$slideStart = $count;
						}
						list($width, $height) = getimagesize($photoRealPath . $photo->name);
						$class = "";
						if ($height > $width) {
							$class = "vertical";
						} else if ($width > $height) {
							$class = "horizontal";
						} else {
							$class = "square";
						}
						?>
						<div class="item" id="item<?= $photo->id; ?>">
							<img src="<?= $photoPath . $photo->name ?>"
								 alt="<?= $model->title ?>" width="<?= $width ?>" height="<?= $height ?>"
								 class="<?= $class ?>" id="<?= $photo->id; ?>"/>
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