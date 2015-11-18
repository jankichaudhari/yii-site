<?php
/**
 * @var $this         PropertyCategoryController
 * @var $dataProvider CActiveDataProvider
 */
$buttonColumn = array(
	'header'   => 'Actions',
	'class'    => 'CButtonColumn',
	'template' => '{edit}',
	'buttons'  => array(
		'edit' => array(
			'label'    => 'Edit',
			'url'      => function ($data) {
				return $this->createUrl('update', ['id' => $data->id]);
			},
			'imageUrl' => Icon::EDIT_ICON
		),
	)
);
$this->widget('AdminGridView',
			  array(
				   'id'           => 'property-category',
				   'dataProvider' => $model->search(),
				   'title'        => 'Property Categories',
				   'actions'      => array(
					   'add' => array(
						   $this->createUrl("create"), 'Add New Property Category',
						   array('rel' => 'category-add-button')
					   ),
				   ),
				   'columns'      => array(
					   $buttonColumn,
					   'id',
					   'title::Name',
					   array(
						   'name'   => 'created',
						   'header' => 'Created',
						   'value'  => function ($data) {

							   return Date::formatDate("d/m/Y", $data->created);
						   }
					   ),
					   array(
						   'name'   => 'modified',
						   'header' => 'Modified',
						   'value'  => function ($data) {

							   return Date::formatDate("d/m/Y", $data->modified);
						   }
					   ),
					   'statusValue.ListItem::Status',
					   $buttonColumn,
				   )
			  )); ?>


