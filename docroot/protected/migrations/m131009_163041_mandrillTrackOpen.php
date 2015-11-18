<?php

class m131009_163041_mandrillTrackOpen extends CDbMigration
{
	private $table = 'mandrillTrackOpen';

	public function up()
	{
		$this->createTable($this->table, array(
											  'id'             => 'pk',
											  'emailId'        => 'varchar(64) NOT NULL',
											  'opened'         => 'datetime',
											  'mobile'         => 'tinyint(1) NOT NULL default 0',
											  'os_company'     => 'string',
											  'os_company_url' => 'string',
											  'os_family'      => 'string',
											  'os_icon'        => 'string',
											  'os_name'        => 'string',
											  'os_url'         => 'string',
											  'type'           => 'string',
											  'ua_company'     => 'string',
											  'ua_company_url' => 'string',
											  'ua_family'      => 'string',
											  'ua_icon'        => 'string',
											  'ua_name'        => 'string',
											  'ua_url'         => 'string',
											  'ua_version'     => 'string',
											  'country_short'  => 'string',
											  'country_long'   => 'string',
											  'region'         => 'string',
											  'timezone'       => 'string',
											  'latitude'       => 'FLOAT',
											  'longitude'      => 'FLOAT',
											  'INDEX emailId (emailId)'
										 ));
	}

	public function down()
	{
		$this->dropTable($this->table);
	}
}