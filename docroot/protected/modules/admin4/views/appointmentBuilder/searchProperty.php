<?php
/**
 * @var $this  AppointmentBuilderController
 * @var $form  AdminFilterForm
 * @var $model Property
 *
 */
?>
	<div class="form-inline">
		<?php $form = $this->beginWidget('AdminFilterForm', array(
																 'id'                   => 'property-filter-form',
																 'enableAjaxValidation' => false,
																 'model'                => array($model),
																 'ajaxFilterGrid'       => 'property-list',
															)); ?>
		<fieldset>
			<div class="block-header">Search</div>
			<div class="content">
				<label>
					Address    <?php echo $form->textField($model, 'fullAddressString', array('size' => 30)) ?>
				</label>
				<label>
					Postcode <?php echo $form->textField($model, 'pro_postcode', array('size' => 10)) ?>
				</label>
			</div>
		</fieldset>
		<?php $this->endWidget() ?>
	</div>

<?php
$buttonColumn = array(
	'header'         => '',
	'type'           => 'raw',
	'value'          => function (Property $data) {

		$html = '';
		$html .= CHtml::link(CHtml::image(Icon::USE_ICON, 'Use property'), ['propertySelected', 'propertyId' => $data->pro_id]);
		$html .= CHtml::link(CHtml::image(Icon::EDIT_ICON, 'Edit property'), [
																			 'property/update', 'id' => $data->pro_id,
																			 'clientId'              => AppointmentBuilder::getCurrent()->getClientId(),
																			 'nextStep'              => 'AppointmentBuilder_propertySelected'
																			 ]);
		return $html;
	}, 'htmlOptions' => ['class' => 'centered-text']
);
$this->widget('AdminGridView', array(
									'id'           => 'property-list',
									'dataProvider' => $model->search(),
									'title'        => 'Select property',
									'actions'      => array(
										'add' => array(
											$this->createUrl("Property/Create", array(
																					 'owner'    => AppointmentBuilder::getCurrent()->getClientId(),
																					 'nextStep' => 'AppointmentBuilder_propertySelected'
																				)), 'Add New Property',
											array('rel' => 'client-list-add-button')
										),
									),
									'columns'      => array(
										$buttonColumn,
										array(
											'name' => 'address.fullAddressString', 'header' => 'Address', 'value' => function (Property $data) {

											return $data->address ? $data->address->getFullAddressString(', ') : '';
										}
										),
										array('name' => 'address.postcode', 'header' => 'Postcode'),
										$buttonColumn,

									)
							   ));
