<?php
class m131025_135413_add_template_file_path_to_mailshotType extends CDbMigration
{
	private $column = 'templatePath';

	private $table = 'mailshotType';

	public function up()
	{
		$this->addColumn($this->table, $this->column, 'string');
	}

	public function down()
	{
		$this->dropColumn($this->table, $this->column);
	}

}