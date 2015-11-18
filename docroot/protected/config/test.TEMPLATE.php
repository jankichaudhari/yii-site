<?php

return CMap::mergeArray(
	require(dirname(__FILE__) . '/main.php'),
	array(
		 'preload'    => array(),
		 'components' => array(
			 'fixture' => array(
				 'class' => 'system.test.CDbFixtureManager',
			 ),
			 'db'      => array(
				 'connectionString' => 'mysql:host=10.1.14.94;dbname=ws_unittest',
				 'emulatePrepare'   => true,
				 'username'         => 'root',
				 'password'         => 'root',
				 'charset'          => 'utf8',
			 ),
		 ),
	)
);
