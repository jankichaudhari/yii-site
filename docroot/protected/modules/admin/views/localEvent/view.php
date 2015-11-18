<?php
$this->breadcrumbs=array(
	'Local Events'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List LocalEvent', 'url'=>array('index')),
	array('label'=>'Create LocalEvent', 'url'=>array('create')),
	array('label'=>'Update LocalEvent', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete LocalEvent', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage LocalEvent', 'url'=>array('admin')),
);
?>

<h1>View LocalEvent #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'strapline',
		'heading',
		'description',
		'dateFrom',
		'dateTo',
		'timeFrom',
		'timeTo',
		'url',
		'addressID',
		'createdBy',
		'created',
	),
)); ?>
