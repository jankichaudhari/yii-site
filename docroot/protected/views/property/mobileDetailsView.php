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
/** @var $branch Branch */
$branch = Branch::model()->findByPk($model->dea_branch);
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

			<div class="row box">
				<div class="span12">
					<div class="main-title"><?php echo $title ?></div>

					<div class="subtitle">
						<span class="instruction-price">
							<?php echo $model->dea_qualifier === Deal::QUALIFIER_POA ? Deal::QUALIFIER_POA : $price ?>
						</span>
						/
						<?php if ($this->getStatusString($model->dea_status, $model->dea_type)) : ?>
							<span class="instruction-status">
								<?php echo $this->getStatusString($model->dea_status, $model->dea_type) ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ($model->dea_tenure) : ?>
						<div class="instruction-tenure"><?php echo $model->dea_tenure ?></div>
					<?php endif; ?>

					<div class="overlay-cell instruction-rooms">
						<?php echo $model->getPropertyRoomString(", ") ?>
					</div>

					<?php if ($model->getQualifier()) : ?>
						<div class="overlay-cell instruction-qualifier">
							<?php echo $model->getQualifier() ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="span12">
					<span class="view-button left"><a href="tel:<?php echo Locale::formatPhone($branch->bra_tel) ?>"
													  class="btn">Call to view</a></span>
					<span class="view-button right"><a href="mailto:<?php echo $branch->bra_email ?>" class="btn">Email
							to view</a></span>
				</div>

				<div class="span12 description-content">
					<div class="info-container">

						<div class="strapline"><?php echo $model->dea_strapline ?></div>

						<div class="description" id="toggle-description">
							<?php echo $model->dea_description ?>
							<p class="disclaimer">
								Lease details, service charge and ground rent (where applicable) should be checked by
								your solicitor
								prior to exchange of contracts, they are displayed for guide purposes only.
							</p>
							<?php if ($area): ?>
								<p class="instruction-area">
									Approximate Gross Internal Area: <?php echo round($area) ?> square metres
									/ <?php echo round(Locale::metersToFeet($area)) ?> square feet
								</p>
							<?php endif ?>
						</div>

					</div>
				</div>
			</div>

			<div class="row box">
				<div class="span12">
					<?php
					$this->renderPartial('detailsView/features', [
							'model' => $model,
					]);
					?>
				</div>
			</div>

			<?php
			$this->renderPartial('detailsView/options', [
					'model'       => $model,
					'smallDevice' => $smallDevice,
					'title'       => $title,
					'price'       => $price
			]);
			?>

			<div class="row box">
				<div class="span12">
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

			<div class="row">
				<div class="span12">
					<?php $this->widget("application.components.public.widgets.BookAViewing.BookAViewing", ['deal' => $model]) ?>
				</div>
			</div>

		</div>
	</div>