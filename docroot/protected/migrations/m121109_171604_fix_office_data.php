<?php

class m121109_171604_fix_office_data extends CDbMigration
{
	public function up()
	{

		/** @var $models Office[] */
		$models = Office::model()->findAll();

		foreach ($models as $model) {
			switch ($model->shortTitle) {
				case 'Camberwell' :
					$this->fixCamberwell($model);
					break;
				case 'Brixton' :
					$this->fixBrixton($model);
					break;
				case 'Sydenham' :
					$this->fixSydenham($model);
					break;
				case 'Property Management' :
					$this->fixManagement($model);
					break;
			}
			$model->address->line1 .= ' ' . $model->address->line2;
			$model->save(false);
			$model->address->save(false);
		}
	}

	public function down()
	{

		// migrate may go down; it only changes data
		return true;
	}

	private function fixCamberwell(Office $model)
	{

		$model->email          = 'cam@woosterstock.co.uk';
		$model->clientMatching = 1;
	}

	private function fixBrixton(Office $model)
	{

		$model->email          = 'brx@woosterstock.co.uk';
		$model->clientMatching = 1;
	}

	private function fixSydenham(Office $model)
	{

		$model->email          = 'syd@woosterstock.co.uk';
		$model->clientMatching = 1;
	}

	private function fixManagement(Office $model)
	{

		$cam              = Office::model()->findByAttributes(array('code' => 'CAM'));
		$model->email     = 'cam@woosterstock.co.uk';
		$model->address   = $cam->address;
		$model->addressId = $cam->addressId;
	}

}
