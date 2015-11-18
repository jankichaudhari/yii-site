<?php
include_once dirname(__FILE__) . '/bootstrap.php';

class PropertyTest extends ActiveRecordTest
{

	/** @var Property */
	private $model;

	public $fixtures = array(
		'address'  => 'Address',
		'property' => 'Property',
		'client'   => 'Client',
	);

	public function setUp()
	{

		$this->model = $this->getModel();
	}

	public function testTenureMayBeNotSet()
	{

		$this->model->pro_tenure = null;
		$this->assertTrue($this->model->validate(['pro_tenure']), "pro_tenure does not validate when empty (set to null)");
	}

	public function testTenureMustBeInAListIfSet()
	{

		foreach (Property::getTenureTypes() as $tenureType) {
			$this->model->pro_tenure = $tenureType;
			$this->assertTrue($this->model->validate(['pro_tenure']), "pro_tenure [value: " . $tenureType . "] is not validated when in list");
		}

		$this->model->pro_tenure = md5("STRING THAT WILL BE INCORRECT");
		$this->assertFalse($this->model->validate(['pro_tenure']), "pro tenure validated with incorrect value");

	}

	public function testSearch()
	{

		parent::testSearch();

		$model               = $this->getModel('search');
		$model->pro_postcode = 'M1 1AA';
		$data                = $model->search()->getData();
		$this->assertCount(1, $data, 'Property::search found many records for "M1 1AA"');

		$model                    = $this->getModel('search');
		$model->fullAddressString = 'Flat Number 1';
		$data                     = $model->search()->getData();
		$this->assertCount(1, $data, 'Property::search found many records for "Flat Number 1"');

//		$model                    = $this->getModel('search');
//		$model->fullAddressString = 'Flat Number';
//		$data                     = $model->search()->getData();
//		$this->assertCount(4, $data, 'Property::search found many records for "Flat Number"');

		$model              = $this->getModel('search');
		$model->pro_bedroom = 1;
		$data               = $model->search()->getData();
		$this->assertCount(1, $data, 'Property::search found many records for number of bedrooms = 1');

		$model                = $this->getModel('search');
		$model->pro_reception = 1;
		$data                 = $model->search()->getData();
		$this->assertCount(1, $data, 'Property::search found many records for number of reception = 1');

	}

	public function testSetClientsThrowsExceptionWhenClientTypeIsIncorrect()
	{

		$model = $this->getModel('search');
		$this->setExpectedException('InvalidArgumentException');
		$model->setClients([1, 2], 'Unknown Type');
	}

	public function testSetClientsPopulatesClientsRelation()
	{

		$model = $this->getModel('search');
		$model->setClients([1, 2], Property::CLIENT_TYPE_TENANT);
		$this->assertTrue(is_array($model->tenants));
		$this->assertCount(2, $model->tenants);
	}

	public function testSetClientsSavesClientsAfterModelIsSaved()
	{

		$model = $this->getModel('search');
		$model->setClients([1, 2], Property::CLIENT_TYPE_TENANT);
		$this->assertTrue(is_array($model->tenants));

		$model->save(false);

		$model = Property::model()->findByPk($model->pro_id);
		$this->assertTrue(is_array($model->tenants));
		$this->assertCount(2, $model->tenants);
	}

	/**
	 * @param string $scenario
	 * @return Property
	 */
	protected function getModel($scenario = 'insert')
	{

		return new Property($scenario);
	}
}
