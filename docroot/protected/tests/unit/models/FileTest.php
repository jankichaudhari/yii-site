<?php
include dirname(__FILE__) . '/bootstrap.php';
class FileTest extends ActiveRecordTest
{

	/**
	 * @param string $scenario
	 * @return CActiveRecord
	 */
	protected function getModel($scenario = 'insert')
	{

		return new File($scenario);
	}

}
