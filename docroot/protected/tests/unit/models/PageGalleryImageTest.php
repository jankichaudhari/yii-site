<?php
require_once dirname(__FILE__) . '/bootstrap.php';
class PageGalleryTest extends ImagesTest
{
    protected function getModel($scenario = 'insert'){
        return new PageGalleryImage($scenario);
    }

    public function getMockRecord()
    {
        return array(
            'recordId'     => 1,
            'recordType'   => 'PageGalleryImage',
            'created'      => date('Y-m-d H:i:s'),
            'createdBy'    => 91,
            'info'         => '',
            'caption'      => null,
            'displayOrder' => 12,
        );
    }

    public function testSaveImages()
    {
        $model = new PageGalleryImage();

        $model->setAttributes($this->getMockRecord(),false);
        $model->file = $this->getMockCuploadedImage('image/jpeg',1);
        $this->assertTrue($model->validate(),"Record not validated");
        $this->assertTrue($model->save(),"Record not saved");

        foreach($model->imageSizes as $imageSize){
            $this->assertFileExists($model->getImageURIPath($imageSize),$imageSize . " does not exist");
        }
    }
}