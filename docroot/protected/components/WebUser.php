<?php
/**
 * User: vitaly.suhanov
 * Date: 21/02/12
 * Time: 15:02
 */

class WebUser extends CWebUser
{
	private $_rolesCache;

	/**
	 * @param IUserIdentity $identity
	 * @param int           $duration
	 * @return bool
	 */
	public function login($identity, $duration = 0)
	{

		$this->setState("__branchId", $identity->getBranchId());
		$this->setState("__scope", $identity->getScope());
		$this->setState("__roles", $identity->getRoles());
		$this->setState("__userData", $identity->getUserData());
		return parent::login($identity, $duration);
	}

	public function getScope()
	{

		return $this->getState("__scope");
	}

	public function getBranchId()
	{

		return $this->getState("__branchId");
	}

	/**
	 * @return UserRole[]
	 */
	public function getRoles()
	{

		return $this->getState("__roles");
	}

	public function getUserObject()
	{

		return unserialize($this->getState('__userData'));
	}

	/**
	 * Checks whether user jhas a role named $role
	 * @param $role
	 * @return bool
	 */
	public function is($role)
	{

		if ($this->_rolesCache === null) {
			if ($this->getRoles()) {
				foreach ($this->getRoles() as $value) {
					$this->_rolesCache[strtolower($value->rol_title)] = true;
				}
			} else {
				$this->_rolesCache = array();
			}

		}
		return array_key_exists(strtolower($role), $this->_rolesCache);
	}

}
