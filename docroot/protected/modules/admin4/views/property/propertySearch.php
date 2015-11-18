<?php
/**
 * @var $this  PropertyController
 * @var $form  AdminFilterForm
 * @var $model Property
 *
 */
?>
<?php $form = $this->beginWidget('AdminFilterForm', array(

														 'id'                   => 'property-filter-form',
														 'enableAjaxValidation' => false,
														 'model'                => array($model, $model->instructions[0]),
														 'ajaxFilterGrid'       => 'property-list',
														 'storeInSession'       => false,

													)); ?>
<fieldset>
	<div class="row">
		<label for="Property[fullAddressString]">Address</label>
		<?php echo $form->textField($model, 'fullAddressString', array('size' => 30)) ?>
		<label for="Property[pro_postcode]">Postcode</label>
		<?php echo $form->textField($model, 'pro_postcode', array('size' => 10)) ?>
		<label for="Deal[dea_status]">Postcode</label>
		<?php echo $form->textField($model, 'pro_postcode', array('size' => 10)) ?>
	</div>
</fieldset>
<?php $this->endWidget() ?>
</div>

<?php
$this->widget('AdminGridView', array(
									'id'           => 'property-list',
									'dataProvider' => $model->search(),
									'columns'      => array(
//										array(
//											'class' => 'CCheckBoxColumn'
//										),
										'pro_id',
										array(
											'header'      => 'Instructions',
											'type'        => 'raw',
											'value'       => function (Property $data) {

												$instructionsHtml = array();
												foreach ($data->instructions as $instruction) {
													$instructionsHtml[] = '<td><a href="/admin4/instruction/summary/' . $instruction->dea_id . '">' . $instruction->dea_id . '</a></td>'
															. '<td style="width:100px">' . $instruction->dea_status . '</td>'
															. '<td>' . Date::formatDate('d/m/Y', $instruction->dea_created) . "</td>"
															. '</td><td>' . $instruction->dea_type . "</td>";
												}

												return '<table class="sublist"><tr>' . implode('</tr><tr>', $instructionsHtml) . '</tr></table>';
											},
											'htmlOptions' => array('style' => 'width:400px;')
										),
//										array('name'=> 'pro_id', 'htmlOptions' => array('style' => 'width:30px;')),
										array('name' => 'fullAddressString', 'header' => 'Address'),
										array('name' => 'pro_postcode', 'header' => 'Postcode'),

									)
							   ));

?>

<script type="text/javascript">
	var goToInstruction = function (id)
	{
		document.location.href = "/admin4/instruction/summary/" + id + "";
	}
</script>