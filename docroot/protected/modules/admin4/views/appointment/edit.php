<?php
/**
 * @var $model Appointment
 * @var $this  AppointmentController
 * @var $form  AdminFilterForm
 */
$form = $this->beginWidget('AdminFilterForm', ['id' => 'appointment-edit-form']);
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header"><?php echo $model->app_type ?></div>
			<div class="content">
				<?php echo $form->beginControlGroup($model, 'calendarID'); ?>
				<?php echo $form->controlLabel($model, 'calendarID'); ?>
				<div class="controls">
					<?php echo $form->dropDownList($model, "calendarID", Branch::listData(), ['class' => 'input-small']) ?>
				</div>
				<?php echo $form->endControlGroup(); ?>
				<?php echo $form->beginControlGroup($model, 'app_user'); ?>
				<?php echo $form->controlLabel($model, 'app_user'); ?>
				<div class="controls">
					<?php echo $form->dropDownList($model, "app_user", User::listData(), ['class' => 'input-small']) ?>
				</div>
				<?php echo $form->endControlGroup(); ?>
				<div class="control-group">
					<label class="control-label">All day event</label>

					<div class="controls">
						<?php echo $form->checkBox($model, 'app_allday', ['value' => Appointment::ALLDAY_YES, 'uncheckValue' => Appointment::ALLDAY_NO, 'id' => 'allday-check']) ?>
					</div>
				</div>
				<div id="start-time-container">
					<div class="control-group">
						<label class="control-label">Start time</label>

						<div class="controls">
							<span><?php echo CHtml::textField('startDate', date('d/m/Y', strtotime($model->app_start)), ['class' => 'datepicker input-small']) ?></span>
							<span><?php echo CHtml::textField('startTime', Date::formatDate('H:i', $model->app_start), ['class' => 'timepicker input-xxsmall', 'placeholder' => 'hh:mm']) ?></span>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Duration</label>

						<div class="controls">
							<?php echo CHtml::dropDownList('duration', '', $this->getTimeIntervals(), ['class' => 'input-xsmall']) ?>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
</div>

<?php $this->endWidget(); ?>
<script type="text/javascript">
	(function ()
	{
		$(".datepicker").datepicker()
		$(".timepicker").timepicker({showOn : 'both'})

		var changeListener = function ()
		{
			var $this = $(this);
			if ($this.is(':checked')) {
				$('#start-time-container').hide();
			} else {
				$('#start-time-container').show();
			}
		}
		$('#allday-check').on('change', changeListener);
		changeListener().call($('#allday-check'));


	})();

</script>