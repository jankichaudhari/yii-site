<?php

/**
 * This is the model class for table "propertyviews".
 *
 * The followings are the available columns in table 'propertyviews':
 * @property integer $id
 * @property integer $dea_id
 * @property string  $datetime
 * @property string  $session
 * @property string  $ip
 */
class PropertyView extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PropertyView the static model class
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

		return 'propertyviews';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'instruction' => array(self::BELONGS_TO, 'Deal', 'dea_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'       => 'ID',
			'dea_id'   => 'Instruction',
			'datetime' => 'Date',
			'session'  => 'Session',
			'ip'       => 'IP',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		return new CActiveDataProvider($this);
	}
}