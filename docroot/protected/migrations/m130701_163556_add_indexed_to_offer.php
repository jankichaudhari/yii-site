<?php

class m130701_163556_add_indexed_to_offer extends CDbMigration
{
	public function up()
	{
		$this->createIndex('off_deal', 'offer', 'off_deal');
		$this->createIndex('off_status', 'offer', 'off_status');
		$this->createIndex('off_app', 'offer', 'off_status');
	}

	public function down()
	{
		$this->dropIndex('off_deal', 'offer');
		$this->dropIndex('off_status', 'offer');
		$this->dropIndex('off_app', 'offer');
	}

}