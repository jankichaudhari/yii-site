<?php
/**
 * @var $this                    PropertyController
 * @var $model                   Deal
 * @var $title String
 * @var $price String
 */
?>

<div class="social-options">
	<?php
	$mainImage = $model->photos ? $model->photos[0] : '';
	$mainPhoto = $mainImage ? 'http://' . $_SERVER['HTTP_HOST'] . $mainImage->getMediaImageURIPath(Media::SUFFIX_THUMB2) : '';
	$dealUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/details/' . $model->dea_id . "/";
	?>
	<div id="fb-root"></div>
	<div class="fb-like" data-href="<?php echo $dealUrl ?>" data-send="true" data-layout="button_count"
		 data-width="450" data-show-faces="true" data-font="verdana"></div>
	<div class="shareButton">
		<div class="facebook-share">
			<a onclick="Share.facebook('<?php echo $dealUrl ?>','<?php echo addslashes($title) ?>','<?php echo $mainPhoto ?>','<?php echo addslashes($model->dea_strapline) ?>')">share</a>
		</div>
		<div class="twitter-share">
			<a onclick="Share.twitter('<?php echo $dealUrl ?>','<?php echo $title ?>')"
			   data-text="@woosterstock <?php echo $model->dea_strapline ?>"
			   data-url="<?php echo $dealUrl ?>"
			   class="twitter-share-button" data-count="none">Tweet</a>
		</div>
	</div>
</div>