<?php
require_once dirname(__FILE__) . '/../bootstrap.php';
class MainGalleryImageTest extends ImageTest
{
	const TEST_IMAGE_NAME        = 'TestGalleryImg_1280x900.jpg';
	const TEST_IMAGE_MEDIUM_NAME = 'TestGalleryImg_1280x900_medium.jpg';
	const TEST_IMAGE_SMALL_NAME  = 'TestGalleryImg_1280x900_small.jpg';

	/**
	 * @param string $scenario
	 * @return \application\models\Place\MainGalleryImage|\CActiveRecord
	 */
	protected function getModel($scenario = 'insert')
	{

		return new application\models\Place\MainGalleryImage($scenario);
	}

	public function testAfterFindPopulatesNames()
	{

		$model = $this->getModel();
		$model->setAttributes($this->getMockRecord(), false);
		$this->assertTrue($model->insert());
		$this->assertNotNull($model->getSizes(), 'Sizes array is not set');
		/** @var $model application\models\Place\MainGalleryImage */
		$model = application\models\Place\MainGalleryImage::model()->findByPk($model->id);
		$this->assertInstanceOf('application\models\Place\MainGalleryImage', $model);

		$this->assertNotNull($model->smallName);
		$this->assertNotNull($model->mediumName);

		$this->assertEquals(self::TEST_IMAGE_SMALL_NAME, $model->smallName);
		$this->assertEquals(self::TEST_IMAGE_MEDIUM_NAME, $model->mediumName);

	}

	public function setUp()
	{

		$pathToImages = $this->getFolderPath();
		FileSystem::createDirectory($pathToImages);

		if (file_exists($pathToImages . '/' . self::TEST_IMAGE_SMALL_NAME)) {
			$this->assertTrue(unlink($pathToImages . '/' . self::TEST_IMAGE_SMALL_NAME));
		}

		if (file_exists($pathToImages . '/' . self::TEST_IMAGE_MEDIUM_NAME)) {
			$this->assertTrue(unlink($pathToImages . '/' . self::TEST_IMAGE_MEDIUM_NAME));
		}

		$testImgName = self::TEST_IMAGE_NAME;
		$source      = Yii::getPathOfAlias('application.tests.data') . '/' . $testImgName;
		$dest        = $pathToImages . '/' . $testImgName;
		$this->assertTrue(copy($source, $dest), 'cannot copy ' . $source . ' to ' . $dest);
	}

	/**
	 * @return array
	 */
	public function getMockRecord()
	{

		return array( // row #452
			'recordId'     => self::TEST_RECORD_ID,
			'recordType'   => \application\models\Place\MainViewImage::RECORD_TYPE,
			'name'         => self::TEST_IMAGE_NAME,
			'realName'     => self::TEST_IMAGE_NAME,
			'mimeType'     => 'image/jpeg',
			'fullPath'     => $this->getFolderPath(),
			'created'      => '2012-09-07 00:54:11',
			'createdBy'    => 49,
			'info'         => '',
			'caption'      => null,
			'displayOrder' => null,
		);
	}

	protected function getMockRecordWithNonExistingFileName()
	{

		return array( // row #452
			'recordId'     => self::TEST_RECORD_ID,
			'recordType'   => \application\models\Place\MainGalleryImage::RECORD_TYPE,
			'name'         => 'NotExisitngFile.jpg',
			'realName'     => 'NotExisitngFile.jpg',
			'mimeType'     => 'image/jpeg',
			'fullPath'     => $this->getFolderPath(),
			'created'      => '2012-09-07 00:54:11',
			'createdBy'    => 49,
			'info'         => '',
			'caption'      => null,
			'displayOrder' => null,
		);
	}

	public function getFolderPath()
	{

		return parent::getFolderPath() . '/' . \application\models\Place\MainGalleryImage::RECORD_TYPE;
	}
}
