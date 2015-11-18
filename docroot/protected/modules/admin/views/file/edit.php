<?php
/**
 * @var    File             $model
 * @var CActiveForm         $form
 * @var CModel              $parentModel
 * @var String              $parentField
 * @var                     $this CController
 */
$this->widget('CMultiFileUpload', array(
									   'name'        => 'files',
									   'accept'      => 'jpg|png:gif',
									   'max'         => 10,
									   'remove'      => Yii::t('ui', 'Remove'),
									   //'denied'=>'', message that is displayed when a file type is not allowed
									   //'duplicate'=>'', message that is displayed when a file appears twice
									   'htmlOptions' => array('size'=> 25),
								  ));
