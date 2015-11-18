<?php

class m140211_170523_add_sendMessage_sendEmail_to_client_interest_linking_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('clientInterest', 'text', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "1 if text message was sent" AFTER clientId');
		$this->addColumn('clientInterest', 'email', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "1 if email was sent" AFTER text');
	}

	public function down()
	{
		$this->dropColumn('clientInterest', 'text');
		$this->dropColumn('clientInterest', 'email');
	}

}