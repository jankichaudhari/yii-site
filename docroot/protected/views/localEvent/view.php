<?php
/**
 * @var $this LocalEventController
 * @var $data LocalEvent
 */
$url = trim(str_replace("http:", "", $data->url), " /");
$detailPageUrl = $this->detailPage($data->id);
?>
<div class="detail-box with-shadow">
	<div class="thumbnail">
		<?php
		if ($data->mainImage) {
			$image = CHtml::image(Yii::app()->params['imgUrl'] . "/LocalEvent/" . $data->id . "/" . $data->mainImage->mediumName, $data->heading);
			echo CHtml::link(
					  CHtml::image(Yii::app()->params['imgUrl'] . "/LocalEvent/" . $data->id . "/" . $data->mainImage->mediumName, $data->heading),
					  $detailPageUrl
			);
		} else {
			echo '<img src="' . Icon::NO_IMAGE_AVAILABLE . '" alt="No Image"/>';
		}
		?>
	</div>
	<div class="info">

		<div class="block titles">
			<div class="inner-block">
				<h3 class="title">
					<?php echo CHtml::link(
									CHtml::encode($data->heading),
									$detailPageUrl
					) ?>
				</h3>

				<div class="subtitle">
				<span class="event-date">
					<?php echo $data->getDate(false); ?>
				</span>
				<span class="event-time">
					<?php echo $data->getTime(); ?>
				</span>
				</div>
			</div>
		</div>

		<div class="block strapline">
			<div class="inner-block">
				<a href="<?= $detailPageUrl ?>"><?php echo Util::strapString(CHtml::encode($data->strapline . $data->strapline), 0, 300) ?></a>
			</div>
		</div>

		<div class="block">
			<div class="inner-block">
				<?php echo CHtml::link(CHtml::encode($url), $data->url, array(
						'target' => '_blank',
						'class'  => 'blue-link block-link'
				)) ?>
			</div>
		</div>

	</div>
</div>
