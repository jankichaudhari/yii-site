<?php
/**
 * @var $form  CActiveForm
 * @var $model CActiveRecord
 * @var $this  CController
 */
?>
<?php $this->widget('zii.widgets.CDetailView', array(
													'data'      => $model,
													'attributes'=> array(
														'id',
														'title',
														'description',
														'link',
														'image',
													),
											   )); ?>
