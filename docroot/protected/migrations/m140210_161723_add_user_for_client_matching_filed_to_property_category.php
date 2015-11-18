<?php

class m140210_161723_add_user_for_client_matching_filed_to_property_category extends CDbMigration
{
	private $table = 'propertyCategory';

	private $column = 'matchClients';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0');
	}

	public function down()
	{

		$this->dropColumn($this->table, $this->column);
	}

}