<?php
/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in test cases
 *
 *
 * The base class for functional test cases.
 * In this class, we set the base URL for the test application.
 * We also provide some common methods to be used by concrete test classes.
 */
include_once __DIR__ . '/Selenium2WebTestCase.php';
class WebTestCase2 extends Selenium2WebTestCase
{

	/**
	 * Sets up before each test method runs.
	 * This mainly sets the base URL for the test application.
	 */
	protected function setUp()
	{

		parent::setUp();
//		$this->setBrowser('phantomjs');
		$this->setBrowser('firefox');
		$this->setBrowserUrl('https://' . Yii::app()->params['hostname'] . '/');

	}

	protected function tearDown()
	{

		if (file_exists(Yii::app()->params['tmpDirPath'] . '/testrun')) {
			unlink(Yii::app()->params['tmpDirPath'] . '/testrun');
		}
	}

	public function testrun()
	{
		touch(Yii::app()->params['tmpDirPath'] . '/testrun');
	}
}
