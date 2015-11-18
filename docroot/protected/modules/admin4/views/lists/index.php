<?php
/**
 * @var $this         ListsController
 * @var $dataProvider CActiveDataProvider
 */
$this->widget("AdminGridView", array(
												  'title' => 'Lists',
												  'actions' => array('export'),
												  'dataProvider'     => $dataProvider,
												  'filter' => $dataProvider->model,
											 ));?>
