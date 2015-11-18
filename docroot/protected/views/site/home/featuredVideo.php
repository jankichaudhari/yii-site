<?php
/**
 * @var $this  SiteController
 * @var $model InstructionVideo
 */

$title = $model->instruction->property->getShortAddressString(', ') . ' ' . ($model->instruction->dea_qualifier === Deal::QUALIFIER_POA ? Deal::QUALIFIER_POA : Locale::formatPrice($model->instruction->dea_marketprice));
?>

<div class="span4 home-container-small"
	 style="background-image: url(<?php echo $model->instruction->getMainImage()->getMediaImageURIPath('_large') ?>);">
	<div class="top-part">
		<a href="//player.vimeo.com/video/<?php echo $model->videoId; ?>?autoplay=1"
		   class="play-video">
			<img src="<?php echo Icon::PUBLIC_VIDEO_PLAY_ICON ?>">
		</a>
	</div>
	<div class="bottom-part">
		<div class="inner-padding">
			<div class="header">
				<a href="//player.vimeo.com/video/<?php echo $model->videoId; ?>?autoplay=1" class="play-video white-text-shadow">FEATURED VIDEO</a>
			</div>
			<div class="content">
				<?php
				echo CHtml::link($title, '/details/' . $model->instruction->dea_id, ['class' => 'gray hover bold white-text-shadow']); ?>
			</div>
		</div>
	</div>
</div>