<?php

include_once __DIR__ . '/bootstrap.php';
class FeedPortalTest extends ActiveRecordTest
{
	const PORTAL_NAME     = 'Rightmove';
	const FTP_SERVER      = '10.1.14.94';
	const FTP_USERNAME    = 'ftp_testing';
	const FTP_PASSWORD    = 'ftp_testing';
	const FTP_DEST_FOLDER = '/live/upload';
	const FILENAME        = 'testfeed.php';

	public function testSaveWorks()
	{

		$model = $this->getModel();

		$model->attributes = array(
			'portal_name'     => self::PORTAL_NAME,
			'ftp_server'      => self::FTP_SERVER,
			'ftp_username'    => self::FTP_USERNAME,
			'ftp_password'    => self::FTP_PASSWORD,
			'ftp_dest_folder' => self::FTP_DEST_FOLDER,
			'filename'        => self::FILENAME,
		);

		$this->assertTrue($model->validate());

		$this->assertEquals(self::PORTAL_NAME, $model->portal_name);
		$this->assertEquals(self::FTP_SERVER, $model->ftp_server);
		$this->assertEquals(self::FTP_USERNAME, $model->ftp_username);
		$this->assertEquals(self::FTP_PASSWORD, $model->ftp_password);
		$this->assertEquals(self::FTP_DEST_FOLDER, $model->ftp_dest_folder);
		$this->assertEquals(self::FILENAME, $model->filename);

	}

	/**
	 * @param string $scenario
	 * @return FeedPortal
	 */
	protected function getModel($scenario = 'insert')
	{

		return new FeedPortal($scenario);
	}

}
