<?php
/**
 * @var $this StatisticController
 * @var $model PageViewStatistic
 * @var $dataProvider CActiveDataProvider
 */
$this->widget("AdminGridView", array(
									'title' => 'Page statistics',
									'dataProvider' => $dataProvider,
									'filter' => $model,
							   ));