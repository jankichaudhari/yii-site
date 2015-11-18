<?php
return array(
	// this is used in contact page
	'hostname'                           => 'woosterstock.co.uk',
	'globalUrlOld'                       => '/v3.0/live/admin/',
	'calendarLink'                       => '/v3.0/live/admin/calendar.php',
	'version'                            => "v4.0",
	'adminEmail'                         => 'vitaly.suhanov@woosterstock.co.uk',
	'tmpDirPath'                         => __DIR__ . "/../tmp",
	'logDirPath'                         => __DIR__ . "/../../../logs",
	'imgPath'                            => __DIR__ . "/../../images",
	'filePath'                           => __DIR__ . "/../../files",
	'feedPath'                           => __DIR__ . '/../../v3.0/live/admin/feed',
	'valuation'                          => array(
			'email'   => 'cam@woosterstock.co.uk',
			'replyTo' => 'zoe@woosterstock.co.uk',
			'sender'  => 'valuations@woosterstock.co.uk',
	),
	'imgUrl'                             => "/images",
	'postcodeAnywhere'                   => array(
			'accCode' => 'WOOST11112',
			'license' => 'YJ67-YN69-YY93-MG96'
	),
	'property'                           => ['allowedOwnersNumber' => 4,],
	'CActiveDataProvider'                => ['pagination' => ['pageSize' => 37]],
	'PropertyNearestTransportDistance'   => 2500,
	'PropertyTotalNearestTransports'     => 10,
	'ParkNearestTransportDistance'       => 2000,
	'ParkTotalNearestTransports'         => 2000,
	'localeventNearestTransportDistance' => 2000,
	'localeventTotalNearestTransports'   => 2000,
	'WKPDF'                              => array(
			'path' => '/bin/wkhtmltopdf',
	),
	'mandrill'                           => array(
			'login'            => 'admin@woosterstock.co.uk',
			'API_KEY'          => 'flZX-z0aZH_7CtsMTnYhOw',
			'TEST_API_KEY'     => '5H8HqHL9FGE4ETnoSYeIaw',
			'mails_in_message' => 500,
			'test_emails'      => array(
					'vitaly@woosterstock.co.uk',
					'vitalijs.suhanovs@gmail.com',
					'vitaly.suhanov@hotmail.com',
					//			'stephen@woosterstock.co.uk',
					//			'janki@woosterstock.co.uk',
					//			'selsworth.ian@gmail.com',
					//			'stephen_elsworth@yahoo.com',
					//			'Pauljohnscanll@yahoo.ie',
					//			'julia-russell@hotmail.co.uk',
					//			'aimeefancourt@btinternet.com',
			),
			//			'test_run'         => true
	),
	'twilio'                             => array(
			'sid'          => "ACd26b27c0e58a20378c524745ea53306c",
			'token'        => "19d5f3344a2f3c2c36e58f800216ebc7",
			'number'       => "441233801360",
			'test_number'  => '447719164650', // Vitaly's phone number
			'replyMessage' => 'This is an automated number, please don\'t reply. If you have any enquires, please call the office on 020 7708 6700. Thanks, Wooster & Stock'
	),
	'contactUs'                          => array(
			'general_email'  => 'cam',
			'email_hostname' => 'woosterstock.co.uk'
	),
	'mailshot'                           => array(
			'sender_email'     => 'admin@woosterstock.co.uk',
			'sender_name'      => 'Wooster & Stock',
			'price_margin_min' => .3,
			'price_margin_max' => .05,
			'alwaysSendTo'     => array(
					'robert.huntly@woosterstock.co.uk',
					'zoe.matheson@woosterstock.co.uk',
					'gemma@woosterstock.co.uk',
					'luke.bishop@woosterstock.co.uk',
					'thanh@woosterstock.co.uk',
					'zoe@woosterstock.co.uk',
					'zack.hill@woosterstock.co.uk',
					'jade@woosterstock.co.uk',
					'robert.huntly@woosterstock.co.uk',
					'ali.devlin@woosterstock.co.uk',
					'Molly@woosterstock.co.uk',
					'charlie.lester@woosterstock.co.uk',
					'nita@woosterstock.co.uk',
					'millie@woosterstock.co.uk',
					'Kathryn@woosterstock.co.uk',
			),
	),
	'blog'                               => array(
			'imagePath' => __DIR__ . "/../../images/blog",
	),
	'followUpAppointments'               => array(
			'rollOverStartingDate' => '2013-12-14',
	),
	'email'                              => array(
			'sender_email' => 'admin@woosterstock.co.uk',
			'sender_name'  => 'Wooster & Stock',
	)
);