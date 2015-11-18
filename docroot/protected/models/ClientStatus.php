<?php

/**
 * This is the model class for table "cstatus".
 *
 * The followings are the available columns in table 'cstatus':
 * @property integer $cst_id
 * @property string  $cst_title
 * @property string  $cst_scope
 *
 * @method \ClientStatus sales()
 * @method \ClientStatus lettings()
 */
class ClientStatus extends CActiveRecord
{
	const STATUS_SALES    = 'Sales';
	const STATUS_LETTINGS = 'Lettings';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ClientStatus the static model class
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

		return 'cstatus';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('cst_title', 'length', 'max' => 255),
			array('cst_scope', 'length', 'max' => 8),
			array('cst_id, cst_title, cst_scope', 'safe', 'on' => 'search'),
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
			'cst_id'    => 'Cst',
			'cst_title' => 'Cst Title',
			'cst_scope' => 'Cst Scope',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('cst_id', $this->cst_id);
		$criteria->compare('cst_title', $this->cst_title, true);
		$criteria->compare('cst_scope', $this->cst_scope, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	public function scopes()
	{

		return array(
			'sales'    => array('condition' => "cst_scope = '" . self::STATUS_SALES . "'"),
			'lettings' => array('condition' => "cst_scope ='" . self::STATUS_LETTINGS . "'")
		);
	}

}