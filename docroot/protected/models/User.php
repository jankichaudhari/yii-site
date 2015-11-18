<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer    $use_id
 * @property string     $use_loa
 * @property string     $use_status
 * @property string     $use_username
 * @property string     $use_password
 * @property string     $use_salt
 * @property string     $use_email
 * @property string     $use_salutation
 * @property string     $use_fname
 * @property string     $use_sname
 * @property integer    $use_pro
 * @property string     $use_pcid
 * @property string     $use_addr1
 * @property string     $use_addr2
 * @property string     $use_addr3
 * @property string     $use_addr4
 * @property string     $use_addr5
 * @property integer    $use_country
 * @property string     $use_postcode
 * @property string     $use_worktel
 * @property string     $use_ext
 * @property string     $use_hometel
 * @property string     $use_mobile
 * @property integer    $use_branch
 * @property string     $use_colour
 * @property string     $use_start
 * @property string     $use_scope
 * @property string     $use_notify
 * @property string     $defaultCalendarID
 * @property UserRole[] $roles
 * @property Branch     $branch
 *
 * @method User onlyActive()
 * @method User alphabetically()
 *
 */
class User extends CActiveRecord implements Filterable
{

	public $use_loa = '';
	public $use_status = 'Active';
	public $use_salutation = '';
	public $use_country = '';
	public $use_branch = '';
	public $use_scope = "";
	public $fullName;

	public $filterCriteria = null;

	const SCOPE_SALES = 'Sales';

	const SCOPE_LETTINGS = 'Lettings';

	public static function listData()
	{
		return CHtml::listData(self::model()->onlyActive()->alphabetically()->findAll(), "use_id", "fullName");
	}

	protected function afterFind()
	{

		$this->fullName = trim($this->use_fname . " " . $this->use_sname);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
				['use_password', 'required', 'on' => 'insert'],
				['use_scope', 'default', 'value' => self::SCOPE_SALES],
				['use_pro, use_country, use_branch, defaultCalendarID', 'numerical', 'integerOnly' => true],
				['use_loa', 'length', 'max' => 13],
				['use_username, use_email', 'required'],
				['use_username', 'unique'],
				array(
						'use_status, use_scope',
						'length',
						'max' => 8
				),
				array(
						'use_username, use_password, use_salt, use_postcode, use_worktel, use_ext, use_hometel, use_mobile',
						'length',
						'max' => 50
				),
				array(
						'use_email',
						'length',
						'max' => 255
				),
				array('use_email', 'email'),
				array(
						'use_salutation',
						'length',
						'max' => 5
				),
				array(
						'use_fname, use_sname, use_pcid, use_addr1, use_addr2, use_addr3, use_addr4, use_addr5',
						'length',
						'max' => 100
				),
				array(
						'use_colour',
						'length',
						'max' => 6
				),
				array(
						'use_notify',
						'length',
						'max' => 3
				),
				array('use_start', 'safe'),
				array(
						'use_id, use_loa, use_status, use_username, use_password, use_salt, use_email, use_salutation, use_fname, use_sname, use_pro, use_pcid, use_addr1, use_addr2, use_addr3, use_addr4, use_addr5, use_country, use_postcode, use_worktel, use_ext, use_hometel, use_mobile, use_branch, use_colour, use_start, use_scope, use_notify, defaultCalendarID',
						'safe',
						'on' => 'search'
				),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
				'roles'                    => array(self::MANY_MANY, 'UserRole', 'link_user_to_role(u2r_use, u2r_rol)'),
				'branch'                   => array(self::BELONGS_TO, 'Branch', 'use_branch'),
				'defaultCalendar'          => array(self::BELONGS_TO, 'Branch', 'defaultCalendarID'),
				'features'                 => [
						self::MANY_MANY,
						'Feature',
						'link_instruction_to_feature(dealId, featureId)'
				],
				"emailAlertsForDealStatus" => array(
						self::HAS_MANY,
						"UserConfig",
						"userId",
						'on' => "emailAlertsForDealStatus.configType = '" . UserConfig::TYPE_EMAIL_ALERT . "' AND emailAlertsForDealStatus.configKey = '" . UserConfig::KEY_EMAIL_ALERT_DEAL_STATUS . "'"
				),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
				'use_id'            => 'ID',
				'use_loa'           => 'Loa',
				'use_status'        => 'Status',
				'use_username'      => 'Username',
				'use_password'      => 'Password',
				'use_salt'          => 'Salt',
				'use_email'         => 'Email',
				'use_salutation'    => 'Salutation',
				'use_fname'         => 'Firstname',
				'use_sname'         => 'Surname',
				'use_pro'           => 'Pro',
				'use_pcid'          => 'Pcid',
				'use_addr1'         => 'Address line1',
				'use_addr2'         => 'Address Line2',
				'use_addr3'         => 'Address Line3',
				'use_addr4'         => 'Address Line4',
				'use_addr5'         => 'Address Line5',
				'use_country'       => 'Country',
				'use_postcode'      => 'Postcode',
				'use_worktel'       => 'Work Tel',
				'use_ext'           => 'Extension',
				'use_hometel'       => 'Home Telephone',
				'use_mobile'        => 'Mobile',
				'use_branch'        => 'Default Branch',
				'use_colour'        => 'Colour',
				'use_start'         => 'Start',
				'use_scope'         => 'Default Scope',
				'use_notify'        => 'Notify',
				'defaultCalendarID' => 'Default Calendar',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(CDbCriteria $criteria = null)
	{

		$criteria = $criteria ? $criteria : new CDbCriteria();

		if ($this->filterCriteria instanceof CDbCriteria) {
			$criteria->mergeWith($this->filterCriteria);
		}
		$criteria->compare('use_id', $this->use_id);
		$criteria->compare('use_status', $this->use_status, false, 'AND', false);
		$criteria->compare('use_username', $this->use_username, true);
		$criteria->compare('use_email', $this->use_email, true);

		$criteria->compare('use_fname', $this->use_fname . '%', true, 'AND', false);
		$criteria->compare('use_sname', $this->use_sname . '%', true, 'AND', false);

		$criteria->compare('use_worktel', $this->use_worktel, true);
		$criteria->compare('use_ext', $this->use_ext, true);
		$criteria->compare('use_hometel', $this->use_hometel, true);
		$criteria->compare('use_mobile', $this->use_mobile, true);
		$criteria->compare('use_branch', '=' . $this->use_branch);
		$criteria->compare('use_scope', $this->use_scope, false, 'AND', false);
		$criteria->compare('defaultCalendarID', $this->defaultCalendarID);

		return new CActiveDataProvider($this, CMap::mergeArray(array(
																	   'criteria' => $criteria,
															   ), Yii::app()->params['CActiveDataProvider']));

	}

	/*
	 *
	 */
	public function getFullUsername()
	{

		return trim($this->use_fname . " " . $this->use_sname);
	}

	/**
	 * @param $password
	 * @return bool
	 */
	public function validatePassword($password)
	{

		$password = $this->hashPassword($password, $this->use_salt);
		return $password == $this->use_password;

	}

	public function hashPassword($password, $salt)
	{

		return md5($salt . md5($password . $salt));
	}

	public function getFullName()
	{

		return trim($this->use_fname . " " . $this->use_sname);
	}

	protected function beforeSave()
	{

		if (!$this->isNewRecord) {
			$copy = $this->findByPk($this->use_id);
			if ($this->use_password !== $copy->use_password) {
				$this->rehashPassword();
			}
		} else {
			$this->rehashPassword();
			$this->use_loa    = 'User';
			$this->use_notify = 'No';
		}
		return parent::beforeSave();
	}

	/**
	 * Users saves roles.
	 */
	protected function afterSave()
	{

		$roles = $this->roles;
		$sql   = "DELETE FROM link_user_to_role WHERE u2r_use = :userId";
		Yii::app()->db->createCommand($sql)->execute(['userId' => $this->use_id]);

		$sql = [];
		foreach ($roles as $role) {
			$sql[] = '("' . $this->use_id . '", "' . $role->rol_id . '")';
		}
		if ($sql) {
			$sql = "INSERT INTO link_user_to_role (u2r_use, u2r_rol) VALUES " . implode(', ', $sql) . "";
			Yii::app()->db->createCommand($sql)->execute();
		}

	}

	public function getPossibleLoaTypes()
	{

		return array_combine($t = array('Guest', 'User', 'Administrator'), $t);
	}

	public function getPossibleUserStatus()
	{

		return array_combine($t = array('Active', 'Disabled'), $t);
	}

	public function getPossibleSalutations()
	{

		return array_combine($t = array('Mr.', 'Mrs.', 'Miss.', 'Ms.', 'Dr.', 'Rev.'), $t);
	}

	public function getPossibleScope()
	{

		return array_combine($t = array(self::SCOPE_SALES, self::SCOPE_LETTINGS), $t);
	}

	public function getPossibleNotify()
	{

		return array_combine($t = array('Yes', 'No'), $t);
	}

	public function userBelongsToRole($roleId)
	{

		static $userRolesCache;
		if ($userRolesCache === null) {
			$userRolesCache = array();
			foreach ($this->roles as $role) {
				$userRolesCache[] = $role->rol_id;
			}
		}
		return in_array($roleId, $userRolesCache);
	}

	/**
	 * @param $dealStatus
	 * @return bool
	 */
	public function userEmailAlertForDealStatus($dealStatus)
	{

		static $userAlerts;
		if ($userAlerts === null) {
			$userAlerts = array();
			foreach ($this->emailAlertsForDealStatus as $alertConfig) {
				$userAlerts[] = $alertConfig->configValue;
			}
		}
		return in_array($dealStatus, $userAlerts);
	}

	public function scopes()
	{

		return array(
				'onlyActive'     => array('condition' => "use_status = 'Active'"),
				'alphabetically' => array('order' => 'use_fname ASC')
		);
	}

	public function setFilterCriteria(CDbCriteria $criteria)
	{

		$this->filterCriteria = $criteria;
	}

	public function getFilterCriteria()
	{

		return $this->filterCriteria;
	}

	public function toArray()
	{

		$data = $this->attributes;
		return array_merge($data, array('fullName' => $this->fullName));
	}

	public function getInitials()
	{

		return strtoupper(substr($this->use_fname, 0, 1) . substr($this->use_sname, 0, 1));
	}

	public function rehashPassword()
	{

		$this->use_salt     = md5(time());
		$this->use_password = $this->hashPassword($this->use_password, $this->use_salt);
	}

}