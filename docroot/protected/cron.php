<?php

// change the following paths if necessary
$yii=dirname(__FILE__) . '/../../../components/yii/framework/yii.php';
$config=dirname(__FILE__).'/config/cron.php';

require_once($yii);

$app = Yii::createConsoleApplication($config)->run();