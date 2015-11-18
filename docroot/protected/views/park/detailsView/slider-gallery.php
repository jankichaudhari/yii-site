<?php
/**
 * @var $this         ParkController
 * @var $model        Place
 * @var $smallDevice bool
 * @var $title string
 */
$photoPath = Yii::app()->params['imgUrl'] . "/Place/" . $model->id . "/";
$photoRealPath = Yii::app()->params['imgPath'] . "/Place/" . $model->id . "/";
$href = $smallDevice ? '/park-gallery/' . $model->id : '';
$galleryClass = $smallDevice ? 'small-device-gallery' : 'gallery';
?>

<div class="slider-container">
	<?php if ($model->images) : ?>
		<div class="slider">
			<?php $totalPhoto = count($model->images);
			$limit = 10;	//limit to start patterned slider
			$patternNo = 7; //must be odd number
			$count = 0;
			foreach ($model->images as $photo):
				$href = $smallDevice ? $href : $photoPath . $photo->name;
				if (file_exists($photoRealPath . $photo->mediumName)):
					$titleTag = $photo->caption ? $photo->caption : $model->title;
					$altTag   = $title . $photo->caption . "; by Wooster & Stock";
					$p        = $count % $patternNo;
					$pos      = $p % 2;
					if ($p == 0 || $totalPhoto <= $limit || ($pos == 1 && ($count + 1) == $totalPhoto)) {
						?>
						<div class="item">
							<div id="item<?php echo $photo->id; ?>">
								<a href="<?php echo $href ?>" rel="park-gallery" class="<?php echo $galleryClass ?>"
								   data-id="<?php echo $photo->id; ?>" title="<?php echo $titleTag ?>">
									<img src="<?php echo $photoPath . $photo->mediumName ?>" alt="<?php echo $altTag ?>">
									<span class="zoom-symbol"></span>
								</a>
							</div>
						</div>
					<?php
					} else if ($totalPhoto > $limit) {
						if ($pos == 1) {
							echo '<div class="item narrow">';
						}
						?>
						<div class="two-thumbs" id="item<?php echo $photo->id; ?>">
							<a href="<?php echo $href ?>" rel="park-gallery" class="<?php echo $galleryClass ?>"
							   data-id="<?php echo $photo->id; ?>" title="<?php echo $titleTag ?>">
								<img src="<?php echo $photoPath . $photo->mediumName ?>" alt="<?php echo $altTag ?>">
								<span class="zoom-symbol"></span>
							</a>
						</div>
						<?php if ($pos == 0) {
							echo '</div>';
						}
					}
					$count++;
				endif;
			endforeach; ?>
		</div>
		<div class="move-arrows">
			<div id="slider-left-arrow" class="arrow left"><span></span></div>
			<div id="slider-right-arrow" class="arrow right"><span></span></div>
		</div>
	<?php endif; ?>
</div>
