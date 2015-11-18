<?php
/**
 * @var $this  InstructionController
 * @var $model Deal
 * @var $form  AdminFilterForm
 *
 */
$form = $this->beginWidget('AdminFilterForm', array(
		'action'               => Yii::app()->getBaseUrl() . '/' . Yii::app()->request->getPathInfo(),
		'id'                   => 'instruction-filter-form',
		'enableAjaxValidation' => false,
		'model'          => [$model],
		'ajaxFilterGrid'       => 'instruction-list',
		'storeInSession' => false,
		'focus'          => [$model, 'searchString']
));
?>
<fieldset>
	<?php $this->renderPartial('searchFilter', compact('model', 'form')) ?>
</fieldset>
<?php
$this->endWidget();
$editColumn = array(
		'type'        => 'raw',
		'htmlOptions' => ['class' => 'centered-text'],
		'value'       => function (Deal $data) {
					$html = CHtml::link(CHtml::image('/images/sys/admin/icons/edit-icon.png', 'Edit instruction'), ['instruction/summary', 'id' => $data->dea_id]);
					$html .= CHtml::link(CHtml::image('/images/sys/admin/icons/print-icon.png', 'Print instruction'), ['/property/pdf', 'id' => $data->dea_id], ['target' => '_blank']);
					return $html;
				}
);

$this->widget('AdminGridView', array(
		'id'                    => 'instruction-list',
		'dataProvider'          => $model->search(),
		'selectableRows'        => 1000,
		'selectionChanged'      => 'instructionListSelectionChanged',
		'title'                 => 'INSTRUCTIONS',
		'actions'               => array(
				'<form action="/v3.0/live/admin/email_deal_multi.php" method="GET">
				<input type="hidden" name="dea_id" id="instructions-to-email-list">
				<div class="block-buttons"><input type="submit" class="btn" value="Email"></div>
				</form>'
		),
		'columns'               => array(
				array(
						'class'  => 'CCheckBoxColumn',
						'value'  => '$data->dea_id',
						'header' => '',
				),
				$editColumn,
				'dea_id::ID',
				array(
						'name'   => 'dea_created',
						'header' => 'created',
						'value'  => function (Deal $data) {

									return Date::formatDate('d/m/Y', $data->dea_created);
								}
				),
				'dea_type::Type',
				'dea_status::Status',
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
										$owners[] = CHtml::link($owner->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $owner->cli_id]), ['class' => 'table-link']);
									}
									return implode(', ', $owners);
								}
				),
				$editColumn,

		),
		'rowCssClassExpression' => '$data->isDIY() ? "{$data->DIY}-property" : ""'
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
	}

	$('.checkbox-enabler.attr-status').on('click', function (e)
	{
		e.preventDefault();
		$('.status-checkbox.attr-status').attr('checked', false);
		$('#Deal_dea_status_' + $(this).data('key')).attr('checked', true);
		$('#Deal_dea_status_' + $(this).data('key')).change();
	})
</script>