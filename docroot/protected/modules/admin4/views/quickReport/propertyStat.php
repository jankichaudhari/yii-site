<?php
/**
 * @var $dataProvider CSqlDataProvider
 * @var $this         QuickReportController
 *
 * @layout /layouts/two_columns
 *
 */
$this->pageTitle = 'Missing EPCs';

$editImage    = CHtml::image("/images/sys/admin/icons/edit-icon.png", "Edit");
$printImage   = CHtml::image("/images/sys/admin/icons/print-icon.png", "Print");
$editFunction = function($data) use ($editImage, $printImage)
{
	return CHtml::link($editImage, "/admin4/instruction/summary/" . $data['deal_id']  . "") .
			CHtml::link($printImage, "/admin4/instruction/summary/" . $data['deal_id'] . "", array('onclick'=> 'return dealPrint(' . $data['deal_id'] . ')'));
};
$this->widget('AdminGridView', array(
									'title'            => 'Instructions Without EPC',
									'actions'          => array('export'),
									'dataProvider'     => $dataProvider,
									'columns'          => array(
										array(
											'header'   => 'Actions',
											'class'    => 'CButtonColumn',
											'template' => '{view} {printLeft}',
											'buttons'  => array(
												'view'  => array(
													'label'    => 'View',
													'url'      => function($data)
													{
														return "/admin4/instruction/summary/" . $data['deal_id'] . "";
													},
													'imageUrl' => "/images/sys/admin/icons/edit-icon.png"
												),
												'printLeft' => array(
													'label'    => 'Print',
													'imageUrl' => "/images/sys/admin/icons/print-icon.png",
													'click' =>'printDeal'
												)
											)
											//
										),
										array('name'   => 'deal_id',
											  'header' => 'Inst ID',
											  'type'   => 'raw',
											  'value'  => function($data)
											  {
												  if ($data['has_old_epc']) {
													  return '<span style="color:#999">' . $data['deal_id'] . '</span>';
												  } else {
													  return $data['deal_id'];
												  }
											  }),
										'deal_type::Type',
										'address::Address',
										'postcode::Postcode',
										'owner::Owner',
										'prop_type::Property Type',
										array('name'   => 'last_available',
											  'header' => "Live Date",
											  'value'  => function($data)
											  {
												  return $data['last_available'] ? date("d/m/Y", strtotime($data['last_available'])) : '';
											  }),
										'officeCode::Office',
										'status::Status',
										array('name'   => 'status_date',
											  'header' => 'Status Date',
											  'value'  => function($data)
											  {
												  return date("d/m/Y", strtotime($data['status_date']));
											  }),
										array('name'   => 'days_on_site',
											  'header' => 'Days on Site',
											  'value'  => function($data)
											  {
												  $status = strtolower($data['status']);
												  if ($status != 'available' && $status != 'under offer') {
													  return 0;
												  } else {
													  return $data['days_on_site'];
												  }
											  }
										),
										array(
											'header'   => 'Actions',
											'class'    => 'CButtonColumn',
											'template' => '{view} {print}',
											'buttons'  => array(
												'view'  => array(
													'label'    => 'View',
													'url'      => function($data)
													{
														return "/admin4/instruction/summary/" . $data['deal_id'] . "";
													},
													'imageUrl' => "/images/sys/admin/icons/edit-icon.png"
												),
												'print' => array(
													'label'    => 'Print',
													'imageUrl' => "/images/sys/admin/icons/print-icon.png",
													'click'    => 'printDeal',
												)
											)
											//
										),

									),
									'selectableRows'   => 0
							   ));
?>
<script type="text/javascript">
	var openDeal = function (id)
	{
		id = $.fn.yiiGridView.getSelection(id);
		if (!id) return; // selection removed
		document.location.href = '/docroot/instruction/summary/' + id;
	}

	var printDeal = function (event)
	{
		var id = this.parentNode.parentNode.cells[1].innerHTML; // this is so baaad. soo bad
		window.open("/v3.0/live/admin/deal_print.php?dea_id=" + id, '', "height=842,width=595,status=yes,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=yes");
	}
</script>