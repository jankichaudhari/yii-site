<?php
include_once __DIR__ . '/../bootstrap.php';
class UserControllerTest extends WebTestCase2
{

	const USERNAME = 'testuser';
	const PASSWORD = 'password';
	const EMAIL    = 'email@something.com';
	const NAME     = 'testname';
	const SURNAME  = 'testsurname';

	public function testUserCreate()
	{

		$this->deleteUser();
		$this->login();

		$this->url('admin4/user/create');
		$this->assertRegExp('/admin v4 - create user/i', $this->title());

		$this->byId('User_use_username')->value(self::USERNAME);
		$this->byId('User_use_password')->value(self::PASSWORD);
		$this->byId('User_use_fname')->value(self::SURNAME);
		$this->byId('User_use_sname')->value(self::EMAIL);
		$this->byId('User_use_email')->value(self::EMAIL);

		/** @var $roles UserRole[] */
		$roles = UserRole::model()->findAll();
		foreach ($roles as $role) {
			$this->byId('User_role_' . $role->rol_id)->click();
		}

		$this->byId('user-form')->submit();
		$this->assertRegExp('/admin4\/user\/update/i', $this->url());
		$this->getBrowser();
//		$this->assertRegExp('/succesfully created/i', $this->source());

	}

	public function login()
	{

		$this->url('admin4/site/login');
		$this->assertRegExp('/Admin v4 - login site/i', $this->title());
		$this->byId('LoginForm_username')->value('vitaly.suhanov');
		$this->byId('LoginForm_password')->value('SuhanovVSuhanovV');
		$this->byId('login-form')->submit();
		$this->assertRegExp('/home - v3.0/i', $this->title());
	}

	public function setUp()
	{

		parent::setUp();
	}

	public function deleteUser()
	{

		User::model()->deleteAllByAttributes(array(
												  'use_username' => self::USERNAME
											 ));
	}

}