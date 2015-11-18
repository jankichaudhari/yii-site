<?php

class m120620_131917_rename_dea2app_to_link_deal_to_appointment extends CDbMigration
{
	public function up()
	{
		$this->renameTable('dea2app', 'link_deal_to_appointment');
	}

	public function down()
	{
		$this->renameTable('link_deal_to_appointment', 'dea2app');
	}
}