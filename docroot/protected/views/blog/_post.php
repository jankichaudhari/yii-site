<?php
/**
 * @var $this  BlogController
 * @var $data  Blog
 */
?>

<div class="detail-box with-shadow">
	<div class="thumbnail">
		<?php echo CHtml::link(CHtml::image($data->featuredImageModel->getUrl(), $data->title), ['blog/view', 'id' => $data->id]) ?>
	</div>
	<div class="info">

		<div class="block titles">
			<div class="inner-block">
				<h3 class="title">
					<?php echo CHtml::link($data->title, ['blog/view', 'id' => $data->id]) ?>
				</h3>
			</div>
		</div>

		<div class="block strapline">
			<div class="inner-block">
				<?php echo Util::strapString(CHtml::encode($data->strapline), 0, 300) ?>
			</div>
		</div>

		<div class="block">
			<div class="inner-block blog-date">
				<?php echo Date::formatDate('F jS', $data->created); ?>
			</div>
		</div>

	</div>
</div>
