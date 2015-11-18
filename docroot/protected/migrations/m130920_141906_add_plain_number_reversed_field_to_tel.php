<?php

class m130920_141906_add_plain_number_reversed_field_to_tel extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tel', 'plainNumberReversed', 'string NOT NULL default ""');
	}

	public function down()
	{
		$this->dropColumn('tel', 'plainNumberReversed');
	}

}