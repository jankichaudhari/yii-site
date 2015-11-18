<?php
/**
 * @var $this        InstructionController
 * @var $model       Client
 * @var $instruction Deal
 *
 */

$price = Locale::formatPrice($instruction->dea_marketprice, $instruction->dea_type == Deal::TYPE_LETTINGS);
if ($instruction->dea_type == 'Lettings') {
	$price .= ' - ' . Locale::formatPrice($instruction->getPrice('pcm'), true, true);
}
$editColumn = array(
	'type'        => 'raw',
	'htmlOptions' => ['class' => 'centered-text'],
	'value'       => function (Client $data) {

		$html = CHtml::link(CHtml::image('/images/sys/admin/icons/edit-icon.png', 'Edit instruction'), Yii::app()->createUrl('admin4/client/update', ['id' => $data->cli_id]));
		return $html;
	}
);
$columns = array(
	$editColumn,
	'cli_id::id',
	'cli_fname::Name',
	'cli_sname::Surname',
	'cli_saleemail::send sales emails',
	'cli_email::Email',
	'cli_salebed::Max bedrooms',
	'cli_salemin::Min price',
	'cli_salemax::Max price',
	'cli_saleptype::property type',
	$editColumn,
);

if ($instruction->dea_type == Deal::TYPE_LETTINGS) {
	$columns = array(
		$editColumn,
		'cli_id::id',
		'cli_fname::Name',
		'cli_sname::Surname',
		'cli_saleemail::send letting emails',
		'cli_email::Email',
		'cli_letbed::Max bedrooms',
		'cli_letmin::Min price',
		'cli_letmax::Max price',
		'cli_letptype::property type',
		$editColumn,
	);
}

?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Instruction info</div>
			<div class="control-group shaded">
				<label class="control-label">Location</label>

				<div class="controls text"><?php echo $instruction->property->getAddressObject()->getFullAddressString(', ') ?></div>
			</div>

			<div class="control-group">
				<label class="control-label">ID:</label>

				<div class="controls"><span class="text"><?= $instruction->dea_id ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Property area:</label>

				<div class="controls"><span
							class="text"><?= $instruction->property->getAreaObject()->are_title ?> <?= $instruction->property->getAreaObject()->are_postcode ?>
						[id:<?php echo $instruction->property->getAreaObject()->are_id ?>]</span></div>
			</div>

			<div class="control-group">
				<label class="control-label">Property bedrooms:</label>

				<div class="controls"><span
							class="text">Property record: <?= (isset($instruction->property->pro_bedroom) ? $instruction->property->pro_bedroom : "N/A") ?>
						<br>
														 instruction record: <?php echo $instruction->dea_bedroom ?></span>
				</div>

			</div>
			<div class="control-group">
				<label class="control-label">Property price:</label>

				<div class="controls"><span class="text"><?= $price ?></span></div>
			</div>

			<div class="control-group">
				<label class="control-label">Property type:</label>

				<div class="controls"><span class="text"><?= $instruction->dea_ptype ?>
						(<?php echo(isset($instruction->property->pro_ptype) ? $instruction->property->pro_ptype : "N/A") ?>
						)</span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Property sub-type:</label>

				<div class="controls"><span class="text"><?= $instruction->dea_psubtype ?>
						(<?php echo(isset($instruction->property->pro_psubtype) ? $instruction->property->pro_psubtype : "N/A") ?>
						)</span></div>
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<?php
		$this->widget('AdminGridView', array(
											'dataProvider' => $model->searchAgainstInstruction($instruction),
											'columns'      => $columns
									   ));
		?>
	</div>
</div>