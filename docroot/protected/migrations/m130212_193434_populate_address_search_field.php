<?php

class m130212_193434_populate_address_search_field extends CDbMigration
{
	public function up()
	{
		$sql = "UPDATE address SET searchString = TRIM(concat_ws(' ', line1, line2, line3, line4, line5, postcode))";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function down()
	{
		return true;
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