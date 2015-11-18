<?php

class m130808_133219_populate_feed_fields_in_deal extends CDbMigration
{
	public function up()
	{
		$sql     = "SELECT
			d.dea_id id,
			a.line1 line1,
			a.line3 line2,
			a.line2 line3,
			a.line4 line4,
			a.line5 city
		FROM deal d
			INNER JOIN property p
				ON p.pro_id = d.dea_prop
			INNER JOIN address a
				ON p.addressId = a.id";
		$data    = Yii::app()->db->createCommand($sql)->queryAll();
		$command = Yii::app()->db->createCommand("UPDATE deal SET
			feed_line1 = :line1,
			feed_line2 = :line2,
			feed_line3 = :line3,
			feed_line4 = :line4,
			feed_city = :city
		WHERE dea_id = :id");
		foreach ($data as $key => $value) {
			$command->execute($value);
		}

	}

	public function down()
	{
		echo "m130808_133219_populate_feed_fields_in_deal does not support migration down.\n";
		return false;
	}
}