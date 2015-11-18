<?php
/**
 * @var $this       OfficeControllerBase
 * @var $model      Office
 *
 */
?>
<div class="row-fluid">
	<div class="span12">
		<?php $this->widget('AdminGridView', array(
												  'dataProvider' => $model->search(),
												  'title'        => 'Offices',
												  'id'           => 'office-list',
												  'actions'      => array(
													  'add' => array(
														  $this->createUrl("Create"), 'Add New Office',
														  array('rel' => 'office-add-button')
													  ),
												  ),
												  'columns'      => array(

													  array(
														  'class'    => 'CButtonColumn',
														  'header'   => 'Actions',
														  'template' => '{edit}',
														  'buttons'  => array(
															  'edit' => array(
																  'label'    => 'Edit',
																  'url'      => function ($data) {

																	  /** @var $this OfficeControllerBase */
																	  return $this->createUrl('update', ['id' => $data->id]);
																  },
																  'imageUrl' => "/images/sys/admin/icons/edit-icon.png",
															  )
														  )
													  ),
													  'code',
													  'title',
													  'shortTitle',
													  array(
														  'header' => 'Active',
														  'type'   => 'raw',
														  'value'  => function (Office $data) {
															  return $data->active ? CHtml::image(Icon::GREEN_TICK_ICON, "active") : '';
														  }
													  )
												  )
											 )) ?>
	</div>
</div>