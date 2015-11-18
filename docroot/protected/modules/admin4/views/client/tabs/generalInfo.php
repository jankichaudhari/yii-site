<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 */
?>
<div class="content">
	<?php echo $form->beginControlGroup($model, 'cli_fname') ?>
	<?php echo $form->controlLabel($model, 'cli_fname') ?>
	<div class="controls">
		<?php echo $form->dropDownList($model, 'cli_salutation', $model->getSalutationTypes(), array('class' => 'input-xxsmall')); ?>
		<?php echo $form->textField($model, 'cli_fname') ?>
	</div>
	<?php echo $form->endControlGroup() ?>

	<?php echo $form->beginControlGroup($model, 'cli_sname') ?>
	<?php echo $form->controlLabel($model, 'cli_sname'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'cli_sname', ['class' => 'input-large']) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>
	<?php echo $form->beginControlGroup($model, 'cli_email') ?>
	<?php echo $form->controlLabel($model, 'cli_email') ?>
	<div class="controls">
		<?php echo $form->textField($model, 'cli_email', ['class' => 'input-large']); ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'secondaryEmail') ?>
	<?php echo $form->controlLabel($model, 'secondaryEmail') ?>
	<div class="controls">
		<?php echo $form->textField($model, 'secondaryEmail', ['class' => 'input-large']); ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'telephones', ['id' => 'phones']) ?>
	<label class="control-label <?php echo $model->isNewRecord ? 'required' : '' ?>">Telephones</label>

	<div class="controls">
		<?php foreach ($model->telephones as $key => $telephone): ?>
			<input type="text" name="telephones[number][]" class="input-small"
				   value="<?php echo $telephone->tel_number ?>">
			<input type="hidden" name="telephones[id][]" value="<?php echo $telephone->tel_id; ?>">
			<?php echo CHtml::dropDownList('telephones[type][]', $telephone->tel_type, Telephone::getTypes(), ['class' => 'input-xsmall']) ?>
			<br>
		<?php endforeach; ?>
		<?php if (isset($_POST['telephones']['number'][0]) && $_POST['telephones']['number'][0]) :
			$tel = $_POST['telephones'];
			$cnt = 0;
			foreach ($tel['number'] as $key => $number) {

				?>
				<input type="text" name="telephones[number][]" class="input-small"
					   value="<?php echo $tel['number'][$key] ?>">
				<input type="hidden" name="telephones[id][]" value="<?php echo $tel['id'][$key]; ?>">
				<?php echo CHtml::dropDownList('telephones[type][]', $tel['type'][$key], Telephone::getTypes(), ['class' => 'input-xsmall']) ?>
				<br>
				<?php
				$cnt++;
			}
		endif; ?>
		<input type="text" name="telephones[number][]" class="input-small" value="" placeholder="Add new phone number">
		<input type="hidden" name="telephones[id][]" value="">
		<?php echo CHtml::dropDownList('telephones[type][]', null, Telephone::getTypes(), ['class' => 'input-xsmall']) ?>
		<input type="button" value="Add phone number" id="addPhoneButton" class="btn btn-green">
		<br>
	</div>
	<input type="hidden" id="lastPhoneID" value="">
	<?php echo $form->endControlGroup(); ?>
	<?php echo $form->beginControlGroup($model, 'cli_salestatus') ?>
	<label class="control-label" for="cli_salestatus">Current Status</label>

	<div class="controls">
		<?php echo $form->dropDownList($model, "cli_salestatus", CHtml::listData(ClientStatus::model()->sales()->findAll(), 'cst_id', 'cst_title'), ['empty' => '', 'class' => 'input-small']) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>
	<div class="control-group">
		<label class="control-label">Email updates</label>

		<div class="controls">
			<?= $form->radioButtonList($model, 'cli_saleemail', Client::getEmailNotifyOptions(), ['separator' => ' ', 'class' => 'email-updates-radio']) ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Preferred contact</label>

		<div class="controls">
			<?php echo $form->radioButtonList($model, 'cli_preferred', Client::getContactMethods(), ['separator' => ' ']); ?>
		</div>
	</div>



	<?php
	$this->renderPartial("application.modules.admin4.views.note.addNote", array(
			'noteTypeId'   => $model->cli_id,
			'noteType'     => Note::TYPE_CLIENT_GENERAL,
			'title'        => 'General note(s)',
			'textBoxTitle' => 'General Note'
	));
	?>


	<div class="control-group"><label class="control-label">Primary Address:</label></div>
	<?php $this->renderPartial('application.modules.admin4.views.address.formInline', array(
			'fieldName'        => 'primaryAddress',
			'noAddressMessage' => 'Primary address is not selected',
			'model'            => $model->address,
			'confirm'          => $model->addressID === null || $model->addressID == -1,
	)); ?>


	<?php if ($model->address): ?>
		<div class="control-group"><label class="control-label">Secondary Address:</label></div>
		<?php $this->renderPartial('application.modules.admin4.views.address.formInline', array(
				'fieldName'        => 'secondAddress',
				'confirm'          => false,
				'noAddressMessage' => 'Correspondence address is not selected',
				'model'            => $model->secondAddress
		)) ?>
	<?php endif; ?>
</div>

<script type="text/phone-template" id="phone-template">
	<input type="text" name="telephones[number][]" class="input-small">
	<input type="hidden" name="telephones[id][]" value="">
	<?php echo CHtml::dropDownList('telephones[type][]', key(Telephone::getTypes()), Telephone::getTypes(), ['class' => 'input-xsmall']); ?>
	<br>
</script>

<script type="text/javascript">
	(function ()
	{
		$("#addPhoneButton").on('click', function ()
		{
			var tpl = $('#phone-template').html();
			$('#phones .controls').append(tpl);
		});
	})();

</script>