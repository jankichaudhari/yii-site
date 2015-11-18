<?php
/**
 * @var $this         CController
 * @var $dataProvider CSqlDataProvider
 */
$this->widget('AdminGridView', array(
									'dataProvider' => $dataProvider,
									'columns' => array(
										'count',
										'address',
//										'ids'
									)
							   ));
?>

