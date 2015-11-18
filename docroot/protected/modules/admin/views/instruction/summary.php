<?php
/**
 * @var  $this              InstructionController
 * @var  $value             Deal
 * @var  $model             Deal
 * @var  $form              AdminForm
 * @var  $clientScript      CClientScript
 * @var  $tabbedView        TabbedLayout
 * @var  $otherInstructions Deal[]
 * @var  $clientScript      CClientScript
 * @var  $notViewings       Appointment[]
 * @var  $appointment       Appointment
 */
$clientScript = Yii::app()->clientScript;
$clientScript->registerCssFile(Yii::app()->baseUrl . '/css/instruction.css');
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtilHead.js', CClientScript::POS_HEAD);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtil.js', CClientScript::POS_END);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/instruction.js', CClientScript::POS_HEAD);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/note.js', CClientScript::POS_HEAD);
$tabWidgetId = 'instruction-summary-' . $model->dea_id;
$notViewings = []; // array to store appointments that are not viewings because they are displayed later.

$saveButton = CHtml::submitButton('Save', ['class' => 'btn', 'onclick' => "stopPopupPreview('#instruction-form')"]);
$form = $this->beginWidget('AdminForm', ['id' => 'instruction-form', 'enableAjaxValidation' => false]);

?>
<div class="row-fluid">
	<div class="span12">
		<fieldset class="<?php echo $model->isDIY() ? $model->DIY . "-property" : '' ?>">
			<div class="block-header">Update Instruction</div>
			<div class="content">
				<?php if ($model->address): ?>
					<?php echo $model->address->getFullAddressString(', ') . " (" . $model->dea_type . ")" ?>
				<?php endif ?>
			</div>

			<div class="block-buttons">
				<?php echo $saveButton ?>
				<input type="submit" class="btn" value="Preview" onclick="popupPreview('#instruction-form','/details/<?= $model->dea_id ?>')">
				<?php echo CHtml::link('Production', ['Instruction/production', 'id' => $model->dea_id,], ['class' => 'btn btn-pink']) ?>
				<?php echo CHtml::link('Property', ['property/update', 'id' => $model->dea_prop,], ['class' => 'btn btn-gray']) ?>
				<?php echo CHtml::link('PDF', '#', ['class' => 'btn btn-gray', 'onclick' => "popupWindow('" . $this->createUrl('/property/pdf', ['id' => $model->dea_id]) . "',700,950)"]) ?>
				<?php echo CHtml::link('Copy', ['instruction/copyInstruction', 'id' => $model->dea_id], ['class' => 'btn btn-gray']) ?>
				<?php if ($model->isDIY(Deal::DIY_DIY)): ?>
					<?php echo CHtml::link('Register Interest', [
							'appointmentBuilder/selectClient',
							'for'           => Appointment::TYPE_VIEWING,
							'instructionId' => $model->dea_id
					], ['class' => 'btn btn-green']) ?>
				<?php endif ?>
			</div>
			<div class="content">
				<div class="flash danger"><?php echo $form->errorSummary($model) ?></div>
				<div class="flash danger"><?php echo Yii::app()->user->getFlash('error'); ?></div>
				<div class="flash success"><?php echo Yii::app()->user->getFlash('success'); ?></div>
				<div class="flash success"><?php echo Yii::app()->user->getFlash('mailshot-sent'); ?></div>
			</div>
		</fieldset>
	</div>
</div>

<div class="row-fluid">
<div class="span12">
<?php $tabbedView = $this->beginWidget('TabbedLayout', ['id' => $tabWidgetId, 'activeTab' => 'propertySummary']); ?>
<?php $tabbedView->beginTab("Property Summary", ['id' => 'propertySummary']) ?>
<?php include('tabs/propertySummary.php') ?>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Marketing Details", ['id' => 'marketingDetails']) ?>
<?php include('tabs/marketingDetails.php') ?>
<div class="block-buttons force-margin">
	<?php echo $saveButton ?>
</div>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Valuation", ['id' => 'valuation']) ?>
<div class="content">
	<div class="control-group">
		<label class="control-label">Valuation Price</label>

		<div class="controls">
			<?php echo $form->textField($model, 'dea_valueprice', ['class' => 'input-xsmall', 'value' => Locale::formatCurrency($model->dea_valueprice, true, false)]); ?>
			<?php echo $form->textField($model, 'dea_valuepricemax', ['class' => 'input-xsmall', 'value' => Locale::formatCurrency($model->dea_valuepricemax, true, false)]); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Valuation Date</label>

		<div class="controls">
			<?php echo $form->textField($model, 'valuationDate', ['placeholder' => 'dd/mm/yyyy', 'class' => 'input-xsmall datepicker']); ?>
		</div>
	</div>
	<?php echo $form->beginControlGroup($model, 'valuationLetterSent'); ?>
	<?php echo $form->controlLabel($model, 'valuationLetterSent'); ?>
	<div class="controls">
		<?php echo $form->checkBox($model, 'valuationLetterSent', ['uncheckValue' => '0', 'value' => 1]) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>
	<?php echo $form->beginControlGroup($model, 'followUpDue'); ?>
	<?php echo $form->controlLabel($model, 'followUpDue'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'followUpDue', ['placeholder' => 'dd/mm/yyyy', 'class' => 'input-xsmall datepicker']) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>
	<?php echo $form->beginControlGroup($model, 'followUpUser'); ?>
	<?php echo $form->controlLabel($model, 'followUpUser'); ?>
	<div class="controls">
		<?php echo $form->dropDownList($model, 'followUpUser', CHtml::listData(User::model()->onlyActive()->alphabetically()->findAll(), 'use_id', 'fullName')) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'vendorFollowUp'); ?>
	<?php echo $form->controlLabel($model, 'vendorFollowUp'); ?>
	<div class="controls">
		<?php echo $form->checkBox($model, 'vendorFollowUp', ['uncheckValue' => '0', 'value' => 1]) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'instructionLetterSent'); ?>
	<?php echo $form->controlLabel($model, 'instructionLetterSent'); ?>
	<div class="controls">
		<?php echo $form->checkBox($model, 'instructionLetterSent', ['uncheckValue' => '0', 'value' => 1]) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>
	<?php $this->renderPartial("application.modules.admin4.views.note.addNote", array(
			'noteTypeId'   => $model->dea_id,
			'noteType'     => Note::TYPE_VALUATION_FOLLOWUP,
			'title'        => 'Valuation followup note(s)',
			'textBoxTitle' => 'Follow up note'
	)) ?>
</div>
<div class="block-buttons">
	<?php echo $saveButton ?>
</div>
<?php $tabbedView->endTab(); ?>

<?php $tabbedView->beginTab("State of Trade", ['id' => 'stateOfTrade']) ?>
<?php include('tabs/stateOfTrade.php') ?>
<div class="block-buttons force-margin">
	<?php echo $saveButton ?>
</div>
<?php $tabbedView->endTab(); ?>

<?php if (!$model->isDIY(Deal::DIY_DIY)): ?>

	<?php $tabbedView->beginTab("Viewings", ['id' => 'viewing']) ?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-buttons">
					<?php echo CHtml::link("Arrange Viewing", array(
							'AppointmentBuilder/selectClient',
							'for'           => Appointment::TYPE_VIEWING,
							'instructionId' => $model->dea_id
					), ['target' => '_blank', 'class' => 'btn btn-green']); ?>
				</div>
				<div class="content">Views on Site : <strong><?php echo $model->siteViews ?></strong></div>
				<div class="content">Total Viewings : <strong><?php echo $model->getTotalViewings(''); ?></strong></div>
				<div class="content">Viewings taken Place :
					<strong><?php echo $model->getTotalViewings('finished'); ?></strong>
				</div>
				<div class="content">Cancelled viewings :
					<strong><?php echo $model->getTotalViewings('cancelled'); ?></strong></div>
				<div class="content">Upcoming viewings: <strong><?php echo $model->getTotalViewings('upcoming'); ?></strong>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="content">
		<div class="row-fluid">
			<div class="span6">

				<?php if ($model->appointments) : ?>
					<table class="small-table">
						<tr>
							<th colspan="2">Date</th>
							<th>Neg</th>
							<th>Client</th>
							<th>Feedback</th>
							<th></th>
						</tr>
						<?php foreach ($model->appointments as $appointment): ?>
							<?php
							if ($appointment->app_type !== Appointment::TYPE_VIEWING) {
								$notViewings[] = $appointment;

								continue;
							}
							?>
							<tr class="<?php echo strtotime($appointment->app_start) > time() ? "highlight green" : "" ?>">
								<td><?php echo Date::formatDate("d/m/Y", $appointment->app_start) ?></td>
								<td><?php echo Date::formatDate("H:i", $appointment->app_start) ?></td>
								<td>
									<?php if ($appointment->user): ?>
									<span class="negotiator-color" style="background-color:#<?php echo $appointment->user->use_colour ?> "></span><?php echo $appointment->user->fullName ?>
									<?php endif ?>
								</td>
								<td>

									<?php $t = [];
									foreach ($appointment->clients as $client): ?>
										<?php $t[] = CHtml::link($client->fullName, $this->createUrl('client/update', ['id' => $client->cli_id])) ?>
									<?php endforeach; ?>
									<?php echo implode(', ', $t); ?>
								</td>

								<td>
									<?php
									if ($appointment->app_status == Appointment::STATUS_CANCELLED || $appointment->app_status == Appointment::STATUS_DELETED) {
										echo '(' . $appointment->app_status . ')';
									} else {
										echo CHtml::link($appointment->feedback, array(
												'appointment/feedback',
												'id' => $appointment->feedbackId
										));
									}
									?>
								</td>
								<td><?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON), AppointmentController::createAppointmentUpdateLink($appointment->app_id)) ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>
			<div class="span6">
				<?php if ($notViewings) : ?>
					<table class="small-table">
						<tr>
							<th colspan="2">Date</th>
							<th>Neg</th>
							<th>type</th>
							<th>Client</th>
							<th></th>
						</tr>
						<?php foreach ($notViewings as $appointment): ?>

							<tr>
								<td><?php echo Date::formatDate("d/m/Y", $appointment->app_start) ?></td>
								<td><?php echo Date::formatDate("H:i", $appointment->app_start) ?></td>
								<td>
									<?php if ($appointment->user): ?>
									<span class="negotiator-color"
										  style="background-color:#<?php echo $appointment->user->use_colour ?> "></span><?php echo $appointment->user->fullName ?>
									<?php endif ?>
								</td>
								<td><?php echo $appointment->app_type ?></td>
								<td>

									<?php $t = [];
									foreach ($appointment->clients as $client): ?>
										<?php $t[] = CHtml::link($client->fullName, $this->createUrl('client/update', ['id' => $client->cli_id])) ?>
									<?php endforeach; ?>
									<?php echo implode(', ', $t); ?>
								</td>
								<td><?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON), AppointmentController::createAppointmentUpdateLink($appointment->app_id)) ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php $tabbedView->endTab(); ?>
<?php endif ?>

<?php $tabbedView->beginTab("Keys & Viewing Times", array('id' => 'keysTimes')) ?>
<div class="content">
	<div class="control-group">
		<label class="control-label">
			<?php echo $form->controlLabel($model, 'dea_key'); ?>
		</label>

		<div class="controls">
			<?php echo $form->textField($model, 'dea_key'); ?>
		</div>
	</div>
	<?php $this->renderPartial("application.modules.admin4.views.note.addNote", array(
			'noteTypeId'   => $model->dea_id,
			'noteType'     => Note::TYPE_VIEWING_ARRANGEMENTS,
			'title'        => 'Viewing arrangement note(s)',
			'textBoxTitle' => 'Viewing arrangement  Note'
	)) ?>
</div>
<div class="block-buttons force-margin">
	<?php echo $saveButton ?>
</div>
<?php $tabbedView->endTab(); ?>

<?php $tabbedView->beginTab("Offers", ['id' => 'offers']) ?>
<span id="offers"></span>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Feed Setup") ?>
<div class="content">
	<div class="control-group">
		<label class="control-label">Feed Address 1</label>

		<div class="controls">
			<?php echo $form->textField($model, 'feed_line1') ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Feed Address 2</label>

		<div class="controls">
			<?php echo $form->textField($model, 'feed_line2') ?>
			<span class="hint">THIS IS  DISPLAYED ON RIGHTMOVE</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Feed Address 3</label>

		<div class="controls">
			<?php echo $form->textField($model, 'feed_line3') ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Feed Address 4</label>

		<div class="controls">
			<?php echo $form->textField($model, 'feed_line4') ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Feed Address City</label>

		<div class="controls">
			<?php echo $form->textField($model, 'feed_city') ?>
			<span class="hint">THIS IS  DISPLAYED ON RIGHTMOVE</span>
		</div>
	</div>
	<div class="block-buttons">
		<?php echo $saveButton ?>
	</div>

</div>
<?php $tabbedView->endTab(); ?>

<?php $tabbedView->beginTab("DIY") ?>
<div class="content">
	<div class="control-group">
		<label class="control-label">DIY mode</label>

		<div class="controls change-diy-mode-container" style="<?php echo $model->isDIY() ? '' : 'display: none' ?>">
			<span class="change-diy-mode" style="color: #1e90ff; border-bottom: 1px dashed #1e90ff; cursor: pointer;"><?php echo $model->DIY ?></span>
		</div>
		<div class="controls diy-mode-container" style="<?php echo !$model->isDIY() ? '' : 'display: none' ?>">
			<?php echo $form->radioButtonList($model, 'DIY', array_combine($t = [Deal::DIY_NONE, Deal::DIY_DIY, Deal::DIY_DIT], $t), ['uncheckValue' => Deal::DIY_NONE, 'separator' => ' ']) ?>
		</div>

	</div>

	<?php if ($model->isDIY(Deal::DIY_DIY)): ?>
		<table class="small-table">
			<tr>
				<th>Date</th>
				<th>Client</th>
				<th>Email</th>
				<th>Text</th>
				<th>Registered</th>
			</tr>

			<?php foreach ($model->interest as $interest): ?>
				<tr>
					<td><?php echo Date::formatDate('d/m/Y H:i', $interest->created) ?></td>
					<td><?php echo CHtml::link($interest->client->getFullName(), ['client/update', 'id' => $interest->client->cli_id]) ?></td>
					<td><?php echo $interest->email ? CHtml::image(Icon::GREEN_TICK_ICON) : CHtml::image(Icon::CROSS_ICON) ?></td>
					<td><?php echo $interest->text ? CHtml::image(Icon::GREEN_TICK_ICON) : CHtml::image(Icon::CROSS_ICON) ?></td>
					<td></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif ?>
	<div class="block-buttons">
		<?php echo $saveButton ?>
	</div>

</div>
<?php $tabbedView->endTab(); ?>
<?php $this->endWidget() ?>
</div>
</div>
<?php $this->endWidget(); ?>
<script>
	(function ()
	{
		$('.statusDate').hide();
		var instructionStatus = '<?php echo $model->dea_status ?>';
		if (instructionStatus == 'Completed' || instructionStatus == 'Exchanged' || instructionStatus == 'Under Offer') {
			$('.statusDate').show();
		}

		$('#Deal_dea_status').on('change', function ()
		{
			var value = $(this).val();
			if (value == 'Completed' || value == 'Exchanged' || value == 'Under Offer') {
				$('.statusDate').show();
			} else {
				$('.statusDate').hide();
			}
		});

		$(".success").animate({opacity : 1.0}, 1000).fadeOut("slow");
		$(".datepicker").datepicker();
		showInstructionOffers('<?php echo $model->dea_id ?>');
		$('.change-diy-mode').on('click', function ()
		{
			$('.change-diy-mode-container').hide();
			$('.diy-mode-container').show();
		});
	})();
</script>