<?php
/**
 * @var $this         OuterLinkController
 * @var $dataProvider CActiveDataProvider
 * @var $model        OuterLink
 */
?>
<?php
$self = $this;
$buttonColumn = array(
	'header'   => 'Actions',
	'class'    => 'CButtonColumn',
	'template' => '{edit}',
	'buttons'  => array(
		'edit' => array(
			'label'    => 'Edit Link',
			'url'      => function ($data) use ($self) {

				return $self->createUrl('update', array('id' => $data->id));
			},
			'imageUrl' => Icon::EDIT_ICON
		),
	)
);?>
<div class="row-fluid">
	<div class="span12">
		<?php $this->widget('AdminGridView', array(
												  'id'           => 'link-list',
												  'dataProvider' => $dataProvider,
												  'title'        => 'Links List',
												  'actions'      => array('add' => array($this->createUrl("Create"))),
												  'columns'      => array(
													  $buttonColumn,
													  array('header' => 'Title', 'name' => 'title'),
													  array('header' => 'Link', 'name' => 'link'),
													  $buttonColumn,
												  )
											 )); ?>
	</div>
</div>