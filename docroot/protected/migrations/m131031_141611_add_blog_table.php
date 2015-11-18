<?php

class m131031_141611_add_blog_table extends CDbMigration
{

	public function up()
	{
		$this->createTable('blog', array(
										'id'            => 'pk',
										'title'         => 'string',
										'body'          => 'text',
										'created'       => 'datetime',
										'createdBy'     => 'int unsigned not null',
										'status'        => 'enum("Draft", "Published", "Archived") NOT NULL default "Draft"',
										'featuredImage' => 'int unsigned',
										'deleted'       => 'tinyint(1) unsigned not null default 0',
								   ));
	}

	public function down()
	{
		$this->dropTable('blog');
	}

}