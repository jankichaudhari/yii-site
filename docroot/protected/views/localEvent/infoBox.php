<?php
/**
 * @var $this          LocalEventController
 * @var $model         LocalEvent
 * @var $detailPageUrl String
 */
?>

<div class="info-box-view local-event">
	<div class="thumbnail">
		<?php
		if ($photo = $model->mainImage->id) {
			$altTag = $model->heading . " by Wooster & Stock";
			echo '<a href="' . $detailPageUrl . '" onclick="parent.closePopUpRedirect(\'' . $detailPageUrl . '\')">' .
					CHtml::image(
						 Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/" . $model->mainImage->smallName,
						 $altTag
					) .
					'</a>';

		} else {
			echo CHtml::image(Icon::NO_IMAGE_AVAILABLE, "No Image Available, Wooster&Stock", array('width' => '150'));
		}
		?>
	</div>

	<div class="info">
		<div class="info-row title">
			<a href="<?php echo $detailPageUrl ?>"
			   onclick="parent.closePopUpRedirect('<?= $detailPageUrl ?>')"><?php echo $model->heading; ?></a>
		</div>


		<div class="info-row subtitle">
			<?php if ($model->getDate()) : ?>
				<span><?php echo $model->getDate(); ?></span>
			<?php endif; ?>
		</div>

		<div class="info-row strapline">
			<?php echo Util::strapString(CHtml::encode($model->strapline), 0, 70); ?>
		</div>
	</div>
</div>
<div class="before"></div>
<div class="after"></div>