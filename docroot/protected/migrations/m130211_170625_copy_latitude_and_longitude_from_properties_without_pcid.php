<?php

class m130211_170625_copy_latitude_and_longitude_from_properties_without_pcid extends CDbMigration
{
	public function up()
	{

		$sql  = "SELECT a.id, p.pro_longitude lng, p.pro_latitude lat
		FROM address a INNER JOIN property p ON p.addressId = a.id
		WHERE a.lat is null AND p.pro_latitude <> -1";
		$data = Yii::app()->db->createCommand($sql)->queryAll();

		$command = "UPDATE address SET lat = :lat, lng=:lng WHERE id=:id";
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