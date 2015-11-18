<?php

// change the following paths if necessary
$yiit   = dirname(__FILE__) . '/../../../components/yii/framework/yiit.php';
$config = dirname(__FILE__) . '/../config/development.php';
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_DEV') or define('YII_DEV', true);
require_once($yiit);
require_once(dirname(__FILE__) . '/WebTestCase.php');
require_once(dirname(__FILE__) . '/WebTestCase2.php');
Yii::createWebApplication($config);
Yii::import('application.tests.unit.*');
Yii::import('application.tests.mock.*');
Yii::import('application.tests.unit.models.Place.*');
Yii::import('application.tests.unit.models.*');