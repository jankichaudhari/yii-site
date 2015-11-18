<?php
/**
 * @var $this         QuickReportController
 * @var $dataProvider CActiveDataProvider
 */
$buttonColumn = array(
	'class'    => 'CButtonColumn',
	'header'   => 'actions',
	'template' => '{open}',
	'buttons'  => array(

		'open' => array(
			'label' => 'Open',
			'url'   => function ($data) {
				return $this->createUrl('view', ['pk' => $data->name]);
			},
		),
	)
);

$actions = ['export'];
if (Yii::app()->user->is('SuperAdmin')) {
	$buttonColumn['buttons']['view'] = array(
		'label'    => 'Edit',
		'url'      => function ($data) {
			return $this->createUrl('update', ['pk' => $data->name]);
		},
		'imageUrl' => Icon::EDIT_ICON
	);
	$buttonColumn['template']        = '{view} {open}';

	$actions['add'] = [$this->createUrl('Create')];
}
$this->widget('AdminGridView', array(
									'title'          => 'Quick Reports',
									'actions'        => $actions,
									'dataProvider'   => $dataProvider,
									'selectableRows' => 0,
									'columns'        => array(
										$buttonColumn,
										'title',
										'description',
										$buttonColumn

									)
							   ));

