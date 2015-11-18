<?php

class m130920_132310_populate_plainNumber_column extends CDbMigration
{
	public function up()
	{
		$sql     = "SELECT tel_id, tel_number FROM tel";
		$data    = Yii::app()->db->createCommand($sql)->queryAll();
		$command = "UPDATE tel SET plainNumber = :num WHERE tel_id = :id";
		$command = Yii::app()->db->createCommand($command);
		foreach ($data as $key => $value) {
			$plainNumber = preg_replace('/[^0-9+]/', '', $value['tel_number']);
			$command->execute(['id' => $value['tel_id'], 'num' => $plainNumber]);
		}

	}

	public function down()
	{
		$sql = "UPDATE tel SET plainNumber = ''";
		Yii::app()->db->createCommand($sql)->execute();
	}

}