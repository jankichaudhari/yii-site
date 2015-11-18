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
			<!--info overlay-->
			<div class="row info-overlay">
				<div class="span12 overlay-row">
					<div>
						<span class="overlay-cell localevent-date"><?php echo $model->getDate(false) ?></span>
						<span class="overlay-cell localevent-time"><?php echo $model->getTime(); ?></span>
					</div>
					<?php if ($model->address) : ?>
						<div class="overlay-cell localevent-location">
							<?php echo $model->address->getFullLocation() ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<!--info overlay-->
			<div class="row margin-bottom">
				<div class="span8 description-content">
					<div class="main-title">
						<?php echo $model->heading; ?>
					</div>

					<div class="orange-big-separator"></div>

					<div class="info-container">

						<?php
						$this->renderPartial('detailsView/social-options', [
								'model' => $model,
						]);
						?>

						<div class="strapline">
							<?php echo $model->strapline ?>
						</div>
						<div class="description">
							<?php echo $model->description ?>
						</div>
					</div>
				</div>

				<div class="span4 widget-info-content">

					<?php
					$this->renderPartial('detailsView/options', [
							'model' => $model,
							'title' => $title,
							'smallDevice' => $smallDevice,
					]);
					?>


					<div class="info-box contact-us-widget">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>



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

<?php $this->renderPartial('//layoutElement/backToTop'); ?>