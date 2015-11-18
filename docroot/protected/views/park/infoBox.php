<?php
/**
 * @var $data          Place
 * @var $instruction   Deal
 * @var $detailPageUrl String
 */
$postcode = $data->addressId && $data->location->postcode ? ucfirst($data->location->postcode) : "";
?>


<div class="info-box-view park">
	<div class="thumbnail">
		<?php
		if ($data->mainViewImageId) {
			$titleTag = (!empty($data->mainViewImage->caption)) ? $data->mainViewImage->caption : $data->title;
			$altTag   = (!empty($data->mainViewImage->caption)) ? $data->title . ", " . $postcode . $data->mainViewImage->caption . "; by Wooster & Stock" : $data->title . ", " . $postcode . " by Wooster & Stock";
			echo '<a href="' . $detailPageUrl . '" title="' . $titleTag . '" onclick="parent.closePopUpRedirect(\'' . $detailPageUrl . '\')">' .
					CHtml::image(
						 Yii::app()->params['imgUrl'] . "/Place/" . $data->id . "/" . $data->mainViewImage->recordType . '/' . $data->mainViewImage->smallName,
						 $altTag,
						 array('title' => $titleTag)
					) .
					'</a>';

		} else {
			echo CHtml::image(Icon::NO_IMAGE_AVAILABLE, "No Image Available, Wooster&Stock", array('width' => '308'));
		}
		?>
	</div>

	<div class="info">
		<div class="title">
			<a href="<?php echo $detailPageUrl ?>"
			   onclick="parent.closePopUpRedirect('<?= $detailPageUrl ?>')"><?php echo $data->title; ?></a>
		</div>

		<?php if (!empty($data->addressId)) : ?>
			<div class="subtitle">
				<?php echo $data->location->city . ', <span class="uppercase">' . $data->location->postcode . '</span>'; ?>
			</div>
		<?php endif; ?>

		<div class="strapline">
			<?php echo Util::strapString(CHtml::encode($data->strapline), 0, 70); ?>
		</div>
	</div>
</div>
<div class="before"></div>
<div class="after"></div>