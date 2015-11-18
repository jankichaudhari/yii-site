<?php
/**
 * @var $this       InstructionController
 * @var $owner      Client
 * @var $model      Deal
 * @var $client     Client
 *
 */
?>
<fieldset>
	<div class="block-header">Text message</div>
	<div class="content">
		<h6>Vendor: <?php echo CHtml::link($owner->getFullName(), ['client/update', 'id' => $owner->cli_id]) ?></h6>
		<?php echo $this->renderPartial('application.modules.admin4.views.sms.textForm', array(
				'client'      => $owner,
				'defaultText' => $client->getFullName() . ' wants to view your property. Please contact them on ' . Locale::formatPhone($client->getPrimaryPhoneNumber()) . ' or ' . $client->email . '. W&S',
				'callback'    => (isset($callback) ? $callback : null),
		)) ?>
	</div>
</fieldset>