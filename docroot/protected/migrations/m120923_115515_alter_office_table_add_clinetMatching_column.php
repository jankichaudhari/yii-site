<?php

class m120923_115515_alter_office_table_add_clinetMatching_column extends CDbMigration
{
	public function up()
	{
		$this->addColumn('office', 'clientMatching', "int(1) NOT NULL DEFAULT 0 COMMENT '1/0 boolean marks office as available for clinet matching'");
	}

	public function down()
	{
		$this->dropColumn('office', 'clientMatching');
	}
}