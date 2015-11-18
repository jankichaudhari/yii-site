<?php
require_once dirname(__FILE__) . '/bootstrap.php';
abstract class ImagesTest extends ActiveRecordTest
{
    abstract public function  getMockRecord();

    protected function getMockCuploadedImage($type = 'image/jpeg',$index = 0){

        $file = $this->availableFiles($index);
        $this->assertFileExists($file,"file not found");

        $fInfo = new finfo(FILEINFO_MIME_TYPE);
        $size = filesize($file);
        $name = basename($file);
        $mimeType = $fInfo->file($file);

        $this->assertEquals($type,$mimeType);

        $CUploadedFile = new MockUploadedFile($name,$file,$mimeType,$size,0);
        $this->assertInstanceOf('CUploadedFile',$CUploadedFile);

        return $CUploadedFile;
    }

    protected function availableFiles($index = 0){
        $files = array(
            Yii::getPathOfAlias('application.tests.data') . '/testImage_1280x1024.jpg',
            Yii::getPathOfAlias('application.tests.data') . '/testImage_1600x1280.jpg',
            Yii::getPathOfAlias('application.tests.data') . '/testImage_100x100.jpg',
            Yii::getPathOfAlias('application.tests.data') . '/testImage_40x40.jpg',
        );

        return $files[$index];
    }
}