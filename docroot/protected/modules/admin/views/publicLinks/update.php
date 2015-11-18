<?php
$this->breadcrumbs=array(
	'Public Links'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List PublicLinks', 'url'=>array('index')),
	array('label'=>'Create PublicLinks', 'url'=>array('create')),
	array('label'=>'View PublicLinks', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage PublicLinks', 'url'=>array('admin')),
);
?>

<h1>Update PublicLinks <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>