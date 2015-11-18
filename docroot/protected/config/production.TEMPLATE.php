<?php
return CMap::mergeArray(require("main.php"), array(
												  // application components
												  //'preload'   => array('log'),
												  'components'=> array(
													  'db'          => array(
														  'connectionString' => 'mysql:host=localhost;dbname=wsv3_live',
														  'emulatePrepare'   => true,
														  'username'         => 'ws_website',
														  'password'         => 'ws_website',
														  'charset'          => 'utf8',
													  ),
												  ),
											 ));
