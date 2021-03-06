<?php

include_once __DIR__ . '/bootstrap.php';
class UserTest extends ActiveRecordTest
{
	const USERNAME = 'testusername';

	/**
	 * @param string $scenario
	 * @return User
	 */
	protected function getModel($scenario = 'insert')
	{

		return new User($scenario);
	}

	public function testPasswordIsRequired()
	{

		$model               = $this->getModel();
		$model->use_password = null;
		$this->assertFalse($model->validate(['use_password']));
	}

	public function testPasswordIsHashedAfterSave()
	{

		$model               = $this->getModel();
		$model->use_password = 'password';
		$model->use_username = self::USERNAME;
		$model->save(false);
		$this->assertNotEquals('password', $model->use_password);
		$this->assertTrue($model->validatePassword('password'));

	}

	public function testPasswordIsNotChangedOnUpdate()
	{

		$model               = $this->getModel();
		$model->use_password = 'password';
		$model->use_username = self::USERNAME;
		$model->save(false);
		$model->save(false);
		$this->assertTrue($model->validatePassword('password'));
	}

	public function testPasswordIsRehashedIfChanged()
	{

		$model               = $this->getModel();
		$model->use_password = 'password';
		$model->use_username = self::USERNAME;
		$model->save(false);

		$salt                = $model->use_salt;
		$model->use_password = 'anotherpassword';

		sleep(1); // salt is generated based on time.

		$model->save(false);
		$this->assertTrue($model->validatePassword('anotherpassword'));
		$this->assertNotEquals($salt, $model->use_salt);

	}

	public function testUserSavesRolesOnSave()
	{

		$model               = $this->getModel();
		$model->use_username = self::USERNAME;

		$model->roles = $roles = UserRole::model()->findAll();

		$model->save(false);
		/** @var $model User */
		$model = User::model()->findByPk($model->use_id);
		$this->assertTrue(is_array($model->roles));
		$this->assertEquals(count($roles), count($model->roles));

	}

	protected function setUp()
	{

		User::model()->deleteAllByAttributes(array('use_username' => self::USERNAME));
		parent::setUp(); // TODO: Change the autogenerated stub
	}

}
