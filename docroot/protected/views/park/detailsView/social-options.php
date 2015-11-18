<?php
/**
 * @var $this         ParkController
 * @var $model        Place
 * @var $title        String
 */
$photoPath = Yii::app()->params['imgUrl'] . "/Place/" . $model->id . "/";
?>
<div class="social-options">
	<?php
	$mainImage = $model->images ? $model->images[0] : '';
	$mainPhoto = $mainImage ? 'http://' . $_SERVER['HTTP_HOST'] . $photoPath . $mainImage->smallName : '';
	$url = 'http://' . $_SERVER['HTTP_HOST'] . '/park/' . $model->id . "/";

	?>
	<div id="fb-root"></div>
	<div class="fb-like" data-href="<?php echo $url ?>" data-send="true" data-layout="button_count"
		 data-width="450" data-show-faces="true" data-font="verdana"></div>
	<div class="shareButton">
		<div class="facebook-share">
			<a onclick="Share.facebook('<?php echo $url ?>','<?php echo addslashes($title) ?>','<?php echo $mainPhoto ?>','<?php echo addslashes($model->strapline) ?>')">share</a>
		</div>
		<div class="twitter-share">
			<a onclick="Share.twitter('<?php echo $url ?>','<?php echo $title ?>')"
			   data-text="@woosterstock <?php echo $model->strapline ?>"
			   data-url="<?php echo $url ?>"
			   class="twitter-share-button" data-count="none">Tweet</a>
		</div>
	</div>
</div>
