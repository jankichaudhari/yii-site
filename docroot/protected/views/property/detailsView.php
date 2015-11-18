<?php
/**
 * @var $this                    PropertyController
 * @var $model                   Deal
 * @var $title                   String
 * @var $price                   String
 * @var $area                    integer
 * @var $smallDevice             bool
 */

$this->pageTitle = $title . ' - ' . $price;
?>
<div class="property-details">

	<?php
	$this->renderPartial('detailsView/slider-gallery', [
			'model'       => $model,
			'smallDevice' => $smallDevice
	]);
	?>

	<div class="body">
		<div class="page-content detail-page-content">
			<!--info overlay-->
			<div class="row info-overlay">
				<div class="span8 overlay-row">
					<?php if ($this->getStatusString($model->dea_status, $model->dea_type)) : ?>
						<div class="overlay-cell instruction-status">
							<?php echo $this->getStatusString($model->dea_status, $model->dea_type) ?>
						</div>
					<?php endif; ?>
					<div class="overlay-cell instruction-price">
						<?php echo $model->dea_qualifier === Deal::QUALIFIER_POA ? Deal::QUALIFIER_POA : $price ?>
					</div>
					<div class="overlay-cell instruction-rooms">
						<?php echo $model->getPropertyRoomString(", ") ?>
					</div>
				</div>
				<div class="span4 overlay-row">
					<?php if ($model->getQualifier()) : ?>
						<div class="overlay-cell instruction-qualifier">
							<?php echo $model->getQualifier() ?>
						</div>
					<?php endif; ?>

					<?php if ($model->dea_tenure) : ?>
						<div class="overlay-cell instruction-tenure"><?php echo $model->dea_tenure ?></div>
					<?php endif; ?>
				</div>
			</div>
			<!--info overlay-->
			<div class="row margin-bottom">

				<div class="span8 description-content">
					<div class="main-title"><?php echo $title ?></div>

					<div class="orange-big-separator"></div>

					<div class="info-container">

						<?php
						$this->renderPartial('detailsView/social-options', [
								'model' => $model,
								'title' => $title
						]);
						?>

						<div class="strapline"><?php echo $model->dea_strapline ?></div>

						<div class="description">
							<?php echo $model->dea_description ?>
						</div>

						<?php
						$this->renderPartial('detailsView/floorplans', [
								'model' => $model,
								'area'  => $area
						]);
						?>

					</div>
				</div>

				<div class="span4 widget-info-content">

					<?php
					$this->renderPartial('detailsView/video', [
							'model' => $model,
					]);
					?>

					<?php
					$this->renderPartial('detailsView/options', [
							'model'       => $model,
							'title'       => $title,
							'price'       => $price,
							'smallDevice' => $smallDevice
					]);
					?>


					<div class="info-box">
						<?php $this->widget("application.components.public.widgets.BookAViewing.BookAViewing", ['deal' => $model]) ?>
					</div>

					<?php
					$this->renderPartial('detailsView/features', [
							'model' => $model,
					]);
					?>

					<?php
					$this->renderPartial('detailsView/transport', [
							'model' => $model,
							'title' => $title,
							'price' => $price,
							'smallDevice' => $smallDevice,
					]);
					?>

				</div>
			</div>
		</div>
	</div>

	<?php $this->renderPartial('//layoutElement/backToTop'); ?>

	<script type="text/javascript">
		openPopUp($('#sendFriendButton'), {
			type: 'iframe',
			width: 648,
			height: 567,
			padding: '0'
		});

		openPopUp($('#epcButton'), {});

		$('.floorplanButton').on('click', function (event) {
			$('.floorplans').show();
			$('.floorplan:first').scrollView();
			return false;
		});
	</script>