<?php

class m130211_122018_copy_latitude_and_longitude_to_address extends CDbMigration
{
	public function up()
	{

		$sql  = "SELECT a.id, p.pro_longitude lng, p.pro_latitude lat, pro_pcid
		FROM address a INNER JOIN property p ON p.addressId = a.id
		WHERE a.postcodeAnywhereID = 0 AND p.pro_pcid > 0";
		$data = Yii::app()->db->createCommand($sql)->queryAll();

		$command = "UPDATE address SET lat = :lat, lng=:lng, postcodeAnywhereID = :pro_pcid WHERE id=:id";
		$command = Yii::app()->db->createCommand($command);
		foreach ($data as $key => $value) {

			$command->execute($value);
		}

	}

	public function down()
	{

		return true;
	}
}