<?php
/**
 * @var $this  InstructionController
 * @var $model Deal
 * @var $form  AdminFilterForm
 * @var $title String
 */

$types = PropertyType::model()->getTypes();
$subtypes = array();
foreach ($types as $key => $value) {
	$subtypes[$value->pty_id] = PropertyType::model()->getTypes($value->pty_id);
}

$offices = Office::model()->enabledClientMatching()->findAll();
$matchingPostcodes = [];
foreach ($offices as $value) {
	$matchingPostcodes[$value->id] = LinkOfficeToPostcode::model()->getPostcodeList($value->id);
}

$form = $this->beginWidget('AdminFilterForm', array(
		'action'               => Yii::app()->getBaseUrl() . '/' . Yii::app()->request->getPathInfo(),
		'id'                   => 'instruction-filter-form',
		'enableAjaxValidation' => false,
		'model'                => [$model],
		'ajaxFilterGrid'       => 'instruction-list',
		'storeInSession'       => false,
		'focus'                => [$model, 'searchString']
));


?>
<fieldset>
	<div class="block-header">Preference</div>
	<div class="control-group">
		<label class="control-label"></label>

		<div class="controls">
			<?php echo $title ?>
			<br>
			<span class="hint">Client will receive email alert for following properties</span>
		</div>
	</div>
	<?php $this->renderPartial('detailSearchFilter', compact('model', 'form')) ?>

	<div class="block-buttons force-margin">
		<a href="<?php echo $this->createUrl('instruction/detailSearch') ?>" class="btn btn-small">Show All</a>
	</div>
	</div>
</fieldset>
<?php $this->endWidget() ?>
<?php $editColumn = array(
		'type'        => 'raw',
		'htmlOptions' => ['class' => 'centered-text'],
		'value'       => function (Deal $data) {

					$html = CHtml::link(CHtml::image('/images/sys/admin/icons/edit-icon.png', 'Edit instruction'), InstructionController::generateLinkToInstruction($data->dea_id));
					$html .= CHtml::link(CHtml::image('/images/sys/admin/icons/print-icon.png', 'Print instruction'), [
							'/property/pdf',
							'id' => $data->dea_id,
					], ['target' => '_blank']);
					return $html;
				}
);

$this->widget('AdminGridView', array(
		'id'               => 'instruction-list',
		'dataProvider'     => $model->search(),
		'selectableRows'   => 1000,
		'selectionChanged' => 'instructionListSelectionChanged',
		'title'            => 'INSTRUCTIONS',
		'columns'          => array(
				$editColumn,
				'dea_id::ID',
				array(
						'name'   => 'dea_created',
						'header' => 'created',
						'value'  => function (Deal $data) {

									return Date::formatDate('d/m/Y', $data->dea_created);
								}
				),
				array(
						'name'   => 'dea_marketprice',
						'header' => 'Price',
						'type'   => 'raw',
						'value'  => function (Deal $data) {

									return Locale::formatPrice($data->dea_marketprice, $data->dea_type == Deal::TYPE_LETTINGS);
								}
				),
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
						'name'   => 'address.fullAddressString',
						'value'  => function (Deal $data) {

									return $data->address ? $data->address->getFullAddressString(", ") : '';
								},
						'header' => 'Address',
				),
				array(
						'name'   => 'address.postcode',
						'header' => 'Postcode',
				),
				array(
						'name'   => 'owners',
						'header' => 'Vendors',
						'type'   => 'raw',
						'value'  => function (Deal $data) {

									$owners = [];
									foreach ($data->owner as $key => $owner) {
										$owners[] = CHtml::link($owner->getFullName(), Yii::app()
																						  ->createUrl('admin4/client/update', ['id' => $owner->cli_id]), ['class' => 'table-link']);
									}
									return implode(', ', $owners);
								}
				),
				$editColumn,

		)
)) ?>

<script type="text/javascript">
	(function ()
	{
		$('#status-trigger').on('change', function ()
		{
			$('.status-checkbox').attr('checked', $(this).is(':checked'));
		});
	})();


	var instructionListSelectionChanged = function (id)
	{
		var selection = $.fn.yiiGridView.getSelection(id);
		$('#instructions-to-email-list').val(selection.join('|'));
	};

	$('.checkbox-enabler.attr-status').on('click', function (e)
	{
		e.preventDefault();
		$('.status-checkbox.attr-status').attr('checked', false);
		$('#Deal_dea_status_' + $(this).data('key')).attr('checked', true);
		$('#Deal_dea_status_' + $(this).data('key')).change();
	});

	$('.propertyPreferenceToggler').on('change', function ()
	{
		var self = $(this);
		$('input[data-parent=' + this.id + ' ]').attr('checked', self.is(':checked'));
	});

	function propertyOfficeOnChange()
	{
		$('.matchingPostcode[data-parent=' + $(this).data('id') + ' ]').attr('checked', $(this).is(':checked'));
	}
	$('.propertyOffice').on('change', propertyOfficeOnChange);
</script>