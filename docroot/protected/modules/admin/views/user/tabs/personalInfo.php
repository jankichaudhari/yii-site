<?php
/**
 * @var $this  UserController
 * @var $model User[]
 * @var $form  AdminForm
 */
?>

<?= $form->beginControlGroup($model, 'use_salutation'); ?>
<?= $form->controlLabel($model, 'use_salutation'); ?>
	<div class="controls">
		<?php echo $form->dropDownList($model, 'use_salutation', $model->getPossibleSalutations()); ?>
	</div>
<?= $form->endControlGroup(); ?>
<?= $form->beginControlGroup($model, 'use_fname'); ?>
<?= $form->controlLabel($model, 'use_fname'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_fname'); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_sname'); ?>
<?= $form->controlLabel($model, 'use_sname'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_sname'); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_addr1'); ?>
<?= $form->controlLabel($model, 'use_addr1'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_addr1'); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_addr2'); ?>
<?= $form->controlLabel($model, 'use_addr2'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_addr2'); ?>
	</div>
<?= $form->endControlGroup(); ?>


<?= $form->beginControlGroup($model, 'use_addr3'); ?>
<?= $form->controlLabel($model, 'use_addr3'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_addr4'); ?>
	</div>
<?= $form->endControlGroup(); ?>


<?= $form->beginControlGroup($model, 'use_addr5'); ?>
<?= $form->controlLabel($model, 'use_addr5'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_addr5'); ?>
	</div>
<?= $form->endControlGroup(); ?>


<?= $form->beginControlGroup($model, 'use_postcode'); ?>
<?= $form->controlLabel($model, 'use_postcode'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_postcode', array('class' => "input-xsmall")); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_colour'); ?>
<?= $form->controlLabel($model, 'use_colour'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_colour', array('class' => "color {pickerPosition:'right'} input-xsmall")); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_status'); ?>
<?= $form->controlLabel($model, 'use_status'); ?>
	<div class="controls">
		<?php echo $form->radioButtonlist($model, 'use_status', $model->getPossibleUserStatus(), array(
																									  'separator'    => '',
																									  'labelOptions' => array('style' => 'float:none')
																								 )); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'use_username'); ?>
<?= $form->controlLabel($model, 'use_username'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'use_username'); ?>
	</div>
<?= $form->endControlGroup(); ?>