<?php
/**
 * @var $this    OfficeController
 * @var $model   Office
 * @var $form    AdminForm
 */
?>
<?php $form = $this->beginWidget('AdminForm', ['id' => 'office-edit-form']); ?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header"><?php echo $model->isNewRecord ? 'Create new Office' : "Edit Office" ?></div>
			<?php if (Yii::app()->user->hasFlash('office-update-success')): ?>
				<div class="flash success remove"><?php echo Yii::app()->user->getFlash('office-update-success') ?></div>
			<?php endif ?>
			<?php if ($model->hasErrors()): ?>
				<div class="flash danger"><?php echo $form->errorSummary($model) ?></div>
			<?php endif ?>
			<div class="content">
				<?= $form->beginControlGroup($model, 'title'); ?>
				<?= $form->controlLabel($model, 'title'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'title', ['class' => 'input-xlarge']) ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'shortTitle'); ?>
				<?= $form->controlLabel($model, 'shortTitle'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'shortTitle') ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'description'); ?>
				<?= $form->controlLabel($model, 'description'); ?>
				<div class="controls">
					<?php echo $form->textArea($model, 'description') ?>
				</div>
				<?= $form->endControlGroup(); ?>


				<?= $form->beginControlGroup($model, 'email'); ?>
				<?= $form->controlLabel($model, 'email'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'email') ?>
				</div>
				<?= $form->endControlGroup(); ?>
				<?= $form->beginControlGroup($model, 'phone'); ?>
				<?= $form->controlLabel($model, 'phone'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'phone') ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'code'); ?>
				<?= $form->controlLabel($model, 'code'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'code', ['class' => 'input-xsmall']) ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'clientMatching'); ?>
				<?= $form->controlLabel($model, 'clientMatching'); ?>
				<div class="controls">
					<?php echo $form->checkbox($model, 'clientMatching', ['value' => 1, 'emptyValue' => 0]) ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'active'); ?>
				<?= $form->controlLabel($model, 'active'); ?>
				<div class="controls">
					<?php echo $form->checkbox($model, 'active', ['value' => 1, 'emptyValue' => 0]) ?>
				</div>
				<?= $form->endControlGroup(); ?>
			</div>
			<div class="block-buttons force-margin">
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'btn']) ?>
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Address</div>
			<div class="content"><?php $this->renderPartial('application.modules.admin4.views.address.formInline', [
																												   'fieldName'        => 'Address',
																												   'noAddressMessage' => 'Address is not selected',
																												   'model'            => $model->address,
																												   ]); ?></div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Operating Areas</div>
			<div class="content">
				<div class="control-group">
					<label class="control-label">Areas</label>

					<div class="controls" id="operationgPostcodes">
						<?php foreach ($model->areas as $postcode): ?>
							<input type="text" value="<?php echo $postcode->postcode ?>" name="operatingPostcode[]" class="input-xsmall"><br>
						<?php endforeach; ?>
						<input type="text" value="" name="operatingPostcode[]" class="input-xsmall">
						<input type="button" value="Add Postcode" class="btn btn-primary" id="addPostcodeButton">
					</div>
				</div>
			</div>
			<div class="block-buttons force-margin">
				<input type="submit" class="btn" value="Save">
			</div>

		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="control-group form-buttons shaded">
				<div class="controls force-margin">

				</div>
			</div>
		</fieldset>
	</div>
</div>
<?php if ($model->branches) : ?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">Branches</div>
				<div class="content">
					<table style="width:100%; border-collapse: collapse;" id="branch-tables">
						<tr>

							<?php foreach ($model->branches as $key => $branch): ?>
								<td>
									<div class="span12">
										<?php $this->renderPartial('application.modules.admin4.views.branch.info', ['model' => $branch]) ?>
									</div>
								</td>
							<?php endforeach; ?>
						</tr>
					</table>
				</div>
				<div class="block-buttons force-margin">
					<input type="submit" class="btn" value="Save">
				</div>
			</fieldset>
		</div>
	</div>
<?php endif ?>
<?php $this->endWidget(); ?>
<script type="text/javascript">
	$('body').on('click', '.editBranch', function ()
				 {
					 var id = $(this).data('branch-id');
					 var popup = new Popup('<?php echo $this->createAbsoluteUrl('Branch/update') ?>/id/' + id + '/callback/updateBranchInfo');
					 popup.open();
				 }
	)

	function updateBranchInfo(id)
	{
		$.get('/admin4/Branch/getInfo/', {'id' : id}, function (data)
		{
			if (el = $('#branch-info-' + id + '')) {
				el.replaceWith($('#branch-info-' + id + '', data));
			} else {

			}
		});
	}
	$('#addPostcodeButton').on('click', function ()
	{
		$(this).after('<br>', '<input type="text" value="" name="operatingPostcode[]" class="input-xsmall">');
	});

</script>