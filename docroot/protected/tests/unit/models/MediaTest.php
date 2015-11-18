<?php
require_once dirname(__FILE__) . '/bootstrap.php';
class MediaTest extends ImagesTest
{
	protected function getModel($scenario = 'insert')
	{

		return new Media($scenario);
	}

	public function getMockRecord()
	{

		return array(
			'med_table'    => 'deal',
			'med_row'      => 82,
			'med_title'    => 'Exterior',
			'med_blurb'    => 'test',
			'med_dims'     => '',
			'med_features' => '',
			'width'        => 0,
			'height'       => 0,
			'orientation'  => 0,
		);
	}

	public function getCropFactor()
	{

		return array(
			'width'  => 1280,
			'height' => 1024,
			'w'      => 800,
			'h'      => 800,
			'x'      => 0,
			'y'      => 0,
		);
	}

	public function testSaveDeleteRecordAndAllImageSizesOfPhotos()
	{

		$this->saveAndDeleteMedia(Media::TYPE_PHOTO);
	}

	public function testSaveDeleteRecordAndAllImageSizesOfOtherMedia()
	{

		$this->saveAndDeleteMedia(Media::TYPE_EPC);
	}

	private function saveAndDeleteMedia($type)
	{

		/** @var $model Media [ ] */
		$model = new Media();

		$mockRecord  = $this->getMockRecord();
		$instruction = Deal::model()->findByPk($mockRecord['med_row']);
		$this->assertNotEmpty($instruction, "instruction not exist");
		$property = Property::model()->findByPk($instruction->dea_prop);
		$this->assertNotEmpty($property, "property not exist");
		$this->assertNotNull($property->addressId, "property has no address");
		$address = Address::model()->findByPk($property->addressId);
		$this->assertNotEmpty($address, " Address not exist");
		$model->setAttributes($this->getMockRecord());
		$model->file = $this->getMockCuploadedImage('image/jpeg', 1);
		if ($type == Media::TYPE_PHOTO) {
			$model->setCropFactor($this->getCropFactor());
		} elseif ($type == Media::TYPE_EPC || $type == Media::TYPE_FLOORPLAN) {
			$model->otherMedia = $type;
		}

		$this->assertTrue($model->validate(), "record not validated");
		$this->assertTrue($model->save(), "record not saved");

		foreach ($model->getImageSizes() as $imageSize) {
			$this->assertFileExists($model->getFullPath($imageSize), $imageSize . " does not exist");
		}

		$this->deleteMedia($model->med_id);

	}

	private function deleteMedia($id)
	{

		/** @var $model Media [ ] */
		$model = Media::model()->findByPk($id);
		$this->assertNotEmpty($model, " media record does not exist");
		$this->assertTrue($model->delete(), " record not deleted");
		foreach ($model->getImageSizes() as $imageSize) {
			$this->assertEquals(false, $model->getFullPath($imageSize), $imageSize . " is not deleted");
		}
	}

}