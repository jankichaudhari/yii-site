<?php
/**
 * @var $this LinkController
 * @var $data OuterLink
 */
?>
<div class="detail-box with-shadow">
	<div class="thumbnail">
		<?php
		if ($data->image) {
			$image = $data->image;
			echo CHtml::image($image->getUrlToFile(), $data->title);
		} else {
			echo '<img src="' . Icon::NO_IMAGE_AVAILABLE . '" alt="No Image"/>';
		}
		?>
	</div>
	<div class="info">

		<div class="block titles">
			<div class="inner-block">
				<h3 class="title">
					<?php echo $data->title ?>
				</h3>
			</div>
		</div>

		<div class="block strapline">
		<div class="inner-block">
				<?php echo Util::strapString(CHtml::encode($data->description), 0, 200) ?>
			</div>
		</div>

		<div class="block">
			<div class="inner-block">
			<?php
			$link = $data->link;
			$target = '_blank';
			if (filter_var($link, FILTER_VALIDATE_EMAIL)) {
				$link = "mailto:" . $link;
				$target = '';
			}
			?>
			<?php echo CHtml::link($data->link, $link, array(
															'target' => $target, 'class' => 'blue-link block-link'
													   )) ?>
		</div>
		</div>

	</div>
</div>
