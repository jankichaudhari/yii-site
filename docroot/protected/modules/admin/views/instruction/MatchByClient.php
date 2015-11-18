<?php
/**
 * @var $this   InstructionController
 * @var $model  Deal
 * @var $client Client
 *
 */
?>
<div class="row-fluid">

	<div class="span12">
		<fieldset>
			<div class="block-header">Client Info</div>
			<div class="control-group">
				<label class="control-label">Client ID:</label>

				<div class="controls"><span class="text"><?php echo $client->cli_id ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">preffered postcodes:</label>

				<div class="controls"><span class="text"><?php echo $client->cli_area ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Client min bedrooms(sales):</label>

				<div class="controls"><span class="text"><?php echo $client->cli_salebed ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">min price</label>

				<div class="controls"><span class="text"><?php echo $client->cli_salemin ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">max price</label>

				<div class="controls"><span class="text"><?php echo $client->cli_salemax ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">preffered prop.types</label>

				<div class="controls"><span class="text"><?php echo $client->cli_saleptype ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label"></label>

				<div class="controls"><span class="text"></span></div>
			</div>
	</div>
	</fieldset>
</div>
<div class="row-fluid">
	<div class="span12">
		<?php
		$this->widget('AdminGridView', array(
											'dataProvider' => $model->findMathingPropertyByClient($client),
											'columns'      => array(
												'dea_type',
												'dea_bedroom',
												'dea_marketprice',
												array(
													'header' => 'property type', 'value' => function (Deal $data) {

													return $data->propertyType ? $data->propertyType->pty_title . '(' . $data->propertyType->pty_id . ')' : '';
												}
												),
												array(
													'header' => 'property subtype', 'value' => function (Deal $data) {

													return $data->propertySubtype ? $data->propertySubtype->pty_title . '(' . $data->propertySubtype->pty_id . ')' : '';
												}
												),
												array(
													'header' => 'address', 'value' => function (Deal $data) {

													return $data->property->getFullAddressString();
												}
												),
												'dea_status',
											)
									   ));
		?>
	</div>
</div>