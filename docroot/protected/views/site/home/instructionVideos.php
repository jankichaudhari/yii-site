<?php
/**
 * @var $instructionVideos
 */
?>
<div class="row margin-bottom">
	<div class="span12 scroll-gallery-container">
		<?php
		foreach ($instructionVideos as $i => $video):
			$instruction = $video->instruction;
			$photo       = $instruction->photos && $instruction->getMainImage()->getMediaImageURIPath(Media::SUFFIX_THUMB1) ? $instruction->getMainImage()
																																		  ->getMediaImageURIPath(Media::SUFFIX_THUMB1) : Icon::NO_IMAGE_AVAILABLE;
			$info        = $instruction->property->getLine(3) . '<br>' . Locale::formatPrice($instruction->dea_marketprice);
			?>
			<div class="home-video-container <?= !$i ? 'first' : '' ?>"
				 style="background-image: url(<?= $photo ?>);">
				<div class="top-part">
					<a href="http://player.vimeo.com/video/<?php echo $video->videoId; ?>?autoplay=1"
					   class="play-video">
						<img src="<?php echo Icon::PUBLIC_VIDEO_PLAY_ICON_SMALL ?>">
					</a>
				</div>
				<div class="bottom-part">
					<div class="inner-padding">
						<div class="content">
							<?php echo CHtml::link($info, '/details/' . $instruction->dea_id, ['class' => 'bold gray hover']); ?>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

