<?php
/**
 * @var $this         ParkController
 * @var $model        Place
 * @var $title        String
 * @var $instructions Deal []
 * @var $smallDevice  bool
 */
$this->pageTitle = $model->title;
?>

<div class="park-details">

	<?php
	$this->renderPartial('detailsView/slider-gallery', [
			'model'       => $model,
			'smallDevice' => $smallDevice,
			'title'       => $title
	])
	?>

	<div class="body">
		<div class="page-content detail-page-content">

			<div class="row box">
				<div class="span12">
					<div class="main-title">
						<?php echo $title; ?>
					</div>
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
					'model' => $model,
					'title' => $title,
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

