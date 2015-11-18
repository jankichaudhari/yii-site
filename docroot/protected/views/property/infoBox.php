<?php
/**
 * @var $this          PropertyController
 * @var $model         Deal
 * @var $title         String
 * @var $price         String
 * @var $detailPageUrl String
 * @var $instruction   Deal
 */
?>

<div class="info-box-view property">
	<div class="thumbnail">
		<?php
		if ($photo = $model->getMainImage()) {
			$altTag = (!empty($photo->med_title)) ? $title . ', ' . $photo->med_title . " by Wooster & Stock" : $title . " by Wooster & Stock";
			echo '<a href="' . $detailPageUrl . '" onclick="parent.closePopUpRedirect(\'' . $detailPageUrl . '\')">' .
					CHtml::image(
						 $photo->getMediaImageURIPath('_thumb1'),
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
			   onclick="parent.closePopUpRedirect('<?= $detailPageUrl ?>')"><?php echo $title; ?></a>
		</div>


		<div class="info-row subtitle">
			<span class="price"><?php echo $price; ?></span>
			<?php if ($model->getQualifier()) : ?>
				<div class="vertical-separator"></div>
				<span class="qualifier"><?php echo $model->getQualifier() ?></span>
			<?php endif;
			if ($this->getStatusString($model->dea_status, $model->dea_type)) :
				?>
				<span class="status"><?php echo $this->getStatusString($model->dea_status, $model->dea_type); ?></span>
			<?php endif; ?>
		</div>

		<div class="info-row strapline">
			<?php echo Util::strapString(CHtml::encode($model->dea_strapline), 0, 150); ?>
		</div>
	</div>
</div>
<div class="before"></div>
<div class="after"></div>