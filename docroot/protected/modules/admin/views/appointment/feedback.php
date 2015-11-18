<?php
/**
 * @var $this  AppointmentController
 * @var $model LinkDealToAppointment
 * @var $form  AdminForm
 */
$form = $this->beginWidget('AdminForm');
?>
<div class="row-fluid">
	<div class="span8">
		<fieldset>
			<div class="block-header">FEEDBACK</div>
			<div class="content">
				<div class="fash danger"><?php echo $form->errorSummary($model); ?></div>
				<div class="control-group">
					<label class="control-label">Property</label>

					<div class="controls text"><?php echo $model->deal->address->toString(', ') ?></div>
				</div>
				<div class="control-group">
					<label class="control-label">Vendors</label>

					<div class="controls text">
						<?php
						$t = [];
						foreach ($model->deal->owner as $v) {
							$t[] = CHtml::link($v->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $v->cli_id]));
						}
						echo implode(', ', $t);
						?>
					</div>
				</div>
				<?php if ($model->appointment->app_type == Appointment::TYPE_VIEWING): ?>
					<div class="control-group">
						<label class="control-label">Clients</label>

						<div class="controls text">
							<?php
							$t = [];
							foreach ($model->appointment->clients as $v) {
								$t[] = CHtml::link($v->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $v->cli_id]));
							}
							echo implode(', ', $t);
							?>
						</div>
					</div>
				<?php endif ?>
				<div class="control-group">
					<label class="control-label">Date</label>

					<div class="controls text">
						<?php echo Date::formatDate('d/m/Y H:i', $model->appointment->app_start); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Outcome</label>

					<div class="controls">
						<?php echo $form->radioButtonList($model, 'd2a_feedback', LinkDealToAppointment::getPossibleOutcomes(), ['separator' => ' ']) ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Add Note</label>

					<div class="controls">
						<?php echo $form->textArea($model, 'd2a_cvnotes') ?>
					</div>
				</div>
				<div class="block-buttons force-margin">
					<input type="submit" value="Save Feedback" class="btn"/>
					<input type="submit" value="Submit Offer" name="submitOffer" class="btn"/>
					<?php echo CHtml::link('Go to Appointment', '/v3.0/live/admin/appointment_edit.php?app_id=' . $model->appointment->app_id, ['class' => 'btn btn-gray']) ?>
				</div>
			</div>

		</fieldset>
	</div>
</div>
<?php $this->endWidget(); ?>
