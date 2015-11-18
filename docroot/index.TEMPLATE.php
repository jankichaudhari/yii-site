<?php
// COMMENT TO GET 1000 GET IN REPO
// change the following paths if necessary
/** test changes to fix my username in the console. vitaly second attempt */
$yii    = dirname(__FILE__) . '/../components/yii/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/production.php';
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_DEV') or define('YII_DEV', false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('WS_PRODUCTION') or define('WS_PRODUCTION', true);

if (defined('YII_DEV')) {
	$config = dirname(__FILE__) . '/protected/config/development.php';
}
if (file_exists(__DIR__ . '/protected/tmp/testrun')) {
	$config = dirname(__FILE__) . '/protected/config/test.php';
}
// specify how many levels of call stack should be shown in each log message
require_once($yii);
/**
 * DUE TO SOME PEOPLE'S STUPIDITY (Apple who made HFS+ case-insensitive by default)
 * THIS CLASS HAS TO BE MAPPED DIRECTLY, BECAUSE, YES I AM LAZY BASTARD
 * THIS LINE WILL ONLY BE REUQIRED IF YOU SET UP SYSTEM ON A CASE-INSENSITIVE FS i.e. FAT, NTFS, HFS
 *
 * GOOD GUY Linus Torvalds has a case-sensitive EXT(N) file systems.
 */
Yii::$classMap = array(
	'Mailshot' => __DIR__ . '/protected/models/Mailshot.php'
);
Yii::createWebApplication($config)->run();
