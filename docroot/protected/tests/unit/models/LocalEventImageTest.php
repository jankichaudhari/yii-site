<?php
require_once dirname(__FILE__) . '/bootstrap.php';
class LocalEventImageTest extends ImagesTest
{
    protected function getModel($scenario = 'insert'){

        return new LocalEventImage($scenario);
    }

    public function getMockRecord($type ='LocalEvent'){
        return array(
            'recordId'     => 35,
            'recordType'   => $type,
            'created'      => date('Y-m-d H:i:s'),
            'createdBy'    => 91,
            'displayOrder' => 12,
        );
    }

    public function testSaveImages()
    {
        /** $model @var LocalEventImage[ ] */
        $model = new LocalEventImage();

        $model->setAttributes($this->getMockRecord(),false);
        $model->file = $this->getMockCuploadedImage('image/jpeg',0);
        $this->assertTrue($model->validate(),"record not validated");
        $this->assertTrue($model->save(),"record not saved");

        foreach($model->imageSizes as $imageSize){
            $this->assertFileExists($model->getImageURIPath($imageSize),$imageSize . " does not exist");
        }
    }

    public function testSaveMainImage()
    {
        /** $model @var LocalEventImage[ ] */
        $model = new LocalEventImage();


        $model->setAttributes($this->getMockRecord('LocalEventMain'),false);
        $model->file = $this->getMockCuploadedImage('image/jpeg',1);

        $model->cropFactor = array(
            'width' => 1280,
            'height' => 1024,
            'cropWidth' => 800,
            'cropHeight' => 800,
            'x' => 0,
            'y' => 0,
        );

        $this->assertTrue($model->validate(),"record not validated");
        $this->assertTrue($model->save(),"record not saved");

        foreach($model->imageSizes as $imageSize){
            $this->assertFileExists($model->getImageURIPath($imageSize),$imageSize . " does not exist");
        }
    }




}