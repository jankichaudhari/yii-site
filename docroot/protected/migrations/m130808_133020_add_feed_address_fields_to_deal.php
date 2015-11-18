<?php

class m130808_133020_add_feed_address_fields_to_deal extends CDbMigration
{
	public function up()
	{
		$this->addColumn('deal', 'feed_line1', 'string');
		$this->addColumn('deal', 'feed_line2', 'string');
		$this->addColumn('deal', 'feed_line3', 'string');
		$this->addColumn('deal', 'feed_line4', 'string');
		$this->addColumn('deal', 'feed_city', 'string');
	}

	public function down()
	{
		$this->dropColumn('deal', 'feed_line1');
		$this->dropColumn('deal', 'feed_line2');
		$this->dropColumn('deal', 'feed_line3');
		$this->dropColumn('deal', 'feed_line4');
		$this->dropColumn('deal', 'feed_city');
	}
}