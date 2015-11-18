<?php
/**
 * @var $this        Addresscontroller
 * @var $model       Address
 * @var $filterForm  AdminFilterForm
 */
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/AddressTools.js', CClientScript::POS_HEAD);
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<?php $filterForm = $this->beginWidget('AdminFilterForm', array(
																		   'id'             => 'address-filter-form',
																		   'model'          => $model,
																		   'storeInSession' => false,
																		   'ajaxFilterGrid' => 'address-list',
																	  )); ?>
			<fieldset>
				<div class="block-header">Search for Address</div>
				<div class="content">
					<label>Search String</label>

					<?php echo $filterForm->textField($model, 'searchString') ?>
					<label>
						Postcode
						<?php echo $filterForm->textField($model, 'postcode') ?>
					</label>
				</div>
			</fieldset>
			<?php $this->endWidget() ?>
		</div>
	</div>
	<div class="row-fluid">
		<?php $this->widget('AdminGridView', array(
												  'id'           => 'address-list',
												  'title'        => 'Addresses',
												  'dataProvider' => $model->search(),
												  'actions'      => array(
													  'add' => array(
														  $this->createUrl("Create", array('name' => (isset($_GET['name']) ? $_GET['name'] : "Address"), 'popup' => true)),
														  'Add new Address',
													  ),
												  ),
												  'columns'      => array(
													  array(
														  'class'    => 'CButtonColumn',
														  'header'   => 'Actions',
														  'template' => '{use}',
														  'buttons'  => array(
															  'use' => array(
																  'label'    => 'Use',
																  'url'      => function ($data) {

																	  return $data->id;
																  },
																  'imageUrl' => "/images/sys/admin/icons/Use-Icon.png",
																  'click'    => "select",
															  ),
//															  'edit'     => array(
//																  'label'    => 'Edit',
//															  	'url'      => function ($data)
//																  {
//
//																	  return '/v3.0/live/admin/client_edit.php?cli_id=' . $data->cli_id;
//																  },
//																  'imageUrl' => "/images/sys/admin/icons/edit-icon.png",
//															  )
														  )
													  ),
													  'postcode::Postcode',
													  array('header' => 'Addres', 'value' => '$data->toString()'),
												  )
											 )) ?>
	</div>
</div>
<script type="text/javascript">
	var name = '<?php echo (isset($_GET['name']) ? $_GET['name'] : "Address") ?>';
	AddressTools(name).init();

	var select = function ()
	{
		AddressTools(name).selectAddress(this.getAttribute('href'));
		window.close();
		return false;
	}
</script>