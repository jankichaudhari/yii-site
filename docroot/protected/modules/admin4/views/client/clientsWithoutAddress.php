<?php
/**
 * @var $this CController
 * @var $dataProvider CActiveDataProvider
 *
 */

$this->widget('AdminGridView', array(
										 'dataProvider' => $dataProvider,
										 'columns' => array(
											 'cli_fname::Name',
											 'cli_sname::Name',
											 'cli_email::Name',
										 ),
									));