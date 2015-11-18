<?php

class m120719_091839_cleanup_client_table_remove_columns extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('client', 'cli_vendor');
		$this->dropColumn('client', 'cli_landlord');
		$this->dropColumn('client', 'cli_password');
		$this->dropColumn('client', 'cli_question');
		$this->dropColumn('client', 'cli_answer');
	}

	public function down()
	{
		echo "m120719_091839_cleanup_client_table_remove_columns does not support migration down.\n";
		return false;
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