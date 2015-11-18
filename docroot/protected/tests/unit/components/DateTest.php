<?php
include_once __DIR__ . '/../../bootstrap.php';

class DateTest extends CTestCase
{
	public function testParseDate()
	{
		$this->assertEquals('2014-01-01', Date::parseDate('2014-01-01'));
		$this->assertEquals('2014-03-02', Date::parseDate('2014-03-02'));
		$this->assertEquals('2018-02-15', Date::parseDate('15/02/2018'));
		$this->assertEquals('2018-12-01', Date::parseDate('01/12/2018'));
	}
}
