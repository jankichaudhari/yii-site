<?php
/**
 * @var $this  PropertyController
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
//																 'storeInSession'       => false,
															)); ?>
		<fieldset>
			<div class="block-header">SEARCH PROPERTY</div>
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
		if (isset($_GET['nextStep']) && $_GET['nextStep']) {
			$nextStepLink = $_GET['nextStep'];
			foreach ($data->attributes as $attrib => $attribValue) {
				$nextStepLink = str_replace('{' . $attrib . '}', $attribValue, $nextStepLink);
			}
			$html .= CHtml::link(CHtml::image(Icon::USE_ICON, 'Use property'), $nextStepLink);
		}

		$html .= CHtml::link(CHtml::image(Icon::EDIT_ICON, 'Edit property'), [
																			 'property/update', 'id' => $data->pro_id, 'clientId' => (isset($_GET['owner']) ? $_GET['owner'] : "")
																			 ]);
		return $html;
	}, 'htmlOptions' => ['class' => 'centered-text']
);
$this->widget('AdminGridView', array(
									'id'           => 'property-list',
									'dataProvider' => $model->search(),
									'title'        => 'SELECT PROPERTY',
									'actions'      => array(
										'add' => array(
											$this->createUrl("Property/Create", array(
																					 'owner'    => (isset($_GET['owner']) ? $_GET['owner'] : ""),
																					 'nextStep' => (isset($_GET['nextStep']) ? $_GET['nextStep'] : "")
																				)), 'Add New Property',
											array('rel' => 'client-list-add-button')
										),
									),
									'columns'      => array(
										$buttonColumn,
										array('name' => 'address.fullAddressString', 'header' => 'Address', 'value' => function(Property $data) {
											return $data->address ? $data->address->getFullAddressString(', ') : '';
										}),
										array('name' => 'address.postcode', 'header' => 'Postcode'),
										$buttonColumn,

									)
							   ));
