<?php
/**
 * @var $this            AppointmentBuilderController
 * @var $model           Client
 * @var $form            AdminFilterForm
 * @var $appointmentDate String
 * @var $appointmentType String
 *
 */


?>
<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_filter', array(
												   'model' => $model,
												   'title' => $appointmentType == AppointmentBuilder::TYPE_VALUATION ? 'SEARCH VENDOR' : 'SEARCH CLIENT'
											  )) ?>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_listing', array(
													'model'            => $model,
													'title'            => $appointmentType == AppointmentBuilder::TYPE_VALUATION ? 'SELECT VENDOR' : 'SELECT CLIENT',
													'createButtonText' => $appointmentType == AppointmentBuilder::TYPE_VALUATION ? 'Add new Vendor' : 'Add new Client',
											   )
		) ?>
	</div>
</div>
<form action="<?php echo $this->createUrl('clientSelected') ?>" id="hidden_form">
	<input type="hidden" name="clientId" id="hidden_form_cli_id" value="">
</form>
<script type="text/javascript">
	var selectClient = function ()
	{
	}
	var useCreatedClient = function (id)
	{
		document.getElementById("hidden_form_cli_id").value = id;
		$('#hidden_form').submit();
	}
</script>