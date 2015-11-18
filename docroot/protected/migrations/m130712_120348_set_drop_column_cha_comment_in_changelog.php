<?php

class m130712_120348_set_drop_column_cha_comment_in_changelog extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('changelog', 'cha_comment');
	}

	public function down()
	{
		echo "m130712_120348_set_drop_column_cha_comment_in_changelog does not support migration down.\n";
		return false;
	}

}