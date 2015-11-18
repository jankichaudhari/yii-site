<?php
/**
 * @var $this  UserController
 * @var $model User[ ]
 * @var $for   AdminForm
 */
?>
<?= $form->beginControlGroup($model, 'use_email'); ?>
<?= $form->controlLabel($model, 'use_email'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_email'); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_branch'); ?>
<?= $form->controlLabel($model, 'use_branch'); ?>
	<div class="controls">
		<?php echo $form->dropDownList($model, 'use_branch', CHtml::listData(Branch::model()
																			 ->findAll(array('scopes' => array('active'))), 'bra_id', 'bra_title')); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'defaultCalendarID'); ?>
<?= $form->controlLabel($model, 'defaultCalendarID'); ?>
	<div class="controls">
		<?php echo $form->dropDownList($model, 'defaultCalendarID', CHtml::listData(Branch::model()
																					->findAll(array('scopes' => array('active'))), 'bra_id', 'bra_title')); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_ext'); ?>
<?= $form->controlLabel($model, 'use_ext'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_ext'); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_worktel'); ?>
<?= $form->controlLabel($model, 'use_worktel'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_worktel'); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_hometel'); ?>
<?= $form->controlLabel($model, 'use_hometel'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_hometel'); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_mobile'); ?>
<?= $form->controlLabel($model, 'use_mobile'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_mobile'); ?>
	</div>
<?= $form->endControlGroup(); ?>