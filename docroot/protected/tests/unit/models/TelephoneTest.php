<?php
include_once dirname(__FILE__) . '/bootstrap.php';
class TelephoneTest extends CDbTestCase
{
	protected function setUp()
	{

		parent::setUp();
		$sql = 'DELETE FROM tel WHERE tel_cli = 9999 OR tel_con = 9999 OR tel_com = 9999';
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function testEveryPhoneGetsOrderNumByDefault()
	{

		$phone             = new Telephone();
		$phone->tel_cli    = 9999;
		$phone->tel_number = 1234567890;
		$phone->save();

		$this->assertEquals(0, $phone->tel_ord, 'Firs check failed');

		$phone             = new Telephone();
		$phone->tel_cli    = 9999;
		$phone->tel_number = 1234567890;
		$phone->save();
		$this->assertEquals(1, $phone->tel_ord, 'second check failed');

		$phone             = new Telephone();
		$phone->tel_con    = 9999;
		$phone->tel_number = 1234567890;
		$phone->save();
		$this->assertEquals(0, $phone->tel_ord, 'first Contacts phones order is not 0');

		$phone             = new Telephone();
		$phone->tel_con    = 9999;
		$phone->tel_number = 1234567890;
		$phone->save();
		$this->assertEquals(1, $phone->tel_ord, 'Second Contacts phones order is not 1');

		$phone             = new Telephone();
		$phone->tel_com    = 9999;
		$phone->tel_number = 1234567890;
		$phone->save();
		$this->assertEquals(0, $phone->tel_ord, 'first Companies phones order is not 0');

		$phone             = new Telephone();
		$phone->tel_com    = 9999;
		$phone->tel_number = 1234567890;
		$phone->save();
		$this->assertEquals(1, $phone->tel_ord, 'Second Contacts phones order is not 1');
	}

	public function testBeforeSaveThrowsExceptionIfPhoneDoesnotBelongToAnyRecord()
	{

		$this->setExpectedException('CDbException');
		$phone             = new Telephone();
		$phone->tel_number = 1234567890;
		$phone->save();
	}

	public function testNewPhoneSavesOrdNumIFItIsSetExternaly()
	{

		$phone             = new Telephone();
		$phone->tel_cli    = 9999;
		$phone->tel_number = 1234567890;
		$phone->tel_ord    = 1000;
		$phone->save();

		$this->assertEquals(1000, $phone->tel_ord, 'New phone doesnt save its order num');
	}

}