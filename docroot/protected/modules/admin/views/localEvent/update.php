<?php
$this->breadcrumbs=array(
	'Local Events'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List LocalEvent', 'url'=>array('index')),
	array('label'=>'Create LocalEvent', 'url'=>array('create')),
	array('label'=>'View LocalEvent', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage LocalEvent', 'url'=>array('admin')),
);
?>

<h1>Update LocalEvent <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>