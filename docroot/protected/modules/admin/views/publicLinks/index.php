<?php
$this->menu=array(
	array('label'=>'Create PublicLinks', 'url'=>array('create')),
	array('label'=>'Manage PublicLinks', 'url'=>array('admin')),
);

?>

<h1>Public Links</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
