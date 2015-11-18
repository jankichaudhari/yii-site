<?php
/**
 * @var $this  AppointmentController
 * @var $model Appointment
 * @var $form  AdminForm
 */
?>
<fieldset>
	<div class="block-header">
		Appointment
	</div>
	<div class="content">
		<?php $form = $this->beginWidget('AdminForm') ?>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<div class="controls force-margin">
						<div class="flash success remove"><?php echo Yii::app()->user->getFlash('appointment-success') ?></div>
						<div class="flash danger"><?php echo $form->errorSummary($model); ?></div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $form->beginControlGroup($model, 'app_subject'); ?>
		<?php echo $form->controlLabel($model, 'app_subject'); ?>
		<div class="controls">
			<?php echo $form->textField($model, 'app_subject', ['class' => 'input-xxlarge']) ?>
		</div>
		<?php echo $form->endControlGroup(); ?>
		<?php echo $form->beginControlGroup($model, 'app_user'); ?>
		<?php echo $form->controlLabel($model, 'app_user'); ?>
		<div class="controls">
			<?php echo $form->dropDownList($model, "app_user", CHtml::listData(User::model()->onlyActive()->alphabetically()->findAll(), "use_id", "fullName")) ?>
		</div>
		<?php echo $form->endControlGroup(); ?>
		<?php echo $form->beginControlGroup($model, 'calendarID'); ?>
		<?php echo $form->controlLabel($model, 'calendarID'); ?>
		<div class="controls">
			<?php echo $form->dropDownList($model, "calendarID", CHtml::listData(Branch::model()->active()->findAll(), "bra_id", "bra_title")) ?>
		</div>
		<?php echo $form->endControlGroup(); ?>
		<?php if ($model->app_type !== Appointment::TYPE_VALUATION_FOLLOW_UP): ?>
			<?php echo $form->beginControlGroup($model, 'app_start'); ?>
			<?php echo $form->controlLabel($model, 'app_start'); ?>
			<div class="controls nofloat">
				<?php echo CHtml::textField('startDay', Date::formatDate('d/m/Y', $model->app_start), array(
						'class'       => 'datepicker',
						'placeholder' => 'dd/mm/yyyy',
				)) ?>
				<?php echo CHtml::textField('startTime', Date::formatDate('H:i', $model->app_start), array(
						'class'       => 'timepicker input-xxsmall',
						'placeholder' => 'hh:mm',
				)) ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'app_end'); ?>
			<?php echo $form->controlLabel($model, 'app_end'); ?>
			<div class="controls nofloat">
				<?php echo CHtml::textField('endDay', Date::formatDate('d/m/Y', $model->app_end), array(
						'class' => 'datepicker',
				)) ?>
				<?php echo CHtml::textField('endTime', Date::formatDate('H:i', $model->app_end), array(
						'class'       => 'timepicker input-xxsmall',
						'placeholder' => 'hh:mm',
				)) ?>
			</div>
			<?php echo $form->endControlGroup(); ?>
		<?php else: ?>
			<div class="control-group">
				<label class="control-label">Date</label>

				<div class="controls">
					This is a <em>follow up</em> appointment to change its date please do it on <?php echo CHtml::link('instruction screen', [
							'instruction/summary',
							'id' => $model->instructions[0]->dea_id,
							'#'  => '#valuation'
					]) ?>
				</div>
			</div>
		<?php endif ?>


		<div class="row-fluid">
			<div class="span4">
				<div class="control-group">
					<div class="controls text force-margin">
						<table class="small-table">
							<tr>
								<th colspan="2">Instructions</th>
							</tr>
							<?php foreach ($model->instructions as $instruction): ?>
								<tr>
									<td><?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON, 'edit', ['style' => 'vertical-align:middle']), [
												'instruction/summary',
												'id' => $instruction->dea_id
										]) ?></td>
									<td><?php echo $instruction->property->address->toString() ?></td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
			</div>
			<?php if ($model->clients): ?>

				<div class="span4">
					<div class="control-group">
						<div class="controls text">
							<table class="small-table">
								<tr>
									<th colspan="2">Clients</th>
									<th>Email</th>
									<th>Tel</th>
								</tr>
								<?php foreach ($model->clients as $client): ?>
									<tr>
										<td><?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON, 'edit', ['style' => 'vertical-align:middle']), [
													'client/update',
													'id' => $client->cli_id
											]) ?></td>
										<td><?php echo $client->getFullName() ?></td>
										<td><?php echo $client->email ?></td>
										<td><?php echo $client->telephones[0] ? Locale::formatPhone($client->telephones[0]->tel_number) : '' ?></td>
									</tr>
								<?php endforeach; ?>
							</table>
						</div>
					</div>
				</div>
			<?php endif ?>
		</div>

		<div class="block-buttons force-margin">
			<input type="submit" value="Save" class="btn" />
			<?php if ($model->app_status == Appointment::STATUS_ACTIVE): ?>
				<input type="submit" value="Delete" name="delete" class="btn btn-red" />
			<?php else: ?>
				<input type="submit" value="Restore" name="restore" class="btn btn-green" />
			<?php endif ?>
		</div>
		<?php $this->endWidget() ?>
	</div>
</fieldset>
<script type="text/javascript">
	$(".datepicker").datepicker();
	$(".timepicker").timepicker({showOn : 'both'});
</script>