<?php
/**
 * @var $model OuterLink[ ]
 * @var $form  AdminForm
 */
?>
<?= $form->beginControlGroup($model, 'title'); ?>
<?= $form->controlLabel($model, 'title'); ?>
<div class="controls">
	<?php echo $form->textField($model, 'title'); ?>
</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'description'); ?>
<?= $form->controlLabel($model, 'description'); ?>
<div class="controls">
	<?php echo $form->textArea($model, 'description', ['rows' => 6, 'cols' => 50]); ?>
</div>
<?= $form->endControlGroup(); ?>

<?= $form->beginControlGroup($model, 'link'); ?>
<?= $form->controlLabel($model, 'link'); ?>
<div class="controls">
	<?php echo $form->textField($model, 'link'); ?>
</div>
<?= $form->endControlGroup(); ?>

<div class="block-buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'btn']); ?>
</div>