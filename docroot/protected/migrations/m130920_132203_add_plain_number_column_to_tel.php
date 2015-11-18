<?php

class m130920_132203_add_plain_number_column_to_tel extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tel', 'plainNumber', 'string NOT NULL default ""');
	}

	public function down()
	{
		$this->dropColumn('tel', 'plainNumber');
	}
}