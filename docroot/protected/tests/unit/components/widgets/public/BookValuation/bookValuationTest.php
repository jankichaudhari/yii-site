<?php

include_once __DIR__ . '/../../../../../bootstrap.php';
class bookValuationTest extends CTestCase
{
	public function testCreateFooter()
	{
		Yii::import('application.components.public.widgets.BookValuation.BookValuation');
		$widget = new BookValuation();

		var_dump($widget->emailText('html', 'vitalijs.suhanovs@gmail.com', 'Vitaly Suhanov'));
	}
}
