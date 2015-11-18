<?php
/**
 * @var $this         LocalEventController
 * @var $model        LocalEvent
 * @var $smallDevice  bool
 */

$photoPath = Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/";
$photoRealPath = Yii::app()->params['imgPath'] . "/LocalEvent/" . $model->id . "/";
$href = $smallDevice ? '/localevent-gallery/' . $model->id : '';
$galleryClass = $smallDevice ? 'small-device-gallery' : 'gallery';
?>

<div class="slider-container">
	<?php
	if ($model->mainImage || $model->images) :
		$href = $smallDevice ? $href : $photoPath . $model->mainImage->name;
		?>
		<div class="slider">
			<?php if (file_exists($photoRealPath . $model->mainImage->name)) : ?>
				<div class="item" id="item<?php echo $model->mainImage->id; ?>">
					<a href="<?php echo $href ?>" rel="localevent-gallery" class="<?php echo $galleryClass ?>"
					   data-id="<?php echo $model->mainImage->id; ?>">
						<img src="<?php echo $photoPath . $model->mainImage->largeName ?>"
							 alt="<?php echo $model->heading ?>">
						<span class="zoom-symbol"></span>
					</a>
				</div>
			<?php endif; ?>
			<?php
			foreach ($model->images as $photo):
				$href = $smallDevice ? $href : $photoPath . $photo->name;
				?>
				<?php if (file_exists($photoRealPath . $photo->name)) : ?>
				<div class="item" id="item<?php echo $photo->id; ?>">
					<a href="<?php echo $href ?>" rel="localevent-gallery" class="<?php echo $galleryClass ?>"
					   data-id="<?php echo $photo->id; ?>">
						<img src="<?php echo $photoPath . $photo->largeName ?>" alt="<?php echo $model->heading ?>">
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
