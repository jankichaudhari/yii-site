<?php

class m130611_144842_create_table_userConfig extends CDbMigration
{
	private $tableName = "userConfig";

	public function up()
	{

		return $this->createTable($this->tableName, [
													'id'          => 'pk',
													'userId'      => 'int',
													'configType'  => 'string',
													'configKey'   => 'string',
													'configValue' => 'string'
													]);
	}

	public function down()
	{

		return $this->dropTable($this->tableName);
	}
}