<?php
include_once dirname(__FILE__) . '/bootstrap.php';
class ClientTest extends ActiveRecordTest
{

	/**
	 * @param string $scenario
	 * @return CActiveRecord
	 */
	protected function getModel($scenario = 'insert')
	{
		return new Client($scenario);
	}
	
}