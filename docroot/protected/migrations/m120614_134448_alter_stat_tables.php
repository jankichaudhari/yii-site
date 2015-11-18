<?php

class m120614_134448_alter_stat_tables extends CDbMigration
{
	public function up()
	{
		$this->renameTable("pageViewStatistic", "stat_pageViewStatistic");
		$this->renameTable("fullViewStatistic", "stat_fullViewStatistic");
	}

	public function down()
	{
		$this->renameTable("stat_pageViewStatistic", "pageViewStatistic");
		$this->renameTable("stat_fullViewStatistic", "fullViewStatistic");
	}
}