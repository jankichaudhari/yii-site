<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	private $_branchId;
	private $_scope;
	private $_roles;

	private $userData = array();

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{

		$username = strtolower($this->username);
		/** @var $user User */
		$user = User::model()->with('roles')->find('LOWER(use_username)=?', array($username));
		if ($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else {
			if (!$user->validatePassword($this->password)) {
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
			} else {
				$this->_id       = $user->use_id;
				$this->username  = $user->use_fname;
				$this->_branchId = $user->use_branch;
				$this->_scope    = $user->use_scope;
				$this->_roles    = $user->roles;

				$this->userData = serialize($user);

				Yii::app()->user->setState("fullname", $user->getFullName());

				$this->errorCode = self::ERROR_NONE;
				$this->loadSessionForOldLogin($user);
			}
		}
		LoginLog::model()->log($this, $user);
		return $this->errorCode == self::ERROR_NONE;
	}

	public function getId()
	{

		return $this->_id;
	}

	/**
	 * @param $user
	 * @return \UserIdentity
	 */
	private function loadSessionForOldLogin(User $user)
	{

		$roles = array();
		foreach ($user->roles as $key => $role) {
			$roles[] = $role->rol_title;
		}

		$auth = array(
			"use_id"        => $user->use_id,
			"use_loa"       => $user->use_loa,
			"use_username"  => $user->use_username,
			"use_email"     => $user->use_email,
			"use_fname"     => $user->use_fname,
			"use_sname"     => $user->use_sname,
			"use_branch"    => $user->use_branch,
			"session_id"    => session_id(),
			"roles"         => $roles,
			"default_scope" => $user->use_scope,
		);
		/** @var $session CHttpSession */
		$session = Yii::app()->getSession();
		$session->add("auth", $auth);
		$session->add("s_userid", $user->use_id);
		$session->add("s_user", $user->use_username);
		$session->add("s_name", trim($user->use_fname . " " . $user->use_sname));
		return $this;
	}

	public function getScope()
	{

		return $this->_scope;
	}

	public function getBranchId()
	{

		return $this->_branchId;
	}

	public function getRoles()
	{

		return $this->_roles;
	}

	public function getUserData()
	{

		return $this->userData;
	}
}