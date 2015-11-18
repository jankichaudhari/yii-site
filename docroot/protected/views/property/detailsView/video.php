<?php
/**
 * @var $this                    PropertyController
 * @var $model                   Deal
 */
?>

<?php if ($model->video): ?>
	<?php foreach ($model->video as $value):
		$videoPhoto = ($model->photos && $model->getMainImage()->getMediaImageURIPath('_large')) ? $model->getMainImage()
																										 ->getMediaImageURIPath('_large') : Icon::NO_IMAGE_AVAILABLE;
		?>
		<div class="instruction-video with-shadow additional-widgets" style="background-image: url('<?php echo $videoPhoto ?>');">
			<div class="video-title">
				WATCH VIDEO
			</div>
			<div class="video-info">
				<a class="play-video"
				   href="//player.vimeo.com/video/<?php echo $value->getVideoData()->id ?>?autoplay=1">
					<img src="<?php echo Icon::PUBLIC_VIDEO_PLAY_ICON ?>">
				</a>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif ?>