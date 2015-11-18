<?php

class m130920_150019_populate_plain_number_reversed extends CDbMigration
{
	public function up()
	{
		$sql = "UPDATE tel SET plainNumberReversed = REVERSE(plainNumber)";
		Yii::app()->db->createCommand($sql)->execute();

	}

	public function down()
	{
		$sql = "UPDATE tel SET plainNumberReversed = ''";
		Yii::app()->db->createCommand($sql)->execute();
	}

}