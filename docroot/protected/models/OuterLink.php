<?php

/**
 * This is the model class for table "outerLinks".
 *
 * The followings are the available columns in table 'outerLinks':
 * @property integer $id
 * @property string  $title
 * @property string  $description
 * @property string  $link
 */
class OuterLink extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OuterLink the static model class
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

		return 'outerLinks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('title, link', 'required'),
//			array('link', 'url'),
			array('title, link', 'length', 'max' => 255),
			array('description', 'type', 'type' => 'string'),
			array('id, title, description, link, image', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'image' => array(self::HAS_ONE, 'OuterLinkImage', 'recordId')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'          => 'ID',
			'title'       => 'Title',
			'description' => 'Description',
			'link'        => 'Link',
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

