<?php

require_once dirname(__FILE__) . '/../bootstrap.php';
class GalleryImageTest extends ImageTest
{
	const TEST_RECORD_ID         = 99999;
	const TEST_IMAGE_NAME        = 'TestGalleryImg_1280x900.jpg';
	const TEST_IMAGE_MEDIUM_NAME = 'TestGalleryImg_1280x900_medium.jpg';
	const TEST_IMAGE_SMALL_NAME  = 'TestGalleryImg_1280x900_small.jpg';
	const TEST_IMAGE_THUMB_NAME  = 'TestGalleryImg_1280x900_thumb.jpg';

	/**
	 * @param string $scenario
	 * @return \application\models\Place\GalleryImage|\CActiveRecord
	 */
	protected function getModel($scenario = 'insert')
	{

		return new application\models\Place\GalleryImage($scenario);
	}

	public function testAfterFindPopulatesNames()
	{

		$model = $this->getModel();
		$model->setAttributes($this->getMockRecord(), false);
		$this->assertTrue($model->insert(), 'cannot insert GalleryImage record ' . print_r($model->getErrors(), true));
		/** @var $model application\models\Place\GalleryImage */
		$model = application\models\Place\GalleryImage::model()->findByPk($model->id);
		$this->assertInstanceOf('application\models\Place\GalleryImage', $model);

		$this->assertNotNull($model->thumbName);
		$this->assertNotNull($model->smallName);
		$this->assertNotNull($model->mediumName);

		$this->assertEquals(self::TEST_IMAGE_THUMB_NAME, $model->thumbName);
		$this->assertEquals(self::TEST_IMAGE_SMALL_NAME, $model->smallName);
		$this->assertEquals(self::TEST_IMAGE_MEDIUM_NAME, $model->mediumName);

	}

	public function setUp()
	{

		$pathToImages = $this->getFolderPath();
		FileSystem::createDirectory($pathToImages);
		if (file_exists($pathToImages . '/' . self::TEST_IMAGE_THUMB_NAME)) {
			$this->assertTrue(unlink($pathToImages . '/' . self::TEST_IMAGE_THUMB_NAME));
		}

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

	public function testAfterSaveResizedImagesAreCreated()
	{

		$pathToImages = $this->getFolderPath();

		$model = $this->getModel();
		$model->setAttributes($this->getMockRecord(), false);
		$this->assertTrue($model->save(), 'Cannot save GalleryImage ' . print_r($model->getErrors(), true));
		$this->assertFileExists($pathToImages . '/' . self::TEST_IMAGE_THUMB_NAME, 'thumb image does not exist ' . print_r($model->getErrors(), true));
		$this->assertFileExists($pathToImages . '/' . self::TEST_IMAGE_SMALL_NAME, 'small image does not exist ' . print_r($model->getErrors(), true));
		$this->assertFileExists($pathToImages . '/' . self::TEST_IMAGE_MEDIUM_NAME, 'medium image does not exist ' . print_r($model->getErrors(), true));

		$resizeSizes = $model->getResizeSizes();

		$thumbFileImageData  = getimagesize($pathToImages . '/' . self::TEST_IMAGE_THUMB_NAME);
		$smallFileImageData  = getimagesize($pathToImages . '/' . self::TEST_IMAGE_SMALL_NAME);
		$mediumFileImageData = getimagesize($pathToImages . '/' . self::TEST_IMAGE_MEDIUM_NAME);
		$this->assertEquals($resizeSizes['_thumb']['w'], $thumbFileImageData[0], 'width of resized thumb is not equal to defined width in GalleryImage');
		$this->assertEquals($resizeSizes['_thumb']['h'], $thumbFileImageData[1], 'height of resized thumb is not equal to defined height in GalleryImage');
		$this->assertEquals(round(($resizeSizes['_small']['h'] / 900) * 1280), $smallFileImageData[0], 'width of resized small image is not equal to calculated width (to save aspect ratio) in GalleryImage');
		$this->assertEquals($resizeSizes['_small']['h'], $smallFileImageData[1], 'height of resized small image is not equal to defined height in GalleryImage');
		$this->assertEquals($resizeSizes['_medium']['w'], $mediumFileImageData[0], 'width of resized medium is not equal to defined width in GalleryImage');

		/**
		 * not clear what is actual size.
		 *height of medium image is not strict and may vary +/- 1 (Mathematical error)
		 */
		$this->assertGreaterThanOrEqual($resizeSizes['_medium']['h'] - 1, $mediumFileImageData[1], 'height of resized medium is not equal to defined height in GalleryImage');
		$this->assertLessThanOrEqual($resizeSizes['_medium']['h'] + 1, $mediumFileImageData[1], 'height of resized medium is not equal to defined height in GalleryImage');

	}



	/**
	 * @return array
	 */
	public function getMockRecord()
	{

		return array(
			'recordId'     => self::TEST_RECORD_ID,
			'recordType'   => \application\models\Place\GalleryImage::RECORD_TYPE,
			'name'         => self::TEST_IMAGE_NAME,
			'realName'     => self::TEST_IMAGE_NAME,
			'mimeType'     => 'image/jpeg',
			'fullPath'     => $this->getFolderPath(),
			'created'      => '2012-09-06 20:00:15',
			'createdBy'    => 49,
			'info'         => '',
			'caption'      => null,
			'displayOrder' => 12,
		);
	}

	protected function getMockRecordWithNonExistingFileName()
	{

		return array(
			'recordId'     => self::TEST_RECORD_ID,
			'recordType'   => \application\models\Place\GalleryImage::RECORD_TYPE,
			'name'         => 'nonExitingFileName.jpg',
			'realName'     => 'nonExitingFileName.jpg',
			'mimeType'     => 'image/jpeg',
			'fullPath'     => $this->getFolderPath(),
			'created'      => '2012-09-06 20:00:15',
			'createdBy'    => 49,
			'info'         => '',
			'caption'      => null,
			'displayOrder' => 12,
		);
	}
}
