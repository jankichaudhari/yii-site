<?php
/**
 * @var $model          Property
 * @var $form           AdminForm
 * @var $this           PropertyController
 * @var $suggestedOwner Client|null
 */
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$cs->registerCssFile('/css/the-modal.css');
$cs->registerScriptFile('/js/jquery.the-modal.js');
$form = $this->beginWidget('AdminForm', array('id' => 'property-edit-form'));
$owners = []; // list of all owners of the property
?>
<div class="row-fluid">
<div class="span12">
<fieldset>
	<div class="block-header">
		<?php echo $model->isNewRecord ? 'Create new Poperty' : 'Update property' ?>
	</div>
	<div class="content">
		<?php echo $model->isNewRecord ? '&nbsp;' : $model->address->getFullAddressString(' ') ?>
	</div>
</fieldset>

<?php $tabbedView = $this->beginWidget('TabbedLayout', ['id' => 'property-tabs-' . $model->pro_id]); ?>
<?php $tabbedView->beginTab('General') ?>
<div class="content">
	<div class="control-group">
		<div class="controls force-margin">
			<?php if ($model->hasErrors()): ?>
				<div class="flash danger input-large">
					<?= $form->errorSummary($model) ?>
				</div>
			<?php endif ?>
			<?php if (Yii::app()->user->hasFlash('property-update-success')) : ?>
				<div class="flash success remove input-xlarge"><?= Yii::app()->user->getFlash('property-update-success') ?></div>
			<?php endif ?></div>
	</div>

	<?php $this->renderPartial('application.modules.admin4.views.address.formInline', [
																					  'fieldName'        => 'propertyAddress',
																					  'noAddressMessage' => 'property address is not selected',
																					  'model'            => $model->address,
																					  ]); ?>

	<input type="hidden" name="clientType" id="clientType" value="">

	<div class="control-group">
		<label class="control-label">Vendor(s) </label>

		<div class="controls text">
			<div id="owners">
				<?php foreach ($model->owners as $owner): ?>
					<?php $owners[$owner->cli_id] = $owner->cli_id ?>
					<span class="owner" id="owner-span-<?php echo $owner->cli_id ?>">
					<input type="hidden" name="owner[]" id="owner-<?php echo $owner->cli_id ?>"
						   value="<?php echo $owner->cli_id ?>">
						<?php echo $owner->getFullName() . '(' . $owner->cli_id . ')' ?>
						<img src="/images/sys/admin/icons/cross-icon.png" alt="delete"
							 data-id="<?php echo $owner->cli_id ?>" class="drop-client" id="drop-owner">
				</span>
				<?php endforeach; ?>
			</div>
			<?php if (count($model->owners) < Yii::app()->params['property']['allowedOwnersNumber']): ?>
				<input type="button" class="btn btn-green add-client-button" value="Add owner" id="owner">
			<?php endif ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Tenants </label>

		<div class="controls text">
			<div id="tenants">
				<?php foreach ($model->tenants as $tenant): ?>
					<?php $tenants[$tenant->cli_id] = $tenant->cli_id ?>
					<span class="tenant" id="tenant-span-<?php echo $tenant->cli_id ?>">
					<input type="hidden" name="tenant[]" value="<?php echo $tenant->cli_id ?>">
						<?php echo $tenant->getFullName() . '(' . $tenant->cli_id . ')' ?>
						<img src="/images/sys/admin/icons/cross-icon.png" alt="delete"
							 data-id="<?php echo $tenant->cli_id ?>" class="drop-client" id="drop-tenant">
				</span>
				<?php endforeach; ?>
			</div>

			<input type="button" class="btn btn-green add-client-button" value="Add tenant" id="tenant">
		</div>
	</div>


	<?= $form->beginControlGroup($model, 'pro_ptype'); ?>
	<label class="control-label" for="Property_pro_ptype">Type</label>

	<div class="controls">
		<?= $form->dropDownList($model, 'pro_ptype', CHtml::listData(PropertyType::model()->findAll('pty_type IS NULL'), 'pty_id', 'pty_title'), ['class' => 'input-xsmall']) ?>
		<?=
		$form->dropDownList($model, 'pro_psubtype', CHtml::listData(PropertyType::model()
																	->findAll('pty_type IS NOT NULL'), 'pty_id', 'pty_title'), ['class' => 'input-xsmall']) ?>
		<span class="hint">Studio must also have bedroom number set to 0</span>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_leaseend'); ?>
	<?= $form->controlLabel($model, 'pro_leaseend'); ?>
	<div class="controls">
		<?= $form->textField($model, 'pro_leaseend') ?>
	</div>
	<?= $form->endControlGroup(); ?>
	<?= $form->beginControlGroup($model, 'groundrent'); ?>
	<?= $form->controlLabel($model, 'groundrent'); ?>
	<div class="controls">
		<?= $form->textField($model, 'groundrent') ?>
	</div>
	<?= $form->endControlGroup(); ?>
	<?= $form->beginControlGroup($model, 'servicecharge'); ?>
	<?= $form->controlLabel($model, 'servicecharge'); ?>
	<div class="controls">
		<?= $form->textField($model, 'servicecharge') ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_tenure'); ?>
	<?= $form->controlLabel($model, 'pro_tenure'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_tenure', Property::getTenureTypes()) ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_bedroom'); ?>
	<?= $form->controlLabel($model, 'pro_bedroom'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_bedroom', range(0, 10), ['class' => 'input-xsmall']) ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_reception'); ?>
	<?= $form->controlLabel($model, 'pro_reception'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_reception', range(0, 10), ['class' => 'input-xsmall']) ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_bathroom'); ?>
	<?= $form->controlLabel($model, 'pro_bathroom'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_bathroom', range(0, 10), ['class' => 'input-xsmall']) ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_garden'); ?>
	<?= $form->controlLabel($model, 'pro_garden'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_garden', Property::getGardenTypes(), ['class' => 'input-xsmall']) ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_parking'); ?>
	<?= $form->controlLabel($model, 'pro_parking'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_parking', Property::getParkigTypes(), ['class' => 'input-xsmall']) ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_floors'); ?>
	<?= $form->controlLabel($model, 'pro_floors'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_floors', range(0, 10), ['class' => 'input-xsmall']) ?>
	</div>
	<?= $form->endControlGroup(); ?>

	<?= $form->beginControlGroup($model, 'pro_floor'); ?>
	<?= $form->controlLabel($model, 'pro_floor'); ?>
	<div class="controls">
		<?= $form->dropDownList($model, 'pro_floor', Property::getFloorNames(), ['class' => 'input-xsmall']) ?>
	</div>
	<?= $form->endControlGroup(); ?></div>

<div class="block-buttons force-margin">
	<input type="submit" class="btn" value="Save">
	<?php if (isset($_GET['nextStep']) && $_GET['nextStep']): ?>
		<input type="submit" class="btn" value="Save & Proceed" name="proceed">
	<?php endif ?>
</div>

<?php $tabbedView->endTab(); ?>

<?php if (!$model->isNewRecord): ?>
	<?php $tabbedView->beginTab('Instructions', array('id' => 'instructions')) ?>
	<div class="content"><?php if ($model->instructions): ?>
			<table class="small-table">
				<tr>
					<th></th>
					<th>Type</th>
					<th>Status</th>
					<th>Date</th>
					<th>id</th>
				</tr>
				<?php foreach ($model->instructions as $instruction): ?>
					<tr>
						<td class="icon-column">
							<?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON, 'Edit appointment'), InstructionController::generateLinkToInstruction($instruction->dea_id), ['class' => 'shaded']) ?>
						</td>
						<td><?php echo $instruction->dea_type ?></td>
						<td><?php echo $instruction->dea_status ?></td>
						<td><?php echo Date::formatDate("d/m/Y", $instruction->dea_created) ?></td>
						<td><?= $instruction->dea_id ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			No instructions found
		<?php endif ?></div>
	<?php $tabbedView->endTab(); ?>
	<?php $tabbedView->beginTab('Viewings & appointments', array('id' => 'viewings')) ?>


	<div class="content">

		<?php foreach ($model->instructions as $key => $instruction): ?>
			<div style="">
				<span style="font-weight: bold;"><?php echo $instruction->dea_type ?> Instruction</span>
				<br>
				<a style="text-decoration: none;"
				   href="<?php echo InstructionController::generateLinkToInstruction($instruction->dea_id) ?>">Status: <?php echo $instruction->dea_status ?></a>

				<?php if ($instruction->appointments): ?>
					<table class="small-table">
						<tr class="record-header">
							<th></th>
							<th>date</th>
							<th>Type</th>
							<th>Neg'</th>
							<th>Client</th>
							<th>Feedback</th>
						</tr>


						<?php foreach ($instruction->appointments as $appointment): ?>
							<tr class="record">
								<td class="icon-column"><?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON, 'Edit appointment'), AppointmentController::createAppointmentUpdateLink($appointment->app_id)) ?></td>
								<td><?php echo Date::formatDate('d/m/Y H:i', $appointment->app_start) ?></td>
								<td><?php echo $appointment->app_type ?></td>
								<?php if ($appointment->user): ?>
									<td title="<?php echo $appointment->user->getFullName() ?>">
										<span class="negotiator-color"
											  style="background: #<?php echo $appointment->user->use_colour ?>"></span><?php echo $appointment->user->getInitials(); ?>
									</td>
								<?php else: ?>
									<td></td>
								<?php endif ?>
								<td>

									<?php foreach ($appointment->clients as $client): ?>
										<?php echo CHtml::link($client->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $client->cli_id]), ['class' => 'shaded']) ?>
									<?php endforeach; ?>
								</td>
								<td><?php echo CHtml::link($appointment->feedback, '/admin4/appointment/feedback/id=' . $appointment->feedbackId, ['class' => 'shaded']) ?></td>
							</tr>

						<?php endforeach; ?>
					</table>
				<?php endif ?>
			</div>
		<?php endforeach; ?>

	</div>

	<?php $tabbedView->endTab(); ?>
<?php endif ?>
<?php $this->endWidget() ?>
</div>
</div>
<?php $this->endWidget() ?>
<script id="owner-span-template" type="text/owner-span-template">
<span class="owner" id="owner-span-{cli_id}">
<input type="hidden" name="owner[]" id="owner-{cli_id}" value="{cli_id}">
		{fullName} ({cli_id})
        <img src="/images/sys/admin/icons/cross-icon.png" alt="delete" data-id="{cli_id}" class="drop-client"
			 id="remove-owner">
</span>
</script>
<script id="tenant-span-template" type="text/tenant-span-template">
<span class="tenant" id="tenant-span-{cli_id}">
<input type="hidden" name="tenant[]" value="{cli_id}">
		{fullName} ({cli_id})
        <img src="/images/sys/admin/icons/cross-icon.png" alt="delete" data-id="{cli_id}" class="drop-client"
			 id="remove-tenant">
</span>
</script>

<script type="text/javascript">
	var clientType = null;
	$('.add-client-button').on('click', function () {
		clientType = this.id;
		var popup = new Popup('/admin4/client/PopupSelect/onSelect/addClient');
		popup.open();
	})

	var addClient = function (clientID, _clientType) {
		clientType = _clientType || clientType;
		if (!clientType) {
			alert('An error occured. Please contact administrator!');
		}

		$.getJSON('/admin4/client/info/id/' + clientID + '/format/JSON', function (data) {
			var tpl = $('#' + clientType + '-span-template').html();
			for (key in data) {
				var regexp = new RegExp('{' + key + '}', 'gi');
				tpl = tpl.replace(regexp, data[key]);
			}
			$('#' + clientType + 's').append(tpl);
			clientType = null;
		});


	}
	var replaceOwners = function (clientID) {
		$('.owner').remove();
		addClient(clientID, 'owner');
	}

	$('body').on('click', '.drop-client', function () {
		var thisId = $(this).attr('id').split("-");
		console.log(thisId);
		var clientType = thisId[1];
		var id = $(this).data('id');
		if (confirm('Are you sure you want to delete this ' + clientType + ' from the list?')) {
			$('#' + clientType + '-span-' + id).remove();
//			$('#' + clientType + '-' + id).val('');
		}
	});


</script>
<?php if (Yii::app()->user->hasFlash('suggest-new-owner') && $suggestedOwner && !in_array($suggestedOwner->cli_id, $owners)): ?>
	<?php Yii::app()->user->setFlash('suggest-new-owner', null); ?>
	<div id="dialogContainer" style="display:none; width:700px;" class="modal">
		<div class="header">
			Select owner for property
			<a href="#" class="close">×</a>
		</div>
		<div class="content">
			<p>Your selected client
				<a href="<?php echo Yii::app()->createUrl('admin4/client/update', ['id' => $suggestedOwner->cli_id]) ?>"
				   target="_blank"><?php echo $suggestedOwner->getFullName() ?></a>
				is not current owner of a property.</p>

			<p>Would you like to set him as owner of the property?</p>

		</div>
		<div class="block-buttons">
			<input type="button" onclick="replaceOwners('<?php echo $suggestedOwner->cli_id ?>'); $.modal().close();"
				   class="btn btn-warning" value="Replace current owners">
			<input type="button"
				   onclick="addClient('<?php echo $suggestedOwner->cli_id ?>', 'owner'); $.modal().close()"
				   class="btn btn-success" value="Add as owner">
			<input type="button" onclick="$.modal().close()" class="btn" value="close">
		</div>
	</div>

<?php endif ?>
<script type="text/javascript">
	(function () {

		$(document).ready(function () {
			if (document.getElementById("dialogContainer")) {
				$('#dialogContainer').modal().open();
			}
			$('.modal .close').on('click', function (e) {
				e.preventDefault();
				$.modal().close();
			})
		});
	})();
</script>