<?php
/**
 * @var                          $this CareerController
 * @var CActiveDataProvider      $dataProvider
 */
$self = $this;
?>
<?php $buttonColumn = array(
	'header'   => 'Actions',
	'class'    => 'CButtonColumn',
	'template' => '{view}',
	'buttons'  => array(
		'view'      => array(
			'label'    => 'View',
			'url'      => function ($data) use ($self) {

				return $self->createUrl('update', array('id'=> $data->id));
			},
			'imageUrl' => "/images/sys/admin/icons/edit-icon.png"
		),
	)
	//
);
$this->widget('AdminGridView', array(
									'dataProvider'     => $dataProvider,
									'title'            => 'Careers',
									'actions'          => array(
										'add' => array(
											$this->createUrl("Create"), 'Add New Career',
											array('rel'=> 'career-add-button')
										),
									),
									'columns'          => array(
										$buttonColumn,
										'id',
										'listOrder',
										'name',
										'email',
										array(
											'name'   => 'created',
											'header' => 'Created',
											'value'  => function ($data) {

												return Date::formatDate("d/m/Y", $data->created);
											}
										),
										array(
											'header' => 'Created by', 'value'=> function ($data) {

											return $data->creator->use_fname . " " . $data->creator->use_sname;
										}
										),
										array(
											'name'   => 'isActive',
											'header' => 'Active',
											'value'  => function ($data) {

												return $data->isActive ? 'Yes' : 'No';
											}
										),
										$buttonColumn,
									)
							   )); ?>


