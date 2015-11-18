<?php
/**
 * @var $model Branch
 * @var $this  BranchControllerBase
 * @var $form  AdminForm
 */
/**
 * @var $cs CClientScript
 */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/jscolor/jscolor.js');
?>
<?php $form = $this->beginWidget('AdminForm') ?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header"><?php echo $model->isNewRecord ? 'Create new Branch' : 'Edit Branch' ?></div>
				<div class="clearfix"></div>

				<?php if (Yii::app()->user->hasFlash('branch-update-success')): ?>
					<div class="flash success remove"><?php echo Yii::app()->user->getFlash('branch-update-success'); ?></div>
				<?php endif ?>
				<?php if ($model->hasErrors()): ?>
					<div class="flash error"><?php echo $form->errorSummary($model); ?></div>
				<?php endif ?>

				<?= $form->beginControlGroup($model, 'bra_title'); ?>
				<?= $form->controlLabel($model, 'bra_title'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'bra_title') ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'bra_tel'); ?>
				<?= $form->controlLabel($model, 'bra_tel'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'bra_tel') ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'bra_fax'); ?>
				<?= $form->controlLabel($model, 'bra_fax'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'bra_fax') ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'bra_email'); ?>
				<?= $form->controlLabel($model, 'bra_email'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'bra_email') ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'bra_colour'); ?>
				<?= $form->controlLabel($model, 'bra_colour'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'bra_colour', ['class' => 'color']) ?>
				</div>
				<?= $form->endControlGroup(); ?>
				<?= $form->beginControlGroup($model, 'businessUnit'); ?>
				<?= $form->controlLabel($model, 'businessUnit'); ?>
				<div class="controls">
					<?php echo $form->dropDownList($model, 'businessUnit', Lists::model()->getList('businessUnit')) ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'bra_status'); ?>
				<?= $form->controlLabel($model, 'bra_status'); ?>
				<div class="controls">
					<?php echo $form->radioButtonList($model, 'bra_status', Branch::getStatuses(), ['separator' => '']) ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<div class="control-group shaded form-buttons">
					<div class="controls force-margin">
						<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn']) ?>
						<?php echo CHtml::submitButton($model->isNewRecord ? 'Create & Close' : 'Update & Close', ['class' => 'btn btn-warning', 'name' => 'close']) ?>
						<input type="button" class="btn btn-danger" value="Close" onclick="window.close()">
					</div>
				</div>
			</fieldset>
		</div>

	</div>
<?php $this->endWidget(); ?>