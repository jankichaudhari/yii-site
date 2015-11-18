<?php

class m131021_131619_add_created_created_by_fields_to_mandrill_mailshot extends CDbMigration
{
	private $table = 'mandrillMailshot';

	public function up()
	{
		$this->addColumn($this->table, 'created', 'datetime not null');
		$this->addColumn($this->table, 'createdBy', 'int unsigned not null');
	}

	public function down()
	{
		$this->dropColumn($this->table, 'created');
		$this->dropColumn($this->table, 'createdBy');
	}

}