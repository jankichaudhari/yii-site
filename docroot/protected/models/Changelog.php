<?php

/**
 * This is the model class for table "changelog".
 *
 * The followings are the available columns in table 'changelog':
 * @property integer $cha_id
 * @property string  $cha_datetime
 * @property integer $cha_user
 * @property string  $cha_session
 * @property string  $cha_action
 * @property string  $cha_table
 * @property integer $cha_row
 * @property string  $cha_field
 * @property string  $cha_old
 * @property string  $cha_new
 * @property User    $creator
 */
class Changelog extends CActiveRecord
{
	const ACTION_UPDATE = 'UPDATE';
	const ACTION_INSERT = 'INSERT';
	const ACTION_DELETE = 'DELETE';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Changelog the static model class
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
		return 'changelog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('cha_old, cha_new, cha_field, cha_table, cha_row', 'required'),
			array('cha_id, cha_datetime, cha_user, cha_session, cha_action, cha_table, cha_row, cha_field, cha_old, cha_new', 'safe', 'on' => 'search'),
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
			'creator' => [self::BELONGS_TO, 'User', 'cha_user', 'together' => true],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'cha_id'       => 'Cha',
			'cha_datetime' => 'Cha Datetime',
			'cha_user'     => 'Cha User',
			'cha_session'  => 'Cha Session',
			'cha_action'   => 'Cha Action',
			'cha_table'    => 'Cha Table',
			'cha_row'      => 'Cha Row',
			'cha_field'    => 'Cha Field',
			'cha_old'      => 'Cha Old',
			'cha_new'      => 'Cha New',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;
		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	protected function beforeSave()
	{
		if (!$this->isNewRecord) {
			return false; // we can not update change log records. that would not make any sense
		}
		$this->cha_datetime = date("Y-m-d H:i:s");
		$this->cha_user     = Yii::app()->user->getId();

		return parent::beforeSave();
	}

}