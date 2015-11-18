<?php

class m130212_193406_add_search_field_to_address extends CDbMigration
{
	public function up()
	{
		$this->addColumn('address', 'searchString', 'text');
	}

	public function down()
	{
		$this->dropColumn('address', 'searchString');
	}


}