<?php
/**
 * @var $this         LocalEventController
 * @var $model        LocalEvent
 */
$photoPath = Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/";
?>

<div class="social-options">
	<?php
	$mainImage = $model->mainImage ? $model->mainImage : '';
	$mainPhoto = $mainImage ? 'http://' . $_SERVER['HTTP_HOST'] . $photoPath . $model->mainImage->smallName : '';
	$url = 'http://' . $_SERVER['HTTP_HOST'] . '/local-event/' . $model->id . "/";

	?>
	<div id="fb-root"></div>
	<div class="fb-like" data-href="<?php echo $url ?>" data-send="true"
		 data-layout="button_count"
		 data-width="450" data-show-faces="true" data-font="verdana"></div>
	<div class="shareButton">
		<div class="facebook-share">
			<a onclick="Share.facebook('<?php echo $url ?>','<?php echo addslashes($model->heading) ?>','<?php echo $mainPhoto ?>','<?php echo addslashes($model->strapline) ?>')">share</a>
		</div>
		<div class="twitter-share">
			<a onclick="Share.twitter('<?php echo $url ?>','<?php echo $model->heading ?>')"
			   data-text="@woosterstock <?php echo $model->strapline ?>"
			   data-url="<?php echo $url ?>"
			   class="twitter-share-button" data-count="none">Tweet</a>
		</div>
	</div>
</div>
