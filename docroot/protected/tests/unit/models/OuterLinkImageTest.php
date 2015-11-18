<?php
include_once dirname(__FILE__) . '/bootstrap.php';
class OuterLinkImageTest extends ActiveRecordTest
{

	/**
	 * @param string $scenario
	 * @return OuterLinkImage
	 */
	protected function getModel($scenario = 'insert')
	{

		return new OuterLinkImage($scenario);
	}

	public function testModelSavesFileOnSave()
	{

		$file     = $this->getUploadedJpeg();
		$recordId = 1;
		$savePath = Yii::app()->params['imgPath'] . '/' . OuterLinkImage::RECORD_TYPE . '/' . $recordId;

		$outerLinkImage           = new OuterLinkImage();
		$outerLinkImage->recordId = $recordId;
		$outerLinkImage->file     = $file;

		$this->assertTrue($outerLinkImage->validate(), 'cannot validate outerLinkImage');
		$this->assertTrue($outerLinkImage->save(), 'cannot save outerLinkImage');
		$this->assertFileExists($savePath);
		$this->assertFileExists($savePath . '/' . basename($file));
		$this->assertCount(1, glob($savePath . '/*'));
	}

	public function testOnlyOneFileIsSavedPerRecordId()
	{

		$file1    = $this->getUploadedJpeg();
		$file2    = $this->getUploadedJpeg(1);
		$recordId = 1;
		$savePath = Yii::app()->params['imgPath'] . '/' . OuterLinkImage::RECORD_TYPE . '/' . $recordId;

		$outerLinkImage           = new OuterLinkImage();
		$outerLinkImage->recordId = $recordId;
		$outerLinkImage->file     = $file1;
		$this->assertTrue($outerLinkImage->validate(), 'cannot validate outerLinkImage');
		$this->assertTrue($outerLinkImage->save(), 'cannot save outerLinkImage');
		$this->assertFileExists($savePath);
		$this->assertFileExists($savePath . '/' . basename($file1));
		$this->assertCount(1, glob($savePath . '/*'));

		$outerLinkImage           = new OuterLinkImage();
		$outerLinkImage->recordId = $recordId;
		$outerLinkImage->file     = $file2;
		$this->assertTrue($outerLinkImage->validate(), 'cannot validate outerLinkImage');
		$this->assertTrue($outerLinkImage->save(), 'cannot save outerLinkImage');
		$this->assertFileExists($savePath);
		$this->assertFileExists($savePath . '/' . basename($file2));
		$this->assertCount(1, glob($savePath . '/*'));

	}

	public function testFileIsRequiredOnInsert()
	{

		$model = $this->getModel('insert');
		$this->assertFalse($model->validate(['file']));
	}

	public function testFileIsNotRequiredOnUpdate()
	{

		$model = $this->getModel('update');
		$this->assertTrue($model->validate(['file']));
	}

	private function getUploadedJpeg($index = 0)
	{

		$files = array(
			Yii::getPathOfAlias('application.tests.data') . '/testImage_40x40.jpg',
			Yii::getPathOfAlias('application.tests.data') . '/testImage_100x100.jpg',
		);
		$path  = $files[$index];

		$this->assertFileExists($path, 'Something went terrebly wrong! Test file does not exist!');
		$finfo    = new finfo(FILEINFO_MIME_TYPE);
		$size     = filesize($path);
		$name     = basename($path);
		$mimeType = $finfo->file($path);
		$this->assertEquals('image/jpeg', $mimeType);
		$file = new MockUploadedFile($name, $path, $mimeType, $size, 0);
		$this->assertInstanceOf('CUploadedFile', $file);
		return $file;
	}
}
