<?php
/**
 * @var $this         LocalEventController
 * @var $model        LocalEvent
 * @var $smallDevice  bool
 */

$this->pageTitle = $model->heading;

$photoPath = Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/";
$title = $model->heading . ', ' . $model->getDate(false);
?>

<div class="localevent-details">

	<?php
	$this->renderPartial('detailsView/slider-gallery', [
			'model'       => $model,
			'smallDevice' => $smallDevice
	])
	?>

	<div class="body">
		<div class="page-content detail-page-content">

			<div class="row box">
				<div class="span12">
					<div class="main-title">
						<?php echo $model->heading; ?>
					</div>

					<div class="subtitle">
						<?php if ($model->address) : ?>
							<div class="localevent-location">
								<?php echo $model->address->getFullLocation() ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="localevent-date"><?php echo $model->getDate(false) ?></div>
					<div class="localevent-time"><?php echo $model->getTime(); ?></div>

				</div>

				<div class="span12 description-content">
					<div class="info-container">
						<div class="strapline">
							<?php echo $model->strapline ?>
						</div>
						<div class="description" id="toggle-description">
							<?php echo $model->description ?>
						</div>
					</div>
				</div>
			</div>


			<?php
			$this->renderPartial('detailsView/options', [
					'model'       => $model,
					'title'       => $title,
					'smallDevice' => $smallDevice
			]);
			?>

			<div class="row box last">
				<div class="span12">
					<?php
					$this->renderPartial('detailsView/transport', [
							'model' => $model,
							'title' => $title,
							'smallDevice' => $smallDevice,
					]);
					?>
				</div>
			</div>

		</div>
	</div>