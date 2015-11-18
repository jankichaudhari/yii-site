<?php
/**
 * @package application.LocalEvent.views
 * @var CActiveDataProvider $dataProvider
 * @var                     $this LocalEventController
 */
?>
<div class="row-fluid">
<div class="span12">
<?php $this->widget('AdminListView', array(
										  'title'       => 'Local Events',
										  'dataProvider'=> $dataProvider,
										  'itemView'    => '_view',
										  'actions'     => ['add' => [$this->createUrl('Create'), 'title' => 'Add']],
									 )); ?></div>
</div>