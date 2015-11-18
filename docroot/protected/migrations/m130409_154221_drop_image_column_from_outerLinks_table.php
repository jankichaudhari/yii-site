<?php

class m130409_154221_drop_image_column_from_outerLinks_table extends CDbMigration
{
	public function up()
	{

		$this->dropColumn('outerLinks', 'image');
	}

	public function down()
	{

		return $this->addColumn('outerLinks', 'image', 'string');
	}

}