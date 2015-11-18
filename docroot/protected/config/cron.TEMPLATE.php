<?php

// This is the configuration for yiic cron jobs
return array(
	'basePath'  => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name'      => 'Wooster & Stock cron',
	'import'    => array(
		'application.models.*',
		'application.components.*',
	),

	// application components
	'components'=> array(
		'db'=> array(
			'connectionString' => 'mysql:host=localhost;dbname=wsv3_live',
//			'connectionString' => 'mysql:host=localhost;dbname=ws_janki_dev',
			'emulatePrepare'   => true,
			'username'         => 'ws_website',
			'password'         => 'ws_website',
			'charset'          => 'utf8',
		),
	),
);

