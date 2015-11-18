<?php
/**
 * @var $this  InstructionController
 * @var $model Deal
 * @var $form  AdminFilterForm
 */
$form = $this->beginWidget('AdminFilterForm', array(
		'action'               => Yii::app()->getBaseUrl() . '/' . Yii::app()->request->getPathInfo(),
		'id'                   => 'instruction-filter-form',
		'enableAjaxValidation' => false,
		'model'                => [$model],
		'ajaxFilterGrid'       => 'missedFollowUps-list',
		'storeInSession'       => false,
		'focus'                => [$model, 'searchString']
));
?>
	<fieldset>
		<div class="block-header">Search</div>
		<div class="control-group">
			<label class="control-label">Property Type</label>

			<div class="controls">
				<?php echo $form->checkBoxListWithSelectOnLabel($model, 'dea_ptype', CHtml::listData(PropertyType::model()->getTypes(), 'pty_id', 'pty_title'), ['separator' => '']) ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Bedrooms</label>

			<div class="controls">
				<?php echo $form->textField($model, 'minBedrooms', ['class' => 'input-xxsmall', 'placeholder' => 'Min']) ?>
				<?php echo $form->textField($model, 'maxBedrooms', ['class' => 'input-xxsmall', 'placeholder' => 'max']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Valuation price</label>

			<div class="controls">
				<?php echo $form->dropDownList($model, 'valuationPriceMin', Util::getPropertyPrices("minimum"), ['class' => 'input-xsmall', 'empty' => 'No min']) ?>
				<?php echo $form->dropDownList($model, 'valuationPriceMax', Util::getPropertyPrices("maximum"), ['class' => 'input-xsmall', 'empty' => 'No max']) ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Branch</label>

			<div class="controls">
				<?php echo $form->checkBoxListWithSelectOnLabel($model, 'dea_branch', CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title'), ['separator' => ' ']); ?>
			</div>
		</div>

	</fieldset>
<?php
$this->endWidget();
$this->widget("AdminGridView", array(
		'title'        => 'Instruction Alerts',
		'dataProvider' => $model->searchMissedFollowUp(),
		'id'           => 'missedFollowUps-list',
		'columns'      => Array(
				array(
						'type'        => 'raw',
						'htmlOptions' => ['class' => 'centered-text'],
						'value'       => function (Deal $data) {

									return CHtml::link(CHtml::image(Icon::EDIT_ICON, 'Edit instruction'), ['instruction/summary', 'id' => $data->dea_id])
									. CHtml::link(CHtml::image(Icon::BLUE_PRINT_ICON, 'Print instruction'), [
											'/property/pdf',
											'id' => $data->dea_id
									], ['target' => '_blank']);
								}
				),
				array(
						'name'   => 'dea_created',
						'header' => 'Created',
						'value'  => 'Date::formatDate("d/m/Y", $data->dea_created)',
				),
				'address.searchString::Address',
				'propertyType.pty_title::Type',
				array(
						'name'   => 'negotiator.use_fname',
						'header' => "Neg'",
						'type'   => 'raw',
						'value'  => function (Deal $data) {

									if (!$data->negotiator) {
										return '<span class="negotiator-color empty"></span>N/A';
									}
									return '<span class="negotiator-color" style="background: #' . $data->negotiator->use_colour . '"></span><span title="' . $data->negotiator->getFullName() . '">' . $data->negotiator->getInitials() . '</span>';
								}
				),
				array(
						'name'   => 'owners',
						'header' => 'Vendors',
						'type'   => 'raw',
						'value'  => function (Deal $data) {

									$owners = [];
									foreach ($data->owner as $owner) {
										$owners[] = CHtml::link($owner->getFullName(), ['client/update', 'id' => $owner->cli_id], ['class' => 'table-link']);
									}
									return implode(', ', $owners);
								}
				),
				array(
						'name'   => 'followUpDue',
						'header' => 'Follow Up date',
						'value'  => 'Date::formatDate("d/m/Y", $data->followUpDue)',
				),
				array(

						'header' => 'Follow Up User',
						'type'   => 'raw',
						'value'  => function (Deal $data) {

									if ($data->followUpAppointment) {
										return '<span class="negotiator-color" style="background: #' . $data->followUpAppointment->user->use_colour . '"></span>
															<span title="' . $data->followUpAppointment->user->getFullName() . '">' . $data->followUpAppointment->user->getInitials() . '</span>';
									} else {
										return '<span class="negotiator-color empty"></span>N/A';
									}
								}
				),
				array(
						'header' => 'val. price',
						'name'   => 'dea_valueprice',
						'value'  => function (Deal $data) {
									if ($data->dea_valueprice) {
										return Locale::formatCurrency($data->dea_valueprice, true, false);
									} else {
										return 'N/A';
									}
								}
				),
				'branch.bra_title::Office'
		)
));