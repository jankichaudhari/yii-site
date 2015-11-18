<?php

class m130920_150607_add_keys_to_tel extends CDbMigration
{
	public function up()
	{
		$this->createIndex('plainNumber', 'tel', 'plainNumber');
		$this->createIndex('plainNumberReversed', 'tel', 'plainNumberReversed');
	}

	public function down()
	{
		$this->dropIndex('plainNumber', 'tel');
		$this->dropIndex('plainNumberReversed', 'tel');
	}
}