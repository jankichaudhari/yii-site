<?php

include_once dirname(__FILE__) . '/bootstrap.php';
class OuterLinkTest extends ActiveRecordTest
{

	/**
	 * @param string $scenario
	 * @return CActiveRecord
	 */
	protected function getModel($scenario = 'insert')
	{

		return new OuterLink($scenario);
	}

	public function testTitleDoesNotValidateIfSetToNull()
	{

		$link        = new OuterLink();
		$link->title = null;
		$this->assertFalse($link->validate(['title']));

	}

	public function testTitleValidateIfSet()
	{

		$link        = new OuterLink();
		$link->title = 'Some title';
		$this->assertTrue($link->validate(['title']), print_r($link->getErrors('title'), true));

	}

	public function testLinkDoesNotValidateIfSetToNull()
	{

		$link       = new OuterLink();
		$link->link = null;
		$this->assertFalse($link->validate(['link']));

	}

	public function testLinkDoesNotValidateIfSetNotToCorrectURL()
	{

		$link       = new OuterLink();
		$link->link = 'string that is not url';
		$this->assertFalse($link->validate(['link']));

	}

	public function testLinkValidateIfSetToCorrectUrl()
	{

		$link       = new OuterLink();
		$link->link = 'http://example.com/?ref=ref';
		$this->assertTrue($link->validate(['link']), print_r($link->getErrors('link'), true));

	}
}
