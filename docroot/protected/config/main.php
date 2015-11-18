<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'   => __DIR__ . DIRECTORY_SEPARATOR . '..',
	'name'       => 'Admin v4',
	'theme'      => 'grey-smooth', // also needs to make changes to widgetFactory in components section
	// autoloading model and component classes
	'import'     => array(
		'application.config.lists.*',
		'application.controllers.*',
		'application.models.*',
		'application.models.forms.*',
		'application.models.public.*',
		'application.components.*',
		'application.components.widgets.*',
		'application.components.public.*',
		'application.components.public.widgets.*',
		'application.components.TabbedLayout.*',
		'application.components.WKPDF.*',
		'application.helpers.*',
		'application.extensions.twilio.Services.*',
		'application.extensions.Mandrill.*',
	),
	// application components
	'components' => array(
		'user'          => array(
			// enable cookie-based authentication
			'class'          => 'WebUser',
			'allowAutoLogin' => true,
		),
		'log'           => array(
			'class'  => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CWebLogRoute',
				),
			),
		),
		'file'          => array(
			'class' => 'application.extensions.file.CFile'
		),
		'image'         => array(
			'class'  => 'application.extensions.image.CImageComponent',
			// GD or ImageMagick
			'driver' => 'GD',
			// ImageMagick setup path
			'params' => array('directory' => '/opt/local/bin'),
		),
		'imagemod'      => array(
			'class' => 'application.extensions.imagemodifier.CImageModifier',
		),
		'commandMap'    => array(
			'cron' => 'application.extensions.PHPDocCrontab.PHPDocCrontab'
		),
		'device'      => array(
			'class' => 'application.extensions.Device.Device',
		),


		// uncomment the following to enable URLs in path-format

		'urlManager'    => require 'urlManager.php',
		'db'            => array(
			'connectionString' => 'mysql:host=localhost;dbname=wsv3_live',
//			'connectionString' => 'mysql:host=localhost;dbname=ws_janki_dev',
			'emulatePrepare'   => true,
			'username'         => 'wsv3_db_user',
			'password'         => 'CHe9adru+*=!a!uC7ubRad!TRu#raN',
			'charset'          => 'utf8',
		),
		'errorHandler'  => array(
			'errorAction' => 'site/page/view/notFound',
		),
		'widgetFactory' => array(
			'widgets' => array(
				'AdminGridView'              => array(
					'cssFile' => "/css/grey-smooth/grid-view/style.css",
					'pager'   => array(
						'cssFile' => "/css/grey-smooth/pager.css"
					),
				),
				'zii.widgets.grid.CGridView' => array(
					'cssFile' => "/css/grey-smooth/grid-view/style.css",
					'pager'   => array(
						'cssFile' => "/css/grey-smooth/pager.css"
					),
				),
				'TabbedView'                 => array(
					'cssFile' => '/css/grey-smooth/TabbedView.css',
				),
			)
		),
		'browser'       => array(
			'class' => 'application.extensions.browser.CBrowserComponent',
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'     => require 'params.php',
	'modules'    => array(
		'admin4',
	),

);

