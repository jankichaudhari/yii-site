<?php
include_once dirname(__FILE__) . '/bootstrap.php';

class AppointmentTest extends ActiveRecordTest
{

	/**
	 * @param string $scenario
	 * @return Appointment
	 */
	protected function getModel($scenario = 'insert')
	{
		return new Appointment($scenario);
	}

//	public function testAppDate(){
//
//		$model = Appointment::model()->findByPk(1);
//
//		$model->app_start = '2013-09-16 00:00:00';
//		$model->app_end = '2013-09-15 00:00:00';
//
//		$this->assertFalse($model->save(),"appointment start date can not be more than end date");
//
//		$model->app_start = '2013-09-15 00:00:00';
//		$model->app_end = '2013-09-16 00:00:00';
//
//		$this->assertTrue($model->save(),"appointment start date can not be more than end date");
//
//		$model->app_start = '2013-09-15 00:00:00';
//		$model->app_end = '2013-09-15 00:00:00';
//
//		$this->assertFalse($model->save(),"appointment end date must be more than start date");
//	}
//
//	public function testAppStatus(){
//
//		$model = $this->getModel();
//		$model->app_status = "";
//		$this->assertFalse($model->save(),"Application status can not be empty");
//	}
}
