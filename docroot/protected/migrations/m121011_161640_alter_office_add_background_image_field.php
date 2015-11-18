<?php

class m121011_161640_alter_office_add_background_image_field extends CDbMigration
{
	public function up()
	{

		$this->addColumn('office', 'backgroundImage', 'string NOT NULL default ""');
	}

	public function down()
	{

		$this->dropColumn('office', 'backgroundImage');
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
