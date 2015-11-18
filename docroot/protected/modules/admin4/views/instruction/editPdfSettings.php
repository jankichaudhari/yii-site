<?php
/**
 * @var $this     InstructionController
 * @var $model    Deal
 * @var $settings InstructionToPdfSettings
 * @var $form     AdminForm
 */
?>
<div class="container-fluid">
	<?php $form = $this->beginWidget('AdminForm') ?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">Edit PDF settings</div>
				<div class="content">
					<?php if (Yii::app()->user->hasFlash('editPdfSettings-success')) : ?>
						<div class="flash success remove"><?php echo Yii::app()->user->getFlash('editPdfSettings-success') ?></div>
					<?php endif ?>
					<?php if ($model->dea_type == Deal::TYPE_SALES): ?>
						<?= $form->beginControlGroup($settings, 'displayLeaseExpires') ?>
						<?=
						$form->controlLabel($settings, 'displayLeaseExpires') ?>
						<div class="controls">
							<?php echo $form->checkBox($settings, 'displayLeaseExpires', ['uncheckValue' => 0]) ?>
						</div>
						<?= $form->endControlGroup() ?>

						<?=
					$form->beginControlGroup($settings, 'displayServiceCharge') ?>
						<?=
					$form->controlLabel($settings, 'displayServiceCharge') ?>
						<div class="controls">
							<?php echo $form->checkBox($settings, 'displayServiceCharge', ['uncheckValue' => 0]) ?>
						</div>
						<?= $form->endControlGroup() ?>

						<?= $form->beginControlGroup($settings, 'displayGroundRent') ?>
						<?= $form->controlLabel($settings, 'displayGroundRent') ?>
						<div class="controls">
							<?php echo $form->checkBox($settings, 'displayGroundRent', ['uncheckValue' => 0]) ?>
						</div>
						<?= $form->endControlGroup() ?>
					<?php endif ?>
					<?= $form->beginControlGroup($settings, 'additionalNotes'); ?>
					<?= $form->controlLabel($settings, 'additionalNotes'); ?>
					<div class="controls">
						<?php echo $form->textArea($settings, 'additionalNotes') ?>
					</div>
					<?= $form->endControlGroup(); ?>
					<div class="control-group form-buttons shaded">
						<div class="controls force-margin">
							<input type="submit" class="btn btn-primary" value="Save">
							<input type="button" class="btn btn-red" value="Close" onclick="window.close()">
						</div>
					</div>
				</div>

			</fieldset>
		</div>
	</div>
	<?php $this->endWidget(); ?>
</div>