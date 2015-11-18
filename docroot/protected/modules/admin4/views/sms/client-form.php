<?php
/**
 * @var $this        SmsController
 * @var $client      Client
 * @var $model       Appointment
 * @var $fieldKey    String
 * @var $instruction Deal if instruction isset then it will be the only one included in the text message
 */
$instruction = isset($instruction) ? $instruction : null;

$hasPhoneNumber = false;
$selectedPhone = 1; // this is a very tricky thing. but fun.
foreach ($client->telephones as $key => $phone) {
	if (Locale::isMobile($phone->tel_number)) $hasPhoneNumber = true;
}
?>
<div class="client-form">
	<div class="control-group">
		<div class="controls">
			<?php echo CHtml::checkBox("{$fieldKey}[{$client->cli_id}][send]", $hasPhoneNumber, [
																								'value' => 1, 'uncheckValue' => 0,
																								'style' => 'margin: 0 7px;',
																								'class' => 'send-to-checkbox'
																								]) ?>
			<?php echo CHtml::link($client->getFullName(), ['client/update', 'id' => $client->cli_id]) ?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<?php foreach ($client->telephones as $key => $phone): ?>
				<?php if (!Locale::isMobile($phone->tel_number)) continue; ?>
				<?php echo CHtml::radioButton("{$fieldKey}[{$client->cli_id}][send_to]", $selectedPhone && $selectedPhone--, ['value' => $phone->tel_id, 'style' => 'margin: 0 7px;']) ?>
				<?php echo CHtml::hiddenField("phones[]", $phone->tel_id) ?>
				<span class="input-toggle-edit" data-url="<?php echo $this->createUrl('telephone/update') ?>">
				<span class="input">
					<input type="hidden" name="tel_id" value="<?php echo $phone->tel_id ?>" />
					<input type="text" class="input-text" name="tel_number" value="<?php echo $phone->tel_number ?>" />
					<span class="confirm">Save</span>
				</span>
				<span class="value" data-name="tel_number"><?php echo $phone->tel_number ?></span>
			</span>
				<br>
			<?php endforeach; ?>
			<?php if (!$hasPhoneNumber): ?>
				<div class="content">
					<?php echo CHtml::radioButton("{$fieldKey}[{$client->cli_id}][send_to]", $selectedPhone && $selectedPhone--, [
																																 'value' => '',
																																 'style' => 'margin: 0 7px; display:none;',
																																 'class' => 'send-to-radio',
																																 ]) ?>
					<?php echo CHtml::hiddenField("phones[]", '') ?>
					<span class="input-toggle-edit creating" data-url="<?php echo $this->createUrl('telephone/update') ?>">
				<span class="input">
					<input type="hidden" name="tel_cli" value="<?php echo $client->cli_id ?>" />
					<input type="hidden" name="tel_id" value="" />
					<input type="hidden" name="tel_type" value="<?php echo Telephone::TYPE_MOBILE ?>" />
					<input type="text" class="input-text" name="tel_number" value="" />
					<span class="confirm">Save</span>
				</span>
				<span class="value" data-name="tel_number">No phone number</span>
			</span>
				</div>
			<?php endif ?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<?php echo CHtml::textArea("{$fieldKey}[{$client->cli_id}][text]",
				(
				@$_POST[$fieldKey][$client->cli_id]['text'] ? : $this->renderPartial("text_{$fieldKey}", compact('client', 'model', 'instruction'), true)
				), ['class' => 'text-message']) ?>
			<br>
			<span class="hint symbol-count">Symbols left: (160)</span>
		</div>
	</div>
</div>