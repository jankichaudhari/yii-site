<?php

/**
 * This is the model class for table "login".
 *
 * The followings are the available columns in table 'login':
 * @property integer $log_id
 * @property string  $log_timestamp
 * @property string  $log_session
 * @property string  $log_ip
 * @property string  $log_agent
 * @property string  $log_result
 * @property string  $log_lock
 * @property integer $log_use_id
 * @property string  $log_use_username
 * @property string  $log_errmsg
 */
class LoginLog extends CActiveRecord
{
	const Fail    = "Fail";
	const Success = "Success";

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LoginLog the static model class
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

		return 'login';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('log_use_id', 'numerical', 'integerOnly'=> true),
			array(
				'log_session', 'length',
				'max' => 100
			),
			array(
				'log_ip', 'length',
				'max' => 30
			),
			array(
				'log_agent', 'length',
				'max' => 255
			),
			array(
				'log_result', 'length',
				'max' => 7
			),
			array(
				'log_use_username, log_errmsg', 'length',
				'max' => 50
			),
			array('log_lock', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'log_id, log_timestamp, log_session, log_ip, log_agent, log_result, log_lock, log_use_id, log_use_username, log_errmsg', 'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'log_id'           => 'Log',
			'log_timestamp'    => 'Log Timestamp',
			'log_session'      => 'Log Session',
			'log_ip'           => 'Log Ip',
			'log_agent'        => 'Log Agent',
			'log_result'       => 'Log Result',
			'log_lock'         => 'Log Lock',
			'log_use_id'       => 'Log Use',
			'log_use_username' => 'Log Use Username',
			'log_errmsg'       => 'Log Errmsg',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('log_id', $this->log_id);
		$criteria->compare('log_timestamp', $this->log_timestamp, true);
		$criteria->compare('log_session', $this->log_session, true);
		$criteria->compare('log_ip', $this->log_ip, true);
		$criteria->compare('log_agent', $this->log_agent, true);
		$criteria->compare('log_result', $this->log_result, true);
		$criteria->compare('log_lock', $this->log_lock, true);
		$criteria->compare('log_use_id', $this->log_use_id);
		$criteria->compare('log_use_username', $this->log_use_username, true);
		$criteria->compare('log_errmsg', $this->log_errmsg, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	public function log(CUserIdentity $userIdentity, User $user = null)
	{

		/** @var $session CHttpSession */
		$session = Yii::app()->getSession();

		$log              = new LoginLog();
		$log->log_ip      = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "");
		$log->log_agent   = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "");
		$log->log_session = $session->getSessionID();
		if ($user === null) {
			$log->log_result = self::Fail;
			$log->log_errmsg = "User is not found";
		} else {
			$log->log_use_username = $user->use_username;
			$log->log_use_id       = $user->use_id;
			if (!$user->use_salt) {
				$log->log_result = self::Fail;
				$log->log_errmsg = "User does not has salt";
			}
		}
		switch ($userIdentity->errorCode) {
			default:
			case CUserIdentity::ERROR_NONE :
				$log->log_result = self::Success;
				break;
			case CUserIdentity::ERROR_USERNAME_INVALID :
				$log->log_result = self::Fail;
				$log->log_errmsg = "User is not found"; // impossible situation. we already check whether user exists or not.
				break;
			case CUserIdentity::ERROR_PASSWORD_INVALID : // password did not match.
				$log->log_result = self::Fail;
				$log->log_errmsg = "Password did not match";
				break;
		}
		$log->save();
		return;
	}
}