<?php
/**
 * @var $this   AddressController
 * @var $model  Address
 * @var $form   AdminForm
 */
?>
<fieldset>
	<div class="block-header">ADDRESS</div>
	<div class="content">
		<div class="flash warning">
			PLEASE NOTE THAT EDITING ADDRESS MAY AFFECT PROPERTIES AND CLIENTS
			<?php if ($model->properties): ?>
				<div>
					This Address is linked to following properties:
					<ul>
						<?php foreach ($model->properties as $value): ?>
							<li><?php echo CHtml::link($model->toString(', '), [
																			   'Property/edit', 'id' => $value->pro_id
																			   ]) ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif ?>
			<?php if ($model->clients): ?>
				<div>
					This Address is linked to following clients:
					<ul>
						<?php foreach ($model->clients as $value): ?>
							<li><?php echo CHtml::link($value->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $value->cli_id])); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif ?>
		</div>
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
			<input type="submit" class="btn" value="Save"/>
			<input type="button" class="btn btn-gray" value="Show on map" id="show-on-map-btn"/>
		</div>
		<?php $this->endWidget() ?>
	</div>
</fieldset>
<script type="text/javascript">
	$('#show-on-map-btn').on('click', function () {
		var popup = new Popup('<?php echo $this->createUrl('showOnMap', ['id' => $model->id]) ?>');
		popup.open();
		return true;
	})
</script>