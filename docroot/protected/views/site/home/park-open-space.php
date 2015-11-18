<?php
/**
 * @var $this SiteController
 */
?>

<div class="row margin-bottom">
	<div class="span12 park-open-space">
		<div class="span4 park-open-space-content">
			<div class="home-orange-headline">
				<a href="<?php echo $this->createUrl('Park/Index') ?>">
					Parks & Open Spaces
				</a>
			</div>
			<div class="home-gray-description">
				<a href="<?php echo $this->createUrl('Park/Index') ?>">
					Take a tour of south London’s finest parks & open spaces. We are adding new parks
					constantly so check for updates.
				</a>
			</div>
		</div>
		<div class="span8 home-page-park-gallery-container">
			<div class="home-page-park-gallery" id="home-page-park-gallery">
				<?php
				$imageFolderPath = Yii::app()->params['imgPath'] . '/galleryImages/';
				if ($galleryImages = glob(realpath($imageFolderPath) . "/*.{jpg,gif,png}", GLOB_BRACE)) {
					foreach ($galleryImages as $galleryImage) {
						$imageName = '/images/galleryImages/' . basename($galleryImage);
						?>
						<img class="home-park-img" src="<?php echo $imageName; ?>"
							 alt="<?php echo $imageName . '; by Wooster & Stock' ?>" /><?php
					}
				}
				?>
			</div>
			<div class="move-arrows-white">
				<span id="park-left-arrow" class="arrow left"></span>
				<span id="park-right-arrow" class="arrow right"></span>
			</div>
		</div>
	</div>
</div>

