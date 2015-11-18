<?php
/**
 * @var $this  PlaceController
 * @var $model Place[ ]
 * @var $form  AdminForm
 */
?>

<?= $form->beginControlGroup($model, 'title'); ?>
<?= $form->controlLabel($model, 'title'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'title'); ?>
	</div>
<?= $form->endControlGroup(); ?>
<?= $form->beginControlGroup($model, 'strapline'); ?>
<?= $form->controlLabel($model, 'strapline'); ?>
	<div class="controls">
		<?php echo $form->textArea($model, 'strapline', array(
															 'maxlength' => 200, 'rows' => 5, 'cols' => 46
														)); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'statusId'); ?>
<?= $form->controlLabel($model, 'statusId'); ?>
	<div class="controls">
		<?php echo $form->radioButtonList($model, 'statusId', Lists::model()->getList("PublicPlacesStatus"), array('separator' => '')); ?>
	</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'types'); ?>
<?= $form->controlLabel($model, 'types'); ?>
	<div class="controls">
		<?php echo $form->radioButtonList($model, 'typeId', Lists::model()->getList("PublicPlacesParkType"), array('separator' => '')); ?>
	</div>
<?= $form->endControlGroup(); ?>