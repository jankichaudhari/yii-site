<?php

class m131204_161340_add_index_on_invalid_email_field extends CDbMigration
{
	private $index = 'invalidEmail';

	private $table = 'client';

	public function up()
	{
		$this->createIndex($this->index, $this->table, 'invalidEmail');
	}

	public function down()
	{
		$this->dropIndex($this->index, $this->table);
	}
}