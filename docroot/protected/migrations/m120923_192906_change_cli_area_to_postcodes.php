<?php

class m120923_192906_change_cli_area_to_postcodes extends CDbMigration
{
	public function up()
	{

		/** @var $propertyAreas PropertyArea[] */
		$propertyAreas   = PropertyArea::model()->findAll();
		$mapIdToPostcode = [];
		foreach ($propertyAreas as $value) {
			$mapIdToPostcode[$value->are_id] = $value->are_postcode;
		}

		$sql  = "SELECT * FROM client where cli_area !=''";
		$data = Yii::app()->db->createCommand($sql)->queryAll();
		$sql = "UPDATE client SET cli_area = ? WHERE cli_id = ?";
		$command = Yii::app()->db->createCommand($sql);
		foreach ($data as $value) {
			$areas    = explode('|', $value['cli_area']);
			$newAreas = [];
			foreach ($areas as $area) {
				if (isset($mapIdToPostcode[$area])) {
					$newAreas[$mapIdToPostcode[$area]] = $mapIdToPostcode[$area];
				}
			}
			$command->execute(array(implode('|', $newAreas), $value['cli_id']));
		}

	}

	public function down()
	{
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}