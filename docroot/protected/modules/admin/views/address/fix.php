<?php
/**
 * @var $this   AddressController
 * @var $models Address[]
 * @var $form   AdminForm
 */
?>
<style type="text/css">
	.address-fix .content:nth-child(odd) {
		background : white;
	}
</style>
<fieldset class="address-fix">
	<div class="block-header">ADDRESSES</div>

	<?php foreach ($models as $model): ?>
		<div class="content">
			<?php $form = $this->beginWidget('AdminForm') ?>
			<div class="control-group">
				<label class="control-label">Address</label>

				<div class="controls text"><?php echo $model->toString(', ') ?></div>
			</div>
			<?= $form->beginControlGroup($model, 'line1'); ?>
			<?= $form->controlLabel($model, 'line1'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'line1') ?>
			</div>
			<?= $form->endControlGroup(); ?>

			<?= $form->beginControlGroup($model, 'line2'); ?>
			<?= $form->controlLabel($model, 'line2'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'line2') ?>
			</div>
			<?= $form->endControlGroup(); ?>

			<?= $form->beginControlGroup($model, 'line3'); ?>
			<?= $form->controlLabel($model, 'line3'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'line3') ?>
			</div>
			<?= $form->endControlGroup(); ?>

			<?= $form->beginControlGroup($model, 'line4'); ?>
			<?= $form->controlLabel($model, 'line4'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'line4') ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'line5'); ?>
			<?= $form->controlLabel($model, 'line5'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'line5') ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'postcode'); ?>
			<?= $form->controlLabel($model, 'postcode'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'postcode') ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?php echo $form->hiddenField($model, 'id') ?>
			<div class="block-buttons force-margin">
				<input type="submit" class="btn" value="Save" />
			</div>
			<?php $this->endWidget() ?>
		</div>
	<?php endforeach; ?>

</fieldset>