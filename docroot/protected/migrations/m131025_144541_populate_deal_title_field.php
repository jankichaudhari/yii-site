<?php

class m131025_144541_populate_deal_title_field extends CDbMigration
{
	public function up()
	{
		$sql = "SELECT
			d.dea_id, p.pro_id, a.*
		FROM deal d INNER JOIN property p
				ON p.pro_id = d.dea_prop
			INNER JOIN address a ON p.addressId = a.id";

		$data = Yii::app()->db->createCommand($sql)->queryAll();

		$command = "UPDATE deal SET title = :title WHERE dea_id = :id";
		$command = Yii::app()->db->createCommand($command);

		foreach ($data as $key => $value) {
			$postcode = explode(' ', $value['postcode'])[0];
			$title    = $value['line3'] . ', ' . $postcode;

			$command->execute(['title' => $title, 'id' => $value['dea_id']]);
		}

	}

	public function down()
	{
		Yii::app()->db->createCommand('UPDATE deal SET title = ""')->execute();
	}
}