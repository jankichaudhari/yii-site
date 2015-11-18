<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
		'basePath'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
		'name'       => 'Wooster & Stock console app',
		'import'     => array(
				'application.models.*',
				'application.models.appointments.*',
				'application.components.*',
				'application.components.widgets.*',
				'application.commands.*',
		),
		// application components
		'components' => array(
				'urlManager' => require 'urlManager.php',
				'db'         => array(
						'connectionString' => 'mysql:host=localhost;dbname={db name}',
						'emulatePrepare'   => true,
						'username'         => '{username}',
						'password'         => '{password}',
						'charset'          => 'utf8',
				),
				'request'    => array(
						'hostInfo'  => 'http://www.woosterstock.co.uk',
						'baseUrl'   => '',
						'scriptUrl' => '',
				)
		),
		'params'     => require 'params.php',
);

