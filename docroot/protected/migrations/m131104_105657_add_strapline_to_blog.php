<?php

class m131104_105657_add_strapline_to_blog extends CDbMigration
{
	public function up()
	{
		$this->addColumn('blog', 'strapline', 'text');
	}

	public function down()
	{
		$this->dropColumn('blog', 'strapline');
	}

}