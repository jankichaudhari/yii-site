<?php

class m130425_164433_add_file_field_to_portal_fedd extends CDbMigration
{
	public function up()
	{

		$this->addColumn('portal_ftp', 'filename', 'string');
	}

	public function down()
	{

		$this->dropColumn('portal_ftp', 'filename');
	}
}