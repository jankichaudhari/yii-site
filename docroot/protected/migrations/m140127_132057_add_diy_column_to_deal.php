<?php

class m140127_132057_add_diy_column_to_deal extends CDbMigration
{
	public function up()
	{
		$this->addColumn('deal', 'DIY', 'enum("None", "DIY", "DIT") NOT NULL DEFAULT "None"');
	}

	public function down()
	{
		$this->dropColumn('deal', 'DIY');
	}

}