<?php

/**
 * This is the model class for table "fullViewStatistic".
 *
 * The followings are the available columns in table 'fullViewStatistic':
 * @property string $id
 * @property string $requestURI
 * @property string $queryString
 * @property string $requestMethod
 * @property string $scriptName
 * @property string $userAgent
 * @property string $ip
 * @property string $referer
 * @property string $session
 * @property string $phpSelf
 * @property string $requestTime
 */
class FullViewStatistic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FullViewStatistic the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'stat_fullViewStatistic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('requestURI, requestMethod, scriptName, userAgent, ip, session, phpSelf, requestTime', 'required'),
			array('requestURI, queryString, scriptName, userAgent, ip, referer, session, phpSelf', 'length', 'max'=>255),
			array('requestMethod', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, requestURI, queryString, requestMethod, scriptName, userAgent, ip, referer, session, phpSelf, requestTime', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'requestURI' => 'Request Uri',
			'queryString' => 'Query String',
			'requestMethod' => 'Request Method',
			'scriptName' => 'Script Name',
			'userAgent' => 'User Agent',
			'ip' => 'Ip',
			'referer' => 'Referer',
			'session' => 'Session',
			'phpSelf' => 'Php Self',
			'requestTime' => 'Request Time',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('requestURI',$this->requestURI,true);
		$criteria->compare('queryString',$this->queryString,true);
		$criteria->compare('requestMethod',$this->requestMethod,true);
		$criteria->compare('scriptName',$this->scriptName,true);
		$criteria->compare('userAgent',$this->userAgent,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('referer',$this->referer,true);
		$criteria->compare('session',$this->session,true);
		$criteria->compare('phpSelf',$this->phpSelf,true);
		$criteria->compare('requestTime',$this->requestTime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}