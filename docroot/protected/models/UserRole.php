<?php

/**
 * This is the model class for table "role".
 *
 * The followings are the available columns in table 'role':
 * @property integer $rol_id
 * @property string  $rol_title
 * @property string  $rol_blurb
 */
class UserRole extends CActiveRecord
{

	const SUPER_ADMIN = 'SuperAdmin';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserRole the static model class
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
		return 'role';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
				array('rol_blurb', 'required'),
				array('rol_title', 'length', 'max' => 100),
				array('rol_id, rol_title, rol_blurb', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'rol_id'    => 'Rol',
				'rol_title' => 'Rol Title',
				'rol_blurb' => 'Rol Blurb',
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
}