<?php

require_once dirname(__FILE__) . '/bootstrap.php';
abstract class ImageTest extends ActiveRecordTest
{
	const TEST_RECORD_ID         = 9999;

	public function testGetResizeSizesReturnsArray()
	{

		/** @var $model application\models\Place\Image */
		$model = $this->getModel();
		$this->assertNotNull($model->getResizeSizes(), 'resizeSize array must be overriden in ' . get_class($this));
		$this->assertTrue(is_array($model->getResizeSizes()), 'resizeSize  must overriden as array in ' . get_class($this));
	}

	public function testRecordDoesnSaveIfFileDoesntExist()
	{

		$model = $this->getModel();
		$model->setAttributes($this->getMockRecordWithNonExistingFileName(), false);
		$this->assertFalse($model->insert(), 'Record inserted when it should fail because of non existing file');
	}

	/**
	 * @return array
	 */
	abstract public function getMockRecord();

	abstract protected function getMockRecordWithNonExistingFileName();

	/**
	 * @return string
	 */
	public function getFolderPath()
	{

		return Yii::app()->params['imgPath'] . '/Place/' . static::TEST_RECORD_ID;
	}
}
