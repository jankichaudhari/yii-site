<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(require("main.php"), array(
//	'preload'   => array('log'),'preload'   => array('log'),
	'modules'   => array(
			// uncomment the following to enable the Gii tool

			'gii'=> array(
				'class'    => 'system.gii.GiiModule',
				'password' => 'wooster',
				// If removed, Gii defaults to localhost only. Edit carefully to taste.
				'ipFilters'=> array('127.0.0.1', '::1', "*"),
			),

		),
	'components' => array(
		'db'          => array(
					'connectionString' => 'mysql:host=localhost;dbname={db_name}',
					'emulatePrepare'   => true,
					'username'         => '{username}',
					'password'         => '{password}',
					'charset'          => 'utf8',
				),
	)

));