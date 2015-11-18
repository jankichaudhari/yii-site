<?php
/**
 * @var $this ClientController
 * @var $sms  Sms
 * @var $date String
 */
?>

<div class="bubble-row">
	<div class="bubble <?php echo $sms->type === Sms::TYPE_INCOMING ? "incoming" : '' ?>" id="message-<?php echo $sms->id ?>">
		<?php echo $sms->text ?>
		<div class="beak"></div>
	</div>
	<?php if ($sms->type === Sms::TYPE_OUTGOING && isset($sms->appointment[0])): ?>
		<?php $app = $sms->appointment[0]; ?>
		<div class="app-info">
			<div><?php echo CHtml::link(($app->app_type == Appointment::TYPE_VIEWING ? "Viewing" : "Valuation"), AppointmentController::createAppointmentUpdateLink($app->app_id)) ?></div>
			<?php foreach ($app->instructions as $key => $instruction): ?>
				<div><?php echo $instruction->address->toString() ?></div>
			<?php endforeach; ?>
		</div>
	<?php endif ?>
</div>
